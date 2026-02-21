<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $sort = 'newest';
    public $selectedCategory = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'newest'],
        'selectedCategory' => ['except' => null, 'as' => 'category'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'sort', 'selectedCategory']);
    }

    public function render()
    {
        $query = Product::query()
            ->approved()
            ->with(['author', 'category'])
            ->orderBy('is_elite_marketed', 'desc')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($q) {
                $q->where('category_id', $this->selectedCategory);
            });

        switch ($this->sort) {
            case 'popular':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('avg_rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        if ($this->search) {
            $searchTerm = \App\Models\SearchTerm::where('query', strtolower($this->search))->first();
            
            if ($searchTerm) {
                $searchTerm->increment('hits');
                $searchTerm->update(['results_count' => $products->total()]);
            } else {
                \App\Models\SearchTerm::create([
                    'query' => strtolower($this->search),
                    'user_id' => auth()->id(),
                    'results_count' => $products->total(),
                    'hits' => 1
                ]);
            }
        }

        return view('livewire.product-catalog', [
            'products' => $products,
            'categories' => Category::orderBy('sort_order')->get(),
            'currentCategory' => $this->selectedCategory ? Category::find($this->selectedCategory) : null,
        ]);
    }
}
