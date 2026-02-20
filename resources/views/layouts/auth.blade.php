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
    <title>@yield('title', 'Authentication') - {{ $platformSettings['site_name'] ?? 'NEXACODE' }}</title>
    @if(isset($platformSettings['site_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($platformSettings['site_favicon']) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles

    {{-- Global Livewire Loading Fix (Prevents skeletons from showing on initial page load) --}}
    <style>
        [wire\:loading], [wire\:loading\.delay], [wire\:loading\.inline-block], [wire\:loading\.inline], [wire\:loading\.block], [wire\:loading\.flex], [wire\:loading\.table], [wire\:loading\.grid], [wire\:loading\.inline-flex] {
            display: none;
        }
    </style>
</head>
<body class="h-full antialiased font-sans text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950">
    {{ $slot }}

    <flux:toast />
    @fluxScripts
    @livewireScripts
</body>
</html>
