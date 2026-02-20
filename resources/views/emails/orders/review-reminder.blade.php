@component('emails.layouts.nexacode', [
    'title' => 'Bagikan Pengalaman Anda',
    'actionText' => 'Tulis Ulasan',
    'actionUrl' => route('products.show', $product->slug) . '#review',
    'actionColor' => 'primary'
])

Kami harap Anda puas dengan pembelian produk **{{ $product->name }}** di NexaCode Marketplace.

---

## Bantu Komunitas
Ulasan Anda sangat berharga bagi author dan pembeli lainnya. Dengan memberikan feedback, Anda membantu:
- Author untuk meningkatkan kualitas produk
- Pembeli lain untuk membuat keputusan yang tepat
- Komunitas NexaCode untuk berkembang

---

### Tulis Ulasan Sekarang
Proses review hanya membutuhkan waktu 2 menit. Bagikan pengalaman Anda mengenai:
- Kualitas produk
- Dokumentasi dan dukungan
- Value for money

Terima kasih atas kontribusi Anda untuk komunitas NexaCode.

@endcomponent
