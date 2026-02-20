<?php

namespace App\Livewire\Account;

use App\Models\Order;
use App\Models\RefundRequest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RefundRequestForm extends Component
{
    public $orderId;
    public $reason;

    public function mount($orderId)
    {
        $this->orderId = $orderId;

        $order = Order::findOrFail($this->orderId);
        
        // Security check
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        // Check if already requested
        $existing = \App\Models\RefundRequest::where('order_id', $order->id)->first();
        if ($existing) {
            \Flux::toast(
                variant: 'info',
                heading: 'Refund Pending',
                text: 'A refund has already been requested for this order.',
            );
            return redirect()->route('dashboard.orders');
        }
    }

    public function submitRefund()
    {
        $this->validate([
            'reason' => 'required|string|min:20|max:2000',
        ]);

        $order = Order::findOrFail($this->orderId);

        \App\Models\RefundRequest::create([
            'order_id' => $order->id,
            'buyer_id' => Auth::id(),
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        \Flux::toast(
            variant: 'success',
            heading: 'Refund Requested',
            text: 'Your request has been submitted for review.',
        );

        return redirect()->route('purchases.index');
    }

    public function render()
    {
        $order = Order::with('items.product')->find($this->orderId);

        return view('livewire.account.refund-request-form', [
            'order' => $order
        ]);
    }
}
