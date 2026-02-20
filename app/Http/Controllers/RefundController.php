<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /**
     * Show the form for requesting a refund.
     */
    public function create(Order $order)
    {
        $user = Auth::user();

        // Security check
        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        // Only completed orders can be refunded
        if ($order->status !== Order::STATUS_COMPLETED) {
            \Flux::toast(
                variant: 'danger',
                heading: 'Refund Unavailable',
                text: 'Hanya pesanan yang sudah selesai (Completed) yang dapat diajukan pengembalian dana.',
            );
            return redirect()->route('dashboard.orders');
        }

        // Check if already requested
        $existing = RefundRequest::where('order_id', $order->id)->first();
        if ($existing) {
            \Flux::toast(
                variant: 'info',
                heading: 'Refund Pending',
                text: 'A refund has already been requested for this order.',
            );
            return redirect()->route('dashboard.orders');
        }

        return view('dashboard.refunds.create', compact('order'));
    }

    /**
     * Store a newly created refund request.
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|min:20',
        ]);

        $user = Auth::user();

        // V3-01: Ownership check
        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        // V3-02: Duplicate check
        $existing = RefundRequest::where('order_id', $order->id)->first();
        if ($existing) {
            \Flux::toast(variant: 'warning', heading: 'Sudah Diajukan', text: 'Permintaan refund untuk order ini sudah ada.');
            return redirect()->route('dashboard.orders');
        }

        $refund = RefundRequest::create([
            'order_id' => $order->id,
            'buyer_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Notify Author
        \Illuminate\Support\Facades\Mail::to($author->email)->queue(new \App\Mail\NewRefundRequest($refund));

        // Database Notification for Author
        $author->notify(new \App\Notifications\SystemNotification([
            'title' => 'New Refund Request ↩️',
            'message' => 'A buyer has requested a refund for Order #' . $order->id . '.',
            'type' => 'warning',
            'action_text' => 'Review Refund',
            'action_url' => route('author.refunds'),
        ]));

        \Flux::toast(
            variant: 'success',
            heading: 'Refund Requested',
            text: 'Your request has been submitted for review.',
        );

        return redirect()->route('dashboard.orders');
    }

    /**
     * Display refund requests for authors to review (simplified for now).
     */
    public function authorIndex()
    {
        $user = Auth::user();

        // Find refund requests for products belonging to this author
        $refunds = RefundRequest::whereHas('order.items.product', function($q) use ($user) {
            $q->where('author_id', $user->id);
        })->with(['order', 'buyer'])->latest()->paginate(10);

        return view('author.refunds.index', compact('refunds'));
    }
}
