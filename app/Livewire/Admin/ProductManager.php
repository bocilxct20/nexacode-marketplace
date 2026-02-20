<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Flux;

class ProductManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $selectedProduct = null;
    public $showModal = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function products()
    {
        return Product::with('author')
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%')
                      ->orWhereHas('author', function ($aq) {
                          $aq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy(
                in_array($this->sortBy, ['name', 'price', 'status', 'sales_count', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )
            ->paginate(10);
    }

    public function viewProduct($productId)
    {
        $this->selectedProduct = Product::with(['author', 'tags', 'versions'])->findOrFail($productId);
        $this->showModal = true;
    }

    public function updateStatus($productId, $status)
    {
        $product = Product::findOrFail($productId);
        $product->update(['status' => $status]);

        if ($this->selectedProduct && $this->selectedProduct->id == $productId) {
            $this->selectedProduct->refresh();
        }
 
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Product status updated to ' . $status . '.');
        $this->dispatch('product-status-updated');
    }

    public function toggleFeatured($productId)
    {
        $product = Product::with('author')->findOrFail($productId);
        $author = $product->author;
        
        // If we are trying to feature it
        if (!$product->is_featured) {
            $plan = $author->currentPlan();
            
            if ($plan->slug === 'pro') {
                $featuredCount = Product::where('author_id', $author->id)
                    ->where('is_featured', true)
                    ->count();
                
                if ($featuredCount >= 3) {
                    Flux::toast(
                        variant: 'danger', 
                        heading: 'Limit Reached', 
                        text: "Pro authors are limited to 3 featured products. {$author->name} already has {$featuredCount}."
                    );
                    return;
                }
            } elseif ($plan->slug === 'basic') {
                 Flux::toast(
                    variant: 'danger', 
                    heading: 'Not Allowed', 
                    text: "Basic authors cannot have featured products. Upgrade them to Pro or Elite first."
                );
                return;
            }
        }

        $product->update(['is_featured' => !$product->is_featured]);

        if ($this->selectedProduct && $this->selectedProduct->id == $productId) {
            $this->selectedProduct->refresh();
        }

        $status = $product->is_featured ? 'Featured' : 'Regular';
        Flux::toast(variant: 'success', heading: 'Updated', text: "Product is now {$status}");
    }

    public function render()
    {
        return view('livewire.admin.product-manager');
    }
}
