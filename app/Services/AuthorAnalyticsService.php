<?php

namespace App\Services;

use App\Models\ProductAnalytics;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthorAnalyticsService
{
    /**
     * Get aggregated analytics for an author within a date range.
     */
    public function getAuthorStats($authorId, $days = 30)
    {
        $startDate = now()->subDays($days)->toDateString();
        $endDate = now()->toDateString();

        $productIds = Product::where('author_id', $authorId)->pluck('id');

        $stats = ProductAnalytics::whereIn('product_id', $productIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(date) as date'),
                DB::raw('SUM(views_count) as views'),
                DB::raw('SUM(sales_count) as sales'),
                DB::raw('SUM(revenue) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->formatForCharts($stats, $days);
    }

    /**
     * Format data for Chart.js.
     */
    protected function formatForCharts($stats, $days)
    {
        $labels = [];
        $views = [];
        $sales = [];
        $revenue = [];

        $statsMap = $stats->keyBy('date');

        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d M');
            
            $dayData = $statsMap->get($date);
            $views[] = $dayData ? (int) $dayData->views : 0;
            $sales[] = $dayData ? (int) $dayData->sales : 0;
            $revenue[] = $dayData ? (float) $dayData->revenue : 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Estimasi Pendapatan (Rp)',
                    'data' => $revenue,
                    'borderColor' => '#10b981', // emerald-500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Total Kunjungan',
                    'data' => $views,
                    'borderColor' => '#6366f1', // indigo-500
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Item Terjual',
                    'data' => $sales,
                    'borderColor' => '#f59e0b', // amber-500
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ]
            ]
        ];
    }

    /**
     * Get top performing products for an author.
     */
    public function getTopProducts($authorId, $limit = 5)
    {
        return Product::where('author_id', $authorId)
            ->orderBy('sales_count', 'desc')
            ->take($limit)
            ->get(['id', 'name', 'sales_count', 'views_count', 'price', 'thumbnail']);
    }

    /**
     * Get summary stats for an author.
     */
    public function getDashboardStats($user)
    {
        $authorId = is_object($user) ? $user->id : $user;
        
        $products = Product::where('author_id', $authorId)->get();
        $totalEarnings = \App\Models\Earning::where('author_id', $authorId)->sum('amount');
        
        return [
            'total_sales' => $products->sum('sales_count'),
            'total_views' => $products->sum('views_count'),
            'total_earnings' => $totalEarnings,
            'conversion_rate' => $products->sum('views_count') > 0 
                ? ($products->sum('sales_count') / $products->sum('views_count')) * 100 
                : 0,
        ];
    }
}
