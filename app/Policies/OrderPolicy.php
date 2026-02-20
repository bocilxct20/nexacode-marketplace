<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id || $user->isAdmin();
    }

    /**
     * Determine if the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id && $order->canBeCancelled();
    }

    /**
     * Determine if the user can confirm payment for the order.
     */
    public function confirmPayment(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id && $order->status === \App\Enums\OrderStatus::PENDING_PAYMENT;
    }
}
