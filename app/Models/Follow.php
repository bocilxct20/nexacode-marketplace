<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    /**
     * The user who follows.
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * The user (or Author) being followed.
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
