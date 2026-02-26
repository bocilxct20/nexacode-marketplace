<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | NEXACODE</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    <style>
        [wire\:cloak] { display: none; }
    </style>
</head>
<body class="h-full antialiased font-sans text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950 select-none overflow-hidden">
    <div class="min-h-full flex flex-col items-center justify-center p-6 text-center bg-aurora relative">
        <div class="absolute inset-0 bg-white/40 dark:bg-zinc-950/60 backdrop-blur-3xl"></div>

        <!-- Brand Logo -->
        <div class="mb-12 relative z-10">
            <flux:brand href="/" name="NEXACODE" class="font-bold text-2xl">
                <x-slot name="logo" class="size-8 rounded-full bg-cyan-500 text-white text-xs font-bold">
                    <flux:icon name="rocket-launch" variant="micro" class="size-5" />
                </x-slot>
            </flux:brand>
        </div>

        <div class="max-w-xl relative z-10 space-y-8">
            <div class="relative inline-block">
                <h1 class="text-[12rem] font-black text-zinc-900/5 dark:text-white/5 leading-none select-none">404</h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <flux:heading size="xl" class="font-black tracking-tighter uppercase italic text-4xl sm:text-5xl">Oops! <span class="text-cyan-500 underline decoration-cyan-500/30 decoration-8">Link Missing.</span></flux:heading>
                </div>
            </div>
            
            <p class="text-lg font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed px-4">
                The page you're searching for seems to have vanished into the digital void. 
                Let's get you back to where the builders are.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <flux:button href="/" variant="primary" size="lg" icon="home" class="w-full sm:w-auto px-12 py-4 rounded-2xl shadow-xl shadow-cyan-500/20 font-black uppercase tracking-widest text-[11px]">Return Home</flux:button>
                <flux:button href="{{ route('products.index') }}" variant="ghost" size="lg" icon="magnifying-glass" class="w-full sm:w-auto px-12 py-4 rounded-2xl font-bold uppercase tracking-widest text-[11px] border border-zinc-200 dark:border-zinc-800 backdrop-blur-md">Explore Marketplace</flux:button>
            </div>
        </div>

        <div class="mt-24 text-zinc-500 dark:text-zinc-600 text-xs font-bold uppercase tracking-[0.3em] relative z-10">
            &copy; {{ date('Y') }} NEXACODE &bull; Error Code: NX-404
        </div>
    </div>

    @fluxScripts
</body>
</html>
