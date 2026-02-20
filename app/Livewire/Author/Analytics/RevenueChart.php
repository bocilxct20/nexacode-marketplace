<?php

namespace App\Livewire\Author\Analytics;

use Livewire\Component;
use App\Services\AnalyticsService;

class RevenueChart extends Component
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
        $revenueAnalytics = $analyticsService->getAuthorRevenueAnalytics(auth()->user(), $this->period);
        
        // Format data for Flux Chart
        $this->data = collect($revenueAnalytics['revenue_by_date'])->map(function ($item) {
            return [
                'date' => $item->date,
                'revenue' => (float) $item->revenue,
                'earnings' => (float) $item->earnings,
            ];
        })->values()->toArray();
    }

    public function render()
    {
        return view('livewire.author.analytics.revenue-chart');
    }
}
