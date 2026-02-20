@component('mail::message')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
            <tr>
                <td style="padding-right: 12px; vertical-align: middle;">
                    {{-- Stylized "N" Logo --}}
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 7px; height: 28px; background-color: #0891b2; border-radius: 2px;"></td>
                            <td style="width: 10px; height: 28px; text-align: center; vertical-align: middle;">
                                <div style="width: 4px; height: 18px; background-color: #10b981; border-radius: 10px; margin: 0 auto;"></div>
                            </td>
                            <td style="width: 7px; height: 28px; background-color: #10b981; border-radius: 2px;"></td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: middle;">
                    <span style="color: #18181b; font-size: 22px; font-weight: 900; letter-spacing: -0.04em; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
                        NEXA<span style="color: #0891b2;">CODE</span>
                    </span>
                </td>
            </tr>
        </table>
    @endcomponent
@endslot

{{-- Main Content Space --}}
<div style="padding-top: 15px;"></div>

{{-- Content Title --}}
# {{ $title }}

{{-- Main Content --}}
{{ $slot }}

{{-- Dynamic Action Button --}}
@isset($actionText)
@component('mail::button', ['url' => $actionUrl, 'color' => $actionColor ?? 'primary'])
{{ $actionText }}
@endcomponent
@endisset

{{-- Closing --}}
<div style="margin-top: 50px; border-top: 1px solid #f4f4f5; padding-top: 30px;">
    <p style="color: #52525b; font-size: 15px; line-height: 1.6;">
        Terima kasih,<br>
        <span style="font-weight: 800; color: #18181b;">Team</span> <span style="font-weight: 900; color: #0891b2;">NexaCode</span>
    </p>
</div>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<div style="text-align: center; color: #a1a1aa; font-size: 12px; line-height: 1.5;">
    <div style="margin-bottom: 5px;">
        <span style="font-weight: 800; color: #71717a; letter-spacing: 1px;">NEXACODE MARKETPLACE</span>
    </div>
    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</div>
@endcomponent
@endslot
@endcomponent
