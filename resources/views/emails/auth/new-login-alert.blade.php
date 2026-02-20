@component('mail::message')
# Login Baru Terdeteksi 🔐

Hei **{{ $user->name }}**,

Kami mendeteksi login baru ke akun NEXACODE kamu.

@component('mail::panel')
**Waktu:** {{ $loginAt }}
**IP Address:** {{ $ip }}
**Device:** {{ \Illuminate\Support\Str::limit($device, 80) }}
@endcomponent

**Bukan kamu?** Segera amankan akun kamu dengan mengklik tombol di bawah ini untuk logout dari semua perangkat dan ganti password.

@component('mail::button', ['url' => route('login'), 'color' => 'red'])
Bukan Saya — Amankan Akun
@endcomponent

Jika ini memang kamu, abaikan email ini.

Salam,
**Tim Keamanan NEXACODE**

---
<small>Email ini dikirim otomatis. Jangan balas email ini.</small>
@endcomponent
