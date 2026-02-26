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
                        <flux:menu.item icon="question-mark-circle" href="{{ route('help.index') }}">Help Center</flux:menu.item>
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
                <flux:profile 
                    :avatar="Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null" 
                    :initials="Auth::user()->initials" 
                    class="rounded-xl ring-2 {{ Auth::user()->isElite() ? 'ring-amber-400' : (Auth::user()->isPro() ? 'ring-indigo-400' : 'ring-transparent') }}"
                />

                <flux:menu>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl mb-2 mx-2 border border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="size-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                                    <flux:icon.sparkles variant="mini" class="size-4" />
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500 leading-none mb-1">Nexus Level</div>
                                    <div class="text-sm font-black text-zinc-900 dark:text-white leading-none">Level {{ Auth::user()->level }}</div>
                                </div>
                            </div>
                            <div class="text-[10px] font-black uppercase text-zinc-400">{{ number_format(Auth::user()->xp) }} XP</div>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                            @php 
                                $xpInLevel = Auth::user()->xp % 1000; 
                                $progress = ($xpInLevel / 1000) * 100;
                            @endphp
                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-20">
                    <div class="lg:col-span-2">
                        <flux:brand href="/" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="mb-8 font-bold text-xl">
                            <x-slot name="logo" class="size-6 flex items-center justify-center">
                                <x-nexacode-brand-n variant="footer" class="size-full" />
                            </x-slot>
                        </flux:brand>
                        <p class="text-zinc-500 dark:text-zinc-400 text-base leading-relaxed mb-4 max-w-sm">
                            {{ $platformSettings['meta_description'] ?? 'The world\'s premium marketplace for the best scripts, themes, and templates.' }}
                        </p>
                        <div class="flex gap-3">
                            <flux:button href="{{ $platformSettings['social_facebook'] ?? '#' }}" variant="ghost" square aria-label="Facebook">
                                <x-lucide-facebook class="w-5 h-5" />
                            </flux:button>
                            <flux:button href="{{ $platformSettings['social_twitter'] ?? '#' }}" variant="ghost" square aria-label="Twitter">
                                <x-lucide-twitter class="w-5 h-5" />
                            </flux:button>
                            <flux:button href="{{ $platformSettings['social_github'] ?? '#' }}" variant="ghost" square aria-label="GitHub">
                                <x-lucide-github class="w-5 h-5" />
                            </flux:button>
                        </div>
                    </div>
                    
                    <div>
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Marketplace</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><a href="{{ route('categories.show', 'php-scripts') }}" class="hover:text-emerald-500 transition-colors">PHP Scripts</a></li>
                            <li><a href="{{ route('categories.show', 'wordpress') }}" class="hover:text-emerald-500 transition-colors">WordPress Themes</a></li>
                            <li><a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hover:text-emerald-500 transition-colors">Latest Items</a></li>
                            <li><a href="{{ route('products.index', ['sort' => 'popular']) }}" class="hover:text-emerald-500 transition-colors">Best Sellers</a></li>
                        </ul>
                    </div>


                    <div>
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Support</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><a href="{{ route('help.index') }}" class="hover:text-emerald-500 transition-colors">Help Center</a></li>
                            <li><a href="{{ route('faq') }}" class="hover:text-emerald-500 transition-colors">FAQ</a></li>
                            <li><a href="{{ route('terms') }}" class="hover:text-emerald-500 transition-colors">Terms of Service</a></li>
                            <li><a href="{{ route('privacy') }}" class="hover:text-emerald-500 transition-colors">Privacy Policy</a></li>
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
