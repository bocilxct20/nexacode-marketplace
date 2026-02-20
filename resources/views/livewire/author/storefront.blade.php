<div>
    {{-- Search & Filters --}}
    <div class="flex flex-col md:flex-row gap-4 mb-10">
        <div class="flex-1">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari produk penulis..." 
                icon="magnifying-glass"
                class="bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800"
            />
        </div>
        
        <div class="flex items-center gap-2">
            <flux:select wire:model.live="selectedCategory" class="w-48">
                <option value="">Semua Kategori</option>
                @foreach($authorTags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="sort" class="w-48">
                <option value="latest">Terbaru</option>
                <option value="popular">Paling Laku</option>
                <option value="rating">Rating Tertinggi</option>
                <option value="price_low">Harga: Rendah ke Tinggi</option>
                <option value="price_high">Harga: Tinggi ke Rendah</option>
            </flux:select>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <flux:card class="p-0 overflow-hidden group border-zinc-200 dark:border-zinc-800 hover:shadow-xl transition-all duration-300">
                <div class="aspect-[4/3] relative overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                    <img src="{{ $product->thumbnail }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <flux:button href="{{ route('products.show', $product->slug) }}" variant="primary" size="sm">Lihat Detail</flux:button>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">{{ $product->tags->first()->name ?? 'Script' }}</span>
                        <div class="flex items-center gap-1">
                            <flux:icon.star variant="mini" class="text-amber-400" />
                            <span class="text-xs font-bold">{{ number_format($product->avg_rating, 1) }}</span>
                        </div>
                    </div>
                    <flux:heading size="sm" class="line-clamp-1 mb-2">
                        <flux:link href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</flux:link>
                    </flux:heading>
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="text-xs text-zinc-500">{{ number_format($product->sales_count) }} Terjual</div>
                        <div class="text-lg font-black text-zinc-900 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full py-20 text-center">
                <flux:icon.magnifying-glass class="w-12 h-12 mx-auto text-zinc-300 mb-4" />
                <flux:heading size="lg">Produk tidak ditemukan</flux:heading>
                <p class="text-zinc-500 mt-2">Coba sesuaikan filter atau kata kunci pencarian kamu.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $products->links() }}
    </div>
</div>
