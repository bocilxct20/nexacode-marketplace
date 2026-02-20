<?php

namespace App\Livewire\Author;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Flux;

class BundleManager extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $selectedProducts = [];
    public $discount_percentage = 0;
    public $discount_amount = 0;
    public $discount_type = 'percentage'; // 'percentage' or 'fixed'
    public $status = 'draft';
    public $thumbnail;
    public $existingThumbnail = null;

    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'selectedProducts' => 'required|array|min:2',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,inactive',
            'thumbnail' => $this->editingId ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
        ];
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $bundle = ProductBundle::where('author_id', Auth::id())->with('products')->findOrFail($id);
        
        $this->editingId = $id;
        $this->name = $bundle->name;
        $this->description = $bundle->description;
        $this->selectedProducts = $bundle->products->pluck('id')->toArray();
        $this->discount_percentage = (float) $bundle->discount_percentage;
        $this->discount_amount = (float) $bundle->discount_amount;
        $this->discount_type = $bundle->discount_amount > 0 ? 'fixed' : 'percentage';
        $this->status = $bundle->status;
        $this->existingThumbnail = $bundle->thumbnail;
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'author_id' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description,
            'discount_percentage' => $this->discount_type === 'percentage' ? $this->discount_percentage : 0,
            'discount_amount' => $this->discount_type === 'fixed' ? $this->discount_amount : 0,
            'status' => $this->status,
        ];

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('bundles', 'public');
        }

        if ($this->editingId) {
            $bundle = ProductBundle::where('author_id', Auth::id())->findOrFail($this->editingId);
            $bundle->update($data);
            $bundle->products()->sync($this->selectedProducts);
            $message = 'Bundle updated successfully.';
        } else {
            $bundle = ProductBundle::create($data);
            $bundle->products()->attach($this->selectedProducts);
            $message = 'Bundle created successfully.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $bundle = ProductBundle::where('author_id', Auth::id())->findOrFail($id);
        $bundle->delete();
        Flux::toast(variant: 'success', text: 'Bundle deleted.');
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->selectedProducts = [];
        $this->discount_percentage = 0;
        $this->discount_amount = 0;
        $this->discount_type = 'percentage';
        $this->status = 'draft';
        $this->thumbnail = null;
        $this->existingThumbnail = null;
        $this->resetValidation();
    }

    public function render()
    {
        $authorProducts = Product::where('author_id', Auth::id())
            ->where('status', 'approved')
            ->get();

        $bundles = ProductBundle::where('author_id', Auth::id())
            ->withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.author.bundle-manager', [
            'authorProducts' => $authorProducts,
            'bundles' => $bundles,
        ])->layout('layouts.author');
    }
}
