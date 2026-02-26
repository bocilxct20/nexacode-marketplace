@component('emails.layouts.nexacode', [
    'title' => 'Balasan Review Baru',
    'actionText' => 'Lihat Produk',
    'actionUrl' => route('products.show', $review->product->slug)
])

Penjual produk **{{ $review->product->name }}** telah membalas review kamu.

**Review kamu:**
> {{ $review->comment }}

**Balasan Penjual:**
> {{ $review->author_reply }}

Terima kasih telah memberikan feedback untuk membantu NexaCode menjadi lebih baik.

@endcomponent
