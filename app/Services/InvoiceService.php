<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate a PDF invoice for the given order.
     *
     * @param Order $order
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generate(Order $order)
    {
        $order->load(['items.product.author', 'buyer']);

        $pdf = Pdf::loadView('emails.invoice', [
            'order' => $order,
            'buyer' => $order->buyer,
            'items' => $order->items,
            'settings' => \App\Models\PlatformSetting::pluck('value', 'key')->all(),
        ]);

        return $pdf;
    }

    /**
     * Save the invoice to storage.
     *
     * @param Order $order
     * @return string Path to the saved file
     */
    public function store(Order $order)
    {
        $pdf = $this->generate($order);
        $filename = 'invoices/invoice-' . $order->transaction_id . '.pdf';
        
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Get the download response for the invoice.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function download(Order $order)
    {
        $pdf = $this->generate($order);
        return $pdf->download('Invoice-' . $order->transaction_id . '.pdf');
    }
}
