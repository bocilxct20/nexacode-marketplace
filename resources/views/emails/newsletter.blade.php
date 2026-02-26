@component('emails.layouts.nexacode', [
    'title' => $title,
    'actionText' => $ctaText ?? null,
    'actionUrl' => $ctaUrl ?? null
])

{!! $content !!}

@component('mail::panel')
kamu menerima email ini karena terdaftar sebagai member di **NexaCode Marketplace**. kamu dapat mengelola preferensi email kapan saja melalui pengaturan profil.
@endcomponent

@endcomponent
