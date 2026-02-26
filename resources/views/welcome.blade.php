@extends('layouts.app')

@section('is_home', true)
@section('title', 'The world\'s leading marketplace for source code')

@section('content')
    {{-- Hero Section --}}
    {{-- Hero Section --}}
    <section class="relative py-32 lg:py-48 overflow-hidden bg-white dark:bg-zinc-950">
        <flux:container>
            <div class="text-center max-w-4xl mx-auto relative z-10 space-y-8 animate-in fade-in slide-in-from-bottom duration-1000">
                <div class="flex justify-center">
                    <div class="uppercase tracking-[0.4em] font-black text-[9px] px-6 py-2 rounded-full border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 text-emerald-600 dark:text-emerald-400">The Nexus of Code</div>
                </div>
                
                <flux:heading level="1" class="text-6xl lg:text-8xl font-black tracking-tighter leading-[0.9] uppercase">
                    World's Elite <br/>
                    <span class="bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-500 bg-clip-text text-transparent">Source Code</span><br/>
                    Marketplace
                </flux:heading>

                <flux:text class="text-xl text-zinc-500 dark:text-zinc-400 font-medium max-w-2xl mx-auto leading-relaxed">
                    Eksplorasi ribuan aset digital premium dari author terbaik dunia. <br class="hidden md:block" />
                    Bangun platform impianmu lebih cepat dengan fondasi kode yang solid.
                </flux:text>

                <div class="max-w-2xl mx-auto pt-4 relative">
                    <form action="{{ route('products.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 p-2 bg-white dark:bg-zinc-900/50 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm focus-within:ring-2 focus-within:ring-emerald-500/20 focus-within:border-emerald-500/50 transition-all group">
                        <div class="flex-1 flex items-center pl-6">
                            <flux:icon name="magnifying-glass" variant="mini" class="size-5 text-zinc-400 group-focus-within:text-emerald-500 transition-colors" />
                            <input name="search" placeholder="Cari Laravel, React, SaaS, E-commerce..." class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 outline-none px-4 py-2">
                        </div>
                        <flux:button type="submit" variant="primary" class="rounded-full px-8 py-3 w-full sm:w-auto font-black uppercase tracking-widest text-[10px] shadow-sm transform transition-transform hover:-translate-y-0.5">Search Items</flux:button>
                    </form>
                </div>

                <div class="flex flex-wrap justify-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
                    <span class="font-bold flex items-center gap-1.5 opacity-60">
                        <flux:icon name="bolt" variant="micro" class="size-3" />
                        Quick Browse:
                    </span>
                    @forelse($trendingTags as $tag)
                        <a href="{{ route('products.index', ['search' => $tag->name]) }}" class="px-3 py-1 text-[10px] items-center rounded-xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/30 hover:text-emerald-600 font-bold uppercase tracking-widest transition-colors">{{ $tag->name }}</a>
                    @empty
                        <a href="{{ route('products.index', ['search' => 'Laravel']) }}" class="px-3 py-1 flex items-center rounded-xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/30 hover:text-emerald-600 text-[10px] font-bold uppercase tracking-widest transition-colors">Laravel</a>
                        <a href="{{ route('products.index', ['search' => 'SaaS']) }}" class="px-3 py-1 flex items-center rounded-xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/30 hover:text-emerald-600 text-[10px] font-bold uppercase tracking-widest transition-colors">SaaS</a>
                        <a href="{{ route('products.index', ['search' => 'React']) }}" class="px-3 py-1 flex items-center rounded-xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/30 hover:text-emerald-600 text-[10px] font-bold uppercase tracking-widest transition-colors">React</a>
                    @endforelse
                </div>
            </div>

            {{-- Background Decorative Blobs --}}
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        </flux:container>
    </section>

    <flux:separator />


    <flux:separator />

    @if($featuredProducts->count() > 0)
        {{-- Elite Spotlight Section --}}
        <section class="py-24 bg-zinc-50 dark:bg-zinc-900 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent dark:from-white/5 pointer-events-none"></div>
            <flux:container>
                <div class="flex items-end justify-between mb-16 gap-6 flex-wrap">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 mb-4">
                            <flux:icon name="sparkles" variant="mini" class="size-4 text-amber-500" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-500">Curated by Editors</span>
                        </div>
                        <div class="font-black uppercase tracking-tight text-4xl lg:text-5xl text-zinc-900 dark:text-white mb-2">Elite Spotlight</div>
                        <p class="mt-2 text-zinc-500 dark:text-zinc-400 font-medium">Hanya aset dengan kualitas kode terbaik dan support bintang lima.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    @foreach($featuredProducts as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="group relative p-0 overflow-hidden border border-zinc-200 dark:border-zinc-800 hover:border-amber-500/40 dark:hover:border-amber-500/40 transition-all duration-700 shadow-sm hover:shadow-2xl hover:shadow-amber-500/10 rounded-3xl bg-white dark:bg-zinc-900/50 flex flex-col h-full hover:-translate-y-1">
                            {{-- ... (Product card logic remains same but container refined) --}}
                            <div class="aspect-[4/3] w-full overflow-hidden relative bg-zinc-100 dark:bg-zinc-800">
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000 ease-out">
                                <div class="absolute top-4 left-4">
                                    <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md font-black uppercase tracking-widest text-[9px] px-3 py-1.5 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex items-center gap-1.5 text-amber-600 dark:text-amber-500">
                                        <flux:icon name="star" variant="solid" class="size-3" />
                                        Top Choice
                                    </div>
                                </div>
                            </div>
                            <div class="p-8 flex-1 flex flex-col">
                                <div class="flex items-start justify-between mb-6 gap-4">
                                    <div class="font-bold text-lg text-zinc-900 dark:text-white uppercase tracking-tight group-hover:text-amber-600 dark:group-hover:text-amber-500 transition-colors line-clamp-2 leading-tight">{{ $product->name }}</div>
                                </div>
                                <div class="mt-auto flex items-center justify-between pt-6 border-t border-zinc-100 dark:border-zinc-800">
                                    <div class="flex items-center gap-3">
                                        <x-user-avatar :user="$product->author" size="xs" />
                                        <span class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-tighter">{{ $product->author->name }}</span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Starting from</span>
                                        <span class="text-lg font-black tabular-nums">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </flux:container>
        </section>
    @endif

    {{-- Trending Products --}}
    <section class="py-32 bg-white dark:bg-zinc-950">
        <flux:container>
            <div class="flex items-end justify-between mb-16 gap-6 flex-wrap">
                <div>
                    <div class="inline-flex items-center gap-2 mb-4">
                        <flux:icon name="fire" variant="mini" class="size-4 text-emerald-500" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Trending</span>
                    </div>
                    <div class="font-black uppercase tracking-tight text-4xl lg:text-5xl text-zinc-900 dark:text-white mb-2">Hot Assets</div>
                    <p class="mt-2 text-zinc-500 dark:text-zinc-400 font-medium text-lg">Aset digital paling dicari komunitas minggu ini.</p>
                </div>
                <flux:button variant="ghost" href="{{ route('products.index') }}" icon-trailing="chevron-right" class="font-black uppercase tracking-widest text-[10px]">
                    Marketplace View
                </flux:button>
            </div>
            
            @livewire('home.product-collections')

            <div class="mt-20 text-center">
                <flux:button variant="outline" class="px-8 h-12 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-200 dark:border-zinc-800 transition-colors" href="{{ route('products.index') }}" icon-trailing="arrow-right">
                    Explore All Marketplace Items
                </flux:button>
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Categories Section --}}
    <section class="py-32 bg-zinc-50 dark:bg-zinc-950 overflow-hidden relative border-y border-zinc-200 dark:border-zinc-800">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-indigo-500/5 pointer-events-none"></div>
        <flux:container class="relative z-10">
            <div class="flex items-end justify-between mb-16 gap-6 flex-wrap">
                <div>
                    <div class="inline-flex items-center gap-2 mb-4">
                        <flux:icon name="queue-list" variant="mini" class="size-4 text-indigo-500" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Classification</span>
                    </div>
                    <div class="font-black uppercase tracking-tight text-4xl lg:text-5xl text-zinc-900 dark:text-white mb-2">Browse Categories</div>
                    <p class="mt-2 text-zinc-500 dark:text-zinc-400 font-medium text-lg">Pilih jalur teknologimu dan temukan aset yang tepat.</p>
                </div>
                <flux:button variant="ghost" href="{{ route('products.index') }}" icon-trailing="chevron-right" class="font-black uppercase tracking-widest text-[10px]">
                    All Categories
                </flux:button>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                @foreach($categories->take(6) as $cat)
                    <a 
                        href="{{ route('products.index', ['category' => $cat->id]) }}" 
                        class="p-8 text-center transition-all group cursor-pointer border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:border-indigo-500/30 shadow-sm rounded-3xl flex flex-col items-center justify-center space-y-4 h-full"
                    >
                        <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700/50 rounded-2xl flex items-center justify-center transition-all group-hover:scale-110 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 group-hover:-rotate-3">
                            @if($cat->icon && (str_starts_with($cat->icon, 'storage/') || str_starts_with($cat->icon, 'http')))
                                <div class="w-8 h-8 bg-zinc-400 group-hover:bg-indigo-500 dark:bg-zinc-500 transition-colors" 
                                     style="mask-image: url('{{ asset($cat->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($cat->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                                </div>
                            @elseif(str_starts_with($cat->icon ?? '', 'lucide-'))
                                <x-dynamic-component :component="$cat->icon" class="w-8 h-8 text-zinc-400 group-hover:text-indigo-500 dark:text-zinc-500 transition-colors" />
                            @else
                                <flux:icon :icon="$cat->icon ?: 'folder'" variant="outline" class="w-8 h-8 text-zinc-400 group-hover:text-indigo-500 dark:text-zinc-500 transition-colors" />
                            @endif
                        </div>
                        <div class="font-black text-sm uppercase tracking-tight text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $cat->name }}</div>
                    </a>
                @endforeach
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Top Authors Leaderboard --}}
    <section class="py-32 bg-white dark:bg-zinc-950 relative overflow-hidden">
        <flux:container>
            <div class="flex flex-col md:flex-row items-end justify-between mb-20 gap-8">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-2 mb-4">
                        <flux:icon name="identification" variant="mini" class="size-4 text-indigo-500" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">The Architects</span>
                    </div>
                    <div class="font-black uppercase tracking-tight text-4xl lg:text-5xl text-zinc-900 dark:text-white mb-2">Meet our Elite Authors</div>
                    <p class="mt-2 text-zinc-500 dark:text-zinc-400 font-medium text-lg">Para kreator terbaik yang membangun masa depan bersama NexaCode.</p>
                </div>
                <flux:button variant="ghost" href="{{ route('page.author-ranking') }}" icon-trailing="chevron-right" class="font-black uppercase tracking-widest text-[10px]">
                    View Global Rankings
                </flux:button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($topAuthors as $author)
                    <a href="{{ route('authors.show', $author->username ?? $author->id) }}" class="p-6 flex items-center gap-6 group rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:border-indigo-500/30 transition-all h-full">
                        <div class="relative shrink-0">
                            <x-user-avatar :user="$author" size="lg" class="rounded-2xl border-2 border-transparent group-hover:border-indigo-500 transition-all" />
                            <div class="absolute -top-2 -right-2 size-7 bg-indigo-600 text-white text-[11px] flex items-center justify-center rounded-xl font-black border-4 border-white dark:border-zinc-950 shadow-xl">
                                {{ $loop->iteration }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2 gap-2">
                                <flux:heading size="sm" class="font-black uppercase tracking-tighter truncate group-hover:text-indigo-600 transition-colors">{{ $author->name }}</flux:heading>
                                @if($author->tierBadge && $author->tierBadge->name !== 'BASIC')
                                    <x-community-badge :badge="$author->tierBadge" size="sm" class="scale-75 origin-left" />
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5">
                                    <flux:icon name="cube" variant="micro" class="size-3.5 text-zinc-400" />
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $author->products_count ?? $author->products()->count() }} Aset</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <flux:icon name="fire" variant="micro" class="size-3.5 text-orange-500" />
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $author->reputation ?? 0 }} XP</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </flux:container>
    </section>

    <flux:separator />

    {{-- Values & Social Proof Merge --}}
    <section class="py-32 bg-zinc-50 dark:bg-zinc-900 border-y border-zinc-200 dark:border-zinc-800 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-white to-transparent dark:from-zinc-950 pointer-events-none"></div>
        <flux:container>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24">
                {{-- Why NexaCode? --}}
                <div class="space-y-12">
                    <div>
                        <div class="inline-flex items-center gap-2 mb-4">
                            <flux:icon name="shield-check" variant="mini" class="size-4 text-emerald-500" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">The Advantage</span>
                        </div>
                        <div class="font-black uppercase tracking-tight text-4xl lg:text-5xl text-zinc-900 dark:text-white mb-2">Why NexaCode?</div>
                        <p class="mt-2 text-zinc-500 dark:text-zinc-400 font-medium text-lg">Platform terbaik bagi para developer profesional untuk menemukan dan menjual aset digital berkualitas tinggi.</p>
                    </div>

                    <div class="space-y-8">
                        @foreach($features as $feature)
                            <div class="flex gap-6 group">
                                <div class="size-16 shrink-0 rounded-[1.5rem] bg-{{ $feature['color'] }}-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <flux:icon name="{{ $feature['icon'] }}" variant="solid" class="size-8 text-{{ $feature['color'] }}-500" />
                                </div>
                                <div class="space-y-1">
                                    <flux:heading size="lg" class="font-black uppercase tracking-tighter">{{ $feature['title'] }}</flux:heading>
                                    <flux:text class="text-zinc-500 leading-relaxed">{{ $feature['description'] }}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Community Trust (Livewire Social Proof) --}}
                <div class="bg-white dark:bg-zinc-900/50 p-12 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-500/5 blur-[100px] rounded-full"></div>
                    @livewire('home.trust-wall')
                </div>
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

    {{-- Author Branding / Recruitment Section --}}
    <section class="py-24 bg-white dark:bg-zinc-950 relative overflow-hidden">
        {{-- Decorative element --}}
        <div class="absolute top-0 right-0 w-1/3 h-full bg-emerald-500/5 -skew-x-12 transform translate-x-1/2"></div>
        
        <flux:container>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8 animate-in fade-in slide-in-from-left duration-700">
                    <div class="inline-flex items-center gap-2 mb-4">
                        <flux:icon name="rocket-launch" variant="mini" class="size-4 text-indigo-500" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Sell on NexaCode</span>
                    </div>

                    <div class="font-black uppercase tracking-tight text-5xl lg:text-6xl text-zinc-900 dark:text-white mb-2 leading-tight">
                        Ready to scale your <span class="bg-gradient-to-r from-indigo-500 to-emerald-500 bg-clip-text text-transparent">code</span> to the world?
                    </div>

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

                    <div class="pt-4 flex flex-col sm:flex-row gap-4">
                        <flux:button href="{{ route('author.register') }}" variant="primary" class="bg-indigo-600 hover:bg-indigo-500 px-8 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm transform transition-transform hover:-translate-y-0.5">
                            Become an Author Now
                        </flux:button>
                        <flux:button href="{{ route('help.category', 'selling') }}" variant="subtle" class="px-8 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-colors">
                            Learn more about selling
                        </flux:button>
                    </div>
                </div>

                <div class="relative animate-in fade-in zoom-in duration-1000">
                    <div class="bg-zinc-900 rounded-3xl p-4 shadow-2xl border border-zinc-800 relative z-10">
                        <div class="aspect-square rounded-2xl overflow-hidden bg-zinc-800">
                            {{-- We can use a stylized placeholder or generated image here --}}
                            <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?q=80&w=800&auto=format&fit=crop" alt="Author Success" class="w-full h-full object-cover mix-blend-overlay opacity-60">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center p-8">
                                    <div class="size-20 bg-white/10 backdrop-blur-xl rounded-full flex items-center justify-center mx-auto mb-6 border border-white/20">
                                        <flux:icon name="presentation-chart-line" variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="text-3xl font-black text-white mb-2">Powering {{ number_format(\App\Models\User::count(), 0) }}+</div>
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
@endsection
