<div class="space-y-8 relative">
    {{-- Dynamic Hero Section --}}
    <div class="text-center mb-12 animate-in fade-in slide-in-from-top-4 duration-700">
        <flux:heading size="xl" class="font-semibold tracking-tight text-zinc-900 dark:text-white">
            @if($search)
                Search results for "{{ $search }}"
            @elseif($currentCategory)
                {{ $currentCategory->name }}
            @else
                Explore Marketplace
            @endif
        </flux:heading>
        <p class="text-zinc-500 dark:text-zinc-400 mt-2 font-medium">
            @if($search)
                Found {{ $products->total() }} premium items matching your search.
            @elseif($currentCategory)
                Discover {{ number_format($currentCategory->products_count) }} professional {{ strtolower($currentCategory->name) }} scripts and templates.
            @else
                Discover over {{ number_format($allApprovedCount) }} premium scripts, themes, and templates from our global community.
            @endif
        </p>
    </div>

    {{-- Glassmorphism Sticky Header & Search --}}
    <div class="sticky top-0 z-20 pb-4 px-4 sm:px-8 xl:px-12 -mx-4 bg-white/80 dark:bg-zinc-900/50 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800/50">
        <div class="w-full mx-auto flex flex-col gap-5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pt-4">
                <div class="w-full md:w-2/5 relative group">
                    <flux:input 
                        wire:model.live.debounce.400ms="search" 
                        type="search" 
                        placeholder="Search for premium scripts, templates..." 
                        icon="magnifying-glass"
                        class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl focus:ring-zinc-900 dark:focus:ring-white transition-all shadow-sm"
                    />
                    
                    {{-- Trending Search Tags --}}
                    @if($trendingTags->count() > 0 && !$search)
                        <div class="mt-2 text-[10px] font-medium text-zinc-400 dark:text-zinc-500 flex items-center gap-2">
                            <span>Trending:</span>
                            @foreach($trendingTags as $tag)
                                <button wire:click="$set('search', '{{ $tag }}')" class="hover:text-zinc-900 dark:hover:text-white transition-colors">#{{ $tag }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <flux:dropdown position="bottom end">
                        <flux:button variant="ghost" icon="adjustments-horizontal" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm text-zinc-700 dark:text-zinc-300">Filters</flux:button>
                        <flux:menu class="w-72 p-5 space-y-6">
                            <div>
                                <flux:heading size="sm" class="font-semibold text-zinc-900 dark:text-white mb-3 tracking-tight">Price Range</flux:heading>
                                <div class="grid grid-cols-2 gap-3">
                                    <flux:input wire:model.live.debounce.500ms="min_price" type="number" placeholder="Min" size="sm" class="rounded-lg shadow-sm" />
                                    <flux:input wire:model.live.debounce.500ms="max_price" type="number" placeholder="Max" size="sm" class="rounded-lg shadow-sm" />
                                </div>
                            </div>
                            
                            <div>
                                <flux:heading size="sm" class="font-semibold text-zinc-900 dark:text-white mb-3 tracking-tight">Minimum Rating</flux:heading>
                                <div class="space-y-2.5">
                                    @foreach([4, 3, 2] as $rating)
                                        <label class="flex items-center gap-3 group cursor-pointer">
                                            <input type="radio" wire:model.live="min_rating" value="{{ $rating }}" class="w-4 h-4 rounded-full border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white focus:ring-zinc-900 dark:focus:ring-white bg-transparent">
                                            <div class="flex items-center gap-1.5 transition-transform">
                                                <div class="flex items-center">
                                                    @for($i=1; $i<=5; $i++)
                                                        <flux:icon.star class="w-3.5 h-3.5 {{ $i <= $rating ? 'text-zinc-900 dark:text-white fill-zinc-900 dark:fill-white' : 'text-zinc-200 dark:text-zinc-800' }}" />
                                                    @endfor
                                                </div>
                                                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">& Up</span>
                                            </div>
                                        </label>
                                    @endforeach
                                    <label class="flex items-center gap-3 group cursor-pointer mt-3">
                                        <input type="radio" wire:model.live="min_rating" value="" class="w-4 h-4 rounded-full border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white focus:ring-zinc-900 dark:focus:ring-white bg-transparent">
                                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Any Rating</span>
                                    </label>
                                </div>
                            </div>

                            @if($min_price || $max_price || $min_rating)
                                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                    <flux:button wire:click="clearFilters" variant="ghost" size="sm" class="w-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white">Reset Filters</flux:button>
                                </div>
                            @endif
                        </flux:menu>
                    </flux:dropdown>

                    <flux:select wire:model.live="sort" class="w-44 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-xl">
                        <option value="newest">Latest Arrivals</option>
                        <option value="popular">Best Sellers</option>
                        <option value="rating">Top Rated</option>
                        <option value="price_low">Lower Price</option>
                        <option value="price_high">Premium Price</option>
                    </flux:select>
                </div>
            </div>

            {{-- Category Pills --}}
            <div 
                class="flex flex-nowrap items-center gap-2.5 overflow-x-auto pb-2 no-scrollbar touch-pan-x cursor-grab active:cursor-grabbing select-none" 
                style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;"
                x-data="{ 
                    isDown: false, 
                    startX: 0, 
                    scrollLeft: 0,
                    handleMouseDown(e) {
                        this.isDown = true;
                        this.startX = e.pageX - $el.offsetLeft;
                        this.scrollLeft = $el.scrollLeft;
                    },
                    handleMouseLeave() {
                        this.isDown = false;
                    },
                    handleMouseUp() {
                        this.isDown = false;
                    },
                    handleMouseMove(e) {
                        if(!this.isDown) return;
                        e.preventDefault();
                        const x = e.pageX - $el.offsetLeft;
                        const walk = (x - this.startX) * 2;
                        $el.scrollLeft = this.scrollLeft - walk;
                    }
                }"
                @mousedown="handleMouseDown"
                @mouseleave="handleMouseLeave"
                @mouseup="handleMouseUp"
                @mousemove="handleMouseMove"
            >
                <button 
                    wire:click="selectCategory(null)" 
                    class="flex items-center gap-2 group px-4 py-1.5 rounded-full border transition-all whitespace-nowrap shrink-0 {{ $selectedCategory === null ? 'bg-zinc-900 border-zinc-900 text-white dark:bg-white dark:border-white dark:text-zinc-900 shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}"
                >
                    <span class="text-[13px] font-medium leading-none">All Items</span>
                </button>
                @foreach($categories as $category)
                    <button 
                        wire:click="selectCategory({{ $category->id }})" 
                        class="flex items-center gap-2 group px-4 py-1.5 rounded-full border transition-all whitespace-nowrap shrink-0 {{ $selectedCategory == $category->id ? 'bg-zinc-900 border-zinc-900 text-white dark:bg-white dark:border-white dark:text-zinc-900 shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}"
                    >
                        @if($category->icon && (str_starts_with($category->icon, 'storage/') || str_starts_with($category->icon, 'http')))
                            <div class="w-3 h-3 bg-current opacity-70 group-hover:opacity-100 transition-opacity" 
                                 style="mask-image: url('{{ asset($category->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($category->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                            </div>
                        @elseif($category->icon && str_starts_with($category->icon, 'lucide-'))
                            <x-dynamic-component :component="$category->icon" class="w-3 h-3 opacity-70 group-hover:opacity-100 transition-opacity" />
                        @else
                            <flux:icon :icon="$category->icon ?: 'package'" variant="mini" class="w-3 h-3 opacity-70 group-hover:opacity-100 transition-opacity" />
                        @endif
                        <span class="text-[13px] font-medium leading-none">{{ $category->name }}</span>
                        @if($selectedCategory == $category->id)
                            <span class="flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-semibold bg-white/20 dark:bg-black/10">
                                {{ $category->products_count }}
                            </span>
                        @else
                            <span class="flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                                {{ $category->products_count }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="px-0 sm:px-4 xl:px-8">
        {{-- Product Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 xl:gap-8 pt-4">
            @forelse($products as $index => $product)
                {{-- Clean & Minimalist Product Card with Asymmetric Layout for 1st Item --}}
                <div class="group flex flex-col relative transition-all duration-500 {{ $index === 0 ? 'sm:col-span-2 sm:row-span-2' : '' }} z-0 hover:z-20">
                    <div class="block relative overflow-hidden rounded-[1.25rem] bg-zinc-100 dark:bg-zinc-800 ring-1 ring-zinc-200/50 dark:ring-white/10 group-hover:ring-zinc-300 dark:group-hover:ring-white/20 transition-all shadow-sm group-hover:shadow-xl {{ $index === 0 ? 'aspect-video sm:aspect-auto sm:h-full' : 'aspect-[4/3]' }}">
                        <a href="{{ route('products.show', $product->slug) }}" class="absolute inset-0 z-10 w-full h-full"></a>
                        
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105 pointer-events-none relative z-0">
                        
                        {{-- Glassmorphism Information Overlay on Hover --}}
                        <div class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/80 via-black/40 to-transparent translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 ease-out flex flex-col justify-end pointer-events-none z-20">
                            <div class="flex items-center gap-2 mb-4 pointer-events-auto w-max">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">by</span>
                            <x-profile-preview :user="$product->author">
                                <div class="flex items-center gap-1.5 hover:opacity-80 transition-opacity cursor-pointer z-50">
                                    <img src="{{ $product->author->avatar ? asset('storage/' . $product->author->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($product->author->name).'&background=random' }}" class="w-4 h-4 rounded-full object-cover">
                                    <span class="text-xs font-medium text-zinc-300 hover:text-white">{{ $product->author->name }}</span>
                                </div>
                            </x-profile-preview>
                        </div>
                        </div>

                        {{-- Floating Badges (Minimalist) --}}
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if($product->is_elite_marketed)
                                <div class="bg-indigo-600/90 backdrop-blur-md text-white px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide flex items-center gap-1">
                                    <flux:icon.check-badge variant="mini" class="w-3 h-3" />
                                    <span>NEXACODE ELITE</span>
                                </div>
                            @endif
                            @if($product->price == 0)
                                <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md text-zinc-900 dark:text-white px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide border border-zinc-200 dark:border-zinc-700">FREE</div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col flex-grow">
                        <div class="flex items-center justify-between mb-1.5">
                            @if($product->category)
                                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                            <div class="flex items-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <flux:icon.star class="w-3 h-3 text-zinc-900 dark:text-white fill-zinc-900 dark:fill-white" />
                                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ number_format($product->avg_rating, 1) }}</span>
                                <span class="text-xs text-zinc-400 dark:text-zinc-600">({{ number_format($product->sales_count) }})</span>
                            </div>
                        </div>

                        <a href="{{ route('products.show', $product->slug) }}" class="block text-base font-semibold tracking-tight text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-1 mb-1 {{ $index === 0 ? 'sm:text-xl sm:mb-2' : '' }}">
                            {{ $product->name }}
                        </a>

                        <div class="mt-auto pt-3 flex items-center justify-between">
                            <div class="flex flex-col">
                                @if($product->is_on_sale)
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="text-[9px] font-bold tracking-widest text-white bg-rose-500 px-1.5 py-0.5 rounded-sm uppercase">Sale</span>
                                        <span class="text-xs text-zinc-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white flex items-baseline gap-1 {{ $index === 0 ? 'sm:text-2xl' : '' }}">
                                        <span class="text-sm font-medium text-zinc-400">Rp</span> {{ number_format($product->discounted_price, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white flex items-baseline gap-1 {{ $index === 0 ? 'sm:text-2xl' : '' }}">
                                        <span class="text-sm font-medium text-zinc-400">Rp</span> {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Micro-interaction reveal --}}
                            <a href="{{ route('products.show', $product->slug) }}" class="opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all duration-300 p-2 rounded-full border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                <flux:icon.arrow-right variant="mini" class="w-4 h-4 text-zinc-600 dark:text-zinc-300" />
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 flex flex-col items-center justify-center bg-zinc-50/50 dark:bg-zinc-900/20 rounded-[2rem] border border-dashed border-zinc-200 dark:border-zinc-800">
                    <div class="w-16 h-16 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center mb-5 shadow-sm ring-1 ring-zinc-200 dark:ring-zinc-700">
                        <flux:icon.magnifying-glass class="w-6 h-6 text-zinc-400" />
                    </div>
                    <flux:heading size="lg" class="font-medium text-zinc-900 dark:text-white">No products found</flux:heading>
                    <p class="text-zinc-500 dark:text-zinc-400 mt-2 max-w-sm text-center text-sm">Try broadening your filters or searching for something else.</p>
                    <flux:button wire:click="clearFilters" variant="subtle" class="mt-6 text-xs">Clear Filters</flux:button>
                </div>
            @endforelse
        </div>

        {{-- Interactive Skeleton Loading State (Triggered during Livewire updates) --}}
        <div wire:loading.flex wire:target="search, sort, selectCategory, min_price, max_price, min_rating, clearFilters" class="absolute inset-x-0 sm:px-4 xl:px-8 top-4 pt-4 z-10 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 xl:gap-8">
            @for ($i = 0; $i < 10; $i++)
                <div class="flex flex-col relative transition-all duration-300 {{ $i === 0 ? 'sm:col-span-2 sm:row-span-2' : '' }}">
                    {{-- Image Skeleton --}}
                    <div class="w-full bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded-[1.25rem] {{ $i === 0 ? 'aspect-video sm:aspect-auto sm:h-full min-h-[300px]' : 'aspect-[4/3]' }}"></div>
                    
                    {{-- Text Skeleton --}}
                    <div class="pt-4 flex flex-col flex-grow gap-3">
                        <div class="flex justify-between items-center">
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-1/4"></div>
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-1/6"></div>
                        </div>
                        <div class="h-5 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-3/4 mb-2"></div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-4 h-4 bg-zinc-200 dark:bg-zinc-800 rounded-full animate-pulse"></div>
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-1/3"></div>
                        </div>
                        <div class="mt-auto flex justify-between items-end pt-3">
                            <div class="h-6 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-1/3"></div>
                            <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-800 animate-pulse"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Load More Pagination --}}
        @if($hasMore)
            <div class="flex justify-center pt-12 pb-8">
                <button 
                    wire:click="loadMore" 
                    wire:loading.attr="disabled"
                    class="group relative inline-flex items-center gap-2 px-8 py-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-full font-medium text-sm text-zinc-700 dark:text-zinc-300 shadow-sm hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-700 transition-all active:scale-95"
                >
                    <flux:icon.arrow-down variant="mini" class="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors" />
                    <span wire:loading.remove>Load More Products</span>
                    <span wire:loading>Loading...</span>
                </button>
            </div>
        @endif
    </div>
    
    {{-- Custom Scrollbar Shadow for Categories --}}
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
