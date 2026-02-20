<?php

namespace App\Livewire\Admin\Help;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

class ArticleEditor extends Component
{
    public ?HelpArticle $article = null;
    public bool $isEdit = false;
    public string $tab = 'content';

    // Form fields
    public $title = '';
    public $slug = '';
    public $help_category_id = '';
    public $content = '';
    public $excerpt = '';
    public $is_published = true;
    public $is_featured = false;
    public $sort_order = 0;

    // Pro fields
    public $schema_type = 'none';
    public $schema_data = []; // Will store items for FAQ or steps for How-To
    public $internal_notes = '';
    public $read_time_minutes = null;

    public function mount(?HelpArticle $article = null)
    {
        if ($article && $article->exists) {
            $this->article = $article;
            $this->isEdit = true;
            $this->title = $article->title;
            $this->slug = $article->slug;
            $this->help_category_id = $article->help_category_id;
            $this->content = $article->content;
            $this->excerpt = $article->excerpt;
            $this->is_published = $article->is_published;
            $this->is_featured = $article->is_featured;
            $this->sort_order = $article->sort_order;
            
            // Pro fields initialization
            $this->schema_type = $article->schema_type ?? 'none';
            $this->schema_data = $article->schema_data ?? [];
            $this->internal_notes = $article->internal_notes ?? '';
            $this->read_time_minutes = $article->read_time_minutes;
        } else {
            $this->help_category_id = HelpCategory::orderBy('sort_order')->first()?->id ?? '';
        }
    }

    public function updatedTitle($value)
    {
        if (empty($this->slug) || !$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSchemaType($value)
    {
        $this->schema_data = [];
    }

    public function addSchemaItem()
    {
        if ($this->schema_type === 'faq') {
            $this->schema_data[] = ['question' => '', 'answer' => ''];
        } elseif ($this->schema_type === 'howto') {
            $this->schema_data[] = ['title' => '', 'step' => ''];
        }
    }

    public function removeSchemaItem($index)
    {
        unset($this->schema_data[$index]);
        $this->schema_data = array_values($this->schema_data);
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:help_articles,slug,' . ($this->article->id ?? 'NULL'),
            'help_category_id' => 'required|exists:help_categories,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $allowedTags = '<p><br><strong><em><u><s><ol><ul><li><blockquote><pre><h1><h2><h3><a><img>';
        
        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'help_category_id' => $this->help_category_id,
            'content' => strip_tags($this->content, $allowedTags),
            'excerpt' => strip_tags($this->excerpt),
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'schema_type' => $this->schema_type !== 'none' ? $this->schema_type : null,
            'schema_data' => $this->schema_data,
            'internal_notes' => strip_tags($this->internal_notes),
            'read_time_minutes' => $this->read_time_minutes,
        ];

        if ($this->isEdit) {
            $this->article->update($data);
            $this->article->createVersion(); // Advanced CMS: Create snapshot
            $message = 'Artikel bantuan berhasil diperbarui.';
        } else {
            $article = HelpArticle::create($data);
            $article->createVersion(); // Advanced CMS: Create initial snapshot
            $message = 'Artikel bantuan berhasil dibuat.';
        }

        session()->flash('toast', [
            'variant' => 'success',
            'heading' => 'Berhasil',
            'text' => $message
        ]);

        return $this->redirect(route('admin.help.articles'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = HelpCategory::orderBy('sort_order')->get();

        return view('livewire.admin.help.article-editor', [
            'categories' => $categories,
        ]);
    }
}
