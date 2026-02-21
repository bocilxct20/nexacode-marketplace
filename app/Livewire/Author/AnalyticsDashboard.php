<?php

namespace App\Livewire\Author;

use App\Services\AuthorAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Flux;

class AnalyticsDashboard extends Component
{
    public $days = 30;
    public $chartData;
    public function mount()
    {
        if (!Auth::user()->isPro()) {
            Flux::toast(variant: 'danger', heading: 'Fitur Terkunci', text: 'Analitik lanjutan hanya tersedia untuk tier Pro & Elite.');
            return redirect()->route('author.plans');
        }
    }

    public function loadData(AuthorAnalyticsService $service)
    {
        $this->chartData = $service->getAuthorStats(Auth::id(), $this->days);
        $this->readyToLoad = true;
    }

    public function updateRange($days, AuthorAnalyticsService $service)
    {
        $this->days = $days;
        $this->chartData = $service->getAuthorStats(Auth::id(), $this->days);
        $this->dispatch('chartDataUpdated', $this->chartData);
    }

    public function render(AuthorAnalyticsService $service, \App\Services\AuthorLevelService $levelService)
    {
        $user = Auth::user();
        $authorId = $user->id;
        
        return view('livewire.author.analytics-dashboard', [
            'topProducts' => $service->getTopProducts($authorId),
            'summaryStats' => $service->getDashboardStats($authorId),
            'levelProgress' => $levelService->getProgress($user),
            'globalRank' => $user->getGlobalRankPosition(),
        ])->layout('layouts.author');
    }
}
