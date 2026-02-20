<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledMessage;
use App\Models\ChatMessage;
use App\Models\Conversation;

class SendScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled chat messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $messages = ScheduledMessage::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($messages as $scheduled) {
            ChatMessage::create([
                'conversation_id' => $scheduled->conversation_id,
                'sender_id' => $scheduled->sender_id,
                'message' => $scheduled->message,
                'image_path' => $scheduled->image_path,
                'is_admin' => true,
            ]);

            $scheduled->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Conversation::find($scheduled->conversation_id)->update([
                'last_message_at' => now(),
            ]);
        }

        $this->info("Sent {$messages->count()} scheduled messages.");
        
        return Command::SUCCESS;
    }
}
