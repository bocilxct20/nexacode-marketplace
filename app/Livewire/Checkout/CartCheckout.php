<?php

namespace App\Livewire\Checkout;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\QrisService;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Flux;

class CartCheckout extends Component
{
    public $paymentMethods;
    public $selectedPaymentMethodId;
    public $cartItems;
    public $totalAmount = 0;
    public $couponCode;
    public $appliedCoupon = null;
    public $discountAmount = 0;

    public function mount()
    {
        $this->loadCart();
        $this->paymentMethods = PaymentMethod::active()->get();
    }

    public function loadCart()
    {
        $this->cartItems = CartItem::with(['product.author', 'bundle.products.author'])
            ->where('user_id', Auth::id())
            ->get();

        if ($this->cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('info', 'Keranjang kamu kosong.');
        }

        // Security Check: Author cannot purchase their own products
        $userId = Auth::id();
        foreach ($this->cartItems as $item) {
            if ($item->product && $item->product->author_id === $userId) {
                $this->dispatch('toast', variant: 'danger', heading: 'Loopholes Blocked 🛡️', text: 'Kamu tidak dapat membeli produk milik sendiri untuk menjaga integritas sistem XP.');
                $this->cartItems = collect();
                return;
            }
            if ($item->bundle) {
                foreach ($item->bundle->products as $p) {
                    if ($p->author_id === $userId) {
                        $this->dispatch('toast', variant: 'danger', heading: 'Loopholes Blocked 🛡️', text: 'Salah satu produk dalam bundle adalah milikmu sendiri. Pembelian dibatalkan.');
                        $this->cartItems = collect();
                        return;
                    }
                }
            }
        }

        $this->recomputeTotal();
    }

    protected function recomputeTotal()
    {
        $grossTotal = $this->cartItems->sum(function($item) {
            if ($item->bundle_id) {
                return $item->bundle->price;
            }
            return $item->product ? $item->product->price : 0;
        });
        $this->totalAmount = max(0, $grossTotal - $this->discountAmount);
    }

    public function applyCoupon()
    {
        $this->validate(['couponCode' => 'required|string']);

        $coupon = \App\Models\Coupon::where('code', $this->couponCode)
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            $this->addError('couponCode', 'Kupon tidak valid atau sudah tidak aktif.');
            return;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $this->addError('couponCode', 'Kupon ini sudah kedaluwarsa.');
            return;
        }

        // Calculate potential discount per applicable product in cart
        $discountValue = 0;
        $anyApplied = false;

        foreach ($this->cartItems as $item) {
            $product = $item->product;
            
            // Check author restriction
            if ($coupon->author_id && $coupon->author_id !== $product->author_id) continue;
            
            // Check product restriction
            $restrictedIds = $coupon->products->pluck('id');
            if ($restrictedIds->isNotEmpty() && !$restrictedIds->contains($product->id)) continue;

            // Apply discount logic
            if ($coupon->type === 'percentage') {
                $itemDiscount = ($product->price * $coupon->value) / 100;
            } else {
                $itemDiscount = $coupon->value;
            }

            $discountValue += min($itemDiscount, $product->price);
            $anyApplied = true;
        }

        if (!$anyApplied) {
            $this->addError('couponCode', 'Kupon ini tidak berlaku untuk produk di keranjang kamu.');
            return;
        }

        $this->discountAmount = $discountValue;
        $this->appliedCoupon = $coupon;
        $this->recomputeTotal();

        $this->dispatch('toast', variant: 'success', heading: 'Kupon Berhasil', text: 'Diskon Rp ' . number_format($this->discountAmount) . ' telah diterapkan!');
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->discountAmount = 0;
        $this->couponCode = '';
        $this->recomputeTotal();
    }

    public function process()
    {
        $this->validate([
            'selectedPaymentMethodId' => 'required|exists:payment_methods,id',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($this->selectedPaymentMethodId);
        $user = Auth::user();

        try {
            $order = DB::transaction(function () use ($user, $paymentMethod) {
                $order = Order::forceCreate([
                    'buyer_id' => $user->id,
                    'total_amount' => $this->totalAmount,
                    'status' => 'pending',
                    'payment_method' => $paymentMethod->name,
                    'payment_method_id' => $paymentMethod->id,
                    'expires_at' => now()->addHours(24),
                    'coupon_id' => $this->appliedCoupon ? $this->appliedCoupon->id : null,
                    'discount_amount' => $this->discountAmount,
                    'affiliate_id' => $this->appliedCoupon->affiliate_id ?? Order::resolveAffiliateId(),
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

                foreach ($this->cartItems as $item) {
                    if ($item->bundle_id) {
                        $bundle = $item->bundle;
                        $bundleProducts = $bundle->products;
                        $bundlePrice = $bundle->price;
                        $totalGrossOfProducts = $bundleProducts->sum('price');
                        
                        foreach ($bundleProducts as $p) {
                            $proportion = $totalGrossOfProducts > 0 ? ($p->price / $totalGrossOfProducts) : (1 / $bundleProducts->count());
                            $effectivePrice = $bundlePrice * $proportion;
                            
                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $p->id,
                                'bundle_id' => $bundle->id,
                                'price' => $effectivePrice,
                            ]);
                        }
                    } else {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'price' => $item->product->price,
                        ]);
                    }
                }

                // Clear Cart
                CartItem::where('user_id', $user->id)->delete();

                return $order;
            });

            // Post-order actions
            $this->handlePostOrderActions($order, $paymentMethod);

            Flux::toast(variant: 'success', heading: 'Order Created', text: 'Pesanan berhasil dibuat. Melanjutkan ke pembayaran...');
            
            return redirect()->route('checkout.payment', $order);

        } catch (\Exception $e) {
            Log::error('Checkout failed: ' . $e->getMessage());
            $this->dispatch('toast', variant: 'danger', text: 'Checkout gagal. Silakan coba lagi.');
        }
    }

    protected function handlePostOrderActions($order, $paymentMethod)
    {
        // Load relationships for email
        $order->load(['items.product', 'buyer']);

        // Send Email
        try {
            Mail::to($order->buyer->email)->send(new \App\Mail\OrderConfirmation($order));
        } catch (\Exception $e) {
            Log::error('Order email failed: ' . $e->getMessage());
        }

        // QRIS Logic
        if ($paymentMethod->isQris() && $paymentMethod->qris_static) {
            try {
                $qrisService = app(QrisService::class);
                $dynamicQris = $qrisService->generateDynamic($paymentMethod->qris_static, $order->total_amount);
                $order->update(['qris_dynamic' => $dynamicQris]);
            } catch (\Exception $e) {
                Log::error('QRIS gen failed: ' . $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.checkout.cart-checkout')->layout('layouts.app');
    }
}
