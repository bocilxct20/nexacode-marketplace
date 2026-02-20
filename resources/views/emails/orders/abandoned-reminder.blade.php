@component('emails.layouts.nexacode', [
    'title' => 'Complete Your Purchase',
    'actionText' => 'View Your Order',
    'actionUrl' => route('purchases.index'),
    'actionColor' => 'success'
])

Kami melihat bahwa Anda belum menyelesaikan pembelian untuk item berikut di NexaCode Marketplace.

---

## Item di Keranjang Anda
@foreach($order->items as $item)
@component('mail::panel')
### {{ $item->product->name }}
**Harga:** Rp {{ number_format($item->price, 0, ',', '.') }}

{{ Str::limit($item->product->description, 120) }}
@endcomponent
@endforeach

**Total:** Rp {{ number_format($order->total_amount, 0, ',', '.') }}

---

### Butuh Bantuan?
Jika Anda memiliki pertanyaan mengenai produk atau metode pembayaran, silakan hubungi tim support kami di [support@nexacode.id](mailto:support@nexacode.id).

Keranjang Anda akan tetap tersimpan selama 7 hari ke depan.

@endcomponent
