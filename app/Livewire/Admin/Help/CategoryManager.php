<?php

namespace App\Livewire\Admin\Help;

use App\Models\HelpCategory;
use Livewire\Component;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    public $categories;
    public $editingCategory = null;
    public $showModal = false;

    // Form fields
    public $name = '';
    public $slug = '';
    public $icon = '';
    public $description = '';
    public $sort_order = 0;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = HelpCategory::orderBy('sort_order')->get();
    }

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

    public function edit(HelpCategory $category)
    {
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->icon = $category->icon;
        $this->description = $category->description;
        $this->sort_order = $category->sort_order;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:help_categories,slug,' . ($this->editingCategory->id ?? 'NULL'),
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        if ($this->editingCategory) {
            $this->editingCategory->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'icon' => $this->icon,
                'description' => $this->description,
                'sort_order' => $this->sort_order,
            ]);
        } else {
            HelpCategory::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'icon' => $this->icon,
                'description' => $this->description,
                'sort_order' => $this->sort_order,
            ]);
        }

        $this->showModal = false;
        $this->loadCategories();
        $this->dispatch('toast', variant: 'success', heading: 'Berhasil', text: 'Kategori bantuan berhasil disimpan.');
    }

    public function delete(HelpCategory $category)
    {
        $category->delete();
        $this->loadCategories();
        $this->dispatch('toast', variant: 'success', heading: 'Dihapus', text: 'Kategori bantuan telah dihapus.');
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
        $this->icon = 'book-open';
        $this->description = '';
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.help.category-manager');
    }
}
