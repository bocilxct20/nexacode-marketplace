<?php

namespace App\Livewire\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Livewire\Component;

class CategoryDetail extends Component
{
    public HelpCategory $category;

    public function mount($slug)
    {
        $this->category = HelpCategory::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $articles = $this->category->articles()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        $allCategories = HelpCategory::orderBy('sort_order')->get();

        return view('livewire.help.category-detail', [
            'articles' => $articles,
            'allCategories' => $allCategories,
        ])->layout('layouts.app');
    }
}
