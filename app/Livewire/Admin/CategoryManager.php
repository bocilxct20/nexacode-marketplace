<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Flux;

class CategoryManager extends Component
{
    use WithFileUploads;

    public $categories;
    public $editingCategory = null;
    public $showModal = false;

    // Form fields
    public $name = '';
    public $slug = '';
    public $icon = ''; // Stores the actual DB value (path or name)
    public $icon_name = 'folder'; // Stores the Lucide fallback name for the UI
    public $iconFile;
    public $description = '';
    public $sort_order = 0;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::orderBy('sort_order')->get();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'iconFile') {
            if (is_array($this->iconFile)) {
                $this->iconFile = $this->iconFile[0];
            }
        }
    }

    public function updatedIconFile() { /* Handled in updated() */ }

    public function updatedName($value)
    {
        if (empty($this->slug) || $this->editingCategory === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(Category $category)
    {
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->icon = $category->icon;
        
        // If the icon is not a path, it's a Lucide icon, so set our UI fallback field
        if ($category->icon && !str_starts_with($category->icon, 'storage/') && !str_starts_with($category->icon, 'http')) {
            $this->icon_name = $category->icon;
        } else {
            $this->icon_name = 'folder'; // Default fallback
        }

        $this->description = $category->description;
        $this->sort_order = $category->sort_order;
        $this->showModal = true;
    }

    public function save()
    {
        \Illuminate\Support\Facades\Log::info('Save method started');
        
        try {
            $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . ($this->editingCategory->id ?? 'NULL'),
            'icon' => 'nullable|string|max:255',
            'iconFile' => 'nullable',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        $finalIcon = $this->icon_name ?: 'folder';

        // Check if there's an existing SVG icon we should keep
        if ($this->icon && (str_starts_with($this->icon, 'storage/') || str_starts_with($this->icon, 'http'))) {
            $finalIcon = $this->icon;
        }

        // Check if a new file was uploaded
        if ($this->iconFile) {
            $file = is_array($this->iconFile) ? $this->iconFile[0] : $this->iconFile;
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $finalIcon = 'storage/' . $file->store('category-icons', 'public');
            }
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $finalIcon,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingCategory) {
            $this->editingCategory->update($data);
            $message = 'Kategori produk berhasil diperbarui.';
        } else {
            Category::create($data);
            $message = 'Kategori produk baru berhasil dibuat.';
        }

        $this->showModal = false;
        $this->loadCategories();
        $this->iconFile = null;
        
        Flux::toast(
            variant: 'success',
            heading: 'Berhasil',
            text: $message,
        );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Save failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    public function delete(Category $category)
    {
        if ($category->products()->count() > 0) {
            Flux::toast(
                variant: 'danger',
                heading: 'Gagal',
                text: 'Kategori ini tidak bisa dihapus karena masih memiliki produk.',
            );
            return;
        }

        $category->delete();
        $this->loadCategories();
        
        Flux::toast(
            variant: 'success',
            heading: 'Dihapus',
            text: 'Kategori produk telah dihapus.',
        );
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->showModal = false;
    }

    private function resetFields()
    {
        $this->editingCategory = null;
        $this->name = '';
        $this->slug = '';
        $this->icon = '';
        $this->icon_name = 'folder';
        $this->iconFile = null;
        $this->description = '';
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.category-manager')
            ->layout('layouts.admin');
    }
}
