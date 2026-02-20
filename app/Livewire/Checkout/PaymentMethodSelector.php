<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\QrisService;
use Livewire\Component;
use Flux;

class PaymentMethodSelector extends Component
{
    public Order $order;
    public $selectedMethodId;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function selectMethod($methodId)
    {
        $this->selectedMethodId = $methodId;
        $method = PaymentMethod::findOrFail($methodId);

        $this->order->update([
            'payment_method_id' => $method->id,
            'payment_method' => $method->name,
        ]);

        if ($method->isQris() && $method->qris_static) {
            try {
                $qrisService = app(QrisService::class);
                $dynamicQris = $qrisService->generateDynamic(
                    $method->qris_static,
                    $this->order->total_amount
                );
                $this->order->update(['qris_dynamic' => $dynamicQris]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate dynamic QRIS in PaymentMethodSelector: ' . $e->getMessage());
            }
        }

        Flux::toast(
            variant: 'success',
            heading: 'Metode Pembayaran Terpilih',
            text: "Silakan selesaikan pembayaran menggunakan {$method->name}.",
        );

        return redirect()->route('payment.show', $this->order);
    }

    public function render()
    {
        return view('livewire.checkout.payment-method-selector', [
            'methods' => PaymentMethod::active()->get(),
        ]);
    }
}
