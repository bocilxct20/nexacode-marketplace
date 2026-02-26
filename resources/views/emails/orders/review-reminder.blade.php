@component('emails.layouts.nexacode', [
    'title' => 'Bagikan Pengalaman kamu',
    'actionText' => 'Tulis Ulasan',
    'actionUrl' => route('products.show', $product->slug) . '#review',
    'actionColor' => 'primary'
])

Kami harap kamu puas dengan pembelian produk **{{ $product->name }}** di NexaCode Marketplace.

---

## Bantu Komunitas
Ulasan kamu sangat berharga bagi author dan pembeli lainnya. Dengan memberikan feedback, kamu membantu:
- Sesama builder memilih produk terbaik
- Author meningkatkan kualitas produk mereka
- Mendapatkan XP tambahan untuk akun kamu
- Komunitas NexaCode untuk berkembang

---

### Tulis Ulasan Sekarang
Proses review hanya membutuhkan waktu 2 menit. Bagikan pengalaman kamu mengenai:
- Kualitas kode
- Kemudahan implementasi
- Kecepatan support (jika ada)
- Value for money

Terima kasih atas kontribusi kamu untuk komunitas NexaCode.

@endcomponent
