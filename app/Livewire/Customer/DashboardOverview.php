<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\Product;

use App\Models\HelpArticle;
use Illuminate\Support\Facades\Auth;
use Flux;

class DashboardOverview extends Component
{
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $user = Auth::user();
        
        $stats = [
            'total_items' => 0,
            'total_spent' => 0,
            'active_tickets' => 0,
        ];
        $recentPurchases = collect();
        $recentSupportTickets = collect();
        $recommendations = collect();
        $suggestedDocs = collect();

        if ($this->readyToLoad) {
            $stats = [
                'total_items' => Order::where('buyer_id', $user->id)
                    ->where('status', 'completed')
                    ->withCount('items')
                    ->get()
                    ->sum('items_count'),
                'total_spent' => Order::where('buyer_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('total_amount'),
                'active_tickets' => SupportTicket::where('user_id', $user->id)
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count(),
            ];

            $recentPurchases = Order::where('buyer_id', $user->id)
                ->with(['items.product', 'items.subscriptionPlan'])
                ->latest()
                ->take(5)
                ->get();

            $recentSupportTickets = SupportTicket::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $recommendations = Product::approved()
                ->inRandomOrder()
                ->take(4)
                ->get();



            // Aggregation: Help Center (Interconnected Docs)
            $purchasedCategoryIds = Order::where('buyer_id', $user->id)
                ->where('status', 'completed')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->pluck('products.category_id')
                ->unique();

            $suggestedDocs = HelpArticle::with('category')->where('is_published', true)
                ->whereIn('help_category_id', $purchasedCategoryIds)
                ->latest()
                ->take(4)
                ->get();
            
            if ($suggestedDocs->isEmpty()) {
                $suggestedDocs = HelpArticle::with('category')->where('is_published', true)->latest()->take(4)->get();
            }
        }

        return view('livewire.customer.dashboard-overview', [
            'user' => $user,
            'stats' => $stats,
            'recentPurchases' => $recentPurchases,
            'recentSupportTickets' => $recentSupportTickets,
            'recommendations' => $recommendations,
            'suggestedDocs' => $suggestedDocs,
        ]);
    }

    public function newTicket()
    {
        $this->dispatch('open-ticket-modal');
        
        Flux::toast(
            heading: 'Support Portal',
            text: 'Opening support request form...',
        );
    }
}
