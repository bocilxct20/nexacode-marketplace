@component('emails.layouts.nexacode', [
    'title' => $title,
    'actionText' => $actionText,
    'actionUrl' => $actionUrl
])

Kami mendeteksi aktivitas penting pada akun NexaCode Anda. Keamanan akun Anda adalah prioritas utama kami.

**Detail Aktivitas:**
> {{ $messageBody }}

Waktu Aktivitas: {{ now()->format('d M Y, H:i') }} (WIB)

Jika ini **bukan** dilakukan oleh Anda, segera ganti password dan hubungi tim support kami melalui [support@nexacode.id](mailto:support@nexacode.id).

@endcomponent
