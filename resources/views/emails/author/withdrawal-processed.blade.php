@component('emails.layouts.nexacode', [
    'title' => 'Penarikan Dana Berhasil Diproses',
    'actionText' => 'Lihat Riwayat Penarikan',
    'actionUrl' => route('author.withdrawals'),
    'actionColor' => 'success'
])

Permintaan penarikan dana Anda telah diproses dan dana akan ditransfer ke rekening terdaftar dalam 1-3 hari kerja.

---

## Rincian Penarikan
**Jumlah:** Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}  
**Tanggal Permintaan:** {{ $withdrawal->created_at->format('d M Y') }}  
**Status:** Diproses  
**Rekening Tujuan:** {{ $withdrawal->payment_method ?? ($withdrawal->bank_name ? $withdrawal->bank_name . ' (' . $withdrawal->account_number . ')' : 'Rekening Terdaftar') }}

---

### Informasi Penting
Dana akan ditransfer sesuai dengan jadwal pemrosesan bank. Jika dana belum diterima setelah 3 hari kerja, silakan hubungi tim support kami.

Anda dapat memantau status penarikan melalui Author Dashboard.

@endcomponent
