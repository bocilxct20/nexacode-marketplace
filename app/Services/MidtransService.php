<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create Midtrans transaction and get Snap token
     */
    public function createTransaction(Order $order): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->buyer->name,
                'email' => $order->buyer->email,
                'phone' => $order->buyer->phone ?? '08123456789',
            ],
            'item_details' => $order->items->map(function ($item) {
                $name = $item->product ? $item->product->name : ($item->subscriptionPlan ? "Packet " . $item->subscriptionPlan->name : 'NexaCode Item');
                $id = $item->product_id ?: ($item->subscription_plan_id ?: 'item-' . $item->id);
                
                return [
                    'id' => $id,
                    'price' => (int) $item->price,
                    'quantity' => 1,
                    'name' => $name,
                ];
            })->toArray(),
            'enabled_payments' => [
                'credit_card', 
                'bca_va', 'bni_va', 'bri_va', 'permata_va',
                'gopay', 'shopeepay', 
                'qris',
                'echannel', // Mandiri Bill
            ],
            'callbacks' => [
                'finish' => route('checkout.success'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // Create transaction record
            Transaction::create([
                'order_id' => $order->id,
                'transaction_id' => $params['transaction_details']['order_id'],
                'payment_type' => 'pending',
                'gross_amount' => $order->total_amount,
                'status' => 'pending',
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle Midtrans notification/webhook
     */
    public function handleNotification(array $payload): bool
    {
        try {
            $notification = new Notification();

            $transactionId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentType = $notification->payment_type;

            Log::info('Midtrans Notification', [
                'transaction_id' => $transactionId,
                'status' => $transactionStatus,
                'payment_type' => $paymentType,
            ]);

            // V3-06: Signature Key Verification
            $serverKey = config('services.midtrans.server_key');
            $signatureKey = hash("sha512", ($notification->order_id . $notification->status_code . $notification->gross_amount . $serverKey));
            
            if ($notification->signature_key !== $signatureKey) {
                Log::error('Invalid Midtrans Signature Key detected!', [
                    'order_id' => $notification->order_id,
                    'provided_signature' => $notification->signature_key,
                    'calculated_signature' => $signatureKey,
                ]);
                return false;
            }

            $transaction = Transaction::where('transaction_id', $transactionId)->first();

            if (!$transaction) {
                Log::error('Transaction not found: ' . $transactionId);
                return false;
            }

            // V3-06: Gross Amount Matching
            if ((int)$notification->gross_amount !== (int)$transaction->gross_amount) {
                Log::error('Midtrans Gross Amount Mismatch!', [
                    'transaction_id' => $transactionId,
                    'expected' => $transaction->gross_amount,
                    'received' => $notification->gross_amount,
                ]);
                return false;
            }

            // Update transaction
            $transaction->update([
                'payment_type' => $paymentType,
                'status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'transaction_time' => $notification->transaction_time ?? now(),
                'settlement_time' => $notification->settlement_time ?? null,
                'bank' => $notification->bank ?? null,
                'va_number' => $notification->va_numbers[0]->va_number ?? null,
                'bill_key' => $notification->bill_key ?? null,
                'biller_code' => $notification->biller_code ?? null,
                'metadata' => $payload,
            ]);

            $order = $transaction->order;

            // Handle different transaction statuses
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->handleSuccessfulPayment($order);
                }
            } elseif ($transactionStatus == 'settlement') {
                $this->handleSuccessfulPayment($order);
            } elseif ($transactionStatus == 'pending') {
                $order->update(['status' => 'pending_payment']);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->update(['status' => 'cancelled']);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Order $order): void
    {
        $order->finalizeCompletion();
        Log::info('Order completed via Midtrans: ' . $order->id);
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus(string $transactionId): ?object
    {
        try {
            return \Midtrans\Transaction::status($transactionId);
        } catch (\Exception $e) {
            Log::error('Get Transaction Status Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction(string $transactionId): bool
    {
        try {
            \Midtrans\Transaction::cancel($transactionId);
            return true;
        } catch (\Exception $e) {
            Log::error('Cancel Transaction Error: ' . $e->getMessage());
            return false;
        }
    }
}
