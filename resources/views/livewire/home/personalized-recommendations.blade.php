<section class="py-16 bg-white dark:bg-zinc-950">
    <flux:container>
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-4">
                <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
                <div>
                    <flux:heading size="xl" class="font-black tracking-tight uppercase">{{ $title }}</flux:heading>
                    <flux:text class="text-zinc-500">Curated picks just for your development needs.</flux:text>
                </div>
            </div>
            <flux:button variant="ghost" href="{{ route('products.index') }}" icon-trailing="chevron-right">Explore More</flux:button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <flux:card href="{{ route('products.show', $product->slug) }}" class="p-0 overflow-hidden group border-zinc-200 dark:border-zinc-800 hover:shadow-2xl transition-all duration-500 rounded-[2rem] flex flex-col h-full">
                    <div class="aspect-[4/3] relative overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                        
                        {{-- Overlay on Hover --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <flux:button variant="primary" size="sm" class="bg-white text-black hover:bg-zinc-100 border-none rounded-xl">View Details</flux:button>
                        </div>

                        {{-- Category Badge --}}
                        <div class="absolute top-4 left-4">
                            <flux:badge size="sm" color="zinc" class="backdrop-blur-md bg-white/20 text-white border-none font-bold text-[9px] uppercase tracking-widest px-2 py-0.5">
                                {{ $product->category->name ?? 'Digital' }}
                            </flux:badge>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <flux:heading size="sm" class="font-bold line-clamp-1 mb-3 group-hover:text-emerald-500 transition-colors uppercase tracking-tight">
                            {{ $product->name }}
                        </flux:heading>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center gap-2">
                                <x-user-avatar :user="$product->author" size="xs" class="shadow-sm" />
                                <span class="text-[10px] font-bold text-zinc-500 uppercase">@ {{ $product->author->name }}</span>
                                    <x-community-badge :user="$product->author" size="sm" class="scale-75 origin-left" />
                            </div>
                            <div class="text-lg font-black text-zinc-900 dark:text-white tabular-nums">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </flux:container>
</section>
