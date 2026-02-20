@component('emails.layouts.nexacode', [
    'title' => $title,
    'actionText' => $actionText ?? null,
    'actionUrl' => $actionUrl ?? null,
    'actionColor' => $actionColor ?? 'primary'
])

{{ $message }}

@endcomponent
