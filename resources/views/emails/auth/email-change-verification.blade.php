@component('mail::message')
# Verifikasi Email Baru ✉️

Hei **{{ $user->name }}**,

Kamu baru saja meminta perubahan alamat email akun NEXACODE ke:

@component('mail::panel')
**Email Baru:** {{ $newEmail }}
@endcomponent

Klik tombol di bawah untuk **mengkonfirmasi email baru** kamu. Link ini berlaku selama **24 jam**.

@component('mail::button', ['url' => $verificationUrl])
Konfirmasi Email Baru
@endcomponent

Jika kamu tidak meminta perubahan ini, abaikan email ini. Email lama kamu **tidak akan berubah**.

Salam,
**Tim NEXACODE**

---
<small>Link verifikasi berlaku 24 jam dan hanya bisa digunakan sekali.</small>
@endcomponent
