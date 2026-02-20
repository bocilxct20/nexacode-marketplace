<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Services\QrisService;
use App\Services\MidtransService;
use App\Http\Requests\Download\UploadProofRequest;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use Flux;

class CheckoutController extends Controller
{
    protected $qrisService;

    public function __construct(QrisService $qrisService)
    {
        $this->qrisService = $qrisService;
    }

    public function checkout(Product $product)
    {
        if (!$product->isApproved()) abort(404);
        $paymentMethods = PaymentMethod::active()->get();
        return view('checkout.checkout', compact('product', 'paymentMethods'));
    }

    public function process(Request $request, Product $product)
    {
        if (!$product->isApproved()) abort(404);
        $request->validate(['payment_method_id' => 'required|exists:payment_methods,id']);

        $user = Auth::user();

        // Prevent double purchase
        if ($user->orders()->where('status', OrderStatus::COMPLETED)->whereHas('items', fn($q) => $q->where('product_id', $product->id))->exists()) {
            return redirect()->route('products.show', $product)->with('info', 'You already own this product. Check your downloads.');
        }

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
        
        $order = DB::transaction(function () use ($user, $paymentMethod, $product) {
            $order = Order::forceCreate([
                'buyer_id' => $user->id,
                'total_amount' => $product->price,
                'status' => OrderStatus::PENDING,
                'payment_method' => $paymentMethod->name,
                'payment_method_id' => $paymentMethod->id,
                'expires_at' => now()->addHours(24),
                'affiliate_id' => Order::resolveAffiliateId(),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
            ]);

            return $order;
        });

        try {
            Mail::to($user->email)->send(new OrderConfirmation($order->load(['items.product', 'buyer'])));
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation: ' . $e->getMessage());
        }

        if ($paymentMethod->isQris() && $paymentMethod->qris_static) {
            try {
                $order->update(['qris_dynamic' => $this->qrisService->generateDynamic($paymentMethod->qris_static, $product->price)]);
            } catch (\Exception $e) {
                Log::error('QRIS generation failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('payment.show', $order)->with('status', 'Order created successfully.');
    }

    public function payment(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->isExpired()) {
            return redirect()->route('dashboard.orders')->with('error', 'This order has expired.');
        }

        $order->load(['items.product', 'paymentMethod']);

        if ($order->paymentMethod?->isQris() && !$order->qris_dynamic && $order->paymentMethod->qris_static) {
            try {
                $order->update(['qris_dynamic' => $this->qrisService->generateDynamic($order->paymentMethod->qris_static, $order->total_amount)]);
                $order->refresh();
            } catch (\Exception $e) {
                Log::error('Auto-QRIS failed: ' . $e->getMessage());
            }
        }

        $snapToken = null;
        if ($order->payment_method === 'midtrans' || !$order->payment_method_id) {
            try {
                $snapToken = app(MidtransService::class)->createTransaction($order);
            } catch (\Exception $e) {
                Log::error('Midtrans token failed: ' . $e->getMessage());
            }
        }

        return view('checkout.payment', compact('order', 'snapToken'));
    }

    public function confirm(UploadProofRequest $request, Order $order)
    {
        $this->authorize('confirmPayment', $order);

        try {
            $order->update([
                'payment_proof' => $request->file('payment_proof')->store('payment-proofs', 'public'),
                'status' => OrderStatus::PENDING_VERIFICATION,
            ]);

            return redirect()->route('dashboard.orders')->with('status', 'Payment proof uploaded.');
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed.');
        }
    }

    public function midtransCheckout(Request $request)
    {
        $product = Product::findOrFail($request->get('product_id', 0));
        
        if (Order::where('buyer_id', auth()->id())->where('status', 'completed')->whereHas('items', fn($q) => $q->where('product_id', $product->id))->exists()) {
            return redirect()->route('products.show', $product)->with('info', 'You already own this product.');
        }

        return view('checkout.index', compact('product'));
    }

    public function midtransProcess(Request $request)
    {
        return redirect()->route('checkout.index', ['product_id' => $request->product_id]);
    }

    public function paymentSuccess()
    {
        Flux::toast(variant: 'success', heading: 'Payment Successful', text: 'Thank you for your purchase!');
        return view('checkout.success');
    }

    public function paymentPending()
    {
        return view('checkout.pending');
    }

    public function paymentFailed()
    {
        Flux::toast(variant: 'danger', heading: 'Payment Failed', text: 'Payment could not be processed.');
        return view('checkout.failed');
    }

    public function buy(Product $product)
    {
        return redirect()->route('checkout.index', ['product_id' => $product->id]);
    }

    public function uploadPaymentProof(UploadProofRequest $request, Order $order)
    {
        $order->uploadPaymentProof($request->file('payment_proof'));
        
        try {
            Mail::to(config('mail.aliases.admin'))->queue(new \App\Notifications\SystemNotification([
                'title' => 'Bukti Bayar Baru 📄',
                'message' => "User {$order->buyer->name} mengupload bukti bayar untuk Order #{$order->transaction_id}.",
                'type' => 'payment',
                'action_text' => 'Buka Moderasi',
                'action_url' => route('admin.moderation'),
            ]));
        } catch (\Exception $e) {
            Log::error('Admin notify failed: ' . $e->getMessage());
        }
        
        return redirect()->route('checkout.payment', $order)->with('success', 'Uploaded successfully.');
    }

    public function cancel(Order $order)
    {
        $this->authorize('cancel', $order);
        $order->update(['status' => OrderStatus::CANCELLED]);
        return redirect()->route('purchases.index')->with('status', 'Pesanan dibatalkan.');
    }

    public function downloadInvoice(Order $order, \App\Services\InvoiceService $invoiceService)
    {
        $this->authorize('view', $order);
        
        if ($order->status !== 'completed') {
            return back()->with('error', 'Invoice hanya tersedia untuk pesanan yang sudah selesai.');
        }

        return $invoiceService->download($order);
    }
}
