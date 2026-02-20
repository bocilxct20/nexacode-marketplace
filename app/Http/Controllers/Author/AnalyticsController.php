<?php

namespace App\Http\Controllers\Author;

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
     * Show analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30days');
        $author = auth()->user();

        $salesAnalytics = $this->analyticsService->getAuthorSalesAnalytics($author, $period);
        $revenueAnalytics = $this->analyticsService->getAuthorRevenueAnalytics($author, $period);
        $topProducts = $this->analyticsService->getAuthorProductPerformance($author);
        $customerInsights = $this->analyticsService->getAuthorCustomerInsights($author);

        return view('author.analytics.index', compact(
            'salesAnalytics',
            'revenueAnalytics',
            'topProducts',
            'customerInsights',
            'period'
        ));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $period = $request->get('period', '30days');
        $type = $request->get('type', 'sales');
        $author = auth()->user();

        $data = match ($type) {
            'sales' => $this->analyticsService->getAuthorSalesAnalytics($author, $period),
            'revenue' => $this->analyticsService->getAuthorRevenueAnalytics($author, $period),
            'products' => $this->analyticsService->getAuthorProductPerformance($author),
            default => [],
        };

        $headers = $this->getExportHeaders($type);
        $rows = $this->formatDataForExport($data, $type);

        $csv = $this->analyticsService->exportToCsv($rows, $headers);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="analytics_' . $type . '_' . date('Y-m-d') . '.csv"');
    }

    private function getExportHeaders(string $type): array
    {
        return match ($type) {
            'sales' => ['Date', 'Sales Count', 'Revenue'],
            'revenue' => ['Date', 'Revenue', 'Earnings'],
            'products' => ['Product', 'Sales', 'Revenue', 'Earnings', 'Views', 'Conversion Rate'],
            default => [],
        };
    }

    private function formatDataForExport(array $data, string $type): array
    {
        if ($type === 'products') {
            return array_map(function ($product) {
                return [
                    $product['name'],
                    $product['sales'],
                    $product['revenue'],
                    $product['earnings'],
                    $product['views'],
                    number_format($product['conversion_rate'], 2) . '%',
                ];
            }, $data);
        }

        // For sales and revenue by date
        $dateKey = $type === 'sales' ? 'sales_by_date' : 'revenue_by_date';
        return collect($data[$dateKey] ?? [])->map(function ($item, $date) use ($type) {
            if ($type === 'sales') {
                return [$date, $item['count'], $item['revenue']];
            }
            return [$date, $item->revenue, $item->earnings];
        })->toArray();
    }
}
