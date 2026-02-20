<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorReport extends Model
{
    protected $fillable = [
        'buyer_id',
        'author_id',
        'conversation_id',
        'category',
        'reason',
        'status',
        'admin_notes',
        'admin_id',
        'resolved_at',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
