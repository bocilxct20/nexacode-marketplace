@component('emails.layouts.nexacode', [
    'title' => 'Pesanan Dikonfirmasi',
    'actionText' => 'Lihat Detail Pesanan',
    'actionUrl' => route('purchases.index'),
    'actionColor' => 'primary'
])

Pesanan Anda telah berhasil dikonfirmasi dan sedang diproses. Berikut adalah rincian lengkap transaksi Anda.

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
Setelah pembayaran diverifikasi, Anda akan menerima email terpisah berisi link unduhan untuk setiap produk.

Anda dapat mengakses semua unduhan melalui dashboard akun Anda kapan saja. Semua produk yang telah dibeli tersedia untuk diunduh kembali tanpa biaya tambahan.

@endcomponent
