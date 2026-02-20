<?php

namespace App\Livewire\Admin\Analytics;

use Livewire\Component;
use App\Services\AnalyticsService;

class UserGrowthChart extends Component
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
        $userAnalytics = $analyticsService->getAdminUserAnalytics($this->period);
        
        // Format data for Flux Chart
        $this->data = collect($userAnalytics['users_by_date'])->map(function ($item) {
            return [
                'date' => $item->date,
                'authors' => (int) $item->new_authors,
                'buyers' => (int) $item->new_buyers,
            ];
        })->values()->toArray();
    }

    public function render()
    {
        return view('livewire.admin.analytics.user-growth-chart');
    }
}
