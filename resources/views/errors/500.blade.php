<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - System Error | NEXACODE</title>
    
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
                <x-slot name="logo" class="size-8 rounded-full bg-red-500 text-white text-xs font-bold shadow-lg shadow-red-500/20">
                    <flux:icon name="exclamation-circle" variant="micro" class="size-5" />
                </x-slot>
            </flux:brand>
        </div>

        <div class="max-w-xl relative z-10 space-y-8">
            <div class="relative inline-block">
                <h1 class="text-[12rem] font-black text-red-500/5 leading-none select-none">500</h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <flux:heading size="xl" class="font-black tracking-tighter uppercase italic text-4xl sm:text-5xl">System <span class="text-red-500 underline decoration-red-500/30 decoration-8">Turbulence.</span></flux:heading>
                </div>
            </div>
            
            <p class="text-lg font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed px-4">
                We've encountered an unexpected system error. Our engineers have already been notified. 
                Don't worry, your data remains secure while we calibrate the core...
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <flux:button href="/" variant="primary" size="lg" class="w-full sm:w-auto px-12 py-4 rounded-2xl bg-zinc-900 border-zinc-900 shadow-xl shadow-zinc-900/20 font-black uppercase tracking-widest text-[11px]">Back to Safety</flux:button>
                <flux:button href="{{ route('support.index') }}" variant="danger" size="lg" class="w-full sm:w-auto px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-[11px] border shadow-xl shadow-red-500/10">Contact Support</flux:button>
            </div>
        </div>

        <div class="mt-24 text-zinc-500 dark:text-zinc-600 text-xs font-bold uppercase tracking-[0.3em] relative z-10">
            &copy; {{ date('Y') }} NEXACODE &bull; Error Code: NX-500
        </div>
    </div>

    @fluxScripts
</body>
</html>
