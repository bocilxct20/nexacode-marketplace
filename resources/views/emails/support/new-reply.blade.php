@component('emails.layouts.nexacode', [
    'title' => 'Balasan Support Baru',
    'actionText' => 'Lihat Balasan',
    'actionUrl' => route('support.show', $reply->support_ticket_id)
])

Ada balasan baru pada tiket support **#{{ $reply->support_ticket_id }}** terkait produk **{{ $reply->ticket->product->name }}**.

**Pesan dari {{ $reply->user->name }}:**
> {{ $reply->message }}

Silakan klik tombol di bawah untuk melihat detail percakapan atau membalas pesan tersebut.

@endcomponent
