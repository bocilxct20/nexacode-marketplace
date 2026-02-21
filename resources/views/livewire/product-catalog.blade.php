<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full md:w-1/3">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                type="search" 
                placeholder="Search products..." 
                icon="magnifying-glass"
            />
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
            <flux:select wire:model.live="sort" class="w-40">
                <option value="newest">Newest</option>
                <option value="popular">Popular</option>
                <option value="rating">Top Rated</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
            </flux:select>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-2"></div>

            <div class="flex gap-2">
                <flux:button 
                    wire:click="selectCategory(null)" 
                    :variant="$selectedCategory === null ? 'filled' : 'ghost'"
                    size="sm"
                >
                    All
                </flux:button>
                @foreach($categories as $category)
                    <flux:button 
                        wire:click="selectCategory({{ $category->id }})" 
                        :variant="$selectedCategory == $category->id ? 'filled' : 'ghost'"
                        size="sm"
                    >
                        @if($category->icon && (str_starts_with($category->icon, 'storage/') || str_starts_with($category->icon, 'http')))
                            <div class="mr-2 w-3.5 h-3.5 bg-current {{ $selectedCategory == $category->id ? 'text-white' : 'text-zinc-500' }}" 
                                 style="mask-image: url('{{ asset($category->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($category->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                            </div>
                        @elseif($category->icon && str_starts_with($category->icon, 'lucide-'))
                            <x-dynamic-component :component="$category->icon" class="mr-2 w-3.5 h-3.5" />
                        @else
                            <flux:icon :icon="$category->icon ?: 'package'" variant="mini" class="mr-2 w-3.5 h-3.5" />
                        @endif
                        {{ $category->name }}
                    </flux:button>
                @endforeach
            </div>
        </div>
    </div>

    @if($currentCategory)
        <div class="flex items-center gap-2">
            <flux:heading size="xl">Exploring {{ $currentCategory->name }}</flux:heading>
            <flux:button variant="ghost" size="sm" wire:click="selectCategory(null)" icon="x-mark">Clear Category</flux:button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <flux:card class="group overflow-hidden flex flex-col p-0 border-none bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow rounded-2xl">
                <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-[4/3] overflow-hidden rounded-2xl">
                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    
                    {{-- Status Badges on Thumbnail --}}
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if($product->author->isElite())
                            <div class="bg-amber-500 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg flex items-center gap-1 border border-amber-400">
                                <flux:icon.check-badge variant="mini" class="w-3 h-3" />
                                <span>ELITE</span>
                            </div>
                        @elseif($product->author->isPro())
                            <div class="bg-indigo-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg flex items-center gap-1 border border-indigo-500">
                                <flux:icon.check-badge variant="mini" class="w-3 h-3" />
                                <span>PRO AUTHOR</span>
                            </div>
                        @endif

                        @if($product->is_elite_marketed)
                            <div class="bg-emerald-500 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg flex items-center gap-1 border border-emerald-400">
                                <flux:icon.bolt variant="mini" class="w-3 h-3" />
                                <span>VERIFIED</span>
                            </div>
                        @endif
                    </div>

                    @if($product->price == 0)
                        <div class="absolute top-2 right-2 bg-zinc-900/80 backdrop-blur-md text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-sm border border-white/20">FREE ITEM</div>
                    @endif
                </a>

                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex flex-wrap gap-1 mb-3">
                        @if($product->category)
                            <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
                                @if($product->category->icon && (str_starts_with($product->category->icon, 'storage/') || str_starts_with($product->category->icon, 'http')))
                                    <div class="w-3 h-3 bg-current" 
                                         style="mask-image: url('{{ asset($product->category->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($product->category->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                                    </div>
                                @elseif($product->category->icon && str_starts_with($product->category->icon, 'lucide-'))
                                    <x-dynamic-component :component="$product->category->icon" class="w-3 h-3" />
                                @else
                                    <flux:icon :icon="$product->category->icon ?: 'package'" variant="mini" class="w-3 h-3" />
                                @endif
                                {{ $product->category->name }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('products.show', $product->slug) }}" class="block mb-2 text-lg font-bold text-zinc-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors line-clamp-1">
                        {{ $product->name }}
                    </a>

                    <x-profile-preview :user="$product->author">
                        <div class="flex items-center gap-2 mb-4 group/author cursor-pointer">
                            <img src="{{ $product->author->avatar ? asset('storage/' . $product->author->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($product->author->name).'&background=random' }}" class="w-5 h-5 rounded-full object-cover border border-zinc-200 dark:border-zinc-800">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 group-hover/author:text-indigo-600 transition-colors">by {{ $product->author->name }}</span>
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

                    <div class="mt-auto pt-4 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex flex-col">
                            @if($product->is_on_sale)
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="text-[9px] font-black text-white bg-cyan-500 px-1.5 py-0.5 rounded uppercase tracking-widest">FLASH</span>
                                    <span class="text-[10px] text-zinc-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-xl font-black text-zinc-900 dark:text-white tabular-nums">
                                    <span class="text-xs text-emerald-500">Rp</span> {{ number_format($product->discounted_price, 0, ',', '.') }}
                                </div>
                            @else
                                <span class="text-xl font-black text-zinc-900 dark:text-white underline decoration-emerald-500/30">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @endif
                            <div class="flex items-center gap-1">
                                <flux:icon.star class="w-3 h-3 text-amber-400 fill-amber-400" />
                                <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400">{{ number_format($product->avg_rating, 1) }}</span>
                                <span class="text-[11px] text-zinc-400 mx-1">•</span>
                                <span class="text-[11px] text-zinc-500">{{ $product->sales_count }} sales</span>
                            </div>
                        </div>

                        <flux:button href="{{ route('products.show', $product->slug) }}" variant="ghost" size="sm" icon="eye"></flux:button>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                    <flux:icon.magnifying-glass class="w-8 h-8 text-zinc-400" />
                </div>
                <flux:heading size="lg">No products found</flux:heading>
                <p class="text-zinc-500 dark:text-zinc-400 mt-2 max-w-md text-center">We couldn't find anything matching your search filters. Try adjusting your query or filters.</p>
                <flux:button wire:click="clearFilters" variant="ghost" class="mt-4">Reset all filters</flux:button>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $products->links() }}
    </div>
</div>
