<?php

namespace App\Livewire\Home;

use App\Models\Product;
use App\Models\UserBehavior;
use Livewire\Component;

class PersonalizedRecommendations extends Component
{
    public $products = [];
    public $title = 'Recommended for You';

    public function mount()
    {
        $this->loadRecommendations();
    }

    public function loadRecommendations()
    {
        if (auth()->check()) {
            $topCategoryIds = UserBehavior::where('user_id', auth()->id())
                ->orderBy('views_count', 'desc')
                ->take(3)
                ->pluck('category_id');

            if ($topCategoryIds->isNotEmpty()) {
                $this->products = Product::approved()
                    ->whereIn('category_id', $topCategoryIds)
                    ->with(['author', 'category'])
                    ->latest()
                    ->take(4)
                    ->get();
                
                $this->title = 'Based on your interests';
            }
        }

        // Fallback to trending if no personalized products found
        if (empty($this->products) || count($this->products) < 4) {
            $excludeIds = collect($this->products)->pluck('id');
            
            $trending = Product::approved()
                ->whereNotIn('id', $excludeIds)
                ->with(['author', 'category'])
                ->orderBy('sales_count', 'desc')
                ->take(4 - count($this->products))
                ->get();

            $this->products = collect($this->products)->concat($trending)->all();
            
            if (empty($topCategoryIds)) {
                $this->title = 'Trending Picks';
            }
        }
    }

    public function render()
    {
        return view('livewire.home.personalized-recommendations');
    }
}
