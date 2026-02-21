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
    <title>Admin Panel - {{ $platformSettings['site_name'] ?? 'NEXACODE' }}</title>

    @fluxAppearance
    @livewireStyles
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @stack('head')

    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Global Livewire Loading Fix (Prevents skeletons from showing on initial page load) --}}
    <style>
        [wire\:loading], [wire\:loading\.delay], [wire\:loading\.inline-block], [wire\:loading\.inline], [wire\:loading\.block], [wire\:loading\.flex], [wire\:loading\.table], [wire\:loading\.grid], [wire\:loading\.inline-flex] {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased"
    x-data="{ 
        originalTitle: document.title, 
        notificationInterval: null,
        resetTitle() {
            if (this.notificationInterval) {
                clearInterval(this.notificationInterval);
                this.notificationInterval = null;
            }
            document.title = this.originalTitle;
        }
    }"
    x-on:new-message.window="
        if (document.visibilityState !== 'visible') {
            if (!notificationInterval) {
                notificationInterval = setInterval(() => {
                    document.title = document.title === originalTitle ? '(1) New Message! - NEXACODE' : originalTitle;
                }, 1000);
            }
        }
    "
    x-on:visibilitychange.window="if (document.visibilityState === 'visible') resetTitle()"
    x-on:focus.window="resetTitle()"
>
    @php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; @endphp
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">

        <flux:brand href="{{ route('admin.dashboard') }}" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }} Admin" class="max-lg:hidden dark:hidden">
            <x-slot name="logo" class="size-6 flex items-center justify-center">
                <x-nexacode-brand-n variant="navbar" class="size-full" />
            </x-slot>
        </flux:brand>
        <flux:brand href="{{ route('admin.dashboard') }}" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }} Admin" class="max-lg:!hidden hidden dark:flex">
            <x-slot name="logo" class="size-6 flex items-center justify-center">
                <x-nexacode-brand-n variant="icon" class="size-full" />
            </x-slot>
        </flux:brand>

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="chart-bar" href="{{ route('admin.dashboard') }}" :current="request()->routeIs('admin.dashboard')">Overview</flux:navbar.item>
            <flux:navbar.item icon="presentation-chart-line" href="{{ route('admin.analytics') }}" :current="request()->routeIs('admin.analytics')">Analytics</flux:navbar.item>
            
            <flux:separator vertical variant="subtle" class="my-2"/>

            <flux:dropdown>
                <flux:navbar.item icon:trailing="chevron-down" :current="request()->routeIs('admin.products', 'admin.orders', 'admin.moderation', 'admin.payment-methods.*')">Commerce</flux:navbar.item>
                <flux:menu>
                    <flux:menu.item icon="shopping-bag" href="{{ route('admin.products') }}" :current="request()->routeIs('admin.products')">Products</flux:menu.item>
                    <flux:menu.item icon="folder" href="{{ route('admin.categories') }}" :current="request()->routeIs('admin.categories')">Product Categories</flux:menu.item>
                    <flux:menu.item icon="shopping-cart" href="{{ route('admin.orders') }}" :current="request()->routeIs('admin.orders')">Orders</flux:menu.item>
                    <flux:menu.item icon="banknotes" href="{{ route('admin.payouts') }}" :current="request()->routeIs('admin.payouts')">Payouts</flux:menu.item>
                    <flux:menu.item icon="chat-bubble-left-right" href="{{ route('admin.chat') }}" :current="request()->routeIs('admin.chat')">Live Chat</flux:menu.item>
                    <flux:menu.item icon="shield-check" href="{{ route('admin.moderation') }}" :current="request()->routeIs('admin.moderation')">Moderation</flux:menu.item>
                    <flux:menu.item icon="bolt" href="{{ route('admin.flash-sales') }}" :current="request()->routeIs('admin.flash-sales')">NexaFlash™</flux:menu.item>
                    <flux:menu.item icon="credit-card" href="{{ route('admin.payment-methods.index') }}" :current="request()->routeIs('admin.payment-methods.*')">Payments</flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:dropdown>
                <flux:navbar.item icon:trailing="chevron-down" :current="request()->routeIs('admin.users', 'admin.author-requests', 'admin.subscriptions', 'admin.settings', 'admin.help.*')">Platform</flux:navbar.item>
                <flux:menu>
                    <flux:menu.item icon="users" href="{{ route('admin.users') }}" :current="request()->routeIs('admin.users')">Users</flux:menu.item>
                    <flux:menu.item icon="user-plus" href="{{ route('admin.author-requests') }}" :current="request()->routeIs('admin.author-requests')">Author Requests</flux:menu.item>
                    <flux:menu.item icon="ticket" href="{{ route('admin.subscriptions') }}" :current="request()->routeIs('admin.subscriptions')">Subscriptions</flux:menu.item>
                    <flux:menu.item icon="arrow-path" href="{{ route('admin.refunds') }}" :current="request()->routeIs('admin.refunds')">Refunds</flux:menu.item>
                    <flux:menu.item icon="banknotes" href="{{ route('admin.affiliate.payouts') }}" :current="request()->routeIs('admin.affiliate.payouts')">Affiliate Payouts</flux:menu.item>
                    <flux:menu.item icon="ticket" href="{{ route('admin.affiliate.coupons') }}" :current="request()->routeIs('admin.affiliate.coupons')">Affiliate Coupons</flux:menu.item>
                    
                    <flux:menu.separator />

                    <flux:menu.item icon="folder" href="{{ route('admin.help.categories') }}" :current="request()->routeIs('admin.help.categories')">Help Categories</flux:menu.item>
                    <flux:menu.item icon="document-text" href="{{ route('admin.help.articles') }}" :current="request()->routeIs('admin.help.articles')">Help Articles</flux:menu.item>
                    
                    <flux:menu.separator />

                    <flux:menu.item icon="cog-6-tooth" href="{{ route('admin.settings') }}" :current="request()->routeIs('admin.settings')">Settings</flux:menu.item>
                    <flux:menu.item icon="envelope" href="{{ route('admin.mail-manager') }}" :current="request()->routeIs('admin.mail-manager')">Mail Manager</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:navbar>

        <flux:spacer />

        <flux:navbar class="me-4">
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

        <flux:dropdown position="top" align="start">
            <flux:profile :avatar="Auth::user()->avatar" :initials="Auth::user()->initials" />

            <flux:menu>
                <flux:menu.item icon="user-circle" href="{{ route('admin.profile') }}">Profile Settings</flux:menu.item>
                <flux:menu.item href="{{ route('purchases.index') }}">My Purchases</flux:menu.item>
                @if(Auth::user()->isAuthor())
                    <flux:menu.item href="{{ route('author.dashboard') }}">Author Dashboard</flux:menu.item>
                @endif

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>


    <flux:main container>
        @if (session('status'))
            <script>
                Flux.toast({
                    variant: 'success',
                    heading: 'Success',
                    text: '{{ session('status') }}'
                });
            </script>
        @endif

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
                    <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Admin</flux:heading>
                    <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-500 transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('admin.moderation') }}" class="hover:text-emerald-500 transition-colors">Moderation</a></li>
                        <li><a href="{{ route('admin.users') }}" class="hover:text-emerald-500 transition-colors">Users</a></li>
                        <li><a href="{{ route('admin.subscriptions') }}" class="hover:text-emerald-500 transition-colors">Subscriptions</a></li>
                        <li><a href="{{ route('admin.settings') }}" class="hover:text-emerald-500 transition-colors">Settings</a></li>
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
    @fluxScripts
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
