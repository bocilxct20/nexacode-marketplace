@extends('layouts.app')

@section('title', $product->name . ' - NexaCode Marketplace')
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('og_type', 'product')
@section('og_image', $product->thumbnail_url)

@push('seo')
    <meta property="product:price:amount" content="{{ $product->is_on_sale ? $product->discounted_price : $product->price }}">
    <meta property="product:price:currency" content="IDR">
    <script type="application/ld+json">
        {!! app(\App\Services\SEOService::class)->generateProductSchema($product) !!}
    </script>
@endpush

@section('content')
    <div class="pt-4 pb-2 animate-fade-in-up">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('products.index') }}" separator="slash">Products</flux:breadcrumbs.item>
            @if($product->category)
                <flux:breadcrumbs.item href="{{ route('products.index', ['category' => $product->category->id]) }}" separator="slash">{{ $product->category->name }}</flux:breadcrumbs.item>
            @endif
            <flux:breadcrumbs.item separator="slash">{{ $product->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pt-4" data-product-id="{{ $product->id }}">
        {{-- Left Column: Main Content --}}
        <div class="lg:col-span-2 space-y-12">
            <div class="animate-fade-in-up stagger-1">
                <div class="flex items-center gap-3 mb-4">
                    @if($product->category)
                        <flux:badge size="sm" color="zinc" variant="outline" class="uppercase font-black tracking-widest text-[9px] px-2 py-0.5 rounded-md">{{ $product->category->name }}</flux:badge>
                    @endif
                    <div class="h-1 w-8 bg-zinc-900 dark:bg-white rounded-full"></div>
                </div>
                <flux:heading size="2xl" class="font-black tracking-tight mb-6 !text-4xl text-zinc-900 dark:text-white leading-tight uppercase">{{ $product->name }}</flux:heading>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-zinc-500">
                    <x-profile-preview :user="$product->author">
                        <div class="flex items-center gap-3 group/author cursor-pointer border-r border-zinc-200 dark:border-zinc-800 pr-4">
                            <img src="{{ $product->author->avatar ? asset('storage/' . $product->author->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($product->author->name).'&background=random' }}" class="w-8 h-8 rounded-full border border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-zinc-900 dark:text-white hover:text-indigo-600 transition-colors">@ {{ $product->author->name }}</span>
                                <x-community-badge :user="$product->author" size="sm" />
                            </div>
                        </div>
                    </x-profile-preview>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <flux:icon.star class="w-4 h-4 text-zinc-900 dark:text-white fill-zinc-900 dark:fill-white" />
                            <span class="font-bold text-zinc-900 dark:text-white">{{ number_format($product->avg_rating, 1) }}</span>
                            <span class="text-xs font-medium text-zinc-400">({{ $product->reviews->count() }})</span>
                        </div>
                        <div class="w-1 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full"></div>
                        <div class="flex items-center gap-1.5">
                            <flux:icon.shopping-cart class="w-4 h-4 text-zinc-400" />
                            <span class="font-bold text-zinc-900 dark:text-white">{{ number_format($product->sales_count) }}</span>
                            <span class="text-xs font-medium text-zinc-400">Sales</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Media Gallery --}}
            <div x-data="{ 
                activeMedia: 'image', 
                activeImage: 0,
                images: {{ json_encode($product->screenshots_urls) }},
                next() { this.activeImage = (this.activeImage + 1) % this.images.length },
                prev() { this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length }
            }" class="space-y-4 animate-fade-in-up stagger-2">
                {{-- Main Display --}}
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800/50 rounded-2xl overflow-hidden ring-1 ring-zinc-200 dark:ring-white/10 relative group">
                    {{-- Image Display --}}
                    <template x-if="activeMedia === 'image'">
                        <div class="w-full h-full relative cursor-zoom-in" x-on:click="Livewire.dispatch('open-lightbox', { images: images, activeIndex: activeImage })">
                            <img :key="activeImage" :src="images[activeImage]" 
                                 class="w-full h-full object-cover transition-all duration-700 animate-in fade-in zoom-in-95" />
                            
                            {{-- Carousel Navigation --}}
                            <div x-show="images.length > 1" class="absolute inset-0 flex items-center justify-between px-6 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <flux:button variant="ghost" square x-on:click.stop="prev()" class="bg-black/20 backdrop-blur-md text-white hover:bg-black/40 border-none pointer-events-auto shadow-2xl">
                                    <x-lucide-chevron-left class="w-6 h-6" />
                                </flux:button>
                                <flux:button variant="ghost" square x-on:click.stop="next()" class="bg-black/20 backdrop-blur-md text-white hover:bg-black/40 border-none pointer-events-auto shadow-2xl">
                                    <x-lucide-chevron-right class="w-6 h-6" />
                                </flux:button>
                            </div>

                            {{-- Image Indicators --}}
                            <div x-show="images.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                                <template x-for="(img, index) in images" :key="index">
                                    <button x-on:click="activeImage = index" :class="activeImage === index ? 'w-8 bg-emerald-500' : 'w-2 bg-white/40'" class="h-2 rounded-full transition-all duration-300"></button>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Video Display --}}
                    @if($product->video_url)
                    <template x-if="activeMedia === 'video'">
                        <div class="w-full h-full">
                            @php
                                $videoId = '';
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $product->video_url, $match)) {
                                    $videoId = $match[1];
                                }
                            @endphp
                            @if($videoId)
                                <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-500">Video player not supported for this link.</div>
                            @endif
                        </div>
                    </template>
                    @endif
                </div>

                {{-- Thumbnails & Media Toggles --}}
                <div class="flex items-center gap-4 overflow-x-auto pb-2 scrollbar-hide">
                    <button x-on:click="activeMedia = 'image'" :class="activeMedia === 'image' ? 'ring-2 ring-emerald-500 opacity-100' : 'opacity-60'" class="relative w-24 h-16 rounded-xl overflow-hidden flex-shrink-0 transition-all border border-zinc-200 dark:border-zinc-800">
                        <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                            <x-lucide-image class="w-5 h-5 text-white" />
                        </div>
                    </button>

                    @if($product->video_url)
                    <button x-on:click="activeMedia = 'video'" :class="activeMedia === 'video' ? 'ring-2 ring-emerald-500 opacity-100' : 'opacity-60'" class="relative w-24 h-16 rounded-xl overflow-hidden flex-shrink-0 transition-all border border-zinc-200 dark:border-zinc-800 bg-zinc-900">
                        <x-lucide-play-circle class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-8 h-8 text-white z-10" />
                        <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover opacity-50" />
                    </button>
                    @endif
                </div>
            </div>

            {{-- Tabs Interface --}}
            {{-- Tabs Interface --}}
            {{-- Tabs Interface --}}
            <flux:tab.group class="space-y-8 animate-fade-in-up stagger-3">
                <flux:tabs class="border-b border-zinc-200 dark:border-zinc-800">
                    <flux:tab name="description">Description</flux:tab>
                    <flux:tab name="changelog">Changelog</flux:tab>
                    <flux:tab name="reviews">Reviews ({{ $product->reviews->count() }})</flux:tab>
                </flux:tabs>

                {{-- Tab Content: Description --}}
                <flux:tab.panel name="description">
                    <div class="bg-zinc-50/50 dark:bg-zinc-900/30 rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50 p-8 lg:p-12">
                        <div class="prose dark:prose-invert prose-indigo max-w-none">
                            <flux:heading size="xl" class="mb-8 font-black uppercase tracking-tight">Product Overview</flux:heading>
                            <div class="text-xl leading-relaxed text-zinc-600 dark:text-zinc-400 font-medium whitespace-pre-line">
                                {{ $product->description }}
                            </div>
                        </div>
                    </div>
                        <div class="mt-6 p-6 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                                <flux:heading size="sm" class="mb-4">Tech Stack</flux:heading>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($product->tags as $tag)
                                        <x-tech-stack-item :name="$tag->name" />
                                    @endforeach
                                </div>
                            </div>

                        {{-- NEW: Bundles Section --}}
                        @if($product->bundles->count() > 0)
                            <div class="mt-12">
                                <flux:heading size="xl" class="mb-6 flex items-center gap-2">
                                    <flux:icon.gift class="w-6 h-6 text-emerald-500" />
                                    Best Value Bundles
                                </flux:heading>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($product->bundles as $bundle)
                                        <div class="group p-6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/50 transition-all duration-300 shadow-sm hover:shadow-xl relative overflow-hidden">
                                            <div class="flex flex-col h-full">
                                                <div class="flex items-center justify-between mb-4">
                                                    <flux:badge color="emerald" variant="solid" class="uppercase font-black text-[10px] tracking-widest px-3 py-1 scale-90 origin-left">SAVE {{ $bundle->discount_percentage > 0 ? $bundle->discount_percentage . '%' : 'Rp ' . number_format($bundle->discount_amount) }}</flux:badge>
                                                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $bundle->products->count() }} Items</div>
                                                </div>
                                                
                                                <flux:heading size="lg" class="mb-2 line-clamp-1">{{ $bundle->name }}</flux:heading>
                                                <div class="flex -space-x-3 mb-6 overflow-hidden">
                                                    @foreach($bundle->products->take(4) as $bp)
                                                        <div class="size-10 rounded-xl border-2 border-white dark:border-zinc-900 overflow-hidden bg-zinc-100 shadow-sm">
                                                            <img src="{{ $bp->thumbnail_url }}" alt="{{ $bp->name }}" class="size-full object-cover">
                                                        </div>
                                                    @endforeach
                                                    @if($bundle->products->count() > 4)
                                                        <div class="size-10 rounded-xl border-2 border-white dark:border-zinc-900 bg-zinc-800 flex items-center justify-center text-[10px] font-black text-white">
                                                            +{{ $bundle->products->count() - 4 }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mt-auto flex items-end justify-between">
                                                    <div>
                                                        <div class="text-[10px] text-zinc-500 line-through">Rp {{ number_format($bundle->total_gross_price) }}</div>
                                                        <div class="text-xl font-black text-zinc-900 dark:text-white">Rp {{ number_format($bundle->price) }}</div>
                                                    </div>
                                                    <flux:button wire:click="$dispatch('bundleAddedToCart', { bundleId: {{ $bundle->id }} })" variant="primary" size="sm" icon="shopping-cart">Add Bundle</flux:button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!$product->author->isElite())
                            <div class="mt-8 p-6 bg-indigo-50/50 dark:bg-zinc-800/20 rounded-2xl border border-indigo-100 dark:border-zinc-700 flex items-center justify-between gap-4 group cursor-pointer hover:border-indigo-500/50 transition-all" onclick="window.location.href='{{ route('author.plans') }}'">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-white dark:bg-zinc-900 shadow-sm flex items-center justify-center text-indigo-500">
                                        <flux:icon.sparkles class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <flux:heading size="sm" class="line-through opacity-50">Standard Author Fee: 80%</flux:heading>
                                        <flux:heading size="md" color="indigo" class="font-black">Elite Member Fee: 90% + 5% Cashback</flux:heading>
                                    </div>
                                </div>
                                <flux:icon.chevron-right class="w-5 h-5 text-indigo-300 group-hover:text-indigo-500" />
                            </div>
                        @endif
                </flux:tab.panel>

                {{-- Tab Content: Changelog --}}
                <flux:tab.panel name="changelog">
                    <div class="space-y-8 pt-8">
                        @forelse($product->versions->sortByDesc('created_at') as $version)
                            <div class="relative pl-8 pb-8 border-l border-zinc-200 dark:border-zinc-800 last:border-0 last:pb-0">
                                <div class="absolute -left-1.5 top-0 w-3 h-3 rounded-full bg-emerald-500"></div>
                                <div class="flex items-center gap-4 mb-4">
                                    <flux:heading size="lg">v{{ $version->version_number }}</flux:heading>
                                    <span class="text-sm text-zinc-500">{{ $version->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                                    <p class="text-zinc-600 dark:text-zinc-400">{{ $version->changelog }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-zinc-500">No versions available.</p>
                        @endforelse
                    </div>
                </flux:tab.panel>

                {{-- Tab Content: Reviews --}}
                <flux:tab.panel name="reviews">
                    <div class="space-y-10 pt-8" x-data="{ showReviewForm: false }">
                        
                        <div class="flex items-center justify-between">
                            <flux:heading size="xl">Customer Reviews</flux:heading>
                            @if(auth()->check() && auth()->user()->orders()->where('status', 'completed')->whereHas('items', function($q) use ($product) { $q->where('product_id', $product->id); })->exists())
                                <flux:button variant="outline" size="sm" x-on:click="showReviewForm = !showReviewForm">Write a Review</flux:button>
                            @endif
                        </div>

                        <div x-show="showReviewForm" x-collapse>
                            <livewire:product.review-form :product="$product" />
                        </div>

                        @forelse($product->reviews as $review)
                            <div class="flex gap-6 pb-8 border-b border-zinc-100 dark:border-zinc-900 last:border-0">
                        <x-profile-preview :user="$review->buyer">
                            <x-user-avatar :user="$review->buyer" size="lg" class="rounded-2xl shrink-0" />
                        </x-profile-preview>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <x-profile-preview :user="$review->buyer">
                                    <div class="font-bold flex items-center gap-2">
                                        {{ $review->buyer->name }}
                                        <x-community-badge :user="$review->buyer" />
                                        @if($review->isVerified())
                                            <div class="flex items-center gap-1 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-emerald-100 dark:border-emerald-800">
                                                <flux:icon.check variant="mini" class="w-2.5 h-2.5" />
                                                Verified Purchase
                                            </div>
                                        @endif
                                    </div>
                                </x-profile-preview>
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <flux:icon.star variant="mini" class="{{ $i <= $review->rating ? 'text-amber-400' : 'text-zinc-200 dark:text-zinc-800' }}" />
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $review->comment }}</p>

                                    {{-- Review Media Display --}}
                                    @if($review->media && count($review->media) > 0)
                                        <div class="flex flex-wrap gap-3 mt-6">
                                            @foreach($review->media as $path)
                                                <div class="relative group cursor-zoom-in overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800" 
                                                     x-on:click="Livewire.dispatch('open-lightbox', { images: {{ json_encode($review->media) }}, activeIndex: {{ $loop->index }} })">
                                                    <img src="{{ Storage::url($path) }}" class="w-24 h-24 object-cover transform group-hover:scale-110 transition-transform duration-500">
                                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if($review->author_reply)
                                        <div class="mt-6 p-6 bg-zinc-50 dark:bg-zinc-900/50 border-l-4 border-emerald-500 rounded-r-2xl relative overflow-hidden">
                                            <div class="flex items-center gap-2 mb-3 relative z-10">
                                                <x-profile-preview :user="$product->author">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shadow-sm">
                                                            @if($product->author->isAdmin())
                                                                <flux:icon.shield-check variant="mini" class="w-3.5 h-3.5 text-rose-600" />
                                                            @elseif($product->author->isElite())
                                                                <flux:icon.check-badge variant="mini" class="w-3.5 h-3.5 text-amber-500" />
                                                            @elseif($product->author->isPro())
                                                                <flux:icon.check-badge variant="mini" class="w-3.5 h-3.5 text-indigo-600" />
                                                            @else
                                                                <flux:icon.user variant="mini" class="w-3.5 h-3.5 text-zinc-400" />
                                                            @endif
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-[10px] font-black uppercase tracking-tighter text-zinc-500">
                                                                Author's Response
                                                            </span>
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="text-[9px] font-bold text-zinc-500">{{ $product->author->name }}</span>
                                                                <x-community-badge :user="$product->author" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </x-profile-preview>
                                                <span class="text-[10px] text-zinc-400 font-medium ml-auto">{{ $review->author_replied_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-zinc-800 dark:text-zinc-200 italic font-medium leading-relaxed relative z-10">"{{ $review->author_reply }}"</p>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-4 mt-4 text-xs text-zinc-500">
                                        <span>{{ $review->created_at->diffForHumans() }}</span>
                                        <flux:separator vertical />
                                        <flux:button variant="ghost" size="sm" icon="hand-thumb-up" class="text-zinc-500 hover:text-emerald-500">Helpful?</flux:button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-zinc-500">No reviews yet. Be the first to review!</p>
                        @endforelse
                    </div>
                </flux:tab.panel>


            </flux:tab.group>
            </div>
        
        {{-- Right Column: Sidebar Actions --}}
        <div class="space-y-8 lg:sticky lg:top-24 lg:self-start">
            {{-- Order Widget --}}
            {{-- Order Widget --}}
            @if(auth()->check() && auth()->user()->orders()->where('status', 'completed')->whereHas('items', function($q) use ($product) { $q->where('product_id', $product->id); })->exists())
                <flux:card class="p-8 shadow-2xl border-emerald-500 bg-emerald-50/10 dark:bg-emerald-950/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4">
                        <flux:icon.check-badge class="w-8 h-8 text-emerald-500 opacity-20" />
                    </div>

                    <div class="space-y-6">
                        <div>
                            <flux:badge color="emerald" variant="solid" class="mb-2">Purchased</flux:badge>
                            <flux:heading size="xl" class="font-black">You own this item</flux:heading>
                            <flux:subheading size="sm">Thank you for your purchase!</flux:subheading>
                        </div>

                        <flux:separator variant="subtle" />

                        <flux:button 
                            href="{{ route('products.download', $product) }}" 
                            variant="primary" 
                            class="w-full py-6 text-lg"
                            icon="arrow-down-tray"
                        >
                            Download Project
                        </flux:button>

                        <div class="pt-4 flex gap-2 text-xs text-zinc-500">
                            <flux:icon.information-circle class="w-4 h-4" />
                            <span>Need help? <a href="{{ route('dashboard.support') }}" class="underline hover:text-emerald-500 transition-colors">Contact Support</a></span>
                        </div>
                    </div>
                </flux:card>
            @else
            @php
                $activeSale = \App\Models\Product::getActiveFlashSale();
            @endphp
            
            @if($activeSale)
                <x-flash-sale-timer :ends-at="$activeSale->ends_at" />
            @endif

            <flux:card class="p-8 shadow-sm border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 rounded-[2rem] animate-fade-in-up stagger-2">
                    <div class="space-y-8">
                        <form action="{{ route('checkout.show', $product) }}" method="GET" class="space-y-8">

                             <div>
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center border border-emerald-100 dark:border-emerald-800">
                                            <flux:icon.shield-check variant="mini" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                        </div>
                                        <flux:heading size="sm" class="font-bold uppercase tracking-widest text-zinc-900 dark:text-white">Lifetime Access</flux:heading>
                                    </div>
                                    <flux:badge size="sm" color="emerald" variant="outline" class="uppercase font-black tracking-widest text-[9px]">Verified Item</flux:badge>
                                </div>

                                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200 dark:border-zinc-700/50 group transition-all">
                                    <div class="relative">
                                        @if($product->is_on_sale)
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-[10px] font-black text-white bg-rose-500 px-2 py-0.5 rounded uppercase tracking-widest">FLASH SALE</span>
                                                <span class="text-[10px] font-bold text-zinc-500 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex items-baseline gap-1 text-zinc-900 dark:text-white">
                                                <span class="text-sm font-medium text-zinc-400">Rp</span>
                                                <span class="text-4xl font-black tracking-tight">{{ number_format($product->discounted_price, 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-baseline gap-1 text-zinc-900 dark:text-white">
                                                <span class="text-sm font-medium text-zinc-400">Rp</span>
                                                <span class="text-4xl font-black tracking-tight">{{ number_format($product->price, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <div class="mt-4 flex items-center justify-between gap-2 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase font-bold tracking-widest">Instant Delivery</span>
                                            </div>
                                            @livewire('product.scarcity-badge', ['productId' => $product->id, 'type' => 'viewers'])
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.check class="w-4 h-4 text-emerald-500" />
                                    <span>Quality checked by NEXACODE</span>
                                </div>
                                <div class="flex gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.check class="w-4 h-4 text-emerald-500" />
                                    <span>Future updates included</span>
                                </div>
                                <div class="flex gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.check class="w-4 h-4 text-emerald-500" />
                                    <span>6 months support included</span>
                                </div>
                            </div>

                            <div class="pt-4 space-y-3">
                                <flux:button type="submit" variant="primary" color="indigo" class="w-full py-6 text-lg" data-track-click="buy_now_direct">
                                    <x-slot name="icon"><flux:icon.bolt class="w-5 h-5" /></x-slot>
                                    Buy Now (Direct)
                                </flux:button>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1" data-track-click="add_to_cart_sidebar">
                                        @livewire('cart.add-to-cart-button', ['productId' => $product->id])
                                    </div>
                                    @livewire('wishlist-toggle', ['productId' => $product->id])
                                </div>
                            </div>

                            {{-- Social Share --}}
                            <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                                <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Share this item</div>
                                <div class="flex gap-2">
                                    <flux:button href="{{ $product->share_urls['twitter'] }}" target="_blank" variant="ghost" size="sm" class="text-zinc-500 hover:text-[#1DA1F2]">
                                        <x-slot name="icon"><x-lucide-twitter class="w-4 h-4" /></x-slot>
                                    </flux:button>
                                    <flux:button href="{{ $product->share_urls['facebook'] }}" target="_blank" variant="ghost" size="sm" class="text-zinc-500 hover:text-[#4267B2]">
                                        <x-slot name="icon"><x-lucide-facebook class="w-4 h-4" /></x-slot>
                                    </flux:button>
                                    <flux:button href="{{ $product->share_urls['linkedin'] }}" target="_blank" variant="ghost" size="sm" class="text-zinc-500 hover:text-[#0077b5]">
                                        <x-slot name="icon"><x-lucide-linkedin class="w-4 h-4" /></x-slot>
                                    </flux:button>
                                    <flux:button type="button" variant="ghost" size="sm" class="text-zinc-500" x-on:click="navigator.clipboard.writeText(window.location.href); $toast({ heading: 'Link copied!', variant: 'success' })">
                                        <x-slot name="icon"><x-lucide-link class="w-4 h-4" /></x-slot>
                                    </flux:button>
                                </div>
                            </div>
                        </form>
                    </div>
                </flux:card>
            @endif

            {{-- Share & Earn Widget (Affiliate) --}}
            <div class="p-6 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 rounded-3xl relative overflow-hidden">
                <div class="relative space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <flux:icon.megaphone variant="mini" class="w-4 h-4 text-zinc-400" />
                        <span class="text-xs font-bold uppercase tracking-widest text-zinc-900 dark:text-white">Affiliate Program</span>
                    </div>
                    
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                        @auth
                            Earn <strong>10% commission</strong> for every sale you refer.
                        @else
                            Join to earn <strong>10%</strong> from every sale you refer.
                        @endauth
                    </flux:text>

                    @auth
                        <div class="flex gap-2 p-1.5 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/50">
                            <input 
                                type="text" 
                                value="{{ route('products.show', ['product' => $product, 'ref' => auth()->user()->affiliate_code ?: 'NC' . auth()->id()]) }}" 
                                readonly 
                                class="flex-1 bg-transparent border-none focus:ring-0 text-xs text-zinc-600 truncate px-2"
                                id="affiliate-link-{{ $product->id }}"
                            >
                            <flux:button 
                                variant="outline" 
                                size="sm" 
                                class="rounded-lg h-8"
                                onclick="copyAffiliateLink('affiliate-link-{{ $product->id }}')"
                            >
                                Copy
                            </flux:button>
                        </div>
                    @else
                        <flux:button href="{{ route('login') }}" variant="outline" class="w-full">
                            Join Affiliate Hub
                        </flux:button>
                    @endauth
                </div>
            </div>

            {{-- Unified Ecosystem Support Widget --}}
            <div class="p-6 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 rounded-3xl space-y-6">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-zinc-900 dark:text-white mb-1">Author Support</div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Direct assistance from the creator.</flux:text>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <x-lucide-clock class="w-4 h-4 text-zinc-400" />
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Replies usually within 24h</span>
                    </div>

                    @if($product->author->isElite())
                        <div class="flex items-center gap-3 px-4 py-3 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-900/20">
                            <flux:icon.sparkles variant="solid" class="w-4 h-4 text-amber-500" />
                            <span class="text-xs font-medium text-amber-700 dark:text-amber-400">Elite Support Priority</span>
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    @auth
                        @if(auth()->id() !== $product->author_id)
                            <flux:button 
                                x-on:click="Livewire.dispatch('open-author-chat', { authorId: {{ $product->author_id }}, productId: {{ $product->id }} })"
                                variant="outline" 
                                class="w-full"
                                icon="chat-bubble-left-right"
                            >
                                Message Author
                            </flux:button>
                        @endif
                    @else
                        <flux:button 
                            href="{{ route('login') }}"
                            variant="outline" 
                            class="w-full"
                            icon="chat-bubble-left-right"
                        >
                            Log in to Message
                        </flux:button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    <div class="mt-24 space-y-24">
        <flux:separator />
        
    </div>

    <script shadow>
        function copyAffiliateLink(inputId) {
            const input = document.getElementById(inputId);
            input.select();
            document.execCommand('copy');
            
            // Temporary alert
            alert('Link affiliate sudah disalin ke clipboard kamu!');
        }
    </script>
@endsection
