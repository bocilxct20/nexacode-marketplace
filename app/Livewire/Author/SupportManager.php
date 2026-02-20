<?php

namespace App\Livewire\Author;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Message;
use App\Models\SupportTicket;
use Flux;

class SupportManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
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
        $query = SupportTicket::with(['user'])
            ->whereHas('product', function($q) {
                $q->where('author_id', auth()->id());
            });

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->readyToLoad) {
            $tickets = $query->orderBy(
                in_array($this->sortBy, ['status', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )->paginate(15);

            // Calculate stats
            $stats = [
                'total' => SupportTicket::whereHas('product', function($q) {
                    $q->where('author_id', auth()->id());
                })->count(),
                'open' => SupportTicket::whereHas('product', function($q) {
                    $q->where('author_id', auth()->id());
                })->where('status', 'open')->count(),
                'resolved' => SupportTicket::whereHas('product', function($q) {
                    $q->where('author_id', auth()->id());
                })->where('status', 'resolved')->count(),
            ];
        } else {
            $tickets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = ['total' => 0, 'open' => 0, 'resolved' => 0];
        }

        return view('livewire.author.support-manager', [
            'tickets' => $tickets,
            'stats' => $stats
        ]);
    }
}
