<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponService
{
    /**
     * Create a new coupon
     */
    public function createCoupon(array $data, User $author)
    {
        $coupon = Coupon::create([
            'author_id' => $author->id,
            'code' => $data['code'] ?? $this->generateCode(),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'value' => $data['value'],
            'min_purchase' => $data['min_purchase'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'usage_per_user' => $data['usage_per_user'] ?? 1,
            'starts_at' => $data['starts_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        // Attach products if specified
        if (isset($data['product_ids'])) {
            $coupon->products()->sync($data['product_ids']);
        }

        return $coupon;
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Coupon $coupon, array $data)
    {
        $coupon->update([
            'code' => $data['code'] ?? $coupon->code,
            'description' => $data['description'] ?? $coupon->description,
            'type' => $data['type'] ?? $coupon->type,
            'value' => $data['value'] ?? $coupon->value,
            'min_purchase' => $data['min_purchase'] ?? $coupon->min_purchase,
            'usage_limit' => $data['usage_limit'] ?? $coupon->usage_limit,
            'usage_per_user' => $data['usage_per_user'] ?? $coupon->usage_per_user,
            'starts_at' => $data['starts_at'] ?? $coupon->starts_at,
            'expires_at' => $data['expires_at'] ?? $coupon->expires_at,
            'status' => $data['status'] ?? $coupon->status,
        ]);

        // Update products if provided
        if (isset($data['product_ids'])) {
            $coupon->products()->sync($data['product_ids']);
        }

        return $coupon->fresh();
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->products()->detach();
        $coupon->delete();

        return true;
    }

    /**
     * Validate coupon
     */
    public function validateCoupon(string $code, User $user, array $productIds = [])
    {
        $coupon = Coupon::byCode($code)->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon not found'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'Coupon is not valid or has expired'];
        }

        if (!$coupon->canBeUsedBy($user)) {
            return ['valid' => false, 'message' => 'You have reached the usage limit for this coupon'];
        }

        // Check if coupon applies to specific products
        if ($coupon->products()->count() > 0 && !empty($productIds)) {
            $couponProductIds = $coupon->products()->pluck('id')->toArray();
            $hasMatch = !empty(array_intersect($couponProductIds, $productIds));

            if (!$hasMatch) {
                return ['valid' => false, 'message' => 'Coupon does not apply to selected products'];
            }
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'message' => 'Coupon is valid'
        ];
    }

    /**
     * Apply coupon to amount
     */
    public function applyCoupon(Coupon $coupon, float $amount)
    {
        return $coupon->apply($amount);
    }

    /**
     * Record coupon usage
     */
    public function recordUsage(Coupon $coupon, Order $order, User $user, float $discountAmount)
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);

        $coupon->incrementUsage();

        // Check if coupon has reached usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            $coupon->update(['status' => 'inactive']);
        }

        return true;
    }

    /**
     * Generate unique coupon code
     */
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check and expire coupons
     */
    public function checkExpiry()
    {
        Coupon::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'expired']);

        return true;
    }
}
