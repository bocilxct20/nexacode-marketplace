@component('emails.layouts.nexacode', [
    'title' => 'Pengajuan Refund Baru',
    'actionText' => 'Tinjau Permintaan',
    'actionUrl' => route('admin.moderation'),
    'actionColor' => 'primary'
])

Permintaan refund baru telah diajukan dan memerlukan peninjauan.

---

## Rincian Permintaan
**Refund ID:** #{{ $refundRequest->id }}  
**Order ID:** #{{ $refundRequest->order_id }}  
**User ID:** {{ $refundRequest->user_id }}  
**Status:** {{ ucfirst($refundRequest->status) }}  
**Tanggal Permintaan:** {{ $refundRequest->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }} WIB

### Alasan Refund
@component('mail::panel')
{{ $refundRequest->reason ?? 'Tidak ada alasan yang diberikan' }}
@endcomponent

---

### Tindakan Diperlukan
Silakan tinjau permintaan ini dan putuskan untuk menyetujui atau menolak refund melalui Admin Panel.

@endcomponent
