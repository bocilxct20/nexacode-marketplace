<?php

namespace App\Livewire\Admin\Analytics;

use Livewire\Component;
use App\Services\AnalyticsService;

class CategoryChart extends Component
{
    public $data = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $analyticsService = new AnalyticsService();
        $productAnalytics = $analyticsService->getAdminProductAnalytics();
        
        $this->data = collect($productAnalytics['products_by_category'])->map(function ($item) {
            return [
                'category' => $item['category'],
                'count' => $item['count'],
            ];
        })->values()->toArray();
    }

    public function render()
    {
        return view('livewire.admin.analytics.category-chart');
    }
}
