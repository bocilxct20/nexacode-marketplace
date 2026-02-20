<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Flux;

class OrderManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $selectedOrder = null;
    public $showModal = false;

    public $readyToLoad = false;

    public function load()
    {
        $this->readyToLoad = true;
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function orders()
    {
        if (!$this->readyToLoad) {
            return Order::whereRaw('1 = 0')->paginate(10);
        }

        return Order::with(['buyer', 'paymentMethod'])
            ->when($this->search, function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('buyer', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy(
                in_array($this->sortBy, ['id', 'status', 'total_amount', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )
            ->paginate(10);
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with(['buyer', 'paymentMethod', 'items.product', 'items.subscriptionPlan'])->findOrFail($orderId);
        $this->showModal = true;
    }

    public function getConversationId($orderId)
    {
        $order = Order::with('items.product')->find($orderId);
        if (!$order) return null;

        // Try to find a conversation for one of the products in the order
        foreach ($order->items as $item) {
            $conv = \App\Models\Conversation::where('user_id', $order->buyer_id)
                ->where('product_id', $item->product_id)
                ->first();
            
            if ($conv) return $conv->id;
        }

        return null;
    }

    public function markAsPaid($orderId)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $order = Order::findOrFail($orderId);
        $order->markAsPaid();

        if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
            $this->selectedOrder->refresh();
        }

        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Order marked as paid.');
        $this->dispatch('order-status-updated');
    }

    public function cancelOrder($orderId)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'cancelled']);

        if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
            $this->selectedOrder->refresh();
        }

        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Order cancelled.');
        $this->dispatch('order-status-updated');
    }

    public function approvePaymentProof($orderId)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $order = Order::findOrFail($orderId);
        $order->approvePaymentProof();

        if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
            $this->selectedOrder->refresh();
        }

        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Payment proof approved. Order marked as completed.');
        $this->dispatch('order-status-updated');
    }

    public function rejectPaymentProof($orderId)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $order = Order::findOrFail($orderId);
        $order->rejectPaymentProof('Please upload a clearer image');

        if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
            $this->selectedOrder->refresh();
        }

        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Payment proof rejected. Customer can upload a new proof.');
        $this->dispatch('order-status-updated');
    }

    public function render()
    {
        return view('livewire.admin.order-manager');
    }
}
