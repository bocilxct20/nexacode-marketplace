@component('emails.layouts.nexacode', [
    'title' => 'Paket Premium Diaktifkan',
    'actionText' => 'Buka Author Dashboard',
    'actionUrl' => route('author.dashboard'),
    'actionColor' => 'success'
])

Your payment has been successfully verified. Your account has been upgraded to **{{ $plan->name ?? 'Premium' }}** tier.

---

## Subscription Details
**Plan:** {{ $plan->name ?? 'Premium' }}  
**Valid Until:** {{ $order->buyer->subscription_ends_at?->format('d M Y') ?? 'N/A' }}  
**Transaction ID:** #{{ $order->transaction_id }}

### Fitur yang Tersedia
@if($plan && $plan->features)
@foreach($plan->features as $feature)
- {{ $feature }}
@endforeach
@else
- Akses penuh ke fitur premium NexaCode
- Prioritas dukungan pelanggan
- Biaya komisi yang lebih rendah
@endif

---

### Langkah Selanjutnya
kamu sekarang dapat mengakses fitur premium melalui Author Dashboard, termasuk analitik mendalam, kupon promosi, dan potongan komisi sesuai tier kamu.

Jika kamu memiliki pertanyaan, tim support kami siap membantu.

@endcomponent
