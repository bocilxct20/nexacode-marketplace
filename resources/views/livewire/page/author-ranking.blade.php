<div class="py-24">
    <flux:container>
        <div class="mb-12">
            <flux:heading size="2xl" class="font-black mb-2 uppercase tracking-tighter">Author Rankings</flux:heading>
            <flux:text class="text-lg text-zinc-500">Kumpulan kreator terbaik yang membangun ekosistem NexaCode.</flux:text>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($authors as $author)
                <flux:card class="p-6 flex flex-col items-center text-center gap-4 hover:border-indigo-500 transition-all cursor-pointer group rounded-3xl border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm hover:shadow-xl hover:shadow-indigo-500/5">
                    <div class="relative shrink-0">
                        <flux:avatar size="xl" :src="$author->avatar_url" class="rounded-2xl border-4 border-transparent group-hover:border-indigo-500 transition-all" />
                        <div class="absolute -top-2 -right-2 size-8 bg-indigo-600 text-white text-xs flex items-center justify-center rounded-full font-bold border-4 border-white dark:border-zinc-900 shadow-lg">
                            {{ ($authors->currentPage() - 1) * $authors->perPage() + $loop->iteration }}
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-2 gap-1.5">
                            <flux:heading size="lg" class="font-bold truncate group-hover:text-indigo-600 transition-colors">{{ $author->name }}</flux:heading>
                            <flux:badge color="{{ $author->tier_badge->color }}" size="sm" variant="solid" class="text-[9px] uppercase font-black tracking-widest px-2 py-0.5 rounded-lg">
                                {{ $author->tier_badge->label }}
                            </flux:badge>
                        </div>
                        <flux:text size="sm" class="text-zinc-500 mb-4 line-clamp-1 italic">Level {{ $author->level }} Master Author</flux:text>
                        <div class="grid grid-cols-2 gap-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Items</span>
                                <span class="text-sm font-bold">{{ $author->products_count }}</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Trust Score</span>
                                <span class="text-sm font-bold text-emerald-600">{{ number_format($author->ranking_score, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <flux:button variant="ghost" class="w-full mt-4 rounded-xl" href="{{ route('authors.show', $author->username ?: $author->id) }}">
                            View Profile
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
        <div class="mt-12">
            {{ $authors->links() }}
        </div>
    </flux:container>
</div>
