<?php

namespace App\Livewire\Author;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentNotification extends Component
{
    public function getUnpaidOrdersProperty()
    {
        return Order::where('buyer_id', Auth::id())
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PENDING_PAYMENT])
            ->where('expires_at', '>', now())
            ->latest()
            ->get();
    }

    public function render()
    {
        $unpaidOrders = $this->unpaidOrders;
        $count = $unpaidOrders->count();

        return view('livewire.author.payment-notification', [
            'unpaidOrders' => $unpaidOrders,
            'count' => $count,
        ]);
    }
}
