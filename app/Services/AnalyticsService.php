<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductView;
use App\Models\Earning;
use App\Models\ProductTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get author sales analytics
     */
    public function getAuthorSalesAnalytics(User $author, string $period = '30days'): array
    {
        $days = $this->parsePeriodToDays($period);
        $cacheKey = "author_sales_{$author->id}_{$period}";

        return Cache::remember($cacheKey, 3600, function () use ($author, $days) {
            $orders = Order::whereHas('items.product', function ($q) use ($author) {
                $q->where('author_id', $author->id);
            })
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

            $salesByDate = $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($dayOrders) {
                return [
                    'count' => $dayOrders->count(),
                    'revenue' => $dayOrders->sum('total_amount'),
                ];
            });

            return [
                'total_sales' => $orders->count(),
                'total_revenue' => $orders->sum('total_amount'),
                'average_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
                'sales_by_date' => $salesByDate,
            ];
        });
    }

    /**
     * Get author revenue analytics
     */
    public function getAuthorRevenueAnalytics(User $author, string $period = '30days'): array
    {
        $days = $this->parsePeriodToDays($period);

        $earningsQuery = Earning::where('author_id', $author->id)
            ->whereHas('order', function ($q) use ($days) {
                $q->where('status', 'completed')
                  ->where('created_at', '>=', now()->subDays($days));
            });

        $totalRevenue = $earningsQuery->sum('amount') + $earningsQuery->sum('commission_amount');
        $totalEarnings = $earningsQuery->sum('amount');
        $platformFee = $earningsQuery->sum('commission_amount');

        $revenueByDate = $earningsQuery->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount + commission_amount) as revenue'),
            DB::raw('SUM(amount) as earnings')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_earnings' => $totalEarnings,
            'platform_fee' => $platformFee,
            'revenue_by_date' => $revenueByDate,
        ];
    }

    /**
     * Get author top products
     */
    public function getAuthorProductPerformance(User $author, int $limit = 10): array
    {
        $products = Product::where('author_id', $author->id)
            ->withCount(['orderItems as sales_count' => function ($q) {
                $q->whereHas('order', function ($query) {
                    $query->where('status', 'completed');
                });
            }])
            ->with(['orderItems' => function ($q) {
                $q->whereHas('order', function ($query) {
                    $query->where('status', 'completed');
                });
            }])
            ->get()
            ->map(function ($product) {
                $earningsData = Earning::where('product_id', $product->id)
                    ->where('status', 'completed') // Assuming earnings have status
                    ->selectRaw('SUM(amount + commission_amount) as total_revenue, SUM(amount) as total_earnings')
                    ->first();

                $revenue = $earningsData->total_revenue ?? 0;
                $earnings = $earningsData->total_earnings ?? 0;
                $views = ProductView::totalViewsCount($product->id);
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sales' => $product->sales_count,
                    'revenue' => $revenue,
                    'earnings' => $earnings,
                    'views' => $views,
                    'conversion_rate' => $views > 0 ? ($product->sales_count / $views) * 100 : 0,
                ];
            })
            ->sortByDesc('sales')
            ->take($limit)
            ->values();

        return $products->toArray();
    }

    /**
     * Get author customer insights
     */
    public function getAuthorCustomerInsights(User $author): array
    {
        $orders = Order::whereHas('items.product', function ($q) use ($author) {
            $q->where('author_id', $author->id);
        })
        ->where('status', 'completed')
        ->get();

        $totalCustomers = $orders->pluck('buyer_id')->unique()->count();
        $newCustomersThisMonth = $orders->where('created_at', '>=', now()->startOfMonth())
            ->pluck('buyer_id')
            ->unique()
            ->count();

        $repeatCustomers = $orders->groupBy('buyer_id')
            ->filter(function ($customerOrders) {
                return $customerOrders->count() > 1;
            })
            ->count();

        return [
            'total_customers' => $totalCustomers,
            'new_customers_this_month' => $newCustomersThisMonth,
            'repeat_customers' => $repeatCustomers,
            'repeat_rate' => $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0,
        ];
    }

    /**
     * Get admin platform metrics
     */
    public function getAdminPlatformMetrics(): array
    {
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $platformCommission = Earning::sum('commission_amount');
        $effectiveCommissionRate = $totalRevenue > 0 ? ($platformCommission / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'platform_commission' => $platformCommission,
            'effective_commission_rate' => $effectiveCommissionRate,
            'total_users' => User::count(),
            'total_authors' => User::whereHas('roles', function($q) {
                $q->where('slug', 'author');
            })->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending_review')->count(),
            'total_orders' => Order::where('status', 'completed')->count(),
        ];
    }

    /**
     * Get admin revenue analytics
     */
    public function getAdminRevenueAnalytics(string $period = '30days'): array
    {
        $days = $this->parsePeriodToDays($period);

        $revenue = Order::where('orders.status', 'completed')
            ->where('orders.created_at', '>=', now()->subDays($days))
            ->leftJoin('earnings', 'orders.id', '=', 'earnings.order_id')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(orders.total_amount) as total_revenue'),
                DB::raw('SUM(earnings.commission_amount) as platform_commission'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_revenue' => $revenue->sum('total_revenue'),
            'platform_commission' => $revenue->sum('platform_commission'),
            'effective_commission_rate' => $revenue->sum('total_revenue') > 0 ? ($revenue->sum('platform_commission') / $revenue->sum('total_revenue')) * 100 : 0,
            'total_orders' => $revenue->sum('order_count'),
            'revenue_by_date' => $revenue,
        ];
    }

    /**
     * Get admin user analytics
     */
    public function getAdminUserAnalytics(string $period = '30days'): array
    {
        $days = $this->parsePeriodToDays($period);

        $users = User::where('users.created_at', '>=', now()->subDays($days))
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->select(
                DB::raw('DATE(users.created_at) as date'),
                DB::raw('COUNT(DISTINCT users.id) as new_users'),
                DB::raw('COUNT(DISTINCT CASE WHEN roles.slug = "author" THEN users.id END) as new_authors'),
                DB::raw('COUNT(DISTINCT CASE WHEN roles.slug = "buyer" THEN users.id END) as new_buyers')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_new_users' => $users->sum('new_users'),
            'total_new_authors' => $users->sum('new_authors'),
            'total_new_buyers' => $users->sum('new_buyers'),
            'users_by_date' => $users,
        ];
    }

    /**
     * Get admin product analytics
     */
    public function getAdminProductAnalytics(): array
    {
        $products = ProductTag::withCount('products')
            ->get()
            ->map(function ($tag) {
                return [
                    'category' => $tag->name,
                    'count' => $tag->products_count,
                ];
            });

        // Add uncategorized products count if any
        $uncategorizedCount = Product::doesntHave('tags')->count();
        if ($uncategorizedCount > 0) {
            $products->push([
                'category' => 'Uncategorized',
                'count' => $uncategorizedCount,
            ]);
        }

        $approvalRate = Product::count() > 0 
            ? (Product::where('status', 'approved')->count() / Product::count()) * 100 
            : 0;

        return [
            'products_by_category' => $products,
            'total_products' => Product::count(),
            'approved_products' => Product::where('status', 'approved')->count(),
            'pending_products' => Product::where('status', 'pending_review')->count(),
            'rejected_products' => Product::where('status', 'rejected')->count(),
            'approval_rate' => $approvalRate,
            'average_price' => Product::avg('price') ?? 0,
        ];
    }

    /**
     * Parse period string to days
     */
    private function parsePeriodToDays(string $period): int
    {
        return match ($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            '1year' => 365,
            default => 30,
        };
    }

    /**
     * Export data to CSV
     */
    public function exportToCsv(array $data, array $headers): string
    {
        $csv = fopen('php://temp', 'r+');
        
        fputcsv($csv, $headers);
        
        foreach ($data as $row) {
            fputcsv($csv, $row);
        }
        
        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);
        
        return $output;
    }
}
