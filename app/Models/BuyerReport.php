<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyerReport extends Model
{
    protected $fillable = [
        'author_id',
        'buyer_id',
        'conversation_id',
        'category',
        'reason',
        'status',
        'admin_notes',
        'admin_id',
        'resolved_at',
    ];

    protected $casts = [
        'status' => \App\Enums\BuyerReportStatus::class,
        'resolved_at' => 'datetime',
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'dismissed']);
    }

    // Methods
    public function markAsResolved($adminId, $notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'admin_id' => $adminId,
            'admin_notes' => $notes,
            'resolved_at' => now(),
        ]);
    }

    public function markAsDismissed($adminId, $notes = null)
    {
        $this->update([
            'status' => 'dismissed',
            'admin_id' => $adminId,
            'admin_notes' => $notes,
            'resolved_at' => now(),
        ]);
    }

    public function assignToAdmin($adminId)
    {
        $this->update([
            'status' => 'under_review',
            'admin_id' => $adminId,
        ]);
    }

    // Category labels
    public static function getCategoryLabels()
    {
        return [
            'abusive_language' => 'Bahasa Kasar / Tidak Sopan',
            'spam' => 'Spam / Pesan Berulang',
            'refund_abuse' => 'Penyalahgunaan Refund',
            'payment_issues' => 'Masalah Pembayaran',
            'other' => 'Lainnya',
        ];
    }

    public function getCategoryLabelAttribute()
    {
        return self::getCategoryLabels()[$this->category] ?? $this->category;
    }

    // Status labels
    public static function getStatusLabels()
    {
        return [
            'pending' => 'Menunggu Review',
            'under_review' => 'Sedang Ditinjau',
            'resolved' => 'Selesai',
            'dismissed' => 'Ditolak',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'amber',
            'under_review' => 'blue',
            'resolved' => 'lime',
            'dismissed' => 'zinc',
            default => 'zinc',
        };
    }
}
