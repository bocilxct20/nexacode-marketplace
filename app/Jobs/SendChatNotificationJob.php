<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\ChatMessage;
use App\Notifications\NewChatMessageNotification;

class SendChatNotificationJob implements ShouldQueue
{
    use Queueable;

    public $messageId;

    /**
     * Create a new job instance.
     */
    public function __construct($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = ChatMessage::with(['conversation', 'sender'])->find($this->messageId);

        if (!$message || $message->is_read) {
            return;
        }

        $conversation = $message->conversation;
        $recipient = null;

        if ($message->is_admin) {
            // Sender is Author/Admin, Recipient is Buyer
            $recipient = $conversation->user;
        } else {
            // Sender is Buyer, Recipient is Author (or Admin if author_id is null)
            $recipient = $conversation->author_id 
                ? $conversation->author 
                : \App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->first();
        }

        if ($recipient) {
            $recipient->notify(new NewChatMessageNotification($message));
        }
    }
}
