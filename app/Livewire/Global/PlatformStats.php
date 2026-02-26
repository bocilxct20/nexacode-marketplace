<?php

namespace App\Livewire\Global;

use App\Models\Order;
use App\Models\User;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

#[Lazy]
class PlatformStats extends Component
{
    public function placeholder()
    {
        return view('livewire.global.platform-stats-placeholder');
    }
    public $stats = [];

    public function mount()
    {
        $this->stats = Cache::remember('platform_global_stats', 3600, function () {
            return [
                'total_sales' => Order::where('status', 'completed')->count(),
                'total_members' => User::count(),
                'active_authors' => User::whereHas('products', function($q) {
                    $q->where('status', \App\Enums\ProductStatus::APPROVED);
                })->count(),
                'total_products' => \App\Models\Product::approved()->count(),
            ];
        });
    }

    public function render()
    {
        return view('livewire.global.platform-stats');
    }
}
