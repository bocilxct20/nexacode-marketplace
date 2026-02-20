<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    protected static function booted()
    {
        static::saved(fn ($setting) => \Illuminate\Support\Facades\Cache::forget("setting.{$setting->key}"));
        static::deleted(fn ($setting) => \Illuminate\Support\Facades\Cache::forget("setting.{$setting->key}"));
    }
}
