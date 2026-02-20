<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Flux;

class OrdersManager extends Component
{
    use WithPagination;

    public $statusFilter = 'all';

    public $readyToLoad = false;

    // Refund Request
    public $showRefundModal = false;
    public $refundOrderId;
    public $refundReason = '';

    public function openRefundModal($orderId)
    {
        $this->refundOrderId = $orderId;
        $this->refundReason = '';
        $this->showRefundModal = true;
    }

    public function closeRefundModal()
    {
        $this->showRefundModal = false;
        $this->refundOrderId = null;
        $this->refundReason = '';
    }

    public function submitRefundRequest()
    {
        $this->validate([
            'refundReason' => 'required|string|min:10|max:500',
        ], [
            'refundReason.required' => 'Alasan refund harus diisi.',
            'refundReason.min' => 'Alasan refund minimal 10 karakter.',
            'refundReason.max' => 'Alasan refund maksimal 500 karakter.',
        ]);

        // Check for existing refund request
        $existingRefund = \App\Models\RefundRequest::where('order_id', $this->refundOrderId)->first();
        
        if ($existingRefund) {
            $this->closeRefundModal();
            
            Flux::toast(
                variant: 'warning',
                heading: 'Refund Sudah Diajukan',
                text: 'Kamu sudah mengajukan refund untuk order ini. Status: ' . $existingRefund->status->label()
            );
            
            return;
        }

        $order = Order::where('id', $this->refundOrderId)
            ->where('buyer_id', auth()->id())
            ->where('status', \App\Enums\OrderStatus::COMPLETED)
            ->where('type', 'product')
            ->firstOrFail();

        // Create refund request
        $refund = \App\Models\RefundRequest::create([
            'order_id' => $this->refundOrderId,
            'user_id' => auth()->id(),
            'reason' => $this->refundReason,
            'status' => 'pending',
        ]);

        // Send email to admin
        try {
            \Illuminate\Support\Facades\Mail::to(config('mail.admin_email', 'admin@nexacode.com'))
                ->queue(new \App\Mail\NewRefundRequest($refund));
        } catch (\Exception $e) {
            \Log::error('Failed to send refund request email: ' . $e->getMessage());
        }

        $this->closeRefundModal();

        Flux::toast(
            variant: 'success',
            heading: 'Refund Diajukan',
            text: 'Permintaan refund kamu telah dikirim dan akan segera ditinjau oleh admin.'
        );
    }

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.customer.orders-manager', [
                'orders' => Order::whereRaw('1 = 0')->paginate(10),
                'stats' => [
                    'total' => 0,
                    'completed' => 0,
                    'pending' => 0,
                ]
            ]);
        }

        $query = Order::where('buyer_id', auth()->id())
            ->with(['items.product']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->latest()->paginate(10);

        $stats = [
            'total' => Order::where('buyer_id', auth()->id())->count(),
            'completed' => Order::where('buyer_id', auth()->id())->where('status', \App\Enums\OrderStatus::COMPLETED)->count(),
            'pending' => Order::where('buyer_id', auth()->id())->whereIn('status', [\App\Enums\OrderStatus::PENDING, \App\Enums\OrderStatus::PROCESSING])->count(),
        ];

        return view('livewire.customer.orders-manager', [
            'orders' => $orders,
            'stats' => $stats
        ]);
    }
}
