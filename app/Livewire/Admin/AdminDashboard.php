<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Earning;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $data = [
            'stats' => [
                'total_sales' => 0,
                'total_users' => 0,
                'total_products' => 0,
                'pending_products' => 0,
                'total_revenue' => 0,
                'community_active_threads' => 0,
                'help_helpful_rate' => 0,
            ],
            'recentOrders' => collect(),
            'top_authors' => collect(),
            'revenueData' => [],
            'userData' => [],
        ];

        if ($this->readyToLoad) {
            $data = [
                'stats' => [
                    'total_sales' => Order::where('status', 'completed')->sum('total_amount'),
                    'total_users' => User::count(),
                    'total_products' => Product::count(),
                    'pending_products' => Product::where('status', 'pending')->count(),
                    'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
                    'net_platform_profit' => \App\Models\PlatformEarning::sum('net_profit'),

                    'help_articles' => \App\Models\HelpArticle::count(),
                    'help_feedback_count' => \App\Models\HelpArticleFeedback::count(),
                ],
                'recentOrders' => Order::with('buyer')->latest()->take(5)->get(),
                'top_authors' => User::whereHas('roles', function($q) {
                        $q->where('slug', 'author');
                    })
                    ->withCount('products')
                    ->orderBy('products_count', 'desc')
                    ->take(5)
                    ->get(),
                'revenueData' => (new \App\Services\AnalyticsService())->getAdminRevenueAnalytics('30days')['revenue_by_date'],
                'userData' => (new \App\Services\AnalyticsService())->getAdminUserAnalytics('30days')['users_by_date'],
            ];
        }

        return view('livewire.admin.admin-dashboard', $data);
    }
}
