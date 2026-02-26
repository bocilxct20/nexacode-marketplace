@component('emails.layouts.nexacode', [
    'title' => 'Produk Disetujui',
    'actionText' => 'Lihat Produk Live',
    'actionUrl' => route('products.show', $product->slug),
    'actionColor' => 'success'
])

Produk kamu telah berhasil melewati proses moderasi dan sekarang tersedia untuk dibeli di NexaCode Marketplace.

---

## Informasi Produk
**Nama Produk:** {{ $product->name }}  
**Kategori:** {{ $product->category->name ?? 'Uncategorized' }}  
**Harga:** Rp {{ number_format($product->price, 0, ',', '.') }}  
**Status:** Live

---

### Langkah Selanjutnya
Produk kamu sekarang dapat ditemukan oleh pembeli melalui pencarian dan kategori. kamu dapat memantau performa penjualan melalui Author Dashboard.

Pastikan untuk merespons pertanyaan pembeli dan memperbarui produk secara berkala untuk mempertahankan kualitas.

@endcomponent
