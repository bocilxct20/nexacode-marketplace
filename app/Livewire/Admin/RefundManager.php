<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use App\Models\RefundRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Flux;

class RefundManager extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $readyToLoad = false;

    // Modal properties
    public $showReviewModal = false;
    public $selectedRefund = null;
    public $adminNotes = '';

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function openReviewModal($refundId)
    {
        $this->selectedRefund = RefundRequest::with(['order.items.product', 'user'])
            ->findOrFail($refundId);
        $this->adminNotes = $this->selectedRefund->admin_notes ?? '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->selectedRefund = null;
        $this->adminNotes = '';
    }

    public function approveRefund()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'adminNotes' => 'nullable|string|max:500',
        ]);

        \DB::transaction(function () {
            // Update refund status
            $this->selectedRefund->update([
                'status' => \App\Enums\RefundStatus::APPROVED,
                'admin_notes' => $this->adminNotes,
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // Cancel the author earning for this order
            $earning = \App\Models\Earning::where('order_id', $this->selectedRefund->order_id)
                ->whereIn('status', [\App\Enums\EarningStatus::PENDING, \App\Enums\EarningStatus::AVAILABLE])
                ->first();

            if ($earning) {
                $earning->cancel();
                
                // Reverse XP
                $xpToDeduct = (int) ($earning->amount / 1000);
                if ($xpToDeduct > 0) {
                    app(\App\Services\AuthorLevelService::class)->deductXp($earning->author, $xpToDeduct);
                }
            }

            // Cancel Platform Earnings
            \App\Models\PlatformEarning::where('order_id', $this->selectedRefund->order_id)->delete();

            // Update order status to refunded
            $this->selectedRefund->order->update(['status' => \App\Enums\OrderStatus::REFUNDED]);

            // Send email notification to user
            try {
                \Illuminate\Support\Facades\Mail::to($this->selectedRefund->user->email)
                    ->queue(new \App\Mail\RefundRequestResolved($this->selectedRefund));
            } catch (\Exception $e) {
                \Log::error('Failed to send refund resolved email: ' . $e->getMessage());
            }
        });

        $this->closeReviewModal();
        
        Flux::toast(
            variant: 'success',
            heading: 'Refund Approved',
            text: 'The refund request has been approved and the user has been notified.'
        );
    }

    public function rejectRefund()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'adminNotes' => 'required|string|min:10|max:500',
        ], [
            'adminNotes.required' => 'Please provide a reason for rejection.',
            'adminNotes.min' => 'Rejection reason must be at least 10 characters.',
        ]);

        $this->selectedRefund->update([
            'status' => \App\Enums\RefundStatus::REJECTED,
            'admin_notes' => $this->adminNotes,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Send email notification to user
        try {
            \Illuminate\Support\Facades\Mail::to($this->selectedRefund->user->email)
                ->queue(new \App\Mail\RefundRequestResolved($this->selectedRefund));
        } catch (\Exception $e) {
            \Log::error('Failed to send refund resolved email: ' . $e->getMessage());
        }

        $this->closeReviewModal();
        
        Flux::toast(
            variant: 'success',
            heading: 'Refund Rejected',
            text: 'The refund request has been rejected and the user has been notified.'
        );
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.admin.refund-manager', [
                'refunds' => RefundRequest::whereRaw('1 = 0')->paginate(10),
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ]
            ]);
        }

        $query = RefundRequest::with(['order.items.product', 'user']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $refunds = $query->latest()->paginate(10);

        $stats = [
            'total' => RefundRequest::count(),
            'pending' => RefundRequest::where('status', \App\Enums\RefundStatus::PENDING)->count(),
            'approved' => RefundRequest::where('status', \App\Enums\RefundStatus::APPROVED)->count(),
            'rejected' => RefundRequest::where('status', \App\Enums\RefundStatus::REJECTED)->count(),
        ];

        return view('livewire.admin.refund-manager', [
            'refunds' => $refunds,
            'stats' => $stats
        ]);
    }
}
