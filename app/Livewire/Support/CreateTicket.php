<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use App\Models\SupportReply;
use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateTicket extends Component
{
    public $product_id;
    public $subject;
    public $message;
    public $priority = 'medium';

    public function mount()
    {
        // Initial mount logic
    }

    #[On('open-ticket-modal')]
    public function openModal($productId = null, $subject = null)
    {
        $this->reset(['subject', 'message', 'priority']);
        $this->product_id = $productId;
        $this->subject = $subject;
        $this->dispatch('modal-show', name: 'create-ticket-modal');
    }

    public function saveTicket()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:20',
            'priority' => 'required|in:low,medium,high',
        ]);

        try {
            DB::beginTransaction();

            $ticket = SupportTicket::create([
                'user_id' => Auth::id(),
                'product_id' => $this->product_id,
                'subject' => $this->subject,
                'status' => 'open',
                'priority' => Auth::user()->isElite() ? 'high' : $this->priority,
            ]);

            SupportReply::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $this->message,
            ]);

            DB::commit();

            // Notify Author
            $author = $ticket->product->author;
            \Illuminate\Support\Facades\Mail::to($author->email)->queue(new \App\Mail\NewSupportTicket($ticket));

            $this->dispatch('modal-close', name: 'create-ticket-modal');
            
            // Show success toast
            $this->dispatch('toast', 
                variant: 'success',
                heading: 'Tiket Berhasil Dibuat',
                text: 'Tiket bantuan kamu telah berhasil dibuka. Author akan segera membalas.'
            );

            $this->dispatch('ticket-created');
            
            $this->reset(['product_id', 'subject', 'message', 'priority']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Gagal membuat tiket: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $orders = Order::where('buyer_id', Auth::id())
            ->where('status', 'completed')
            ->with('items.product')
            ->get();

        $purchasedProducts = $orders->flatMap->items->pluck('product')->filter();
        
        // If user is an author, also include their own products
        $authorProducts = Auth::user()->isAuthor() 
            ? \App\Models\Product::where('author_id', Auth::id())->get()
            : collect();

        $products = $purchasedProducts->concat($authorProducts)->unique('id');

        return view('livewire.support.create-ticket', [
            'products' => $products
        ]);
    }
}
