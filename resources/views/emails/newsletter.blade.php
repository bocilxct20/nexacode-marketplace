@component('emails.layouts.nexacode', [
    'title' => $title,
    'actionText' => $ctaText ?? null,
    'actionUrl' => $ctaUrl ?? null
])

{!! $content !!}

@component('mail::panel')
Anda menerima email ini karena terdaftar sebagai member di **NexaCode Marketplace**. Anda dapat mengelola preferensi email kapan saja melalui pengaturan profil.
@endcomponent

@endcomponent
