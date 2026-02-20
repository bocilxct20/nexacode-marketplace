<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchTerm extends Model
{
    protected $fillable = ['query', 'user_id', 'results_count', 'hits'];
}
