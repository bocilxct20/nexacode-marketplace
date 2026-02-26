@component('emails.layouts.nexacode', [
    'title' => 'Konfirmasi Pembelian',
    'actionText' => 'Akses Koleksi Saya',
    'actionUrl' => route('purchases.index'),
    'actionColor' => 'success'
])

Terima kasih atas pembelian kamu di NexaCode Marketplace. Pembayaran telah diverifikasi dan item digital kamu siap untuk diunduh.

---

## Rincian Produk
@foreach($order->items as $item)
@component('mail::panel')
### {{ $item->product->name ?? 'Product' }}
[Unduh Produk]({{ $item->product ? route('products.download', $item->product->slug) : '#' }})
@endcomponent
@endforeach

---

## Rincian Pesanan
**Transaction ID:** #{{ $order->transaction_id }}  
**Total Pembayaran:** Rp {{ number_format($order->total_amount, 0, ',', '.') }}  
**Metode Pembayaran:** {{ $order->payment_method_label ?? $order->payment_method }}

---

### Invoicing
kamu dapat mengunduh invoice resmi untuk pesanan ini melalui tautan berikut:
[Download PDF Invoice]({{ route('orders.invoice', $order) }})

---

### Bantuan Teknis
Jika kamu mengalami kendala saat mengunduh produk, silakan hubungi penulis melalui sistem pesan atau buka tiket support.

Semua produk yang telah dibeli dapat diunduh kembali kapan saja melalui dashboard akun kamu.

@endcomponent
