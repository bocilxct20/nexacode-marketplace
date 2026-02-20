@component('emails.layouts.nexacode', [
    'title' => 'Update Status Refund',
    'actionText' => 'Lihat Detail Pesanan',
    'actionUrl' => route('purchases.index'),
    'actionColor' => 'primary'
])

Permintaan refund Anda telah ditinjau dan diproses.

---

## Rincian Refund
**Order ID:** #{{ $refundRequest->order_id }}  
**Jumlah Refund:** Rp {{ number_format($refundRequest->amount, 0, ',', '.') }}  
**Status:** {{ $refundRequest->status === 'approved' ? 'DISETUJUI' : 'DITOLAK' }}

@if($refundRequest->status === 'approved')
### Informasi Pengembalian Dana
Dana akan dikembalikan ke metode pembayaran original Anda dalam 5-7 hari kerja.

Jika dana belum diterima setelah periode tersebut, silakan hubungi tim support kami.
@else
### Alasan Penolakan
@component('mail::panel')
{{ $refundRequest->admin_notes ?? 'Permintaan refund tidak memenuhi syarat kebijakan pengembalian dana.' }}
@endcomponent

Jika Anda memiliki pertanyaan mengenai keputusan ini, silakan hubungi tim support kami.
@endif

@endcomponent
