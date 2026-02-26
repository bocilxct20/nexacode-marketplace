<div>
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div>
            <flux:heading size="xl" class="font-bold mb-2">
                @if($collection === 'best_sellers')
                    Top Selling Marketplace Items
                @else
                    Freshly Released Scripts & Themes
                @endif
            </flux:heading>
            <flux:subheading size="lg">The most popular and highly-rated items from our global community.</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button.group>
                <flux:button 
                    wire:click="setCollection('best_sellers')" 
                    :variant="$collection === 'best_sellers' ? 'primary' : 'ghost'" 
                    size="sm"
                >Best Sellers</flux:button>
                <flux:button 
                    wire:click="setCollection('new_releases')" 
                    :variant="$collection === 'new_releases' ? 'primary' : 'ghost'" 
                    size="sm"
                >New Releases</flux:button>
            </flux:button.group>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 relative">
        {{-- Loading Skeleton Overlay --}}
        <div wire:loading wire:target="setCollection" class="absolute inset-x-0 inset-y-0 z-10 bg-white/60 dark:bg-zinc-950/60 backdrop-blur-sm flex items-start justify-center animate-in fade-in duration-300">
            <flux:skeleton.group animate="shimmer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
                @foreach(range(1, 6) as $i)
                    <flux:card class="p-0 overflow-hidden flex flex-col h-full border-zinc-200 dark:border-zinc-800 rounded-2xl">
                        <flux:skeleton class="aspect-video w-full" />
                        <div class="p-6 flex-1 flex flex-col space-y-6">
                            <div class="flex justify-between items-center">
                                <flux:skeleton class="h-5 w-16 rounded-md" />
                                <div class="flex gap-1">
                                    <flux:skeleton class="size-4 rounded-sm" />
                                    <flux:skeleton class="h-4 w-12 rounded-sm" />
                                </div>
                            </div>
                            <div class="space-y-3">
                                <flux:skeleton.line size="lg" />
                                <div class="space-y-2">
                                    <flux:skeleton.line />
                                    <flux:skeleton.line class="w-3/4" />
                                </div>
                            </div>
                            <div class="mt-auto pt-6 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <flux:skeleton class="size-7 rounded-lg" />
                                    <flux:skeleton.line class="w-20" />
                                </div>
                                <flux:skeleton.line class="w-24" />
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </flux:skeleton.group>
        </div>

        @forelse($products as $product)
            <flux:card class="overflow-hidden group flex flex-col h-full border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-shadow duration-300 rounded-2xl">
                <div class="aspect-video bg-zinc-200 dark:bg-zinc-800 relative overflow-hidden">
                    <img 
                        src="{{ $product->thumbnail_url ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=800&auto=format&fit=crop' }}" 
                        alt="{{ $product->name }}" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        loading="lazy"
                        width="800"
                        height="450"
                    />
                    <div class="absolute top-4 right-4">
                        <flux:badge color="emerald" variant="solid" class="shadow-lg uppercase tracking-tighter font-bold">
                            {{ $collection === 'best_sellers' ? 'TOP SELLER' : 'NEW' }}
                        </flux:badge>
                    </div>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <flux:button href="{{ route('products.show', $product->slug) }}" variant="primary" size="sm" class="rounded-xl">
                            <flux:icon name="eye" variant="micro" class="mr-2" /> Quick View
                        </flux:button>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <flux:badge color="emerald" variant="subtle" size="sm" class="uppercase tracking-widest font-black text-[9px] px-2 py-0.5">
                            {{ $product->tags->first()->name ?? 'Script' }}
                        </flux:badge>
                        <div class="flex items-center gap-1.5 bg-zinc-50 dark:bg-zinc-800/50 px-2 py-1 rounded-lg border border-zinc-100 dark:border-zinc-700/50">
                            <flux:icon.star variant="mini" class="text-amber-400 size-3" />
                            <span class="text-[10px] font-black text-zinc-900 dark:text-white">{{ number_format($product->avg_rating, 1) }}</span>
                            <span class="text-[10px] font-bold text-zinc-400">({{ $product->reviews_count }})</span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <flux:heading size="lg" class="font-bold leading-tight line-clamp-2">
                            <flux:link href="{{ route('products.show', $product->slug) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors block">
                                {{ $product->name }}
                            </flux:link>
                        </flux:heading>
                    </div>
                    <div class="mt-auto pt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <x-profile-preview :user="$product->author">
                            <div class="flex items-center gap-2 group/author cursor-pointer">
                                <x-user-avatar :user="$product->author" size="xs" class="shadow-sm transition-all group-hover/author:ring-emerald-500/30" />
                                <div class="flex items-center gap-1.5">
                                    <flux:text size="xs" class="font-bold group-hover/author:text-emerald-600 transition-colors text-zinc-900 dark:text-white">@ {{ $product->author->name }}</flux:text>
                                        <x-community-badge :user="$product->author" size="sm" class="scale-75 origin-left" />
                                </div>
                            </div>
                        </x-profile-preview>

                        <div class="flex flex-col items-end shrink-0">
                            @if($product->is_on_sale)
                                <div class="flex items-center gap-1.5 mb-1">
                                    <flux:badge color="cyan" variant="solid" size="sm" class="px-1.5 py-0.5 font-black text-[8px] scale-90 origin-right">FLASH</flux:badge>
                                    <flux:text size="xs" class="line-through text-zinc-400">Rp {{ number_format($product->price, 0, ',', '.') }}</flux:text>
                                </div>
                                <div class="flex items-baseline gap-0.5 text-emerald-600 dark:text-emerald-400 tabular-nums">
                                    <span class="text-[10px] font-bold">Rp</span>
                                    <span class="text-lg font-black">{{ number_format($product->discounted_price, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="flex items-baseline gap-0.5 text-zinc-900 dark:text-white tabular-nums">
                                    <span class="text-[10px] font-bold text-zinc-500">Rp</span>
                                    <span class="text-lg font-black">{{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-zinc-500">No items found in this collection.</p>
            </div>
        @endforelse
    </div>
</div>
