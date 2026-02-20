<?php

namespace App\Livewire\Help;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Livewire\Component;

class HelpIndex extends Component
{
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function render()
    {
        $categories = HelpCategory::orderBy('sort_order')->get();
        
        $featuredArticles = HelpArticle::where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $searchResults = HelpArticle::query()
            ->where('is_published', true)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->with('category')
            ->take(12)
            ->get();

        // Insight & Optimization: Log search queries
        if ($this->search && strlen($this->search) >= 3) {
            $lastSearch = session()->get('last_help_search');
            if ($lastSearch !== $this->search) {
                \App\Models\HelpSearchLog::create([
                    'query' => $this->search,
                    'results_count' => $searchResults->count(),
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
                session()->put('last_help_search', $this->search);
            }
        }

        return view('livewire.help.help-index', [
            'categories' => $categories,
            'featuredArticles' => $featuredArticles,
            'searchResults' => $searchResults,
        ])->layout('layouts.app');
    }
}
