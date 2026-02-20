@component('emails.layouts.nexacode', [
    'title' => $type === 'anniversary' ? 'Selamat Anniversary' : 'Pencapaian Baru',
    'actionText' => 'Lihat Dashboard',
    'actionUrl' => route('author.dashboard'),
    'actionColor' => 'success'
])

@if($type === 'anniversary')
Terima kasih telah menjadi bagian dari komunitas NexaCode selama {{ $data['years'] ?? '1' }} tahun.

---

## Perjalanan Anda
Selama periode ini, Anda telah:
- Menjual **{{ $data['total_sales'] ?? '0' }}** produk
- Melayani **{{ $data['total_customers'] ?? '0' }}** pelanggan
- Mendapatkan rating rata-rata **{{ $data['avg_rating'] ?? '0' }}/5**

Kami sangat menghargai kontribusi Anda dalam membangun ekosistem marketplace yang berkualitas.
@else
Selamat! Anda baru saja mencapai milestone: **{{ $data['milestone'] }} Penjualan** di NexaCode.

---

## Pencapaian Anda
Ini adalah bukti dari dedikasi dan kualitas produk yang Anda tawarkan. Terus pertahankan standar tinggi Anda dan tingkatkan performa penjualan.
@endif

@endcomponent
