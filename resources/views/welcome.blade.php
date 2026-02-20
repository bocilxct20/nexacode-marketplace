@extends('layouts.app')

@section('is_home', true)
@section('title', 'The world\'s leading marketplace for source code')

@section('content')
    {{-- Hero Section --}}
    {{-- Hero Section --}}
    <section class="relative py-24 overflow-hidden bg-zinc-50 dark:bg-zinc-900">
        <flux:container>
            <div class="text-center max-w-3xl mx-auto relative z-10">
                <flux:heading level="1" class="text-5xl mb-6 font-extrabold tracking-tight">
                    The world's leading marketplace for <span class="text-emerald-600">source code</span>
                </flux:heading>
                <flux:subheading size="lg" class="mb-10 text-zinc-500">
                    Browse thousands of premium scripts, themes, and templates from the world's best authors. Professional tools for professional developers.
                </flux:subheading>

                <form action="{{ route('products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-2xl mx-auto mb-6">
                    <div class="flex-1">
                        <flux:input name="search" placeholder="Search for anything (e.g. Laravel, React, E-commerce...)">
                            <x-slot name="icon">
                                <flux:icon name="magnifying-glass" variant="micro" class="size-4" />
                            </x-slot>
                        </flux:input>
                    </div>
                    <flux:button type="submit" variant="primary">Search Items</flux:button>
                </form>

                <div class="flex flex-wrap justify-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <span>Trending searches:</span>
                    @foreach($trendingTags as $tag)
                        <flux:badge color="zinc" variant="outline" href="{{ route('products.index', ['search' => $tag->name]) }}">{{ $tag->name }}</flux:badge>
                    @endforeach
                </div>
            </div>

            {{-- Background Decorative Blobs --}}
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Trust Ribbon: Platform Stats --}}
    <section class="py-12 bg-zinc-50 dark:bg-zinc-900 border-y border-zinc-200 dark:border-zinc-800 relative overflow-hidden">
        {{-- Discrete Background Pattern --}}
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.01]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 40px 40px;"></div>
        
        <flux:container>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 relative z-10">
                <div class="flex flex-col items-center text-center group">
                    <div class="text-4xl font-black text-zinc-900 dark:text-white mb-1 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['products_count'], 0, ',', '.') }}</div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.2em] text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-400 transition-colors">Premium Items</div>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div class="text-4xl font-black text-zinc-900 dark:text-white mb-1 group-hover:text-cyan-600 transition-colors">{{ number_format($stats['authors_count'], 0, ',', '.') }}</div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.2em] text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-400 transition-colors">Expert Authors</div>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div class="text-4xl font-black text-zinc-900 dark:text-white mb-1 group-hover:text-amber-600 transition-colors">{{ number_format($stats['buyers_count'], 0, ',', '.') }}</div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.2em] text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-400 transition-colors">Happy Buyers</div>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div class="text-4xl font-black text-zinc-900 dark:text-white mb-1 group-hover:text-indigo-600 transition-colors">99.9%</div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.2em] text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-400 transition-colors">Uptime SLA</div>
                </div>
            </div>
        </flux:container>
    </section>

    <flux:separator />

    @if($featuredProducts->count() > 0)
        {{-- Elite Spotlight Section --}}
        <section class="py-12 bg-white dark:bg-zinc-950 overflow-hidden">
            <flux:container>
                <div class="flex items-center gap-2 mb-8 animate-in fade-in slide-in-from-left duration-700">
                    <flux:badge color="amber" variant="solid" class="flex items-center gap-2">
                        <flux:icon name="sparkles" variant="mini" class="size-3" />
                        <span class="text-[10px] font-black uppercase tracking-widest font-mono">Elite Spotlight</span>
                    </flux:badge>
                    <div class="h-px flex-1 bg-gradient-to-r from-amber-500/20 to-transparent"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($featuredProducts as $product)
                        <flux:card href="{{ route('products.show', $product->slug) }}" class="group relative p-0 overflow-hidden border-zinc-200 dark:border-zinc-800 hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-all duration-500 shadow-sm hover:shadow-2xl hover:shadow-amber-500/10 rounded-2xl flex flex-col h-full">
                            <div class="aspect-video w-full overflow-hidden relative bg-zinc-100 dark:bg-zinc-800">
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6">
                                    <flux:button variant="primary" size="sm" class="w-fit bg-amber-500 hover:bg-amber-600 border-none shadow-lg shadow-amber-500/20 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                        View Item Spotlight
                                    </flux:button>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <flux:badge color="amber" size="sm" class="shadow-lg backdrop-blur-md bg-amber-500/80 text-white border-none font-bold">Featured</flux:badge>
                                </div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-start justify-between mb-4 gap-4">
                                    <flux:heading size="lg" class="font-bold group-hover:text-amber-500 transition-colors line-clamp-2 leading-tight">{{ $product->name }}</flux:heading>
                                    <div class="flex flex-col items-end shrink-0">
                                        @if($product->is_on_sale)
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <span class="text-[8px] font-black text-white bg-cyan-500 px-1 rounded uppercase tracking-widest">FLASH</span>
                                                <span class="text-[10px] text-zinc-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex items-baseline gap-1 text-emerald-600 dark:text-emerald-400 tabular-nums">
                                                <span class="text-xs font-bold">Rp</span>
                                                <span class="text-xl font-black">{{ number_format($product->discounted_price, 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-baseline gap-1 text-emerald-600 dark:text-emerald-400 tabular-nums">
                                                <span class="text-xs font-bold text-zinc-500">Rp</span>
                                                <span class="text-xl font-black">{{ number_format($product->price, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-auto pt-6 border-t border-zinc-100 dark:border-zinc-800">
                                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                                        <x-profile-preview :user="$product->author">
                                            <div class="flex items-center gap-2 cursor-pointer group/author">
                                                <flux:avatar size="xs" :src="$product->author->avatar ? asset('storage/' . $product->author->avatar) : null" :initials="$product->author->initials" class="ring-2 ring-transparent group-hover/author:ring-amber-500/30 transition-all" />
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-medium group-hover/author:text-amber-500 transition-all text-zinc-900 dark:text-white">{{ $product->author->name }}</span>
                                                    @if($product->author->isElite())
                                                        <flux:icon.check-badge variant="mini" class="text-amber-500 w-3.5 h-3.5 animate-pulse" />
                                                    @elseif($product->author->isPro())
                                                        <flux:icon.check-badge variant="mini" class="text-indigo-500 w-3.5 h-3.5" />
                                                    @else
                                                        <flux:icon.check-badge variant="mini" class="text-zinc-400 w-3.5 h-3.5" />
                                                    @endif
                                                </div>
                                            </div>
                                        </x-profile-preview>
                                    </div>
                                </div>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </flux:container>
        </section>
        <flux:separator />
    @endif

    {{-- Categories Section --}}
    <section class="py-20 bg-white dark:bg-zinc-950">
        <flux:container>
            <div class="flex items-center justify-between mb-12">
                <flux:heading size="xl" class="font-bold">Featured Categories</flux:heading>
                <flux:button variant="ghost" href="{{ route('products.index') }}" icon-trailing="chevron-right">
                    View All Categories
                </flux:button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories->take(6) as $cat)
                    <flux:card href="{{ route('categories.show', $cat->slug) }}" class="p-8 text-center hover:border-emerald-500 transition-all group cursor-pointer border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md">
                        <div class="w-12 h-12 mx-auto mb-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 transition-colors">
                            @php 
                                $icons = [
                                    'php-scripts' => 'code-bracket',
                                    'wordpress' => 'globe-alt',
                                    'mobile-apps' => 'device-phone-mobile',
                                    'html-css' => 'document-text',
                                    'ui-kits' => 'rectangle-group',
                                    'plugins' => 'puzzle-piece'
                                ];
                                $iconName = $icons[$cat->slug] ?? 'cube';
                            @endphp
                            <flux:icon :name="$iconName" variant="mini" class="size-6 text-zinc-400 group-hover:text-emerald-500 transition-colors" />
                        </div>
                        <flux:heading size="lg" class="group-hover:text-emerald-600 transition-colors">{{ $cat->name }}</flux:heading>
                        <flux:text size="sm" class="mt-1">Explore items</flux:text>
                    </flux:card>
                @endforeach
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Value Propositions: Why NexaCode? --}}
    <section class="py-24 bg-white dark:bg-zinc-950">
        <flux:container>
            <div class="text-center max-w-2xl mx-auto mb-16">
                <flux:heading size="xl" class="font-black mb-4 uppercase tracking-tighter">Why NexaCode Marketplace?</flux:heading>
                <flux:text class="text-lg">Platform terbaik bagi para developer profesional untuk menemukan dan menjual aset digital berkualitas tinggi.</flux:text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @foreach($features as $feature)
                    <div class="flex flex-col items-center text-center space-y-4 p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-2">
                        <div class="size-14 rounded-2xl bg-{{ $feature['color'] }}-500/10 flex items-center justify-center">
                            <flux:icon name="{{ $feature['icon'] }}" variant="solid" class="size-8 text-{{ $feature['color'] }}-500" />
                        </div>
                        <flux:heading size="lg">{{ $feature['title'] }}</flux:heading>
                        <flux:text class="text-zinc-500 leading-relaxed">{{ $feature['description'] }}</flux:text>
                    </div>
                @endforeach
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Newsletter Curation Loop --}}
    <section class="py-20 bg-white dark:bg-zinc-950">
        <flux:container>
            @livewire('home.newsletter-subscription')
        </flux:container>
    </section>

    <flux:separator />

    {{-- Trending Products --}}
    <section class="py-20 bg-zinc-50 dark:bg-zinc-900">
        <flux:container>
            @livewire('home.product-collections')

            <div class="mt-16 text-center">
                <flux:button variant="outline" class="px-10" href="{{ route('products.index') }}" icon-trailing="arrow-right">
                    Explore All Marketplace Items
                </flux:button>
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Social Proof: Testimonials --}}
    <section class="py-24 bg-white dark:bg-zinc-950 relative overflow-hidden border-y border-zinc-200 dark:border-zinc-800">
        {{-- Background Glow --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 size-[600px] bg-emerald-500/5 dark:bg-emerald-500/[0.02] blur-[120px] rounded-full"></div>

        <flux:container>
            <div class="text-center max-w-2xl mx-auto mb-20 relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 mb-6">
                    <div class="flex -space-x-2">
                        @foreach(range(1, 4) as $i)
                            <div class="size-6 rounded-full border-2 border-white dark:border-zinc-950 bg-zinc-200 dark:bg-zinc-800 overflow-hidden">
                                <img src="https://ui-avatars.com/api/?background=random&name=User+{{ $i }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                    <span class="text-[10px] uppercase font-black tracking-widest text-zinc-500">Trusted by 45k+ Devs</span>
                </div>
                <flux:heading size="2xl" class="!text-4xl font-black mb-4">What our community says</flux:heading>
                <flux:text class="text-zinc-500 text-lg">Ribuan pembeli telah menggunakan aset kami untuk membangun startup mereka lebih cepat.</flux:text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                @foreach($testimonials as $testimonial)
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900/50 backdrop-blur-xl border border-zinc-200 dark:border-white/5 rounded-[2.5rem] relative group hover:border-emerald-500/30 transition-all duration-500 shadow-sm">
                        <flux:icon name="chat-bubble-bottom-center-text" variant="solid" class="size-8 text-emerald-500 opacity-20 absolute top-8 right-8" />
                        <div class="flex items-center gap-4 mb-6">
                            <flux:avatar src="https://ui-avatars.com/api/?background={{ $testimonial['color'] }}&color=fff&name={{ $testimonial['avatar'] }}" class="size-12 rounded-2xl shadow-lg" />
                            <div>
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $testimonial['name'] }}</div>
                                <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">{{ $testimonial['role'] }}</div>
                            </div>
                        </div>
                        <flux:text class="text-zinc-600 dark:text-zinc-400 leading-relaxed italic content-secondary">{{ $testimonial['quote'] }}</flux:text>
                    </div>
                @endforeach
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Author Branding / Recruitment Section --}}
    <section class="py-24 bg-white dark:bg-zinc-950 relative overflow-hidden">
        {{-- Decorative element --}}
        <div class="absolute top-0 right-0 w-1/3 h-full bg-emerald-500/5 -skew-x-12 transform translate-x-1/2"></div>
        
        <flux:container>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8 animate-in fade-in slide-in-from-left duration-700">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20">
                        <flux:icon name="rocket-launch" variant="micro" class="size-4 text-indigo-500" />
                        <span class="text-[10px] uppercase font-black tracking-widest text-indigo-500">Sell on NexaCode</span>
                    </div>

                    <flux:heading size="2xl" class="!text-5xl font-black tracking-tight leading-tight">
                        Ready to scale your <span class="text-indigo-600">code</span> to the world?
                    </flux:heading>

                    <flux:text class="text-lg text-zinc-500 dark:text-zinc-400">
                        Join our elite community of authors. NexaCode memberikan platform terbaik untuk menjual script, theme, dan template kamu ke ribuan pembeli global.
                    </flux:text>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex gap-4">
                            <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                <flux:icon name="banknotes" variant="micro" class="size-5 text-indigo-500" />
                            </div>
                            <div>
                                <div class="font-bold text-zinc-900 dark:text-white">High Commission</div>
                                <div class="text-sm text-zinc-500">Dapatkan hingga 90% dari setiap penjualan.</div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                <flux:icon name="chart-bar" variant="micro" class="size-5 text-indigo-500" />
                            </div>
                            <div>
                                <div class="font-bold text-zinc-900 dark:text-white">Global Reach</div>
                                <div class="text-sm text-zinc-500">Akses langsung ke ribuan pembeli dari seluruh dunia.</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-4">
                        <flux:button href="{{ route('author.register') }}" variant="primary" color="indigo" class="px-8 shadow-xl shadow-indigo-500/20">
                            Become an Author Now
                        </flux:button>
                        <flux:button href="{{ route('help.category', 'selling') }}" variant="ghost" icon-trailing="chevron-right">
                            Learn more about selling
                        </flux:button>
                    </div>
                </div>

                <div class="relative animate-in fade-in zoom-in duration-1000">
                    <div class="bg-zinc-900 rounded-[3rem] p-4 shadow-2xl border border-zinc-800 relative z-10">
                        <div class="aspect-square rounded-[2.5rem] overflow-hidden bg-zinc-800">
                            {{-- We can use a stylized placeholder or generated image here --}}
                            <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?q=80&w=800&auto=format&fit=crop" alt="Author Success" class="w-full h-full object-cover mix-blend-overlay opacity-60">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center p-8">
                                    <div class="size-20 bg-white/10 backdrop-blur-xl rounded-full flex items-center justify-center mx-auto mb-6 border border-white/20">
                                        <flux:icon name="presentation-chart-line" variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="text-3xl font-black text-white mb-2">Powering {{ number_format($stats['powered_count'], 0, ',', '.') }}+</div>
                                    <div class="text-indigo-400 font-bold uppercase tracking-widest text-xs">Digital Businesses Everyday</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Decorative glow --}}
                    <div class="absolute -top-12 -left-12 size-64 bg-indigo-500/20 blur-[100px] rounded-full"></div>
                    <div class="absolute -bottom-12 -right-12 size-64 bg-cyan-500/20 blur-[100px] rounded-full"></div>
                </div>
            </div>
        </flux:container>
    </section>
    @livewire('home.social-proof')
@endsection
