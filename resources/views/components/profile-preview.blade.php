@props(['user', 'isAuthor' => false])

<flux:dropdown hover position="bottom" align="start" offset="-16" gap="10">
    <button type="button" class="flex items-center gap-3 group">
        {{ $slot }}
    </button>

    <flux:popover class="rounded-2xl shadow-2xl p-0 min-w-[300px] max-w-[300px] bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 overflow-hidden">

        {{-- Header --}}
        <div class="relative px-5 pt-6 pb-5 flex items-start justify-between gap-3">
            <div class="flex items-center gap-3.5 min-w-0">
                <flux:avatar
                    size="lg"
                    :name="$user->name"
                    :src="$user->avatar ? asset('storage/' . $user->avatar) : null"
                    :initials="$user->initials"
                    class="rounded-xl shrink-0"
                />
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-black text-sm text-zinc-900 dark:text-white truncate leading-snug">{{ $user->name }}</span>
                        @if($user->isAuthor())
                            <flux:badge
                                color="{{ $user->tier_badge->color }}"
                                size="sm"
                                variant="solid"
                                class="text-[9px] uppercase font-black tracking-widest px-1.5 py-0.5 rounded-md shrink-0 leading-none flex items-center gap-0.5"
                            >
                                <flux:icon :icon="$user->tier_badge->icon" variant="mini" class="w-2.5 h-2.5" />
                                {{ $user->tier_badge->label }}
                            </flux:badge>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 mt-1.5">
                        <span class="text-xs text-zinc-400 font-medium truncate">{{ $user->username ?? $user->id }}</span>
                        @if($user->isAuthor())
                            <flux:icon.check-badge variant="mini" class="text-indigo-500 w-3.5 h-3.5 shrink-0" />
                        @endif
                        @if(auth()->check() && $user->followers->contains(auth()->user()))
                            <flux:badge size="sm" color="zinc" variant="outline" class="text-[9px] uppercase font-black tracking-tighter ml-1 shrink-0">Follows you</flux:badge>
                        @endif
                    </div>
                </div>
            </div>

            @if(auth()->check() && auth()->id() !== $user->id)
                <div class="shrink-0">
                    @livewire('follow-button', ['authorId' => $user->id], key('hover-follow-'.$user->id))
                </div>
            @endif
        </div>

        @if($user->bio)
            <div class="px-5 pb-3">
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed italic line-clamp-2">"{{ $user->bio }}"</p>
            </div>
        @endif

        {{-- Stats bar --}}
        <div class="grid grid-cols-4 border-t border-zinc-100 dark:border-zinc-800">
            <div class="flex flex-col items-center py-3 px-2 border-r border-zinc-100 dark:border-zinc-800">
                <flux:icon.shield-check variant="mini" class="text-emerald-500 w-3.5 h-3.5 mb-1" />
                <span class="text-[11px] font-black text-zinc-800 dark:text-white leading-none">{{ number_format($user->ranking_score, 0, ',', '.') }}</span>
                <span class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold mt-0.5">Trust</span>
            </div>
            <div class="flex flex-col items-center py-3 px-2 border-r border-zinc-100 dark:border-zinc-800">
                <flux:icon.academic-cap variant="mini" class="text-indigo-500 w-3.5 h-3.5 mb-1" />
                <span class="text-[11px] font-black text-zinc-800 dark:text-white leading-none">Lv.{{ $user->level }}</span>
                <span class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold mt-0.5">Mastery</span>
            </div>
            <div class="flex flex-col items-center py-3 px-2 border-r border-zinc-100 dark:border-zinc-800">
                <flux:icon.users variant="mini" class="text-sky-500 w-3.5 h-3.5 mb-1" />
                <span class="text-[11px] font-black text-zinc-800 dark:text-white leading-none">{{ number_format($user->followers_count ?? $user->followers->count()) }}</span>
                <span class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold mt-0.5">Fans</span>
            </div>
            <div class="flex flex-col items-center py-3 px-2">
                <flux:icon.cube variant="mini" class="text-amber-500 w-3.5 h-3.5 mb-1" />
                <span class="text-[11px] font-black text-zinc-800 dark:text-white leading-none">{{ number_format($user->products_count ?? $user->products->count()) }}</span>
                <span class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold mt-0.5">Items</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 p-4 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/60 dark:bg-white/[0.03]">
            @if($user->isAuthor())
                <flux:button href="{{ route('authors.show', $user->username ?? $user->id) }}" variant="outline" size="sm" class="flex-1 text-xs font-semibold">View Shop</flux:button>
            @else
                <flux:button href="#" variant="outline" size="sm" class="flex-1 text-xs font-semibold">View Profile</flux:button>
            @endif

            @auth
                @if(auth()->id() !== $user->id)
                    <flux:button
                        x-on:click="Livewire.dispatch('open-author-chat', { authorId: {{ $user->id }} })"
                        variant="primary"
                        size="sm"
                        icon="chat-bubble-left-right"
                        class="flex-1 text-xs font-semibold bg-emerald-600 border-none hover:bg-emerald-700"
                    >
                        Message
                    </flux:button>
                @endif
            @endauth
        </div>
    </flux:popover>
</flux:dropdown>

