<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'author_id',
        'product_id',
        'session_id',
        'status',
        'last_message_at',
        'tags',
        'archived_at',
        'archived_by',
        'private_notes',
        'notifications_enabled',
        'notification_priority',
        'current_context',
        'last_buyer_message_at',
    ];

    protected $casts = [
        'status' => \App\Enums\SupportStatus::class,
        'last_message_at' => 'datetime',
        'tags' => 'array',
        'archived_at' => 'datetime',
        'notifications_enabled' => 'boolean',
        'current_context' => 'json',
        'last_buyer_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function unreadMessages()
    {
        return $this->hasMany(ChatMessage::class)->where('is_read', false);
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_values(array_diff($tags, [$tag]));
        $this->update(['tags' => $tags]);
    }
}
