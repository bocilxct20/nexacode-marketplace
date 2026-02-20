<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpArticleVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'help_article_id',
        'title_snapshot',
        'content_snapshot',
        'updated_by',
        'created_at',
    ];

    protected static function booted()
    {
        static::creating(function ($version) {
            $version->created_at = now();
        });
    }

    public function article()
    {
        return $this->belongsTo(HelpArticle::class, 'help_article_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
