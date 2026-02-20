@component('emails.layouts.nexacode', [
    'title' => '🔥 Price Drop Alert!',
    'actionText' => 'Shop this Deal',
    'actionUrl' => $url,
    'actionColor' => 'emerald'
])

# Item di Wishlist kamu turun harga!

Kabar gembira! Produk **{{ $product->name }}** yang kamu incar baru saja turun harga. Ini saat yang tepat untuk menambahkannya ke koleksi script kamu.

<div style="padding: 24px; background-color: #f9fafb; border-radius: 12px; text-align: center; margin: 24px 0;">
    <div style="font-size: 14px; text-decoration: line-through; color: #9ca3af;">Rp {{ number_format($oldPrice, 0, ',', '.') }}</div>
    <div style="font-size: 32px; font-weight: 900; color: #10b981;">Rp {{ number_format($newPrice, 0, ',', '.') }}</div>
    <div style="font-size: 12px; font-weight: 700; color: #059669; margin-top: 8px;">YOU SAVE Rp {{ number_format($savings, 0, ',', '.') }}</div>
</div>

Jangan sampai ketinggalan, miliki produk ini sekarang sebelum harga kembali normal atau stok (jika terbatas) habis.

@endcomponent
