<?php

namespace App\Livewire\Home;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Livewire\Component;
use Illuminate\Support\Collection;

class TrustWall extends Component
{
    public Collection $activities;

    public function mount()
    {
        $this->fetchActivities();
    }

    public function fetchActivities()
    {
        $activities = collect();

        // 1. Recent Sales (Last 5)
        $sales = OrderItem::whereNotNull('product_id')
            ->with(['product', 'order.buyer'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'sale',
                    'user' => $item->order->buyer,
                    'product_name' => $item->product->name,
                    'time' => $item->created_at,
                    'icon' => 'shopping-cart',
                    'color' => 'emerald'
                ];
            });
        $activities = $activities->concat($sales);

        // 2. High Rated Reviews (Last 5)
        $reviews = Review::where('rating', '>=', 4)
            ->with(['buyer', 'product'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($review) {
                return [
                    'type' => 'review',
                    'user' => $review->buyer,
                    'product_name' => $review->product->name,
                    'rating' => $review->rating,
                    'time' => $review->created_at,
                    'icon' => 'star',
                    'color' => 'amber'
                ];
            });
        $activities = $activities->concat($reviews);

        // 3. New Elite Authors (Last 3)
        $authors = User::whereHas('products')
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'author',
                    'user' => $user,
                    'time' => $user->created_at,
                    'icon' => 'bolt',
                    'color' => 'indigo'
                ];
            });
        $activities = $activities->concat($authors);

        $this->activities = $activities->sortByDesc('time')->values();
    }

    public function render()
    {
        return view('livewire.home.trust-wall');
    }
}
