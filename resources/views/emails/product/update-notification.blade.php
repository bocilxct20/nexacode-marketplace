@component('emails.layouts.nexacode', [
    'title' => 'Update Produk Tersedia',
    'actionText' => 'Unduh Update',
    'actionUrl' => route('products.download', $product->slug),
    'actionColor' => 'success'
])

Produk **{{ $product->name }}** yang Anda beli telah diperbarui ke versi **{{ $version->version_number }}**.

---

## Informasi Update
**Versi Baru:** {{ $version->version_number }}  
**Tanggal Rilis:** {{ $version->created_at->format('d M Y') }}

### Changelog
@component('mail::panel')
{!! $version->changelog !!}
@endcomponent

---

### Cara Mengunduh
Anda dapat mengunduh versi terbaru melalui dashboard akun Anda. Semua update produk tersedia gratis untuk pembeli yang sudah ada.

Kamu dapat langsung mengunduh versi terbaru melalui dashboard pesanan.

@endcomponent
