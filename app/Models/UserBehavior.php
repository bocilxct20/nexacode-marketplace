<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBehavior extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'views_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Record a view for a category by a user.
     */
    public static function trackView($userId, $categoryId)
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'category_id' => $categoryId],
            [
                'views_count' => \Illuminate\Support\Facades\DB::raw('views_count + 1'),
                'last_viewed_at' => now(),
            ]
        );
    }
}
