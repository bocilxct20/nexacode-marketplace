<div wire:init="loadData">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Ulasan</flux:subheading>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Rata-rata Rating</flux:subheading>
                <div class="text-2xl font-bold text-amber-500 flex items-center gap-2">
                    {{ number_format($stats['average_rating'], 1) }}
                    <flux:icon.star variant="mini" class="w-5 h-5 fill-amber-500" />
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Dibalas</flux:subheading>
                <div class="text-2xl font-bold text-emerald-500">{{ $stats['replied'] }}</div>
            </div>
        </flux:card>
    </div>

    <flux:card>
        <div class="mb-6 flex gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari ulasan..." icon="magnifying-glass" class="flex-1" />
            <flux:select wire:model.live="ratingFilter" class="w-48">
                <option value="all">Semua Rating</option>
                <option value="5">Bintang 5</option>
                <option value="4">Bintang 4</option>
                <option value="3">Bintang 3</option>
                <option value="2">Bintang 2</option>
                <option value="1">Bintang 1</option>
            </flux:select>
        </div>

        <flux:table :paginate="$reviews">
            <flux:table.columns>
                <flux:table.column>Produk</flux:table.column>
                <flux:table.column>Pembeli</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'rating'" :direction="$sortDirection" wire:click="sort('rating')">Rating</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Tanggal</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Skeleton Rows --}}
                @if (!$readyToLoad)
                    @for ($i = 0; $i < 5; $i++)
                        <flux:table.row>
                            <flux:table.cell><div class="h-4 w-48 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-12 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell align="right"><div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                        </flux:table.row>
                    @endfor
                @else

                    @forelse ($reviews as $review)
                        <flux:table.row :key="$review->id">
                            <flux:table.cell variant="strong" class="truncate max-w-[200px]">{{ $review->product->name }}</flux:table.cell>
                            <flux:table.cell>{{ $review->user->name }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-1">
                                    <span class="font-bold tabular-nums">{{ $review->rating }}</span>
                                    <flux:icon.star variant="mini" class="w-3 h-3 text-amber-400 fill-amber-400" />
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $review->created_at->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell align="right">
                                <flux:button variant="ghost" size="sm" icon="chat-bubble-left-right" inset="top bottom" />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500 py-12 italic">
                                Belum ada ulasan ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
