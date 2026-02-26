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
    
    // Advanced Filters
    public $min_price = null;
    public $max_price = null;
    public $min_rating = null;
    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'newest'],
        'selectedCategory' => ['except' => null, 'as' => 'category'],
        'min_price' => ['except' => null],
        'max_price' => ['except' => null],
        'min_rating' => ['except' => null],
    ];

    public function updated($property)
    {
        if (in_array($property, ['search', 'selectedCategory', 'sort', 'min_price', 'max_price', 'min_rating'])) {
            $this->resetPage();
            if ($property !== 'perPage') {
                $this->perPage = 12;
            }
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
        $this->perPage = 12;
    }

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'sort', 'selectedCategory', 'min_price', 'max_price', 'min_rating', 'perPage']);
    }

    public function render()
    {
        $query = Product::query()
            ->select('products.*')
            ->join('users', 'products.author_id', '=', 'users.id')
            ->leftJoin('subscription_plans', 'users.subscription_plan_id', '=', 'subscription_plans.id')
            ->approved()
            ->with(['author', 'category'])
            // Prioritize Elite/Pro tiers first
            ->orderByRaw('CASE WHEN subscription_plans.is_elite = 1 THEN 2 WHEN subscription_plans.slug = \'pro\' OR subscription_plans.allow_trial = 1 THEN 1 ELSE 0 END DESC')
            ->orderBy('products.is_elite_marketed', 'desc')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('products.name', 'like', '%' . $this->search . '%')
                          ->orWhere('products.description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($q) {
                $q->where('products.category_id', $this->selectedCategory);
            })
            ->when($this->min_price, function ($q) {
                $q->where('products.price', '>=', $this->min_price);
            })
            ->when($this->max_price, function ($q) {
                $q->where('products.price', '<=', $this->max_price);
            })
            ->when($this->min_rating, function ($q) {
                $q->where('products.avg_rating', '>=', $this->min_rating);
            });

        switch ($this->sort) {
            case 'popular':
                $query->orderBy('products.sales_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('products.avg_rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('products.price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('products.price', 'desc');
                break;
            default:
                $query->orderBy('products.created_at', 'desc');
                break;
        }

        $products = $query->paginate($this->perPage);

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

        // For Counter: Get all categories with their product counts
        $categoriesWithCounts = Category::orderBy('sort_order')
            ->withCount(['products' => function($q) {
                $q->approved();
            }])
            ->get();

        // Get Trending Tags (Popular search terms)
        $trendingTags = \App\Models\SearchTerm::orderBy('hits', 'desc')
            ->limit(5)
            ->pluck('query');

        return view('livewire.product-catalog', [
            'products' => $products,
            'categories' => $categoriesWithCounts,
            'trendingTags' => $trendingTags,
            'allApprovedCount' => Product::approved()->count(),
            'currentCategory' => $this->selectedCategory ? Category::withCount(['products' => fn($q) => $q->approved()])->find($this->selectedCategory) : null,
            'hasMore' => $products->hasMorePages(),
        ]);
    }
}
