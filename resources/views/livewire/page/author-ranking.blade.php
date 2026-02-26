<div class="py-24 relative overflow-hidden bg-white dark:bg-zinc-950">
    {{-- Background Decorative Glows --}}
    <div class="absolute top-0 left-1/4 size-[500px] bg-indigo-500/[0.03] blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 size-[500px] bg-amber-500/[0.03] blur-[120px] rounded-full pointer-events-none"></div>

    <flux:container>
        <div class="mb-12">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item separator="slash">Hall of Fame</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="mb-20 flex flex-col items-center text-center">
            <flux:heading size="2xl" class="text-6xl font-black mb-6 uppercase tracking-tighter">Hall of Fame</flux:heading>
            <flux:text class="text-xl text-zinc-500 max-w-2xl mx-auto">Mengenal para kreator paling berpengaruh di ekosistem NexaCode.</flux:text>
        </div>

        {{-- Loading State --}}
        <div wire:loading.grid class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 w-full">
            @for($i = 0; $i < 4; $i++)
                <div class="p-8 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 flex flex-col items-center gap-6 animate-pulse">
                    <div class="size-24 rounded-3xl bg-zinc-100 dark:bg-zinc-800"></div>
                    <div class="h-6 w-32 bg-zinc-100 dark:bg-zinc-800 rounded-lg"></div>
                    <div class="h-4 w-48 bg-zinc-100 dark:bg-zinc-800 rounded-lg"></div>
                    <div class="grid grid-cols-2 gap-3 w-full">
                        <div class="h-16 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl"></div>
                        <div class="h-16 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Main List --}}
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($authors as $author)
                @php
                    $rank = ($authors->currentPage() - 1) * $authors->perPage() + $loop->index + 1;
                    $isAdmin = $author->isAdmin();
                    
                    $cardClass = 'p-8 flex flex-col items-center text-center gap-6 transition-all duration-300 cursor-pointer group rounded-[2.5rem] border bg-white dark:bg-zinc-900 shadow-sm hover:-translate-y-2 relative overflow-hidden';
                    
                    if ($rank === 1) {
                        $cardClass .= ' border-amber-500/40 bg-white dark:bg-zinc-900 shadow-[0_0_50px_rgba(251,191,36,0.1)] hover:shadow-[0_0_60px_rgba(251,191,36,0.25)] elite-glow-gold-hover z-10';
                    } elseif ($rank === 2) {
                        $cardClass .= ' border-zinc-200 dark:border-zinc-800 hover:shadow-xl';
                    } elseif ($rank === 3) {
                        $cardClass .= ' border-orange-400/30 hover:shadow-xl';
                    } else {
                        $cardClass .= ' border-zinc-100 dark:border-zinc-800/50 hover:shadow-lg';
                    }
                @endphp

                <flux:card href="{{ route('authors.show', $author->username ?: $author->id) }}" 
                    class="{{ $cardClass }} animate-fade-in-up" 
                    style="animation-delay: {{ $loop->index * 50 }}ms"
                >

                    <div class="relative shrink-0">
                        <x-user-avatar :user="$author" size="xl" thickness="0" border="false" class="rounded-3xl shadow-md transition-all group-hover:scale-105" />
                        
                        {{-- Rank Badge --}}
                        <div class="absolute -top-3 -right-3 size-11 text-white flex items-center justify-center rounded-2xl font-black shadow-lg z-[2] transition-transform group-hover:scale-110
                            @if($rank === 1) bg-amber-500 text-amber-950 border-2 border-white dark:border-zinc-800
                            @elseif($rank === 2) bg-zinc-400 text-zinc-950 border-2 border-white dark:border-zinc-800
                            @elseif($rank === 3) bg-orange-500 text-white border-2 border-white dark:border-zinc-800
                            @else bg-zinc-900 dark:bg-black text-[10px]
                            @endif">
                            @if($rank === 1)
                                <flux:icon name="star" variant="solid" class="size-6 animate-pulse" />
                            @else
                                {{ $rank }}
                            @endif
                        </div>
                    </div>

                    <div class="w-full space-y-5 relative">
                        <div class="space-y-2">
                            <flux:heading size="lg" class="text-xl font-bold group-hover:text-amber-600 dark:group-hover:text-amber-500 transition-colors">
                                {{ $author->name }}
                            </flux:heading>
                            
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                    <x-community-badge :user="$author" size="sm" />
                                
                                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Level {{ $author->level }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-100 dark:border-zinc-800/50">
                                <span class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Items</span>
                                <span class="text-lg font-black text-zinc-900 dark:text-white leading-none">{{ number_format($author->products_count) }}</span>
                            </div>
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-100 dark:border-zinc-800/50">
                                <span class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Impact</span>
                                <span class="text-lg font-black text-zinc-900 dark:text-white leading-none">{{ number_format($author->ranking_score, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 group-hover:text-amber-500 transition-colors">
                            <span>Explore Profile</span>
                            <flux:icon name="chevron-right" variant="mini" class="size-3 translate-x-0 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-20 flex justify-center">
            @if($authors->hasPages())
                <div class="bg-white dark:bg-zinc-900 p-2 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800">
                    {{ $authors->links() }}
                </div>
            @endif
        </div>
    </flux:container>
</div>
