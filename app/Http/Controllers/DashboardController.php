<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard overview.
     */
    public function index()
    {
        $user = Auth::user();

        $recentOrders = Order::where('buyer_id', $user->id)
            ->with(['items.product', 'paymentMethod'])
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = SupportTicket::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->take(3)
            ->get();
        $purchasedItemsCount = Order::where('buyer_id', $user->id)->where('status', 'completed')->count();

        return redirect()->route('home');
    }


    /**
     * Display the user's order history.
     */
    public function orders()
    {
        $user = Auth::user();

        $orders = Order::where('buyer_id', $user->id)->with('items.product')->latest()->paginate(10);

        return view('dashboard.orders', compact('orders'));
    }

    /**
     * Display the user's wishlist.
     */
    public function wishlist()
    {
        $user = Auth::user();

        $wishlistItems = \App\Models\Wishlist::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->paginate(12);

        return view('dashboard.wishlist', compact('wishlistItems'));
    }
}
