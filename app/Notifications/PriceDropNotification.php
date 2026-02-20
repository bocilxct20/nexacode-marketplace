<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class PriceDropNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $oldPrice;
    protected $newPrice;

    public function __construct(Product $product, $oldPrice, $newPrice)
    {
        $this->product = $product;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $savings = $this->oldPrice - $this->newPrice;
        
        return (new MailMessage)
            ->subject("🔥 Price Drop! Save Rp " . number_format($savings, 0, ',', '.') . " on {$this->product->name}")
            ->markdown('emails.price_drop', [
                'product' => $this->product,
                'oldPrice' => $this->oldPrice,
                'newPrice' => $this->newPrice,
                'savings' => $savings,
                'url' => route('products.show', $this->product->slug)
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'price_drop',
            'title' => 'Price Drop! 🔥',
            'message' => "Produk di wishlist kamu \"{$this->product->name}\" turun harga menjadi Rp " . number_format($this->newPrice, 0, ',', '.') . "!",
            'product_id' => $this->product->id,
            'action_url' => route('products.show', $this->product->slug),
        ];
    }
}
