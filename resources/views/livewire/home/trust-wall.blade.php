<div class="h-full flex flex-col">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <div class="size-2 bg-emerald-500 rounded-full animate-ping"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Live Ecosystem Pulse</p>
        </div>
        <div class="flex -space-x-1">
            @foreach($activities->take(5) as $act)
                <div class="relative group/avatar">
                    <x-user-avatar :user="$act['user']" size="sm" class="ring-2 ring-white dark:ring-zinc-950 hover:scale-110 transition-transform relative z-[1]" />
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex-1 space-y-6 overflow-hidden relative">
        @foreach($activities as $activity)
            <div class="flex gap-4 animate-fade-in-up" style="animation-delay: {{ $loop->index * 150 }}ms">
                <div class="size-10 shrink-0 rounded-xl bg-{{ $activity['color'] }}-500/10 flex items-center justify-center text-{{ $activity['color'] }}-500">
                    <flux:icon name="{{ $activity['icon'] }}" variant="mini" class="size-5" />
                </div>
                
                <div class="flex-1 min-w-0 space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                {{ $activity['user']->name }}
                            </p>
                                <x-community-badge :user="$activity['user']" size="sm" class="scale-75 origin-left" />
                        </div>
                        <span class="text-[10px] font-medium text-zinc-400 tabular-nums">
                            {{ $activity['time']->diffForHumans(short: true) }}
                        </span>
                    </div>
                    
                    <p class="text-[11px] leading-relaxed text-zinc-500 dark:text-zinc-400">
                        @if($activity['type'] === 'sale')
                            Baru saja membeli <span class="font-bold text-zinc-700 dark:text-zinc-200">{{ $activity['product_name'] }}</span>
                        @elseif($activity['type'] === 'review')
                            Memberikan rating <span class="font-bold text-amber-500">{{ $activity['rating'] }} Bintang</span> pada <span class="font-bold text-zinc-700 dark:text-zinc-200">{{ $activity['product_name'] }}</span>
                        @elseif($activity['type'] === 'author')
                            Resmi bergabung sebagai <span class="font-bold text-indigo-500">Author</span> platform.
                        @endif
                    </p>
                </div>
            </div>
        @endforeach

        {{-- Bottom Gradient Fade --}}
        <div class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-white dark:from-zinc-950 to-transparent pointer-events-none"></div>
    </div>

    <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="text-center">
                <div class="text-lg font-black text-zinc-900 dark:text-white">{{ number_format(\App\Models\Order::count()) }}</div>
                <div class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Orders</div>
            </div>
            <flux:separator vertical />
            <div class="text-center">
                <div class="text-lg font-black text-zinc-900 dark:text-white">{{ number_format(\App\Models\Review::count()) }}</div>
                <div class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Reviews</div>
            </div>
        </div>
        
        <flux:button variant="ghost" size="sm" class="font-black uppercase tracking-[0.2em] text-[9px]">Connect Now</flux:button>
    </div>
</div>
