@component('emails.layouts.nexacode', [
    'title' => 'Pengajuan Author Baru',
    'actionText' => 'Tinjau Pengajuan',
    'actionUrl' => route('admin.author-requests')
])

Pengajuan author baru telah diterima dan memerlukan peninjauan.

### Detail Pemohon
- **Nama:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Portfolio:** [{{ $authorRequest->portfolio_url }}]({{ $authorRequest->portfolio_url }})
- **Pesan:** {{ $authorRequest->message }}
- **Terdaftar:** {{ $user->created_at->format('d F Y') }}
- **Tanggal Pengajuan:** {{ $authorRequest->created_at->format('d F Y H:i') }} WIB

@component('mail::panel')
**Tindakan Diperlukan:** Mohon tinjau pengajuan ini dalam 48 jam untuk menjaga pertumbuhan platform dan memberikan pengalaman pengguna yang responsif.
@endcomponent

@endcomponent
