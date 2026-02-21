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

        <title>{{ $platformSettings['site_name'] ?? config('app.name', 'NEXACODE') }} - Author Dashboard</title>
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
        @stack('head')

    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
        @php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; @endphp
        <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 sticky top-0 z-50">
            <flux:brand href="{{ route('home') }}" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="dark:hidden">
                <x-slot name="logo" class="size-6 flex items-center justify-center">
                    <x-nexacode-brand-n variant="navbar" class="size-full" />
                </x-slot>
            </flux:brand>
            <flux:brand href="{{ route('home') }}" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="hidden dark:flex">
                <x-slot name="logo" class="size-6 flex items-center justify-center">
                    <x-nexacode-brand-n variant="icon" class="size-full" />
                </x-slot>
            </flux:brand>

            <flux:navbar class="-mb-px ml-8">
                <flux:navbar.item icon="chart-bar" href="{{ route('author.dashboard') }}" :current="request()->routeIs('author.dashboard')">Dashboard</flux:navbar.item>
                <flux:navbar.item icon="sparkles" href="{{ route('author.insights') }}" :current="request()->routeIs('author.insights')">Insights</flux:navbar.item>
                <flux:navbar.item icon="cube" href="{{ route('author.products') }}" :current="request()->routeIs('author.products*')">My Products</flux:navbar.item>
                <flux:navbar.item icon="banknotes" href="{{ route('author.earnings') }}" :current="request()->routeIs('author.earnings')">Earnings</flux:navbar.item>
                
                <flux:separator vertical variant="subtle" class="mx-2 my-2" />

                <flux:dropdown>
                    <flux:navbar.item icon:trailing="chevron-down" :current="request()->routeIs('author.chat', 'author.reviews', 'author.support', 'author.refunds')">Engagement</flux:navbar.item>
                    <flux:menu>
                        <flux:menu.item icon="chat-bubble-left-right" href="{{ route('author.chat') }}" :current="request()->routeIs('author.chat')">Messages</flux:menu.item>
                        <flux:menu.item icon="star" href="{{ route('author.reviews') }}" :current="request()->routeIs('author.reviews')">Reviews</flux:menu.item>
                        <flux:menu.item icon="ticket" href="{{ route('author.coupons') }}" :current="request()->routeIs('author.coupons')">Coupons</flux:menu.item>
                        <flux:menu.item icon="puzzle-piece" href="{{ route('author.bundles') }}" :current="request()->routeIs('author.bundles')">Product Bundles</flux:menu.item>
                        <flux:menu.item icon="lifebuoy" href="{{ route('author.support') }}" :current="request()->routeIs('author.support')">Support Tickets</flux:menu.item>
                        <flux:menu.item icon="arrow-uturn-left" href="{{ route('author.refunds') }}" :current="request()->routeIs('author.refunds')">Refund Requests</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-4">
                <flux:navbar.item icon="magnifying-glass" href="#" label="Search" />
                
                <flux:dropdown>
                    <flux:navbar.item icon:trailing="chevron-down">Shortcuts</flux:navbar.item>
                    <flux:menu>
                        <flux:menu.item icon="home" href="{{ route('home') }}">Marketplace</flux:menu.item>
                        <flux:menu.item icon="shopping-bag" href="{{ route('purchases.index') }}">My Purchases</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <livewire:global.notification-hub />

                <flux:dropdown x-data align="end">
                    <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                        <div class="flex items-center justify-center w-5 h-5 relative">
                            <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="absolute inset-0 text-zinc-500 dark:text-white" x-cloak />
                            <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="absolute inset-0 text-zinc-500 dark:text-white" x-cloak />
                            <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" class="absolute inset-0" x-cloak />
                            <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" class="absolute inset-0" x-cloak />
                        </div>
                    </flux:button>
                    
                    <flux:menu>
                        <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                        <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                        <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>

            <flux:dropdown align="end">
                <flux:profile :avatar="Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null" :initials="Auth::user()->initials" />

                <flux:menu>
                    <flux:menu.item icon="user-circle" href="{{ route('author.profile') }}">Profile Settings</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" x-ref="logoutForm">
                        @csrf
                        <flux:menu.item x-on:click="$refs.logoutForm.submit()" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main container>
            {{ $slot ?? '' }}
            @yield('content')

            <flux:footer container class="mt-24 pt-16 pb-8 border-t">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-16">
                    <div>
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Marketplace</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><flux:link href="{{ route('categories.show', 'php-scripts') }}" variant="subtle">PHP Scripts</flux:link></li>
                            <li><flux:link href="{{ route('categories.show', 'wordpress') }}" variant="subtle">WordPress Themes</flux:link></li>
                            <li><flux:link href="{{ route('products.index', ['sort' => 'newest']) }}" variant="subtle">Latest Items</flux:link></li>
                            <li><flux:link href="{{ route('products.index', ['sort' => 'popular']) }}" variant="subtle">Best Sellers</flux:link></li>
                        </ul>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Community</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><flux:link href="{{ route('author.register') }}" variant="subtle">Become an Author</flux:link></li>
                            <li><flux:link href="{{ route('author.dashboard') }}" variant="subtle">Author Dashboard</flux:link></li>
                            <li><flux:link href="#" variant="subtle">Affiliates</flux:link></li>
                            <li><flux:link href="#" variant="subtle">Forum</flux:link></li>
                        </ul>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Support</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><flux:link href="{{ route('home') }}" variant="subtle">Help Center</flux:link></li>
                            <li><flux:link href="{{ route('faq') }}" variant="subtle">FAQ</flux:link></li>
                            <li><flux:link href="{{ route('terms') }}" variant="subtle">Terms of Service</flux:link></li>
                            <li><flux:link href="{{ route('privacy') }}" variant="subtle">Privacy Policy</flux:link></li>
                        </ul>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Author</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><a href="{{ route('author.dashboard') }}" class="hover:text-emerald-500 transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('author.products') }}" class="hover:text-emerald-500 transition-colors">My Products</a></li>
                            <li><a href="{{ route('author.earnings') }}" class="hover:text-emerald-500 transition-colors">Earnings</a></li>
                            <li><a href="{{ route('author.profile') }}" class="hover:text-emerald-500 transition-colors">Profile</a></li>
                        </ul>
                    </div>
                </div>

                <flux:separator variant="subtle" />

                <div class="pt-12 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="text-sm text-zinc-500 dark:text-zinc-500">
                        &copy; {{ date('Y') }} {{ $platformSettings['site_name'] ?? 'NexaCode Marketplace' }}. All rights reserved.
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="font-bold text-zinc-400 opacity-50">NEXACODE</span>
                        <flux:separator vertical />
                        <div class="flex gap-6 text-sm text-zinc-500 dark:text-zinc-500">
                            <a href="#" class="hover:text-emerald-500 transition-colors">Indonesia (IDR)</a>
                        </div>
                    </div>
                </div>
            </flux:footer>
        </flux:main>


        <flux:toast />
        @livewireScripts

        <script>
            document.addEventListener('livewire:init', () => {
                // Global Toast Listener
                Livewire.on('toast', (data) => {
                    window.Flux.toast(data);
                });

                // Handle Session Flash for Flux Toast
                @if(session('success'))
                    window.Flux.toast({ variant: 'success', heading: 'Success', text: '{{ session('success') }}' });
                @endif

                @if(session('error'))
                    window.Flux.toast({ variant: 'danger', heading: 'Error', text: '{{ session('error') }}' });
                @endif

                @if(session('status'))
                    window.Flux.toast({ heading: 'Status Update', text: '{{ session('status') }}' });
                @endif
            });
        </script>
    </body>
</html>
