<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Earning;
use App\Models\AffiliateEarning;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Enums\OrderStatus;

class Order extends Model
{
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'transaction_id';
    }

    protected $fillable = [
        'buyer_id',
        'type',
        'status',
        'payment_method',
        'payment_method_id',
        'transaction_id',
        'qris_dynamic',
        'payment_proof',
        'payment_proof_uploaded_at',
        'expires_at',
        'paid_at',
        'coupon_id',
        'discount_amount',
        'affiliate_id',
        'last_reminded_at',
        'reminder_count',
        'abandoned_reminded_at',
        'review_reminded_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_proof_uploaded_at' => 'datetime',
        'discount_amount' => 'float',
        'last_reminded_at' => 'datetime',
        'abandoned_reminded_at' => 'datetime',
        'review_reminded_at' => 'datetime',
    ];

    public static function generateTransactionId(): string
    {
        do {
            $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 16));
            $transactionId = 'NEX' . $random;
        } while (self::where('transaction_id', $transactionId)->exists());
        
        return $transactionId;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    /**
     * Resolve affiliate ID from cookie
     */
    public static function resolveAffiliateId(): ?int
    {
        // 1. Check for Coupon Attribution (Direct & Explicit)
        $couponCode = request()->input('coupon_code') ?: session('coupon_code');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->whereNotNull('affiliate_id')->first();
            if ($coupon && $coupon->affiliate_id !== auth()->id()) {
                return $coupon->affiliate_id;
            }
        }

        // 2. Check for Cookie Attribution (30-day window)
        $refCode = request()->cookie('nexacode_affiliate');
        if ($refCode) {
            $affiliate = User::where('affiliate_code', $refCode)->first();
            if ($affiliate && $affiliate->id !== auth()->id()) {
                return $affiliate->id;
            }
        }

        return null;
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at', 'desc');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Check if order payment has expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return now()->isAfter($this->expires_at);
    }

    /**
     * Check if order is pending payment
     */
    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    public function isPendingPayment(): bool
    {
        return $this->status === OrderStatus::PENDING_PAYMENT;
    }

    public function isPendingVerification(): bool
    {
        return $this->status === OrderStatus::PENDING_VERIFICATION;
    }

    public function isProcessing(): bool
    {
        return $this->status === OrderStatus::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    public function isFailed(): bool
    {
        return $this->status === OrderStatus::FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === OrderStatus::REFUNDED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            OrderStatus::PENDING,
            OrderStatus::PENDING_PAYMENT,
            OrderStatus::PENDING_VERIFICATION,
        ]);
    }

    /**
     * Finalize the order completion (Shared logic via Service)
     */
    public function finalizeCompletion(): void
    {
        app(\App\Services\OrderFulfillmentService::class)->complete($this);
    }

    /**
     * Mark order as paid (Admin override)
     */
    public function markAsPaid()
    {
        $this->finalizeCompletion();
    }

    // Payment Proof Methods
    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (!$this->payment_proof) {
            return null;
        }
        
        return \Illuminate\Support\Facades\Storage::url($this->payment_proof);
    }

    public function uploadPaymentProof($file): void
    {
        // Delete old proof if exists
        if ($this->payment_proof) {
            \Illuminate\Support\Facades\Storage::delete($this->payment_proof);
        }
        
        // Store new proof
        $path = $file->store('payment-proofs', 'public');
        
        $this->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
            'status' => OrderStatus::PENDING_VERIFICATION,
        ]);
    }

    public function approvePaymentProof(): void
    {
        $this->finalizeCompletion();
    }

    public function rejectPaymentProof(string $reason = null): void
    {
        // Keep the proof but change status back
        $this->update([
            'status' => OrderStatus::PENDING_PAYMENT,
        ]);
        
        // Add note to history
        try {
            $this->histories()->create([
                'status' => OrderStatus::PENDING_PAYMENT->value,
                'note' => 'Payment proof rejected' . ($reason ? ": {$reason}" : ''),
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to create rejection history: ' . $e->getMessage());
        }
    }

    public function getEscrowEndsAtAttribute(): ?\Illuminate\Support\Carbon
    {
        if ($this->status !== OrderStatus::COMPLETED || !$this->paid_at) {
            return null;
        }

        return $this->paid_at->addDays(14);
    }

    public function getIsInEscrowAttribute(): bool
    {
        $endsAt = $this->escrow_ends_at;
        
        return $endsAt && now()->isBefore($endsAt);
    }
}
