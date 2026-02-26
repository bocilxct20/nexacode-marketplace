<?php

namespace App\Livewire\Product;

use Livewire\Component;

class ScarcityBadge extends Component
{
    public $productId;
    public $type = 'sales'; // 'sales', 'viewers'
    public $value = 0;

    public function mount($productId, $type = 'sales')
    {
        $this->productId = $productId;
        $this->type = $type;
        $this->generateValue();
    }

    public function generateValue()
    {
        $product = \App\Models\Product::find($this->productId);
        if (!$product) return;

        if ($this->type === 'viewers') {
            // Real unique viewers in last 15 mins (at least 1 to count current user)
            $count = \App\Models\ProductView::recentViewsCount($this->productId, 15);
            $this->value = max(1, $count);
        } elseif ($this->type === 'sales') {
            // Real sales count from DB
            $this->value = $product->sales_count;
        }
    }

    public function render()
    {
        return view('livewire.product.scarcity-badge');
    }
}
