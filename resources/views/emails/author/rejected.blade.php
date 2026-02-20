@component('emails.layouts.nexacode', [
    'title' => 'Update Pengajuan Akun Author',
    'actionText' => 'Hubungi Support',
    'actionUrl' => 'mailto:support@nexacode.id'
])

Terima kasih telah tertarik untuk menjadi Author di NexaCode. Setelah melalui proses review, mohon maaf kami belum dapat menyetujui pengajuan akun Author Anda saat ini.

@if($reason)
### Alasan Penolakan
{{ $reason }}
@endif

Anda tetap dapat mencoba mengajukan kembali di masa mendatang dengan memastikan seluruh persyaratan dan kualitas portofolio terpenuhi.

Jika ada pertanyaan, silakan hubungi tim support kami.

@endcomponent
