@php
    if (! isset($errors)) {
        $errors = new \Illuminate\Support\ViewErrorBag;
    }
    view()->share('errors', $errors);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') - {{ $platformSettings['site_name'] ?? 'NEXACODE' }}</title>
    
    @if(isset($platformSettings['site_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($platformSettings['site_favicon']) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles

    {{-- Global Livewire Loading Fix (Prevents skeletons from showing on initial page load) --}}
    <style>
        [wire\:loading], [wire\:loading\.delay], [wire\:loading\.inline-block], [wire\:loading\.inline], [wire\:loading\.block], [wire\:loading\.flex], [wire\:loading\.table], [wire\:loading\.grid], [wire\:loading\.inline-flex] {
            display: none !important;
        }
    </style>
</head>
<body class="h-full antialiased font-sans text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950">
    <div class="flex min-h-screen">
        {{-- Left: Form Content --}}
        <div class="flex-1 flex flex-col justify-center items-center p-8 overflow-y-auto">
            <div class="w-full max-w-sm space-y-8 py-12">
                <div class="flex justify-center opacity-80 mb-4">
                    <flux:brand href="/" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="font-bold text-2xl tracking-tighter">
                        <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold leading-none flex items-center justify-center">
                            <flux:icon name="rocket-launch" variant="micro" />
                        </x-slot>
                    </flux:brand>
                </div>

                {{ $slot }}

                @if(!request()->routeIs('logout'))
                    <flux:subheading class="text-center pt-8 border-t border-zinc-100 dark:border-zinc-800">
                        @if(request()->routeIs('register'))
                            Already have an account? <flux:link href="{{ route('login') }}" class="font-bold">Sign in</flux:link>
                        @elseif(request()->routeIs('login'))
                            First time around here? <flux:link href="{{ route('register') }}" class="font-bold">Sign up for free</flux:link>
                        @elseif(request()->routeIs('password.request') || request()->routeIs('password.reset') || request()->routeIs('verification.notice'))
                            Remember your password? <flux:link href="{{ route('login') }}" class="font-bold">Back to login</flux:link>
                        @endif
                    </flux:subheading>
                @endif
            </div>
        </div>

        {{-- Right: Aurora Testimonial Sidebar --}}
        <div class="flex-1 p-4 max-lg:hidden">
            <div class="text-white relative rounded-[3rem] h-full w-full bg-zinc-950 overflow-hidden shadow-2xl">
                {{-- Aurora Background Gradients --}}
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-zinc-950 to-emerald-900 opacity-80"></div>
                <div class="absolute top-0 right-0 w-[40rem] h-[40rem] bg-indigo-500/20 rounded-full blur-[120px] mix-blend-screen translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 left-0 w-[40rem] h-[40rem] bg-emerald-500/20 rounded-full blur-[120px] mix-blend-screen -translate-x-1/2 translate-y-1/2"></div>
                
                <div class="absolute inset-0 flex flex-col items-start justify-end p-20 z-10">
                    <div class="flex gap-1.5 mb-8 text-amber-400">
                        @for($i=0; $i<5; $i++) <flux:icon.star variant="solid" size="sm" /> @endfor
                    </div>

                    <div class="mb-10 italic font-black text-3xl xl:text-5xl leading-tight tracking-tight">
                        @if(trim($__env->yieldContent('testimonial')))
                            @yield('testimonial')
                        @elseif(request()->routeIs('register'))
                            "Elevate your craft and join a community that values quality above all else. Your journey to elite status starts here."
                        @else
                            "NEXACODE has enabled me to design, build, and deliver premium digital assets faster than ever before. The community is unmatched."
                        @endif
                    </div>

                    <div class="flex gap-5 items-center p-4 rounded-[2rem] bg-white/5 backdrop-blur-2xl border border-white/10 w-fit shadow-xl">
                        <div class="size-14 rounded-2xl bg-white/10 flex items-center justify-center overflow-hidden border border-white/20">
                            <x-nexacode-brand-n class="size-8" />
                        </div>

                        <div class="flex flex-col pr-4">
                            <div class="text-sm font-black uppercase tracking-widest">
                                @yield('side-author', 'Ahmad Dani Saputra')
                            </div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">
                                @yield('side-role', 'Creator of NEXACODE')
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Decorative element --}}
                <div class="absolute top-20 right-20 opacity-5 rotate-12">
                    <x-nexacode-brand-n class="size-96" />
                </div>
            </div>
        </div>
    </div>

    <flux:toast />
    @livewireScripts
    @fluxScripts
</body>
</html>
