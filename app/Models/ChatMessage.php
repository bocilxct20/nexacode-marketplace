<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected static function booted()
    {
        static::created(function ($message) {
            // Queue the notification job with a 5 minute delay
            \App\Jobs\SendChatNotificationJob::dispatch($message->id)
                ->delay(now()->addMinutes(5));
        });
    }

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'image_path',
        'voice_path',
        'voice_duration',
        'is_admin',
        'is_read',
        'type',
        'metadata',
        'reactions',
        'is_pinned',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_read' => 'boolean',
        'is_pinned' => 'boolean',
        'metadata' => 'array',
        'reactions' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function addReaction($emoji, $userId)
    {
        $reactions = $this->reactions ?? [];
        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = [];
        }
        if (!in_array($userId, $reactions[$emoji])) {
            $reactions[$emoji][] = $userId;
        }
        $this->update(['reactions' => $reactions]);
    }

    public function removeReaction($emoji, $userId)
    {
        $reactions = $this->reactions ?? [];
        if (isset($reactions[$emoji])) {
            $reactions[$emoji] = array_values(array_diff($reactions[$emoji], [$userId]));
            if (empty($reactions[$emoji])) {
                unset($reactions[$emoji]);
            }
        }
        $this->update(['reactions' => $reactions]);
    }
}
