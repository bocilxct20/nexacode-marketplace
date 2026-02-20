@component('emails.layouts.nexacode', [
    'title' => 'Pengajuan Produk Baru',
    'actionText' => 'Tinjau Produk',
    'actionUrl' => route('admin.products.moderate', $product->id),
    'actionColor' => 'primary'
])

Produk baru telah diajukan untuk moderasi dan memerlukan peninjauan.

---

## Informasi Produk
**Nama:** {{ $product->name }}  
**Author:** {{ $product->author->name }}  
**Kategori:** {{ $product->category->name ?? 'Uncategorized' }}  
**Harga:** Rp {{ number_format($product->price, 0, ',', '.') }}  
**Tanggal Pengajuan:** {{ $product->created_at->format('d M Y, H:i') }} WIB

---

### Tindakan Diperlukan
Silakan tinjau produk ini dan putuskan untuk menyetujui atau menolak melalui Admin Panel.

@endcomponent
