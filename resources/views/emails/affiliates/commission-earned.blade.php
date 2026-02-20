@component('emails.layouts.nexacode', [
    'title' => 'Komisi Affiliate Berhasil Didapat',
    'actionText' => 'Lihat Dashboard Affiliate',
    'actionUrl' => route('affiliate.dashboard')
])

Seseorang telah melakukan pembelian menggunakan link affiliate Anda. Komisi telah ditambahkan ke akun Anda.

### Detail Komisi
- **Produk:** {{ $earning->product->name }}
- **Jumlah Komisi:** Rp {{ number_format($earning->amount, 0, ',', '.') }}
- **Tanggal:** {{ $earning->created_at->format('d M Y H:i') }}

Terus bagikan link affiliate Anda untuk mendapatkan lebih banyak penghasilan pasif.

@endcomponent
