<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        if (empty($order->transaction_id)) {
            $order->transaction_id = Order::generateTransactionId();
        }
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        try {
            $order->histories()->create([
                'status' => $order->status,
                'note' => 'Order created',
                'user_id' => Auth::id() ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create order history: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            try {
                $order->histories()->create([
                    'status' => $order->status,
                    'note' => 'Status changed from ' . $order->getOriginal('status')->value . ' to ' . $order->status->value,
                    'user_id' => Auth::id() ?? 0,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create status change history: ' . $e->getMessage());
            }
        }
    }
}
