<?php

namespace App\Livewire\Affiliate;

use App\Models\AffiliateEarning;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AffiliateDashboard extends Component
{
    public $withdrawalAmount;
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $user = Auth::user();
        
        // Ensure affiliate code exists
        $clickCount = $user->affiliateClicks()->count();
        $salesCount = $user->affiliateEarnings()->count();
        $ctr = $clickCount > 0 ? ($salesCount / $clickCount) * 100 : 0;

        $totalEarned = $user->affiliateEarnings()->where('status', 'completed')->sum('amount');
        $totalPaid = $user->affiliatePayouts()->whereIn('status', ['pending', 'approved', 'paid'])->sum('amount');
        $balance = max(0, $totalEarned - $totalPaid);

        $stats = [
            'total_earnings' => $user->affiliateEarnings()->sum('amount'),
            'pending_earnings' => $user->affiliateEarnings()->where('status', 'pending')->sum('amount'),
            'completed_earnings' => $totalEarned,
            'balance' => $balance,
            'referral_count' => $user->referrals()->count(),
            'sales_count' => $salesCount,
            'click_count' => $clickCount,
            'ctr' => number_format($ctr, 2),
        ];

        $recentEarnings = $user->affiliateEarnings()
            ->with(['product', 'order'])
            ->latest()
            ->take(10)
            ->get();

        $recentPayouts = $user->affiliatePayouts()
            ->latest()
            ->take(5)
            ->get();

        $activeCoupons = Coupon::where('affiliate_id', $user->id)
            ->where('status', 'active')
            ->get();

        $referralLink = $user->affiliate_code ? route('home', ['ref' => $user->affiliate_code]) : null;

        return view('livewire.affiliate.affiliate-dashboard', [
            'stats' => $stats,
            'recentEarnings' => $recentEarnings,
            'recentPayouts' => $recentPayouts,
            'activeCoupons' => $activeCoupons,
            'referralLink' => $referralLink,
            'isAffiliate' => !empty($user->affiliate_code),
        ])->layout('layouts.app');
    }

    public function joinProgram()
    {
        $user = Auth::user();
        
        if (!$user->affiliate_code) {
            $user->update([
                'affiliate_code' => strtoupper(\Illuminate\Support\Str::random(8))
            ]);

            $this->dispatch('toast', [
                'variant' => 'success',
                'heading' => 'Welcome to Affiliate Hub! 🚀',
                'text' => 'You have successfully joined the NexaAffiliate program.'
            ]);
        }
    }

    public function requestWithdrawal()
    {
        $user = Auth::user();
        
        $totalEarned = $user->affiliateEarnings()->where('status', 'completed')->sum('amount');
        $totalPaid = $user->affiliatePayouts()->whereIn('status', ['pending', 'approved', 'paid'])->sum('amount');
        $balance = max(0, $totalEarned - $totalPaid);

        // Validation
        $this->validate([
            'withdrawalAmount' => [
                'required', 
                'numeric', 
                'min:50000', 
                'max:' . $balance
            ],
        ], [
            'withdrawalAmount.min' => 'Minimum penarikan adalah Rp 50.000.',
            'withdrawalAmount.max' => 'Jumlah penarikan melebihi saldo yang tersedia.',
            'withdrawalAmount.required' => 'Masukkan jumlah yang ingin ditarik.',
        ]);

        // Anti-spam check: check for pending or approved requests
        $hasPending = $user->affiliatePayouts()->whereIn('status', ['pending', 'approved'])->exists();
        if ($hasPending) {
            $this->dispatch('toast', [
                'variant' => 'error',
                'heading' => 'Request Denied',
                'text' => 'You already have a withdrawal request being processed.'
            ]);
            return;
        }

        if (!$user->hasBankDetails()) {
            $this->dispatch('toast', [
                'variant' => 'error',
                'heading' => 'Missing Bank Details',
                'text' => 'Please update your bank details in your profile before requesting a withdrawal.'
            ]);
            return;
        }

        $payout = \App\Models\AffiliatePayout::create([
            'user_id' => $user->id,
            'amount' => $this->withdrawalAmount,
            'bank_name' => $user->bank_name,
            'account_number' => $user->bank_account_number,
            'account_name' => $user->bank_account_name,
            'status' => 'pending',
        ]);

        // Notify Affiliate
        \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\WithdrawalRequested($payout));

        // Notify Admin (Finance)
        try {
            $financeEmail = config('mail.aliases.finance', config('mail.from.address'));
            \Illuminate\Support\Facades\Notification::route('mail', $financeEmail)
                ->notify(new \App\Notifications\SystemNotification([
                    'title' => 'Permintaan Penarikan Dana Affiliate Baru 🛡️',
                    'message' => "Affiliate {$user->name} mengajukan penarikan dana sebesar Rp " . number_format($this->withdrawalAmount, 0, ',', '.') . ".",
                    'type' => 'payout',
                    'action_text' => 'Kelola Penarikan',
                    'action_url' => route('admin.payouts'),
                ]));
        } catch (\Exception $e) {
            \Log::error('Failed to notify finance of affiliate withdrawal: ' . $e->getMessage());
        }

        $this->dispatch('toast', [
            'variant' => 'success',
            'heading' => 'Withdrawal Requested',
            'text' => 'Your withdrawal request of Rp ' . number_format($this->withdrawalAmount, 0, ',', '.') . ' has been submitted.'
        ]);

        $this->reset('withdrawalAmount');
        $this->dispatch('close-modal', name: 'withdrawal-modal');
        $this->loadData();
    }
}
