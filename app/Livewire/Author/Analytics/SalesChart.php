<?php

namespace App\Livewire\Author\Analytics;

use Livewire\Component;
use App\Services\AnalyticsService;

class SalesChart extends Component
{
    public $period = '30days';
    public $data = [];

    public function mount($period = '30days')
    {
        $this->period = $period;
        $this->loadData();
    }

    public function loadData()
    {
        $analyticsService = new AnalyticsService();
        $salesAnalytics = $analyticsService->getAuthorSalesAnalytics(auth()->user(), $this->period);
        
        // Format data for Flux Chart
        $this->data = collect($salesAnalytics['sales_by_date'])->map(function ($item, $date) {
            return [
                'date' => $date,
                'sales' => $item['count'],
            ];
        })->values()->toArray();
    }

    public function render()
    {
        return view('livewire.author.analytics.sales-chart');
    }
}
