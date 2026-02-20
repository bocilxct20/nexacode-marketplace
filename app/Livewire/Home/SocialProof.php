<?php

namespace App\Livewire\Home;

use Livewire\Component;
use App\Models\OrderItem;

class SocialProof extends Component
{
    public $recentSale;
    protected $listeners = ['triggerSocialProof' => 'fetchRecentSale'];

    public function mount()
    {
        $this->fetchRecentSale();
    }

    public function fetchRecentSale()
    {
        // Get a random sale from the last 24 hours to make it feel fresh
        $this->recentSale = OrderItem::whereNotNull('product_id')
            ->where('created_at', '>=', now()->subHours(24))
            ->with('product')
            ->inRandomOrder()
            ->first();
            
        // If no recent sales, just pick any random sale
        if (!$this->recentSale) {
            $this->recentSale = OrderItem::whereNotNull('product_id')
                ->with('product')
                ->inRandomOrder()
                ->first();
        }
    }

    public function render()
    {
        return view('livewire.home.social-proof');
    }
}
