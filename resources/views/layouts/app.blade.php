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

        <title>@yield('title', $platformSettings['meta_title'] ?? config('app.name', 'NexaCode Marketplace'))</title>
        <meta name="description" content="@yield('meta_description', $platformSettings['meta_description'] ?? 'Premium source code and digital products.')">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', $platformSettings['meta_title'] ?? config('app.name', 'NexaCode Marketplace'))">
        <meta property="og:description" content="@yield('meta_description', $platformSettings['meta_description'] ?? 'Premium source code and digital products.')">
        <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@yield('title', $platformSettings['meta_title'] ?? config('app.name', 'NexaCode Marketplace'))">
        <meta property="twitter:description" content="@yield('meta_description', $platformSettings['meta_description'] ?? 'Premium source code and digital products.')">
        <meta property="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

        @stack('seo')

        @if(isset($platformSettings['site_favicon']))
            <link rel="icon" type="image/x-icon" href="{{ Storage::url($platformSettings['site_favicon']) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Flux UI -->
        @fluxAppearance
        @livewireStyles
        @fluxScripts
        @livewireScripts
        @stack('head')

        <script>
            // Global Flux Toast Wrapper (Polyfill for Flux.toast is not a function)
            window.Flux = window.Flux || {};
            window.Flux.toast = function (data) {
                const toastData = {
                    variant: data.variant || 'success',
                    heading: data.heading || (data.variant === 'error' || data.variant === 'danger' ? 'Error' : 'Success'),
                    text: data.text || data
                };
                window.dispatchEvent(new CustomEvent('flux-toast', { detail: toastData }));
            };
        </script>
    </head>
    <body class="min-h-screen flex flex-col bg-white dark:bg-zinc-950 antialiased font-sans text-zinc-900 dark:text-zinc-100 {{ request()->routeIs('home') || request()->is('/') ? 'is-homepage' : '' }}" 
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
        @php
            $activeAnnouncement = \App\Models\FlashSale::active();
        @endphp

        @if($activeAnnouncement)
            <div 
                x-data="{ show: !sessionStorage.getItem('announcement_dismissed_{{ $activeAnnouncement->id }}') }"
                x-show="show"
                class="bg-zinc-900 dark:bg-emerald-950 text-white py-2 px-4 relative flex items-center justify-center text-xs font-medium z-[150] border-b border-emerald-500/20"
            >
                <div class="flex items-center gap-3">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>
                        <span class="font-black text-emerald-400 uppercase tracking-widest mr-2">Sale Live:</span>
                        {{ $activeAnnouncement->banner_message ?: "Flash sale is currently active! Save up to {$activeAnnouncement->discount_percentage}% on premium scripts." }}
                        <a href="{{ route('products.index') }}" class="ml-2 font-bold underline hover:text-emerald-400 transition-colors">Shop now &rarr;</a>
                    </span>
                </div>
                <button 
                    x-on:click="show = false; sessionStorage.setItem('announcement_dismissed_{{ $activeAnnouncement->id }}', true)"
                    class="absolute right-4 p-1 hover:bg-white/10 rounded-lg transition-colors"
                >
                    <flux:icon name="x-mark" variant="micro" class="size-3" />
                </button>
            </div>
        @endif

        <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            @php
                $activeSale = \App\Models\FlashSale::active();
            @endphp

            @if($activeSale)
                <div 
                    x-data="{ show: !localStorage.getItem('nexaflash_dismissed_{{ $activeSale->id }}') }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="fixed inset-0 z-[200] flex items-center justify-center p-4"
                    style="display: none;"
                >
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" x-on:click="show = false; localStorage.setItem('nexaflash_dismissed_{{ $activeSale->id }}', true)"></div>

                    {{-- Modal Card --}}
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-8 shadow-2xl relative overflow-hidden w-full max-w-lg">
                        {{-- Background Glow --}}
                        <div class="absolute -top-12 -right-12 size-48 bg-cyan-500/10 dark:bg-cyan-500/20 blur-3xl rounded-full"></div>
                        <div class="absolute -bottom-12 -left-12 size-48 bg-emerald-500/10 dark:bg-emerald-500/20 blur-3xl rounded-full"></div>

                        <div class="relative flex flex-col items-center text-center gap-6">
                            <div class="size-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-emerald-500 flex items-center justify-center shadow-lg shadow-cyan-500/30">
                                <flux:icon name="bolt" variant="solid" class="size-8 text-white animate-pulse" />
                            </div>

                            <div class="space-y-2">
                                <flux:heading size="xl">NexaFlash™ is LIVE</flux:heading>
                                <flux:text class="text-zinc-500 dark:text-zinc-400 text-lg leading-relaxed">
                                    {{ $activeSale->banner_message ?: "Mega Sale Event! Dapatkan akses instan ke aset premium dengan diskon permanen hari ini." }}
                                </flux:text>
                            </div>

                            <div class="flex items-center gap-4 w-full">
                                <flux:button href="{{ route('products.index') }}" variant="primary" color="cyan" class="grow py-3 text-lg shadow-xl shadow-cyan-500/20">
                                    Shop the Sale Now
                                </flux:button>
                                <div class="text-xl font-mono text-cyan-600 dark:text-cyan-500 font-black px-5 py-2 bg-cyan-500/5 dark:bg-cyan-500/10 rounded-2xl border border-cyan-500/20">
                                    {{ $activeSale->discount_percentage }}% OFF
                                </div>
                            </div>

                            <flux:button 
                                variant="ghost"
                                size="sm"
                                x-on:click="show = false; localStorage.setItem('nexaflash_dismissed_{{ $activeSale->id }}', true)"
                            >
                                Not now, I'll browse later
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif

            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:brand href="/" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}" class="max-lg:hidden font-bold tracking-tighter">
                <x-slot name="logo" class="size-6 flex items-center justify-center">
                    <x-nexacode-brand-n variant="navbar" class="size-full" />
                </x-slot>
            </flux:brand>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item href="{{ route('products.index') }}" :current="request()->routeIs('products.index')">Browse</flux:navbar.item>
                <flux:navbar.item href="{{ route('products.index', ['sort' => 'popular']) }}" :current="request()->routeIs('products.index') && request('sort') === 'popular'">Trending</flux:navbar.item>
                <flux:navbar.item href="{{ route('products.index', ['sort' => 'newest']) }}" :current="request()->routeIs('products.index') && request('sort') === 'newest' || (request()->routeIs('products.index') && !request('sort'))">New Arrivals</flux:navbar.item>

                <flux:navbar.item href="{{ route('help.index') }}" :current="request()->routeIs('help.*')">Help Center</flux:navbar.item>

                <flux:separator vertical variant="subtle" class="my-2"/>

                <flux:dropdown class="max-lg:hidden">
                    <flux:navbar.item icon:trailing="chevron-down">Categories</flux:navbar.item>

                    <flux:navmenu>
                        @foreach($categories->take(5) as $cat)
                            <flux:navmenu.item href="{{ route('categories.show', $cat->slug) }}">{{ $cat->name }}</flux:navmenu.item>
                        @endforeach
                    </flux:navmenu>
                </flux:dropdown>

                @auth
                    <flux:separator vertical variant="subtle" class="my-2"/>
                    
                    <flux:navbar.item href="{{ route('purchases.index') }}" :current="request()->routeIs('purchases.*')">My Purchases</flux:navbar.item>
                    <flux:navbar.item href="{{ route('affiliate.dashboard') }}" :current="request()->routeIs('affiliate.dashboard')">Affiliates</flux:navbar.item>
                    <flux:navbar.item href="{{ route('inbox') }}" :current="request()->routeIs('inbox')">Messages</flux:navbar.item>
                    <flux:navbar.item href="{{ route('wishlist.index') }}" :current="request()->routeIs('wishlist.*')">Wishlist</flux:navbar.item>
                @endauth
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-4">

                @auth
                    @livewire('global.notification-hub')
                    @livewire('cart.cart-manager')
                @endauth
                
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

            @if (Route::has('login'))
                @auth
                    <flux:dropdown position="top" align="start">
                        <flux:profile 
                            :avatar="auth()->user()->avatar" 
                            :initials="auth()->user()->initials" 
                            name="{{ auth()->user()->name }}" 
                        />

                        <flux:menu>
                            <flux:menu.item icon="user-circle" href="{{ auth()->user()->isAdmin() ? route('admin.profile') : (auth()->user()->isAuthor() ? route('author.profile') : route('profile')) }}">Profile Settings</flux:menu.item>
                            <flux:menu.item icon="chat-bubble-left-right" href="{{ route('inbox') }}">Messages</flux:menu.item>
                            <flux:menu.item icon="shopping-bag" href="{{ route('purchases.index') }}">My Purchases</flux:menu.item>
                            <flux:menu.item icon="heart" href="{{ route('wishlist.index') }}">Wishlist</flux:menu.item>
                            <flux:menu.item icon="presentation-chart-line" href="{{ route('affiliate.dashboard') }}">Affiliate Hub</flux:menu.item>
                            
                            @if(auth()->user()->isAuthor())
                                <flux:menu.separator />
                                <flux:menu.item href="{{ route('author.dashboard') }}">Author Panel</flux:menu.item>
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                                <flux:menu.separator />
                                <flux:menu.item href="{{ route('admin.dashboard') }}">Admin Panel</flux:menu.item>
                            @endif

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" id="logout-form" x-ref="form">
                                @csrf
                                <flux:menu.item x-on:click="$refs.form.submit()" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                @else
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('login') }}" variant="ghost">Log in</flux:button>
                        <flux:button href="{{ route('author.register') }}" variant="primary">Become an Author</flux:button>
                    </div>
                @endauth
            @endif
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800">
            <flux:sidebar.header>
                <flux:sidebar.brand href="/" name="{{ $platformSettings['site_name'] ?? 'NEXACODE' }}">
                    @if(isset($platformSettings['site_logo']))
                        <x-slot name="logo" class="size-6 flex items-center justify-center">
                            <x-nexacode-brand-n variant="navbar" class="size-full" />
                        </x-slot>
                    @endif
                </flux:sidebar.brand>
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item href="{{ route('products.index') }}" :current="request()->routeIs('products.index')">Browse</flux:sidebar.item>
                <flux:sidebar.item href="{{ route('products.index', ['sort' => 'popular']) }}" :current="request()->routeIs('products.index') && request('sort') === 'popular'">Trending</flux:sidebar.item>
                <flux:sidebar.item href="{{ route('products.index', ['sort' => 'newest']) }}" :current="request()->routeIs('products.index') && request('sort') === 'newest' || (request()->routeIs('products.index') && !request('sort'))">New Arrivals</flux:sidebar.item>

                <flux:sidebar.group expandable heading="Categories" class="grid">
                    @foreach($categories->take(5) as $cat)
                        <flux:sidebar.item href="{{ route('categories.show', $cat->slug) }}">{{ $cat->name }}</flux:sidebar.item>
                    @endforeach
                </flux:sidebar.group>

                @auth
                    <flux:separator />
                    <flux:sidebar.item href="{{ route('purchases.index') }}" :current="request()->routeIs('purchases.*')">My Purchases</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('inbox') }}" :current="request()->routeIs('inbox')">Messages</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('wishlist.index') }}" :current="request()->routeIs('wishlist.*')">Wishlist</flux:sidebar.item>
                @endauth
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            @guest
                <flux:sidebar.nav>
                    <flux:sidebar.item href="{{ route('login') }}">Log in</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('author.register') }}">Become an Author</flux:sidebar.item>
                </flux:sidebar.nav>
            @endguest
        </flux:sidebar>

        <flux:main container class="flex-1 flex flex-col">
            <div class="flex-1">
                {{ $slot ?? '' }}
                @yield('content')
            </div>

            {{-- Footer --}}
            <flux:footer class="bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 pt-24 pb-12 mt-24">
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
                        @if(isset($platformSettings['site_address']))
                            <p class="text-zinc-400 dark:text-zinc-500 text-xs mb-8 max-w-sm whitespace-pre-line">
                                {{ $platformSettings['site_address'] }}
                            </p>
                        @endif
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
                            @if(isset($platformSettings['social_instagram']))
                                <flux:button href="{{ $platformSettings['social_instagram'] }}" variant="ghost" square aria-label="Instagram">
                                    <x-lucide-instagram class="w-5 h-5" />
                                </flux:button>
                            @endif
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
                        <flux:heading size="sm" class="mb-6 uppercase tracking-[0.2em]">Community</flux:heading>
                        <ul class="space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <li><flux:link href="{{ route('author.register') }}" variant="subtle">Become an Author</flux:link></li>
                            <li><flux:link href="{{ route('author.dashboard') }}" variant="subtle">Author Dashboard</flux:link></li>
                            <li><flux:link href="{{ route('affiliate.dashboard') }}" variant="subtle">Affiliates</flux:link></li>
                            <li><flux:link href="#" variant="subtle">Forum</flux:link></li>
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
                            <a href="#" class="hover:text-emerald-500 transition-colors">English (USD)</a>
                        </div>
                    </div>
                </div>
            </flux:footer>
        </flux:main>

        <flux:toast />

        <livewire:chat-widget />
        <livewire:help.help-widget />
        {{-- Elite Help Center: Proactive Engine --}}
        <div x-data="{
            rules: [
                { path: '/checkout', articleId: 2, delay: 10000 },
                { path: '/author/register', articleId: 1, delay: 30000 },
                { path: '/', articleId: 1, delay: 60000 },
            ],
            timer: null,
            init() {
                this.checkRules();
                document.addEventListener('livewire:navigated', () => this.checkRules());
            },
            checkRules() {
                if (this.timer) clearTimeout(this.timer);
                
                const currentPath = window.location.pathname;
                const rule = this.rules.find(r => currentPath === r.path || (r.path !== '/' && currentPath.startsWith(r.path)));
                
                if (rule) {
                    this.timer = setTimeout(() => {
                        if (window.location.pathname === currentPath) {
                            Livewire.dispatch('triggerProactiveHelp', { articleId: rule.articleId });
                        }
                    }, rule.delay);
                }
            }
        }"></div>

        <livewire:global.lightbox />

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

                // Real-time Context Tracking for Chat
                @auth
                    const reportContext = () => {
                        const title = document.title.split('-')[0].trim();
                        const url = window.location.href;
                        // Dispatch to both possible chat managers
                        Livewire.dispatch('update-context', { url: url, title: title });
                    };

                    // Initial report
                    setTimeout(reportContext, 2000);

                    // Report on navigation (for SPA-like feel if using Livewire navigate)
                    document.addEventListener('livewire:navigated', () => {
                        setTimeout(reportContext, 1000);
                    });
                @endauth
            });
        </script>
    </body>
</html>
