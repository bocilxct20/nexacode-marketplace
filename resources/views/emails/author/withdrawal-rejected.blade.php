@component('emails.layouts.nexacode', [
    'title' => 'Penarikan Dana Ditolak',
    'actionText' => 'Perbarui Informasi Rekening',
    'actionUrl' => route('author.settings'),
    'actionColor' => 'primary'
])

Permintaan penarikan dana kamu tidak dapat diproses karena alasan berikut:

---

## Rincian Penarikan
**Jumlah:** Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}  
**Tanggal Permintaan:** {{ $withdrawal->created_at->format('d M Y') }}  
**Status:** Ditolak

### Alasan Penolakan
{{ $reason }}

---

### Langkah Selanjutnya
Pastikan informasi rekening bank kamu sudah benar dan lengkap di pengaturan profil. Setelah memperbarui informasi, kamu dapat mengajukan penarikan dana kembali.

Jika kamu yakin terjadi kesalahan, silakan hubungi tim support kami untuk bantuan lebih lanjut.

@endcomponent
