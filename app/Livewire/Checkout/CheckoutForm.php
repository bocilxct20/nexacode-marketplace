<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\QrisService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Flux;

class CheckoutForm extends Component
{
    public Product $product;
    public $paymentMethods;
    public $selectedPaymentMethodId;
    public $couponCode;
    public $appliedCoupon = null;
    public $discountAmount = 0;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->paymentMethods = PaymentMethod::active()->get();
        
        // Auto-select first available payment method
        if ($this->paymentMethods->isNotEmpty()) {
            $this->selectedPaymentMethodId = $this->paymentMethods->first()->id;
        }
    }

    public function applyCoupon()
    {
        if (empty(trim($this->couponCode))) {
            return;
        }

        $this->validate(['couponCode' => 'required|string']);

        $coupon = \App\Models\Coupon::where('code', $this->couponCode)
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            $this->addError('couponCode', 'Invalid or inactive coupon code.');
            return;
        }

        // Validity Checks
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $this->addError('couponCode', 'This coupon has expired.');
            return;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            $this->addError('couponCode', 'This coupon usage limit has been reached.');
            return;
        }

        if ($coupon->author_id !== $this->product->author_id) {
            $this->addError('couponCode', 'This coupon is not valid for this author\'s products.');
            return;
        }

        if ($coupon->min_purchase && $this->product->price < $coupon->min_purchase) {
            $this->addError('couponCode', 'Minimum purchase of Rp ' . number_format($coupon->min_purchase) . ' required.');
            return;
        }

        // Check if coupon is restricted to specific products
        $restrictedProductIds = $coupon->products->pluck('id');
        if ($restrictedProductIds->isNotEmpty() && !$restrictedProductIds->contains($this->product->id)) {
            $this->addError('couponCode', 'This coupon is not valid for this specific product.');
            return;
        }

        // Calculate discount
        if ($coupon->type === 'percentage') {
            $this->discountAmount = ($this->product->price * $coupon->value) / 100;
        } else {
            $this->discountAmount = $coupon->value;
        }

        $this->discountAmount = min($this->discountAmount, $this->product->price);
        $this->appliedCoupon = $coupon;

        $this->dispatch('toast', variant: 'success', heading: 'Coupon Applied', text: 'Discount of Rp ' . number_format($this->discountAmount) . ' applied!');
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->discountAmount = 0;
        $this->couponCode = '';
    }

    public function process()
    {
        $this->validate([
            'selectedPaymentMethodId' => 'required|exists:payment_methods,id',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($this->selectedPaymentMethodId);
        $user = Auth::user();
        $originalPrice = $this->product->price;
        $totalAmount = max(0, $originalPrice - $this->discountAmount);

        $order = Order::forceCreate([
            'buyer_id' => $user->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $paymentMethod->name,
            'payment_method_id' => $paymentMethod->id,
            'expires_at' => now()->addHours(24),
            'coupon_id' => $this->appliedCoupon ? $this->appliedCoupon->id : null,
            'discount_amount' => $this->discountAmount,
            'affiliate_id' => Order::resolveAffiliateId(),
        ]);

        if ($this->appliedCoupon) {
            $this->appliedCoupon->increment('used_count');
            \App\Models\CouponUsage::create([
                'coupon_id' => $this->appliedCoupon->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'discount_amount' => $this->discountAmount,
            ]);
        }

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'price' => $originalPrice,
        ]);

        // Load relationships for email
        $order->load(['items.product', 'buyer']);

        // Send Order Confirmation Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\OrderConfirmation($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
        }

        if ($paymentMethod->isQris() && $paymentMethod->qris_static) {
            try {
                $qrisService = app(QrisService::class);
                $dynamicQris = $qrisService->generateDynamic(
                    $paymentMethod->qris_static,
                    $totalAmount
                );
                $order->update(['qris_dynamic' => $dynamicQris]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate dynamic QRIS in Livewire: ' . $e->getMessage());
            }
        }

        Flux::toast(
            variant: 'success',
            heading: 'Order Created',
            text: 'Your order has been created. Processing payment...',
        );

        return redirect()->route('checkout.payment', $order);
    }

    public function render()
    {
        return view('livewire.checkout.checkout-form');
    }
}
