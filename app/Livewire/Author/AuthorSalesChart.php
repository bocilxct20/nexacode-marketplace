<?php

namespace App\Livewire\Author;

use Livewire\Component;
use App\Models\Earning;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthorSalesChart extends Component
{
    public $data = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $userId = Auth::id();
        
        // Get earnings for the last 30 days, grouped by date
        $earnings = Earning::where('author_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with zero revenue to ensure a smooth line
        $formattedData = [];
        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->startOfDay();

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dateString = $date->toDateString();
            $earningForDate = $earnings->firstWhere('date', $dateString);
            
            $formattedData[] = [
                'date' => $dateString,
                'revenue' => $earningForDate ? (float) $earningForDate->revenue : 0,
            ];
        }

        $this->data = $formattedData;
    }

    public function render()
    {
        return view('livewire.author.author-sales-chart');
    }
}
