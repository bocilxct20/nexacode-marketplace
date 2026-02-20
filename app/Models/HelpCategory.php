<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
    ];

    public function articles()
    {
        return $this->hasMany(HelpArticle::class);
    }
}
