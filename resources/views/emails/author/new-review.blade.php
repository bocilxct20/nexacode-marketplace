@component('emails.layouts.nexacode', [
    'title' => 'Ulasan Baru untuk Produk kamu',
    'actionText' => 'Lihat dan Balas Ulasan',
    'actionUrl' => route('author.products.show', $review->product->id) . '#reviews',
    'actionColor' => 'primary'
])

Produk kamu baru saja menerima ulasan dari pembeli.

---

## Informasi Ulasan
**Produk:** {{ $review->product->name ?? 'Product' }}  
**Rating:** {{ $review->rating }}/5  
**Reviewer:** {{ $review->buyer->name ?? 'Customer' }}  
**Tanggal:** {{ $review->created_at->format('d M Y') }}

### Isi Ulasan
@component('mail::panel')
{{ $review->comment }}
@endcomponent

---

### Langkah Selanjutnya
Merespons ulasan—baik yang positif maupun konstruktif—menunjukkan bahwa kamu adalah author yang aktif dan peduli terhadap kepuasan pelanggan.

kamu dapat membalas ulasan ini melalui Author Dashboard.

@endcomponent
