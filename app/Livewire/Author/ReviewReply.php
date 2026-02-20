<?php

namespace App\Livewire\Author;

use App\Models\Review;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ReviewReply extends Component
{
    public $reviewId;
    public $reply;
    public $isEditing = false;

    public function mount($reviewId)
    {
        $this->reviewId = $reviewId;
        $review = Review::findOrFail($this->reviewId);
        $this->reply = $review->author_reply;
    }

    public function saveReply()
    {
        $this->validate([
            'reply' => 'required|string|min:5|max:1000',
        ]);

        $review = Review::findOrFail($this->reviewId);

        // Security check
        if ($review->product->author_id !== Auth::id()) {
            abort(403);
        }

        $review->update([
            'author_reply' => $this->reply,
            'author_replied_at' => now(),
        ]);

        // Notify Buyer
        \Illuminate\Support\Facades\Mail::to($review->buyer->email)->queue(new \App\Mail\ReviewReplyNotification($review));

        $this->isEditing = false;
        
        $this->dispatch('toast', variant: 'success', heading: 'Response Saved', text: 'Your reply has been sent to the reviewer.');
    }

    public function render()
    {
        $review = Review::with(['buyer', 'product'])->find($this->reviewId);
        return view('livewire.author.review-reply', [
            'review' => $review
        ]);
    }
}
