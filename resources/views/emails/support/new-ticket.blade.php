@component('emails.layouts.nexacode', [
    'title' => 'Tiket Support Baru',
    'actionText' => 'Lihat Tiket & Balas',
    'actionUrl' => route('support.show', $ticket->id)
])

kamu menerima tiket support baru untuk produk **{{ $ticket->product->name }}**.

### Detail Tiket
- **Subjek:** {{ $ticket->subject }}
- **Prioritas:** {{ ucfirst($ticket->priority) }}
- **Pembeli:** {{ $ticket->user->name }}

**Pesan:**
> {{ $ticket->replies->first()->message }}

Memberikan support yang cepat dan responsif adalah kunci untuk mempertahankan rating tinggi dan kepuasan pelanggan.

@endcomponent
