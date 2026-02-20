<?php

namespace App\Livewire\Author;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductTag;
use Livewire\Component;
use Livewire\WithPagination;

class Storefront extends Component
{
    use WithPagination;

    public $authorId;
    public $search = '';
    public $selectedCategory = null;
    public $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'sort' => ['except' => 'latest'],
    ];

    public function mount($authorId)
    {
        $this->authorId = $authorId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $author = User::findOrFail($this->authorId);

        $query = Product::where('author_id', $this->authorId)
            ->approved()
            ->with(['author', 'tags'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($q) {
                $q->whereHas('tags', function ($query) {
                    $query->where('product_tags.id', $this->selectedCategory);
                });
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

        $allAuthorTags = ProductTag::whereHas('products', function($q) {
            $q->where('author_id', $this->authorId);
        })->get();

        return view('livewire.author.storefront', [
            'author' => $author,
            'products' => $query->paginate(12),
            'authorTags' => $allAuthorTags,
        ]);
    }
}
