<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;

class LogSentMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $to = collect($message->getTo())->map(fn($recipient) => $recipient->getAddress())->implode(', ');
            
            // Safely extract subject - ensure it's always a string
            $subject = $message->getSubject();
            if (is_object($subject)) {
                $subject = method_exists($subject, '__toString') ? (string)$subject : get_class($subject);
            }
            $subject = substr((string)$subject, 0, 255); // Truncate to fit database column
            
            \App\Models\EmailLog::create([
                'to' => $to,
                'subject' => $subject,
                'message_id' => $message->getHeaders()->get('Message-ID')?->getBodyAsString(),
                'mailable_class' => data_get($event->data, '__laravel_notification') ?? get_class($event->data['mailable'] ?? $this),
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't break email sending
            \Illuminate\Support\Facades\Log::error('Failed to log sent email: ' . $e->getMessage());
        }
    }
}
