<?php

namespace App\Livewire\Author;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Review;

class ReviewManager extends Component
{
    use WithPagination;

    public $search = '';
    public $ratingFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'ratingFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Review::with(['user', 'product'])
            ->whereHas('product', function($q) {
                $q->where('author_id', auth()->id());
            });

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('comment', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Rating filter
        if ($this->ratingFilter !== 'all') {
            $query->where('rating', $this->ratingFilter);
        }

        if ($this->readyToLoad) {
            $reviews = $query->orderBy(
                in_array($this->sortBy, ['rating', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )->paginate(15);

            // Calculate stats
            $stats = [
                'total' => Review::whereHas('product', function($q) {
                    $q->where('author_id', auth()->id());
                })->count(),
                'average_rating' => Review::whereHas('product', function($q) {
                    $q->where('author_id', auth()->id());
                })->avg('rating'),
                'replied' => Review::whereHas('product', function($q) {
                    $q->where('author_id', auth()->id());
                })->whereNotNull('author_reply')->count(),
            ];
        } else {
            $reviews = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = ['total' => 0, 'average_rating' => 0, 'replied' => 0];
        }

        return view('livewire.author.review-manager', [
            'reviews' => $reviews,
            'stats' => $stats
        ]);
    }
}
