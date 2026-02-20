@extends('layouts.app')

@section('title', 'System Error')

@section('content')
<main class="min-h-[70vh] flex items-center justify-center container">
    <div class="text-center space-y-8 max-w-lg mx-auto">
        <div class="relative inline-block">
            <h1 class="text-[12rem] font-black text-zinc-100 dark:text-zinc-900 leading-none">500</h1>
            <div class="flex flex-col items-center">
                <flux:brand href="/" name="NEXACODE" logo="https://fluxui.dev/img/logo.png" class="mb-12 opacity-50 grayscale hover:grayscale-0 transition-all">
                    <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                        <flux:icon name="rocket-launch" variant="micro" />
                    </x-slot>
                </flux:brand>
                
                <div class="w-full max-w-lg">
                    <flux:callout variant="danger" icon="x-circle" heading="Something went wrong. Try again or contact support." class="p-8">
                        <div class="mt-4 space-y-4">
                            <flux:text>We're experiencing a technical issue on our end. Our engineers have been notified.</flux:text>
                            
                            <div class="flex gap-3">
                                <flux:button href="/" variant="primary">Back to Home</flux:button>
                                <flux:button href="{{ route('support.index') }}" variant="outline">Contact Support</flux:button>
                            </div>
                        </div>
                    </flux:callout>
                </div>
            </div>
        </div>
        
        <div class="pt-12">
            <flux:separator variant="subtle" />
            <p class="mt-8 text-xs text-zinc-400 uppercase tracking-[0.3em] font-bold">Error Code: NX-500</p>
        </div>
    </div>
</main>
@endsection
