@props([
    'variant' => 'full', // 'full', 'navbar', 'footer', or 'icon'
])

@php
    $classes = match($variant) {
        'full' => 'rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 shadow-sm hover:shadow-md overflow-hidden p-4',
        'navbar' => 'p-0.5',
        'footer' => 'p-1',
        default => '',
    };
@endphp

<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center transition-all duration-500 ' . $classes]) }}>
    @if($variant === 'full')
        {{-- Subtle Brand Gradient Background (Soft & Light) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-50/50 to-emerald-50/50 dark:from-cyan-950/10 dark:to-emerald-950/10"></div>
    @endif

    {{-- The "N" Nexa-Minimalist Structure --}}
    <div class="relative z-10 w-full h-full flex items-center justify-center">
        @php
            // Calculate sizes based on variant
            $w = match($variant) {
                'navbar' => 'min-w-[18px] w-[1em]',
                'footer' => 'min-w-[24px] w-[1.2em]',
                default => 'min-w-[20px] w-[1em]',
            };
            $h = match($variant) {
                'navbar' => 'min-h-[22px] h-[1.2em]',
                'footer' => 'min-h-[28px] h-[1.4em]',
                default => 'min-h-[24px] h-[1.2em]',
            };
        @endphp
        
        <div class="relative {{ $w }} {{ $h }} flex justify-between">
            {{-- Left Pillar --}}
            <div class="w-[30%] h-full bg-cyan-600 dark:bg-cyan-500 rounded-sm shadow-sm relative z-20"></div>
            
            {{-- Precisely Engineered Diagonal of the "N" --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                {{-- This bar connects top-left to bottom-right --}}
                <div class="w-[35%] h-[125%] bg-gradient-to-b from-cyan-600 to-emerald-500 dark:from-cyan-500 dark:to-emerald-400 rounded-full rotate-[32deg] shadow-sm z-10 border border-white/10 translate-x-[1px]"></div>
            </div>
            
            {{-- Right Pillar --}}
            <div class="w-[30%] h-full bg-emerald-500 dark:bg-emerald-400 rounded-sm shadow-sm relative z-20"></div>
        </div>
    </div>

    @if($variant === 'full')
        {{-- Marketplace Accent (Bottom Stripe) --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-emerald-500 opacity-20"></div>
    @endif
</div>
