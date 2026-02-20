<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpArticle extends Model
{
    protected $fillable = [
        'help_category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'is_published',
        'is_featured',
        'views_count',
        'helpful_count',
        'unhelpful_count',
        'sort_order',
        'schema_type',
        'schema_data',
        'internal_notes',
        'read_time_minutes',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'schema_data' => 'array',
    ];

    /**
     * Get the estimated read time.
     */
    public function getReadTimeAttribute()
    {
        if ($this->read_time_minutes) {
            return $this->read_time_minutes;
        }

        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    public function category()
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(HelpArticleFeedback::class);
    }

    public function versions()
    {
        return $this->hasMany(HelpArticleVersion::class)->latest();
    }

    /**
     * Create a version snapshot of the article.
     */
    public function createVersion($userId = null)
    {
        return $this->versions()->create([
            'title_snapshot' => $this->title,
            'content_snapshot' => $this->content,
            'metadata_snapshot' => [
                'excerpt' => $this->excerpt,
                'schema_type' => $this->schema_type,
                'schema_data' => $this->schema_data,
                'read_time_minutes' => $this->read_time_minutes,
                'is_featured' => $this->is_featured,
                'help_category_id' => $this->help_category_id,
            ],
            'updated_by' => $userId ?? auth()->id(),
        ]);
    }
}
