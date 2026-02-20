<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show admin analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30days');

        $platformMetrics = $this->analyticsService->getAdminPlatformMetrics();
        $revenueAnalytics = $this->analyticsService->getAdminRevenueAnalytics($period);
        $userAnalytics = $this->analyticsService->getAdminUserAnalytics($period);
        $productAnalytics = $this->analyticsService->getAdminProductAnalytics();

        return view('admin.analytics.index', compact(
            'platformMetrics',
            'revenueAnalytics',
            'userAnalytics',
            'productAnalytics',
            'period'
        ));
    }

    /**
     * Export admin analytics data
     */
    public function export(Request $request)
    {
        $period = $request->get('period', '30days');
        $type = $request->get('type', 'revenue');

        $data = match ($type) {
            'revenue' => $this->analyticsService->getAdminRevenueAnalytics($period),
            'users' => $this->analyticsService->getAdminUserAnalytics($period),
            'products' => $this->analyticsService->getAdminProductAnalytics(),
            default => [],
        };

        $headers = $this->getExportHeaders($type);
        $rows = $this->formatDataForExport($data, $type);

        $csv = $this->analyticsService->exportToCsv($rows, $headers);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="admin_analytics_' . $type . '_' . date('Y-m-d') . '.csv"');
    }

    private function getExportHeaders(string $type): array
    {
        return match ($type) {
            'revenue' => ['Date', 'Total Revenue', 'Platform Commission', 'Orders'],
            'users' => ['Date', 'New Users', 'New Authors', 'New Buyers'],
            'products' => ['Category', 'Product Count'],
            default => [],
        };
    }

    private function formatDataForExport(array $data, string $type): array
    {
        if ($type === 'products') {
            return array_map(function ($item) {
                return [$item['category'], $item['count']];
            }, $data['products_by_category']->toArray());
        }

        $dateKey = $type === 'revenue' ? 'revenue_by_date' : 'users_by_date';
        return collect($data[$dateKey] ?? [])->map(function ($item) use ($type) {
            if ($type === 'revenue') {
                return [$item->date, $item->total_revenue, $item->platform_commission, $item->order_count];
            }
            return [$item->date, $item->new_users, $item->new_authors, $item->new_buyers];
        })->toArray();
    }
}
