@component('emails.layouts.nexacode', [
    'title' => 'Permintaan Penarikan Dana Diterima',
    'actionText' => 'Lihat Status',
    'actionUrl' => route('author.withdrawals'),
    'actionColor' => 'primary'
])

Permintaan penarikan dana Anda telah diterima dan sedang dalam proses verifikasi.

---

## Rincian Permintaan
**Jumlah:** Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}  
**Tanggal Permintaan:** {{ $withdrawal->created_at->format('d M Y, H:i') }} WIB  
**Status:** Sedang Diproses

---

### Estimasi Waktu
Permintaan Anda akan ditinjau dalam 1-2 hari kerja. Setelah disetujui, dana akan ditransfer ke rekening terdaftar Anda dalam 1-3 hari kerja.

Anda akan menerima notifikasi email saat status permintaan diperbarui.

@endcomponent
