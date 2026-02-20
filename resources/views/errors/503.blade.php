<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance - {{ $platformSettings['site_name'] ?? config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    @if(isset($platformSettings['site_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($platformSettings['site_favicon']) }}">
    @endif

    <style>
        [wire\:cloak] { display: none; }
    </style>
</head>
<body class="h-full antialiased font-sans text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950 select-none">
    <div class="min-h-full flex flex-col items-center justify-center p-6 text-center">
        <!-- Brand Logo -->
        <div class="mb-12">
            <div class="size-20 rounded-3xl overflow-hidden mx-auto shadow-2xl shadow-cyan-500/20 ring-4 ring-white dark:ring-zinc-900">
                @if(isset($platformSettings['site_logo']))
                    <img src="{{ Storage::url($platformSettings['site_logo']) }}" class="h-full w-full object-cover">
                @else
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white w-full h-full flex items-center justify-center">
                        <x-lucide-rocket class="size-10" />
                    </div>
                @endif
            </div>
        </div>

        <div class="max-w-xl">
            <h1 class="text-4xl font-extrabold tracking-tight text-zinc-900 dark:text-white sm:text-5xl mb-6">
                {{ $platformSettings['site_name'] ?? 'NEXACODE' }} is taking a short break.
            </h1>
            
            <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-10 leading-relaxed">
                We're currently performing some scheduled maintenance to improve your experience. 
                Don't worry, we'll be back online very soon!
            </p>

            <div class="inline-flex items-center gap-3 px-6 py-3 bg-zinc-100 dark:bg-zinc-900 rounded-full border border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-sm font-medium">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                </span>
                Refining the experience for you
            </div>
        </div>

        <div class="mt-16 text-zinc-400 dark:text-zinc-600 text-sm">
            &copy; {{ date('Y') }} {{ $platformSettings['site_name'] ?? config('app.name') }}. All rights reserved.
        </div>
    </div>

    @fluxScripts
</body>
</html>
