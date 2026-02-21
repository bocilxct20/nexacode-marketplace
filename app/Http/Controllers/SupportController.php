<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportReply;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Display a listing of the user's tickets.
     */
    public function index()
    {
        return view('dashboard.support.index');
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $user = Auth::user();

        // Only allow tickets for purchased products
        $orders = Order::where('buyer_id', $user->id)
            ->where('status', 'completed')
            ->with('items.product')
            ->get();

        $products = $orders->flatMap->items->pluck('product')->unique('id');

        return view('dashboard.support.create', compact('products'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $user = Auth::user();

        // Security check: Must have purchased the product
        $hasPurchased = Order::where('buyer_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->exists();

        if (!$hasPurchased) {
            abort(403, 'Kamu harus membeli produk ini terlebih dahulu untuk mendapatkan bantuan teknis.');
        }

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
        ]);

        SupportReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        // Notify Author
        $author = $ticket->product->author;
        \Illuminate\Support\Facades\Mail::to($author->email)->queue(new \App\Mail\NewSupportTicket($ticket));

        // Database Notification for Author
        $author->notify(new \App\Notifications\SystemNotification([
            'title' => 'New Support Ticket 🎫',
            'message' => 'A buyer has opened a support ticket for "' . $ticket->product->name . '".',
            'type' => 'info',
            'action_text' => 'View Ticket',
            'action_url' => route('author.support'),
        ]));

        \Flux::toast(
            variant: 'success',
            heading: 'Ticket Created',
            text: 'Your support inquiry has been submitted.',
        );

        return redirect()->route('support.show', $ticket);
    }

    /**
     * Display a listing of tickets for the author's products.
     */
    public function authorIndex()
    {
        return view('author.support.index');
    }

    /**
     * Display the specified ticket.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load('product.author');
        $user = Auth::user();
        
        // Authorization check: User must be buyer, author, or admin
        $isAuthor = $ticket->product->author_id === $user->id;
        $isAdmin = $user->isAdmin();

        if ($ticket->user_id !== $user->id && !$isAuthor && !$isAdmin) {
             abort(403);
        }

        // Determine which layout to use
        $layout = $isAuthor && request()->routeIs('author.*') ? 'layouts.author' : 'layouts.app';

        return view('dashboard.support.show', compact('ticket', 'layout'));
    }

    public function chat()
    {
        $user = Auth::user();

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
}
