<?php

namespace App\Livewire\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Livewire\Component;

class ArticleDetail extends Component
{
    public HelpCategory $category;
    public HelpArticle $article;
    public bool $hasVoted = false;
    public ?string $voteType = null;
    public string $feedbackComment = '';

    public function mount($categorySlug, $articleSlug)
    {
        $this->category = HelpCategory::where('slug', $categorySlug)->firstOrFail();
        $this->article = HelpArticle::where('slug', $articleSlug)
            ->where('help_category_id', $this->category->id)
            ->where('is_published', true)
            ->firstOrFail();
        
        $this->article->increment('views_count');

        // Check for existing vote in session
        $votes = session()->get('article_votes', []);
        if (isset($votes[$this->article->id])) {
            $this->hasVoted = true;
            $this->voteType = $votes[$this->article->id];
        }
    }

    public function voteAction($type)
    {
        if ($this->hasVoted) return;

        if ($type === 'helpful') {
            $this->article->increment('helpful_count');
            
            // Store vote in session
            $votes = session()->get('article_votes', []);
            $votes[$this->article->id] = 'helpful';
            session()->put('article_votes', $votes);

            $this->hasVoted = true;
            $this->voteType = 'helpful';
            $this->dispatch('toast', variant: 'success', heading: 'Terima Kasih!', text: 'Feedback kamu sangat membantu kami.');
        } else {
            // "No" usually proceeds to detailed feedback
            // We'll let submitDetailedFeedback handle the increment and session storage
        }
    }

    public function submitDetailedFeedback()
    {
        if ($this->hasVoted) return;

        $this->validate([
            'feedbackComment' => 'required|min:5|max:1000',
        ]);

        \App\Models\HelpArticleFeedback::create([
            'help_article_id' => $this->article->id,
            'is_helpful' => false,
            'comment' => $this->feedbackComment,
            'user_id' => auth()->id(),
        ]);

        $this->article->increment('unhelpful_count');
        
        // Store vote in session
        $votes = session()->get('article_votes', []);
        $votes[$this->article->id] = 'unhelpful';
        session()->put('article_votes', $votes);

        $this->hasVoted = true;
        $this->voteType = 'unhelpful';
        $this->feedbackComment = '';

        $this->dispatch('toast', variant: 'success', heading: 'Terkirim!', text: 'Terima kasih atas masukan detail kamu.');
    }

    public function getSeoSchemaProperty()
    {
        if (!$this->article->schema_type || !$this->article->schema_data) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $this->article->schema_type === 'faq' ? 'FAQPage' : ($this->article->schema_type === 'howto' ? 'HowTo' : 'Article'),
        ];

        if ($this->article->schema_type === 'faq') {
            $schema['mainEntity'] = array_map(fn($item) => [
                '@type' => 'Question',
                'name' => $item['question'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'] ?? '',
                ],
            ], $this->article->schema_data);
        } elseif ($this->article->schema_type === 'howto') {
            $schema['name'] = $this->article->title;
            $schema['step'] = array_map(fn($item) => [
                '@type' => 'HowToStep',
                'name' => $item['title'] ?? '',
                'text' => $item['step'] ?? '',
            ], $this->article->schema_data);
        }

        return $schema;
    }

    public function render()
    {
        $relatedArticles = HelpArticle::where('help_category_id', $this->category->id)
            ->where('id', '!=', $this->article->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $allCategories = HelpCategory::orderBy('sort_order')->get();

        return view('livewire.help.article-detail', [
            'relatedArticles' => $relatedArticles,
            'allCategories' => $allCategories,
        ])->layout('layouts.app');
    }
}
