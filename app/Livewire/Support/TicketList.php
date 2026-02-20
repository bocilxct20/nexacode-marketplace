<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TicketList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all';
    public $userRole = 'buyer'; // default to buyer

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function mount($role = 'buyer')
    {
        $this->userRole = $role;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $query = SupportTicket::query()
            ->with(['product', 'user'])
            ->when($this->userRole === 'author', function ($q) use ($user) {
                $q->whereHas('product', function ($productQuery) use ($user) {
                    $productQuery->where('author_id', $user->id);
                });
            })
            ->when($this->userRole === 'buyer', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->when($this->search, function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            })
            ->latest();

        return view('livewire.support.ticket-list', [
            'tickets' => $query->paginate(10),
        ]);
    }
}
