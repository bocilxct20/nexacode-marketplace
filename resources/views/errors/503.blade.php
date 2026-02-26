<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance - {{ $platformSettings['site_name'] ?? config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    @if(isset($platformSettings['site_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($platformSettings['site_favicon']) }}">
    @endif

    <style>
        [wire\:cloak] { display: none; }
    </style>
</head>
<body class="h-full antialiased font-sans text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950 select-none overflow-hidden">
    <div class="min-h-full flex flex-col items-center justify-center p-6 text-center bg-aurora relative">
        <div class="absolute inset-0 bg-white/40 dark:bg-zinc-950/60 backdrop-blur-3xl"></div>

        <!-- Brand Logo -->
        <div class="mb-12 relative z-10">
            <div class="size-24 rounded-[2rem] overflow-hidden mx-auto shadow-2xl shadow-cyan-500/20 ring-4 ring-white dark:ring-zinc-900 bg-white dark:bg-zinc-900 flex items-center justify-center p-4">
                @if(isset($platformSettings['site_logo']))
                    <img src="{{ Storage::url($platformSettings['site_logo']) }}" class="h-full w-full object-contain">
                @else
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white w-full h-full flex items-center justify-center rounded-2xl">
                        <x-lucide-rocket class="size-10" />
                    </div>
                @endif
            </div>
        </div>

        <div class="max-w-xl relative z-10">
            <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-6xl mb-6 uppercase">
                {{ $platformSettings['site_name'] ?? 'NEXACODE' }} <span class="text-cyan-500">Upgrade</span>
            </h1>
            
            <p class="text-lg font-bold text-zinc-600 dark:text-zinc-400 mb-10 leading-relaxed px-4">
                We're currently performing some scheduled maintenance to improve your experience. 
                Don't worry, we'll be back online very soon with even better tools for you.
            </p>

            <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-full border border-white dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-sm font-black uppercase tracking-widest shadow-xl">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-cyan-500"></span>
                </span>
                Refining the experience for you
            </div>
        </div>

        <div class="mt-24 text-zinc-500 dark:text-zinc-600 text-xs font-bold uppercase tracking-[0.3em] relative z-10">
            &copy; {{ date('Y') }} {{ $platformSettings['site_name'] ?? config('app.name') }} &bull; Premium Ecosystem
        </div>
    </div>

    <script>
        window.Flux = window.Flux || {};
        window.Flux.toast = function (data) {
            const d = { variant: data[0]?.variant||data.variant||'success', heading: data[0]?.heading||data.heading||'', text: data[0]?.text||data.text||(typeof data==='string'?data:'') };
            window.dispatchEvent(new CustomEvent('toast-show', { detail: d }));
        };
        window.fluxModal = window.fluxModal || function(name, ...args) {
            document.addEventListener('alpine:initialized', () => { if (window.fluxModal) window.fluxModal(name, ...args); }, { once: true });
        };
    </script>
    @fluxScripts
</body>
</html>
