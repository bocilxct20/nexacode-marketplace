<?php

namespace App\Livewire\Admin\Analytics;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\AnalyticsService;

class Dashboard extends Component
{
    public $period = '30days';
    public $platformMetrics = [];
    public $productAnalytics = [];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedPeriod()
    {
        $this->loadData();
        $this->dispatch('period-updated', period: $this->period);
    }

    public function loadData()
    {
        $analyticsService = new AnalyticsService();
        $this->platformMetrics = $analyticsService->getAdminPlatformMetrics();
        $this->productAnalytics = $analyticsService->getAdminProductAnalytics();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.analytics.dashboard');
    }
}
