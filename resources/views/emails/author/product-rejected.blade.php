@component('emails.layouts.nexacode', [
    'title' => 'Update Pengajuan Produk',
    'actionText' => 'Edit Produk',
    'actionUrl' => route('author.products.edit', $product->id),
    'actionColor' => 'primary'
])

Produk kamu tidak dapat disetujui untuk dipublikasikan karena tidak memenuhi standar kualitas NexaCode Marketplace.

---

## Informasi Produk
**Nama Produk:** {{ $product->name }}  
**Status:** Ditolak

### Alasan Penolakan
{{ $reason }}

---

### Langkah Selanjutnya
Silakan perbaiki produk kamu sesuai dengan feedback di atas dan ajukan kembali untuk review. Tim moderasi kami akan meninjau ulang pengajuan kamu.

Jika kamu memiliki pertanyaan mengenai standar kualitas atau proses moderasi, silakan hubungi tim support kami.

@endcomponent
