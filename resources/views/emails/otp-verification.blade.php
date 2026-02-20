@component('emails.layouts.nexacode', [
    'title' => 'Verifikasi Email Anda',
])

Terima kasih telah bergabung di **NexaCode Marketplace**. Untuk menyelesaikan pendaftaran dan mengamankan akun Anda, silakan gunakan kode verifikasi 6-digit di bawah ini:

@component('mail::panel')
<div style="text-align: center; font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: bold; letter-spacing: 0.2em; color: #06b6d4; padding: 10px 0;">
    {{ $otp }}
</div>
@endcomponent

Kode ini akan kadaluwarsa dalam **15 menit**. Jika Anda tidak melakukan permintaan ini, silakan abaikan email ini atau hubungi support jika Anda merasa khawatir.

@endcomponent
