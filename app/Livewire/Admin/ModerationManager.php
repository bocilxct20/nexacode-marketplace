<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Flux;

class ModerationManager extends Component
{
    use WithPagination;

    public $rejectionReason = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $selectedProduct = null;

    public $readyToLoad = false;

    public function load()
    {
        $this->readyToLoad = true;
    }

    protected $queryString = [
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

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
        if (!$this->readyToLoad) {
            return Product::whereRaw('1 = 0')->paginate(10);
        }

        return Product::with('author')
            ->where('status', 'pending')
            ->orderBy(
                in_array($this->sortBy, ['name', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )
            ->paginate(10);
    }

    public function viewProduct($productId)
    {
        $this->selectedProduct = Product::with(['author', 'tags', 'versions'])->findOrFail($productId);
        $this->rejectionReason = '';
        $this->dispatch('modal-opened', name: 'review-product');
    }

    public function approve($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $product = Product::findOrFail($id);
        $product->update(['status' => 'approved']);

        // Send Notification to Author
        try {
            \Illuminate\Support\Facades\Mail::to($product->author->email)->queue(new \App\Mail\ProductApproved($product->load('author')));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to queue product approval email: ' . $e->getMessage());
        }

        if ($this->selectedProduct && $this->selectedProduct->id == $id) {
            $this->selectedProduct->refresh();
        }
        
        Flux::toast(variant: 'success', heading: 'Approved', text: 'Product approved and is now live.');
        $this->dispatch('product-moderated');
    }

    public function reject($productId)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'rejectionReason' => 'required|min:10',
        ], [
            'rejectionReason.required' => 'Mohon berikan alasan penolakan agar author tahu apa yang harus diperbaiki.',
            'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $product = Product::findOrFail($productId);
        $product->update(['status' => 'rejected']);

        // Notify Author
        try {
            \Illuminate\Support\Facades\Mail::to($product->author->email)->queue(new \App\Mail\ProductRejected($product->load('author'), $this->rejectionReason));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to queue product rejection email: ' . $e->getMessage());
        }

        if ($this->selectedProduct && $this->selectedProduct->id == $productId) {
            $this->selectedProduct->refresh();
        }
 
        Flux::toast(variant: 'success', heading: 'Rejected', text: 'Product submission has been rejected.');
        $this->dispatch('product-moderated');
    }

    public function render()
    {
        return view('livewire.admin.moderation-manager');
    }
}
