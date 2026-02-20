<?php

namespace App\Livewire\Admin\Help;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ArticleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';

    protected $queryString = ['search', 'categoryFilter'];

    public function updatedTitle($value)
    {
        if (empty($this->slug) || $this->editingArticle === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        return $this->redirect(route('admin.help.articles.create'), navigate: true);
    }

    public function edit(HelpArticle $article)
    {
        return $this->redirect(route('admin.help.articles.edit', $article), navigate: true);
    }

    public function save()
    {
        // Removed as logic moved to ArticleEditor
    }

    public function delete(HelpArticle $article)
    {
        $article->delete();
        $this->dispatch('toast', variant: 'success', heading: 'Dihapus', text: 'Artikel bantuan telah dihapus.');
    }

    public function render()
    {
        $categories = HelpCategory::orderBy('sort_order')->get();
        
        $articles = HelpArticle::with('category')
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn($q) => $q->where('help_category_id', $this->categoryFilter))
            ->orderBy('sort_order')
            ->paginate(10);

        return view('livewire.admin.help.article-manager', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }
}
