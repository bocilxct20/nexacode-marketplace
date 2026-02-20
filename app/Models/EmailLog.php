<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'to',
        'subject',
        'status',
        'error_message',
        'message_id',
        'mailable_class',
        'emailable_id',
        'emailable_type',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function emailable()
    {
        return $this->morphTo();
    }
}
