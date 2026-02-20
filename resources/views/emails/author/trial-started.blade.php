@component('emails.layouts.nexacode', [
    'title' => 'Masa Percobaan Dimulai',
    'actionText' => 'Akses Dashboard',
    'actionUrl' => route('author.dashboard'),
    'actionColor' => 'success'
])

Masa percobaan gratis untuk paket **{{ $plan->name }}** telah diaktifkan. Selama 7 hari ke depan, Anda dapat mengakses semua fitur premium tanpa biaya.

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
1. **Analitik Mendalam**: Pantau performa produk Anda secara real-time
2. **Kupon Promosi**: Buat kampanye diskon untuk meningkatkan konversi
3. **Komisi Lebih Rendah**: Nikmati margin keuntungan yang lebih besar

Setelah masa trial berakhir, akun Anda akan kembali ke paket Basic secara otomatis kecuali Anda melakukan upgrade. Semua data Anda akan tetap tersimpan dengan aman.

@endcomponent
