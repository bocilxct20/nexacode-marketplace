<?php

namespace App\Livewire\Admin\Affiliate;

use App\Models\AffiliatePayout;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WithdrawalProcessed;
use App\Mail\WithdrawalRejected;

class AffiliatePayoutManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusOrder = 'pending'; // pending, approved, paid, rejected
    public $selectedPayout;
    public $adminNote = '';

    protected $queryString = ['search', 'statusOrder'];

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = AffiliatePayout::with('user')
            ->when($this->statusOrder, function ($q) {
                return $q->where('status', $this->statusOrder);
            })
            ->when($this->search, function ($q) {
                return $q->whereHas('user', function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            });

        return view('livewire.admin.affiliate.affiliate-payout-manager', [
            'payouts' => $query->latest()->paginate(10),
            'stats' => [
                'pending_count' => AffiliatePayout::where('status', 'pending')->count(),
                'pending_amount' => AffiliatePayout::where('status', 'pending')->sum('amount'),
                'total_paid' => AffiliatePayout::where('status', 'paid')->sum('amount'),
            ]
        ]);
    }

    public function selectPayout($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->selectedPayout = AffiliatePayout::with('user')->find($id);
        $this->adminNote = $this->selectedPayout->admin_notes;
    }

    public function updateStatus($id, $status)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $payout = AffiliatePayout::findOrFail($id);
        
        $payout->update([
            'status' => $status,
            'admin_notes' => $this->adminNote,
            'processed_at' => in_array($status, ['paid', 'rejected']) ? now() : null,
        ]);

        $this->dispatch('toast', [
            'variant' => $status === 'paid' ? 'success' : ($status === 'rejected' ? 'error' : 'info'),
            'heading' => 'Payout Updated',
            'text' => 'The payout request has been marked as ' . $status . '.'
        ]);

        // Send notification to user
        $payout->user->notify(new \App\Notifications\SystemNotification([
            'title' => 'Payout ' . ucfirst($status) . ' 💰',
            'message' => 'Your withdrawal request of Rp ' . number_format($payout->amount, 0, ',', '.') . ' has been ' . $status . '.',
        ]));

        // Send Email Notifications
        if ($status === 'paid') {
            Mail::to($payout->user->email)->send(new WithdrawalProcessed($payout));
        } elseif ($status === 'rejected') {
            Mail::to($payout->user->email)->send(new WithdrawalRejected($payout, $payout->admin_notes));
        }

        $this->selectedPayout = null;
    }
}
