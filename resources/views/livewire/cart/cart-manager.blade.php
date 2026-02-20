<div class="relative">
    <flux:modal name="cart-modal" class="md:w-[500px]" variant="flyout">
        <div class="space-y-6 flex flex-col h-full">
            <div>
                <flux:heading size="xl">Keranjang Belanja</flux:heading>
                <flux:subheading>Kamu punya {{ $items->count() }} item di keranjang.</flux:subheading>
            </div>

            <div class="flex-1 overflow-y-auto min-h-0 space-y-4 pr-2">
                @forelse($items as $item)
                    <div class="flex gap-4 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 group relative">
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-zinc-200 shrink-0">
                            @if($item->bundle_id)
                                <img src="{{ $item->bundle->thumbnail_url }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $item->product->thumbnail_url }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if($item->bundle_id)
                                <div class="font-bold text-sm truncate">{{ $item->bundle->name }}</div>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <flux:badge size="sm" variant="subtle" color="emerald">Bundle</flux:badge>
                                    <div class="text-[10px] text-zinc-500">{{ $item->bundle->products->count() }} items</div>
                                </div>
                                <div class="mt-1 font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($item->bundle->price, 0, ',', '.') }}
                                </div>
                            @else
                                <div class="font-bold text-sm truncate">{{ $item->product->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $item->product->author->name }}</div>
                                <div class="mt-1 font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        <button wire:click="removeItem({{ $item->id }})" class="absolute top-2 right-2 p-1 text-zinc-400 hover:text-red-500 transition-colors">
                            <flux:icon.x-mark class="size-4" />
                        </button>
                    </div>
                @empty
                    <div class="py-12 text-center text-zinc-500 flex flex-col items-center gap-3">
                        <flux:icon.shopping-cart class="size-12 opacity-20" />
                        <p>Keranjang kamu masih kosong.</p>
                        <flux:button href="{{ route('products.index') }}" variant="ghost" size="sm">Mulai Belanja</flux:button>
                    </div>
                @endforelse
            </div>

            @if($items->count() > 0)
                <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 space-y-4">
                    <div class="flex justify-between items-center px-2">
                        <div class="text-zinc-500">Total Harga</div>
                        <div class="text-xl font-black">Rp {{ number_format($total, 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <flux:button wire:click="clearCart" variant="ghost" color="red">Kosongkan</flux:button>
                        <flux:button href="{{ route('checkout.index') }}" variant="primary" color="indigo">Checkout</flux:button>
                    </div>
                    
                    <p class="text-[10px] text-center text-zinc-400">
                        Pajak dan biaya layanan akan dihitung di halaman checkout.
                    </p>
                </div>
            @endif
        </div>
    </flux:modal>

    {{-- Trigger Component (Bisa diletakkan di Navbar) --}}
    <button x-on:click="Flux.modal('cart-modal').show()" class="relative p-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
        <flux:icon.shopping-cart class="size-6" />
        @if($items->count() > 0)
            <span class="absolute top-1 right-1 size-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white dark:border-zinc-950">
                {{ $items->count() }}
            </span>
        @endif
    </button>
</div>
