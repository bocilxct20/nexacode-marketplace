<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use App\Models\SupportReply;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TicketChat extends Component
{
    public $ticketId;
    public $message = '';

    protected $rules = [
        'message' => 'required|string|min:2',
    ];

    public function mount($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function postReply()
    {
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('support-reply:'.auth()->id(), 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('support-reply:'.auth()->id());
            $this->dispatch('toast', variant: 'error', heading: 'Slow Down', text: "Please wait {$seconds} seconds.");
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit('support-reply:'.auth()->id());

        $this->validate();

        $user = Auth::user();
        $ticket = SupportTicket::findOrFail($this->ticketId);

        // Security check
        $isAuthor = $ticket->product->author_id === $user->id;
        $isAdmin = $user->isAdmin();

        if ($ticket->user_id !== $user->id && !$isAuthor && !$isAdmin) {
            abort(403);
        }

        $reply = SupportReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $this->message,
        ]);

        // Smart status update
        if ($user->id !== $ticket->user_id) {
            $ticket->update(['status' => 'answered']);
        } else {
            if ($ticket->status === 'closed') {
                $ticket->update(['status' => 'open']);
            }
        }

        // Notify other party
        $recipient = ($user->id === $ticket->user_id) 
            ? $ticket->product->author 
            : $ticket->user;

        // Use standard Mail facade here for direct communication
        \Illuminate\Support\Facades\Mail::to($recipient->email)->queue(new \App\Mail\NewSupportReply($reply, $recipient->name));

        $this->message = '';
        $this->dispatch('reply-posted');
    }

    public function closeTicket()
    {
        $ticket = SupportTicket::findOrFail($this->ticketId);
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $ticket->update(['status' => 'closed']);
        $this->dispatch('ticket-closed');
    }

    public function render()
    {
        $ticket = SupportTicket::with(['replies.user', 'product.author'])->findOrFail($this->ticketId);

        return view('livewire.support.ticket-chat', [
            'ticket' => $ticket,
        ]);
    }
}
