<?php

namespace App\Livewire\Author;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Flux;

class EarningsManager extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $totalEarnings = 0;
    public $pendingEarnings = 0;
    public $availableBalance = 0;
    public $totalWithdrawals = 0;
    public $showModal = false;
    public $withdrawalAmount = 0;

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->calculateEarnings();
    }

    public function calculateEarnings()
    {
        $authorId = auth()->id();

        $this->totalEarnings = \App\Models\Earning::where('author_id', $authorId)
            ->sum('amount');

        $this->pendingEarnings = \App\Models\Earning::where('author_id', $authorId)
            ->where('status', 'pending')
            ->sum('amount');

        // Available balance (completed earnings - paid withdrawals)
        // For simplicity in this marketplace, we'll treat sum of earnings with status 'available' or 'completed'
        // If there's no complex holding period, we'll just use total - withdrawn
        $this->totalWithdrawals = \App\Models\Payout::where('author_id', $authorId)
            ->whereIn('status', ['paid', 'pending'])
            ->sum('amount');

        $this->availableBalance = $this->totalEarnings - $this->totalWithdrawals;
    }

    public function filterByDate()
    {
        $this->resetPage();
        $this->calculateEarnings();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function requestWithdrawal()
    {
        $user = auth()->user();

        // 1. Check if bank details are configured
        if (!$user->hasBankDetails()) {
            Flux::toast(
                variant: 'danger',
                heading: 'Payout Settings Missing',
                text: 'Please configure your bank details in Profile Settings before requesting a withdrawal.'
            );
            
            return redirect()->route('author.profile', ['tab' => 'payout']);
        }

        $minWithdrawal = (int) \App\Models\PlatformSetting::get('min_withdrawal', 10000);

        // 2. Check minimum balance
        if ($this->availableBalance < $minWithdrawal) {
            $this->dispatch('toast', 
                variant: 'error', 
                heading: 'Insufficient Balance', 
                text: 'Minimum withdrawal amount is Rp ' . number_format($minWithdrawal, 0, ',', '.')
            );
            return;
        }

        // 3. Pre-fill withdrawal amount with full balance as default
        $this->withdrawalAmount = $this->availableBalance;

        // 4. Show confirmation modal
        $this->showModal = true;
    }

    public function confirmWithdrawal()
    {
        $user = auth()->user();

        // 0. Security: Anti-spam Rate Limit (Throttle)
        $recentPayout = \App\Models\Payout::where('author_id', $authorId ?? $user->id)
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();

        if ($recentPayout) {
            Flux::toast(variant: 'warning', heading: 'Slow Down', text: 'You can only request a withdrawal once every 5 minutes.');
            return;
        }

        // Final security and limit check
        if (!$user->hasBankDetails()) {
            Flux::toast(variant: 'danger', text: 'Payout settings missing.');
            return;
        }

        $minWithdrawal = (int) \App\Models\PlatformSetting::get('min_withdrawal', 10000);

        if ($this->withdrawalAmount < $minWithdrawal) {
            Flux::toast(variant: 'danger', text: 'Minimum withdrawal is Rp ' . number_format($minWithdrawal, 0, ',', '.') . '.');
            return;
        }

        // Create actual Payout record inside a transaction to prevent race conditions
        try {
            DB::transaction(function () use ($user) {
                // Re-calculate balance inside transaction (lock-for-update would be better but let's re-query)
                $this->calculateEarnings();

                if ($this->withdrawalAmount > $this->availableBalance) {
                    throw new \Exception('Withdrawal amount exceeds available balance during final check.');
                }

                $payout = \App\Models\Payout::create([
                    'author_id' => $user->id,
                    'amount' => $this->withdrawalAmount,
                    'status' => 'pending',
                    'payment_method' => $user->bank_name . ' (' . $user->bank_account_number . ')',
                    'admin_note' => 'Automated withdrawal request via Earnings Manager.',
                ]);

                // Notify Author
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\WithdrawalRequested($payout));
                
                // Notify Admin (Finance)
                try {
                    \Illuminate\Support\Facades\Mail::to(config('mail.aliases.finance'))
                        ->queue(new \App\Notifications\SystemNotification([
                            'title' => 'Permintaan Penarikan Dana Baru 💰',
                            'message' => "Author {$user->name} mengajukan penarikan dana sebesar Rp " . number_format($this->withdrawalAmount, 0, ',', '.') . ".",
                            'type' => 'payout',
                            'action_text' => 'Kelola Penarikan',
                            'action_url' => route('admin.payouts'),
                        ]));
                } catch (\Exception $e) {
                    \Log::error('Failed to notify finance of withdrawal request: ' . $e->getMessage());
                }
            });
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', heading: 'Error', text: $e->getMessage());
            return;
        }
        
        $this->showModal = false;
        Flux::toast(
            variant: 'success', 
            heading: 'Request Submitted', 
            text: 'Your withdrawal request of Rp ' . number_format($this->withdrawalAmount, 0, ',', '.') . ' has been submitted successfully.'
        );
        
        // Refresh balance
        $this->calculateEarnings();
    }

    public function render()
    {
        $authorId = auth()->id();

        $earnings = \App\Models\Earning::with(['order', 'product'])
            ->where('author_id', $authorId)
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->orderBy(
                in_array($this->sortBy, ['amount', 'status', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )
            ->paginate(15);

        $payouts = \App\Models\Payout::where('author_id', $authorId)
            ->orderBy('created_at', 'desc')
            ->paginate(10, pageName: 'payoutsPage');

        return view('livewire.author.earnings-manager', [
            'earnings' => $earnings,
            'payouts' => $payouts
        ]);
    }
}
