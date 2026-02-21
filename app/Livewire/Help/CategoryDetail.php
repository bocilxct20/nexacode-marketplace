<?php

namespace App\Livewire\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Livewire\Component;

class CategoryDetail extends Component
{
    public HelpCategory $category;

    public function contactSupport()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Find or create a conversation with Nexa Support (author_id = null)
        $conversation = \App\Models\Conversation::firstOrCreate(
            [
                'user_id' => $user->id,
                'author_id' => null,
            ],
            [
                'status' => \App\Enums\SupportStatus::OPEN,
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('inbox', ['id' => $conversation->id]);
    }

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
