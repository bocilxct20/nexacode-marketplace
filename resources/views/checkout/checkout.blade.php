@extends('layouts.app')

@section('title', 'Checkout - ' . $product->name)

@section('content')
<div class="max-w-5xl mx-auto py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Order Summary --}}
        <div class="lg:col-span-2 space-y-8">
            <div>
                <flux:heading size="2xl" class="mb-2">Checkout</flux:heading>
                <p class="text-zinc-500">Complete your purchase securely</p>
            </div>

            {{-- Professional Checkout Stepper --}}
            <div class="relative py-4">
                <div class="flex items-center justify-between">
                    {{-- Step 1: Select Payment --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold shadow-sm">
                            <flux:icon.check class="w-5 h-5" />
                        </div>
                        <div class="mt-3 text-center">
                            <div class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Select Payment</div>
                        </div>
                    </div>

                    {{-- Connector Line 1 --}}
                    <div class="flex-1 h-[2px] bg-zinc-200 dark:bg-zinc-800 -mx-8 relative top-[-14px]">
                        <div class="h-full bg-zinc-900 dark:bg-white transition-all duration-500" style="width: 0%;"></div>
                    </div>

                    {{-- Step 2: Review Order --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full border border-zinc-900 dark:border-white bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white font-bold shadow-sm ring-4 ring-zinc-50 dark:ring-zinc-900/50">
                            2
                        </div>
                        <div class="mt-3 text-center">
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">Review Order</div>
                        </div>
                    </div>

                    {{-- Connector Line 2 --}}
                    <div class="flex-1 h-[2px] bg-zinc-200 dark:bg-zinc-800 -mx-8 relative top-[-14px]">
                        <div class="h-full bg-zinc-200 dark:bg-zinc-800 transition-all duration-500" style="width: 0%;"></div>
                    </div>

                    {{-- Step 3: Instant Access --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 text-zinc-400 font-bold">
                            3
                        </div>
                        <div class="mt-3 text-center">
                            <div class="text-xs font-bold text-zinc-400">Instant Access</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Summary --}}
            <div class="p-6 bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm">
                <flux:heading size="lg" class="mb-6">Order Summary</flux:heading>
                <div class="flex gap-6">
                    <img src="{{ $product->thumbnail_url }}" class="w-24 h-24 rounded-xl object-cover border border-zinc-200 dark:border-zinc-800" alt="{{ $product->name }}">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-zinc-500 mb-3">by {{ $product->author->name }}</p>
                        
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800 rounded-2xl mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <flux:icon.shield-check variant="mini" class="text-emerald-500" />
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Lifetime Access</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black tabular-nums tracking-tight">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('checkout.checkout-form', ['product' => $product])
        </div>

        {{-- Right: Security Info --}}
        <div class="space-y-6 lg:mt-8">
            <div class="p-6 bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm">
                <div class="text-xs font-bold uppercase tracking-widest mb-6 text-zinc-900 dark:text-white">Secure Checkout</div>
                <div class="space-y-5">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shrink-0 border border-zinc-100 dark:border-zinc-700">
                            <flux:icon.shield-check class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <div>
                            <div class="font-bold text-sm">Secure Payment</div>
                            <div class="text-xs text-zinc-500">Your payment is encrypted</div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shrink-0 border border-zinc-100 dark:border-zinc-700">
                            <flux:icon.clock class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <div>
                            <div class="font-bold text-sm">24 Hour Window</div>
                            <div class="text-xs text-zinc-500">Complete payment within 24 hours</div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shrink-0 border border-zinc-100 dark:border-zinc-700">
                            <flux:icon.check-circle class="w-5 h-5 text-emerald-500" />
                        </div>
                        <div>
                            <div class="font-bold text-sm">Instant Access</div>
                            <div class="text-xs text-zinc-500">Download after verification</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-200/50 dark:border-zinc-800/50 rounded-3xl">
                <div class="text-sm font-bold text-zinc-900 dark:text-white mb-4">What's included:</div>
                <ul class="space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                    <li class="flex items-start gap-3">
                        <flux:icon.check class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Quality checked by NEXACODE</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <flux:icon.check class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                        <span>Future updates included</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <flux:icon.check class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                        <span>6 months support</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
