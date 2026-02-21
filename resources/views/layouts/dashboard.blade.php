@php
    if (! isset($errors)) {
        $errors = new \Illuminate\Support\ViewErrorBag;
    }
    view()->share('errors', $errors);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $platformSettings['site_name'] ?? config('app.name', 'NEXACODE') }} - Dashboard</title>
        @if(isset($platformSettings['site_favicon']))
            <link rel="icon" type="image/x-icon" href="{{ Storage::url($platformSettings['site_favicon']) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @fluxAppearance
        @fluxScripts
        @livewireStyles

    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950 antialiased font-sans text-zinc-900 dark:text-zinc-100">
    @php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; @endphp
    <div class="flex h-screen overflow-hidden">
        <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:brand href="{{ route('home') }}" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="px-2 dark:hidden">
            <x-slot name="logo" class="size-6 flex items-center justify-center">
                <x-nexacode-brand-n variant="navbar" class="size-full" />
            </x-slot>
            </flux:brand>
            <flux:brand href="{{ route('home') }}" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="px-2 hidden dark:flex">
            <x-slot name="logo" class="size-6 flex items-center justify-center">
                <x-nexacode-brand-n variant="navbar" class="size-full" />
            </x-slot>
            </flux:brand>

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">Overview</flux:navlist.item>
                <flux:navlist.item icon="shopping-bag" href="{{ route('dashboard.orders') }}" :current="request()->routeIs('dashboard.orders')">Orders</flux:navlist.item>
                <flux:navlist.item icon="heart" href="{{ route('dashboard.wishlist') }}" :current="request()->routeIs('dashboard.wishlist')">Wishlist</flux:navlist.item>
                <flux:navlist.item icon="chat-bubble-left-right" href="{{ route('support.index') }}" :current="request()->routeIs('support.*')">Support</flux:navlist.item>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" href="{{ route('home') }}">Marketplace</flux:navlist.item>
                @if(Auth::user()->hasRole('author'))
                    <flux:navlist.item icon="chart-bar" href="{{ route('author.dashboard') }}">Author Dashboard</flux:navlist.item>
                @endif
            </flux:navlist>

            <div class="px-2 mb-4">
                <flux:dropdown x-data="{ appearance: $flux.appearance }" x-on:flux:appearance-changed.window="appearance = $event.detail" align="start">
                    <flux:button variant="subtle" square class="group w-full justify-start gap-3" aria-label="Preferred color scheme">
                        <div class="relative w-5 h-5 flex items-center justify-center">
                            <flux:icon.sun x-show="appearance === 'light'" variant="mini" class="absolute inset-0 text-zinc-500 dark:text-white" x-cloak />
                            <flux:icon.moon x-show="appearance === 'dark'" variant="mini" class="absolute inset-0 text-zinc-500 dark:text-white" x-cloak />
                            <flux:icon.moon x-show="appearance === 'system' && $flux.dark" variant="mini" class="absolute inset-0" x-cloak />
                            <flux:icon.sun x-show="appearance === 'system' && ! $flux.dark" variant="mini" class="absolute inset-0" x-cloak />
                        </div>
                        <span class="text-sm font-medium">Appearance</span>
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                        <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                        <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:profile :avatar="Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null" name="{{ Auth::user()->name }}" />

                <flux:menu>
                    <flux:menu.item icon="user-circle" href="{{ route('profile') }}">Profile Settings</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item icon="arrow-right-start-on-rectangle" href="{{ route('logout') }}" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="start">
                <flux:profile :avatar="Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null" />

                <flux:menu>
                    <flux:menu.item icon="user-circle" href="{{ route('profile') }}">Profile Settings</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item icon="arrow-right-start-on-rectangle" href="{{ route('logout') }}" 
                        onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                        Logout
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </flux:header>

        <flux:main>
            @yield('content')
        </flux:main>

        <flux:toast />
        @livewireScripts
    </body>
</html>
