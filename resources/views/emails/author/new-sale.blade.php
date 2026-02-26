@component('emails.layouts.nexacode', [
    'title' => 'Konfirmasi Penjualan Baru',
    'actionText' => 'Lihat Detail Penjualan',
    'actionUrl' => route('author.sales'),
    'actionColor' => 'success'
])

Produk kamu baru saja terjual. Berikut adalah rincian transaksi.

---

## Rincian Penjualan
**Produk:** {{ $orderItem->product->name ?? 'Product' }}  
**Harga Jual:** Rp {{ number_format($orderItem->price, 0, ',', '.') }}  
**Komisi Platform:** {{ $orderItem->commission_rate ?? '15' }}%  
**Pendapatan kamu:** Rp {{ number_format($orderItem->price * (1 - ($orderItem->commission_rate ?? 15) / 100), 0, ',', '.') }}

**Transaction ID:** #{{ $orderItem->order->transaction_id }}  
**Tanggal:** {{ $orderItem->created_at->format('d M Y, H:i') }} WIB

---

### Informasi Penting
Dana dari penjualan ini akan ditahan dalam sistem escrow selama 14 hari untuk menjamin kualitas produk. Setelah periode escrow berakhir, dana akan tersedia untuk penarikan.

kamu dapat memantau semua penjualan dan pendapatan melalui Author Dashboard.

@endcomponent
