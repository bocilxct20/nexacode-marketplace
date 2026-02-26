@component('emails.layouts.nexacode', [
    'title' => 'Pesanan Dikonfirmasi',
    'actionText' => 'Lihat Detail Pesanan',
    'actionUrl' => route('purchases.index'),
    'actionColor' => 'primary'
])

Pesanan kamu telah berhasil dikonfirmasi dan sedang diproses. Berikut adalah rincian lengkap transaksi kamu.

---

## Informasi Pesanan
**Nomor Pesanan:** #{{ $order->id }}  
**Transaction ID:** {{ $order->transaction_id }}  
**Tanggal:** {{ $order->created_at->format('d M Y, H:i') }} WIB

---

## Item yang Dibeli
@foreach($order->items as $item)
- **{{ $item->product->name ?? 'Product' }}** - Rp {{ number_format($item->price, 0, ',', '.') }}
@endforeach

**Total Pembayaran:** Rp {{ number_format($order->total_amount, 0, ',', '.') }}

---

### Langkah Selanjutnya
Setelah pembayaran diverifikasi, kamu akan menerima email terpisah berisi link unduhan untuk setiap produk.

kamu dapat mengakses semua unduhan melalui dashboard akun kamu kapan saja. Semua produk yang telah dibeli tersedia untuk diunduh kembali tanpa biaya tambahan.

@endcomponent
