<?php

namespace App\Livewire\Checkout;

use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Flow extends Component
{
    public $user;
    public $product;
    public $couponCode = '';
    public $discount = 0;
    public $appliedCoupon = null;
    public $step = 1;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function applyCoupon()
    {
        $this->validate([
            'couponCode' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $this->couponCode)->first();

        if (!$coupon || !$coupon->isValid()) {
            $this->addError('couponCode', 'Invalid or expired coupon code.');
            return;
        }

        if (!$coupon->canBeUsedBy(Auth::user())) {
            $this->addError('couponCode', 'You cannot use this coupon.');
            return;
        }

        // Apply discount logic
        $this->appliedCoupon = $coupon;
        $this->calculateDiscount();
    }

    public function calculateDiscount()
    {
        if (!$this->appliedCoupon) return;

        $basePrice = $this->product->price;
        $this->discount = $this->appliedCoupon->calculateDiscount($basePrice);
    }

    public function getFinalPriceProperty()
    {
        $basePrice = $this->product->price;
        return max(0, $basePrice - $this->discount);
    }

    public function nextStep()
    {
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function processPayment(MidtransService $midtrans)
    {
        try {
            DB::beginTransaction();

            $price = $this->finalPrice;

            // Create order
            $order = Order::forceCreate([
                'buyer_id' => Auth::id(),
                'total_amount' => $price,
                'status' => \App\Enums\OrderStatus::PENDING,
                'payment_method' => 'midtrans',
                'coupon_id' => $this->appliedCoupon?->id,
                'affiliate_id' => Order::resolveAffiliateId(),
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
                'price' => $price,
                'type' => 'product',
            ]);

            // Load relationships for email
            $order->load(['items.product', 'buyer']);

            // Send Order Confirmation Email
            try {
                \Illuminate\Support\Facades\Mail::to(Auth::user()->email)
                    ->send(new \App\Mail\OrderConfirmation($order));
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            // Track coupon usage
            if ($this->appliedCoupon) {
                $this->appliedCoupon->recordUsage(Auth::user(), $order);
            }

            // Get Midtrans Snap token
            $snapToken = $midtrans->createTransaction($order);

            DB::commit();

            $this->dispatch('payment-ready', ['snapToken' => $snapToken]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('payment', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout.flow');
    }
}
