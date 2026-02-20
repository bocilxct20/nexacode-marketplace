<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpArticleFeedback extends Model
{
    protected $table = 'help_article_feedbacks';

    protected $fillable = [
        'help_article_id',
        'is_helpful',
        'comment',
        'user_id',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(HelpArticle::class, 'help_article_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
