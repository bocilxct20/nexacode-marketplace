<?php

namespace App\Livewire\Help;

use App\Models\HelpArticle;
use App\Models\HelpSearchLog;
use Livewire\Component;

class HelpWidget extends Component
{
    public $search = '';
    public $isOpen = false;
    public $proactiveArticle = null;

    protected $listeners = ['triggerProactiveHelp' => 'showProactive'];

    public function updatedIsOpen($value)
    {
        if (!$value) {
            $this->search = '';
            $this->proactiveArticle = null;
        }
    }

    public function showProactive($articleId)
    {
        if ($this->isOpen) return;

        $this->proactiveArticle = HelpArticle::find($articleId);
        
        // Auto-close after 15 seconds if ignored
        $this->dispatch('proactive-trigger-received');
    }

    public function render()
    {
        $results = collect();
        if (strlen($this->search) >= 2) {
            $results = HelpArticle::where('is_published', true)
                ->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->search . '%');
                })
                ->with('category')
                ->take(5)
                ->get();

            // Instant Search Analytics (The Mind Reader)
            if (strlen($this->search) >= 3) {
                $lastSearch = session()->get('last_widget_search');
                if ($lastSearch !== $this->search) {
                    HelpSearchLog::create([
                        'query' => $this->search,
                        'results_count' => $results->count(),
                        'user_id' => auth()->id(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                    session()->put('last_widget_search', $this->search);
                }
            }
        }

        $suggestedArticles = HelpArticle::where('is_published', true)
            ->where('is_featured', true)
            ->with('category')
            ->take(3)
            ->get();

        return view('livewire.help.help-widget', [
            'results' => $results,
            'suggestedArticles' => $suggestedArticles,
        ]);
    }
}
