@component('emails.layouts.nexacode', [
    'title' => $title,
    'actionText' => $actionText,
    'actionUrl' => $actionUrl
])

{!! $content !!}

@if(isset($metadata))
<p style="font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 20px;">
    {!! $metadata !!}
</p>
@endif

@endcomponent
