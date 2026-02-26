<?php

namespace App\Livewire\Home;

use App\Models\Product;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ProductCollections extends Component
{
    public $collection = 'best_sellers';

    public function placeholder()
    {
        return view('livewire.home.product-collections-placeholder');
    }

    public function setCollection($name)
    {
        $this->collection = $name;
    }

    public function render()
    {
        $query = Product::query()
            ->approved()
            ->with(['author', 'tags'])
            ->withCount('reviews');

        if ($this->collection === 'best_sellers') {
            $query->orderBy('sales_count', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->take(6)->get();

        return view('livewire.home.product-collections', [
            'products' => $products
        ]);
    }
}
