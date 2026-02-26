@component('emails.layouts.nexacode', [
    'title' => 'Masa Percobaan Dimulai',
    'actionText' => 'Akses Dashboard',
    'actionUrl' => route('author.dashboard'),
    'actionColor' => 'success'
])

Masa percobaan gratis untuk paket **{{ $plan->name }}** telah diaktifkan. Selama 7 hari ke depan, kamu dapat mengakses semua fitur premium tanpa biaya.

---

## Detail Masa Percobaan
**Paket:** {{ $plan->name }}  
**Berakhir Pada:** {{ $user->trial_ends_at->format('d M Y') }}

### Fitur yang Tersedia
@foreach($plan->features as $feature)
- {{ $feature }}
@endforeach

---

## Panduan Penggunaan
Manfaatkan masa percobaan ini untuk:
1. **Analitik Mendalam**: Pantau performa produk kamu secara real-time
2. **Kupon Promosi**: Buat kampanye diskon untuk meningkatkan konversi
3. **Komisi Lebih Rendah**: Nikmati margin keuntungan yang lebih besar

Setelah masa trial berakhir, akun kamu akan kembali ke paket Basic secara otomatis kecuali kamu melakukan upgrade. Semua data kamu akan tetap tersimpan dengan aman.

@endcomponent
