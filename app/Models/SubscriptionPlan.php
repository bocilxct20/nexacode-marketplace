<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'allow_trial' => 'boolean',
        'is_elite' => 'boolean',
        'commission_rate' => 'float',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getDefaultPlan()
    {
        return self::where('is_default', true)->first() ?? self::where('slug', 'basic')->first();
    }
}
