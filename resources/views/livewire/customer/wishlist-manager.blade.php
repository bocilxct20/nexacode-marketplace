<div wire:init="load">
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Wishlist</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <flux:heading size="xl">My Wishlist</flux:heading>
    <flux:subheading>Save products you're interested in for later.</flux:subheading>

    <flux:separator variant="subtle" class="my-8" />

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @if(!$readyToLoad)
            <flux:card class="animate-pulse">
                <div class="space-y-2">
                    <flux:skeleton class="h-3 w-20" />
                    <flux:skeleton class="h-8 w-16" />
                </div>
            </flux:card>
        @else
            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Saved Items</flux:subheading>
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                </div>
            </flux:card>
        @endif
    </div>

    {{-- Wishlist Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @if(!$readyToLoad)
            @for($i=0; $i<6; $i++)
                <flux:card class="animate-pulse">
                    <flux:skeleton class="aspect-video rounded-lg mb-4" />
                    <div class="space-y-3">
                        <div class="space-y-2">
                            <flux:skeleton class="h-5 w-3/4" />
                            <flux:skeleton class="h-4 w-1/2" />
                        </div>
                        <flux:skeleton class="h-8 w-24" />
                        <flux:separator variant="subtle" />
                        <div class="flex gap-2">
                            <flux:skeleton class="h-9 flex-1 rounded-md" />
                            <flux:skeleton class="h-9 w-9 rounded-md" />
                        </div>
                    </div>
                </flux:card>
            @endfor
        @else
            @forelse($wishlist as $item)
                <flux:card>
                    {{-- Product Thumbnail --}}
                    <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 rounded-lg mb-4 overflow-hidden relative group">
                        <img src="{{ Storage::url($item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    {{-- Product Info --}}
                    <div class="space-y-3">
                        <div>
                            <h3 class="font-bold text-lg truncate">{{ $item->product->name }}</h3>
                            <p class="text-[10px] uppercase font-black text-zinc-400 tracking-widest">{{ $item->product->category->name ?? 'Premium Item' }}</p>
                        </div>

                        <div class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                            Rp {{ number_format($item->product->price, 0, ',', '.') }}
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <flux:button href="{{ route('products.show', $item->product->slug) }}" variant="primary" size="sm" class="flex-1 font-bold">
                                View Product
                            </flux:button>
                            <flux:button 
                                wire:click="removeFromWishlist({{ $item->id }})"
                                wire:confirm="Remove this item from wishlist?"
                                variant="ghost" 
                                size="sm"
                                square
                                class="text-red-600"
                            >
                                <flux:icon.trash class="w-4 h-4" />
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @empty
                <div class="col-span-full">
                    <flux:card class="text-center py-20 px-6">
                        <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <flux:icon.heart class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                        </div>
                        <flux:heading size="xl" class="mb-2">Your wishlist is empty</flux:heading>
                        <flux:subheading class="mb-8">Start adding products you love to your wishlist.</flux:subheading>
                        <flux:button href="{{ route('products.index') }}" variant="primary" icon="sparkles">Browse Marketplace</flux:button>
                    </flux:card>
                </div>
            @endforelse
        @endif
    </div>

    {{-- Pagination --}}
    @if(method_exists($wishlist, 'hasPages') && $wishlist->hasPages())
        <div class="mt-6">
            {{ $wishlist->links() }}
        </div>
    @endif
</div>
