<?php

namespace App\Livewire\Author;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RefundRequest;
use App\Models\Review;
use Flux;

class RefundManager extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    protected $queryString = [
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

    public function approveRefund($refundId)
    {
        $refund = RefundRequest::whereHas('order.items.product', function($q) {
            $q->where('author_id', auth()->id());
        })->findOrFail($refundId);

        $refund->update([
            'status' => 'approved',
            'processed_at' => now()
        ]);

        // Notify Buyer
        \Illuminate\Support\Facades\Mail::to($refund->user->email)->queue(new \App\Mail\RefundRequestResolved($refund));

        Flux::toast(variant: 'success', text: 'Refund request has been approved.');
    }

    public function rejectRefund($refundId)
    {
        $refund = RefundRequest::whereHas('order.items.product', function($q) {
            $q->where('author_id', auth()->id());
        })->findOrFail($refundId);

        $refund->update([
            'status' => 'rejected',
            'processed_at' => now()
        ]);

        // Notify Buyer
        \Illuminate\Support\Facades\Mail::to($refund->user->email)->queue(new \App\Mail\RefundRequestResolved($refund));

        Flux::toast(variant: 'success', text: 'Refund request has been rejected.');
    }

    public function render()
    {
        $query = RefundRequest::with(['order.user'])
            ->whereHas('order.items.product', function($q) {
                $q->where('author_id', auth()->id());
            });

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->readyToLoad) {
            $refunds = $query->orderBy(
                in_array($this->sortBy, ['status', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )->paginate(15);

            // Calculate stats
            $stats = [
                'total' => RefundRequest::whereHas('order.items.product', function($q) {
                    $q->where('author_id', auth()->id());
                })->count(),
                'pending' => RefundRequest::whereHas('order.items.product', function($q) {
                    $q->where('author_id', auth()->id());
                })->where('status', 'pending')->count(),
                'approved' => RefundRequest::whereHas('order.items.product', function($q) {
                    $q->where('author_id', auth()->id());
                })->where('status', 'approved')->count(),
            ];
        } else {
            $refunds = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = ['total' => 0, 'pending' => 0, 'approved' => 0];
        }

        return view('livewire.author.refund-manager', [
            'refunds' => $refunds,
            'stats' => $stats
        ]);
    }
}
