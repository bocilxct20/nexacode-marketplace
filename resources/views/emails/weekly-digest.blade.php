@component('emails.layouts.nexacode', [
    'title' => 'Kurasi Mingguan NexaCode',
    'actionText' => 'Jelajahi Semua Produk',
    'actionUrl' => route('products.index'),
    'actionColor' => 'success'
])

Berikut adalah kurasi produk terbaik minggu ini di NexaCode Marketplace.

---

## Sedang Tren
*Produk paling dicari oleh komunitas minggu ini.*

@foreach($trending as $product)
@component('mail::panel')
### {{ $product->name }}
**Rp {{ number_format($product->price, 0, ',', '.') }}**
{{ Str::limit($product->description, 100) }}

[Lihat Detail]({{ route('products.show', $product->slug) }})
@endcomponent
@endforeach

---

## Produk Baru
*Aset terbaru yang baru saja disetujui.*

@foreach($newArrivals as $product)
**[{{ $product->name }}]({{ route('products.show', $product->slug) }})** - Rp {{ number_format($product->price, 0, ',', '.') }}
@endforeach

---

## Author Terpopuler
*Author dengan performa terbaik minggu ini.*

@foreach($stars as $author)
- **{{ $author->name }}** ({{ $author->products_count }} Produk)
@endforeach

@endcomponent
