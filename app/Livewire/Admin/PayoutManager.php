<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payout;
use App\Models\User;
use Flux;

class PayoutManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $showModal = false;
    public $selectedPayout = null;
    public $adminNote = '';
    public $modalType = ''; // 'approve' or 'reject'

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openApproveModal($id)
    {
        $this->selectedPayout = Payout::with('author')->findOrFail($id);
        $this->adminNote = '';
        $this->modalType = 'approve';
        $this->showModal = true;
    }

    public function openRejectModal($id)
    {
        $this->selectedPayout = Payout::with('author')->findOrFail($id);
        $this->adminNote = '';
        $this->modalType = 'reject';
        $this->showModal = true;
    }

    public function processPayout()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        if (!$this->selectedPayout) return;

        $payout = Payout::findOrFail($this->selectedPayout->id);

        if ($this->modalType === 'approve') {
            $payout->update([
                'status' => 'paid',
                'admin_note' => $this->adminNote ?: 'Payout approved and processed.',
            ]);

            Flux::toast(variant: 'success', heading: 'Payout Approved', text: 'Withdrawal for ' . $payout->author->name . ' has been marked as paid.');

            // Notify Author
            \Illuminate\Support\Facades\Mail::to($payout->author->email)->queue(new \App\Mail\WithdrawalProcessed($payout));
        } else {
            if (empty($this->adminNote)) {
                $this->addError('adminNote', 'A reason for rejection is required.');
                return;
            }

            $payout->update([
                'status' => 'rejected',
                'admin_note' => $this->adminNote,
            ]);

            Flux::toast(variant: 'success', heading: 'Payout Rejected', text: 'Withdrawal for ' . $payout->author->name . ' has been rejected.');

            // Notify Author
            \Illuminate\Support\Facades\Mail::to($payout->author->email)->queue(new \App\Mail\WithdrawalRejected($payout, $payout->admin_note));
        }

        $this->showModal = false;
        $this->selectedPayout = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Payout::with('author');

        if ($this->search) {
            $query->whereHas('author', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $query->orderBy(
            in_array($this->sortBy, ['amount', 'status', 'created_at']) ? $this->sortBy : 'created_at',
            in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
        );

        return view('livewire.admin.payout-manager', [
            'payouts' => $query->paginate(15)
        ]);
    }
}
