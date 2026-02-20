<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'data',
        'response_status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a security event
     */
    public static function log(string $action, ?int $userId = null, array $data = [], ?int $status = null): void
    {
        static::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => $data,
            'response_status' => $status,
        ]);
    }

    /**
     * Get failed login attempts for IP
     */
    public static function getFailedLoginAttempts(string $ip, int $minutes = 15): int
    {
        return static::where('action', 'login_failed')
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Get recent activity for user
     */
    public static function getRecentActivity(int $userId, int $limit = 10)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}
