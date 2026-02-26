@extends('layouts.app')

@section('content')
<flux:container class="py-12">
    <div class="max-w-2xl mx-auto text-center">
        @php
            $hasEliteItem = $order && $order->items->contains(fn($item) => $item->product->author->isElite());
        @endphp

        <div class="mb-8">
            <div class="w-24 h-24 {{ $hasEliteItem ? 'bg-amber-100 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : 'bg-emerald-100 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' }} rounded-full flex items-center justify-center mx-auto mb-6 border">
                <flux:icon.check-circle class="w-16 h-16 {{ $hasEliteItem ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}" />
            </div>
            
            <flux:heading size="xl" class="mb-4 {{ $hasEliteItem ? 'text-amber-600 dark:text-amber-500 font-black' : '' }}">
                {{ $hasEliteItem ? 'Elite Purchase successful!' : 'Payment Successful!' }}
            </flux:heading>
            <flux:subheading class="mb-6">
                {{ $hasEliteItem ? 'Kamu baru saja mendapatkan aset premium dari Elite Creator. Pesanan kamu telah dikonfirmasi.' : 'Terima kasih atas pembeliannya. Pesanan kamu telah berhasil dikonfirmasi.' }}
            </flux:subheading>
        </div>

        <flux:card class="mb-8 {{ $hasEliteItem ? 'border-amber-500/30 bg-amber-500/5' : '' }}">
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400">Order ID</span>
                    <span class="font-semibold">#{{ $order ? $order->id : request('order_id') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-zinc-600 dark:text-zinc-400">Status</span>
                    <flux:badge color="{{ $hasEliteItem ? 'amber' : 'emerald' }}">Completed</flux:badge>
                </div>
            </div>
        </flux:card>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <flux:button href="{{ route('downloads.index') }}" variant="primary">
                <flux:icon.arrow-down-tray class="w-4 h-4" />
                Download Your Products
            </flux:button>
            <flux:button href="{{ route('purchases.index') }}" variant="ghost">
                <flux:icon.shopping-bag class="w-4 h-4" />
                View Orders
            </flux:button>
        </div>

        <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex items-start gap-3">
                <flux:icon.envelope class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                <div class="text-left text-sm text-blue-900 dark:text-blue-100">
                    <p class="font-semibold mb-1">Email Konfirmasi Terkirim</p>
                    <p class="text-blue-800 dark:text-blue-200">
                        Kami telah mengirimkan email konfirmasi berisi detail pesanan dan link download kamu ke alamat email yang terdaftar.
                    </p>
                </div>
            </div>
        </div>
    </div>
</flux:container>
@endsection
