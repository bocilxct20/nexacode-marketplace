@props(['user', 'isAuthor' => false])

<flux:dropdown hover position="bottom" align="start" offset="-16" gap="10">
    <button type="button" class="flex items-center gap-3 group">
        {{ $slot }}
    </button>

    <flux:popover class="flex flex-col gap-4 rounded-3xl shadow-2xl p-6 min-w-[320px] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
        <div class="flex items-start justify-between">
            <flux:avatar size="xl" :name="$user->name" :src="$user->avatar ? asset('storage/' . $user->avatar) : null" :initials="$user->initials" class="rounded-2xl" />
            
            @if(auth()->check() && auth()->id() !== $user->id)
                @livewire('follow-button', ['authorId' => $user->id], key('hover-follow-'.$user->id))
            @endif
        </div>

        <div class="space-y-1">
            <div class="flex items-center gap-1.5">
                <flux:heading size="lg" class="font-black tracking-tight">{{ $user->name }}</flux:heading>
                @if($user->isAuthor())
                    @if($user->isElite())
                        <div class="relative group/badge">
                            <flux:icon.check-badge variant="mini" class="text-amber-500 w-4 h-4 animate-pulse" />
                            <div class="absolute inset-0 bg-amber-400 blur-sm opacity-50 animate-pulse rounded-full"></div>
                        </div>
                    @elseif($user->isPro())
                        <div class="relative group/badge">
                            <flux:icon.check-badge variant="mini" class="text-indigo-500 w-4 h-4" />
                            <div class="absolute inset-0 bg-indigo-400 blur-[2px] opacity-20 rounded-full"></div>
                        </div>
                    @else
                        <flux:icon.check-badge variant="mini" class="text-zinc-400 w-4 h-4" />
                    @endif
                @endif
            </div>

            <div class="flex items-center gap-2">
                <flux:text size="sm" class="text-zinc-500 font-medium">@<span>{{ $user->username ?? $user->id }}</span></flux:text>
                @if(auth()->check() && $user->followers->contains(auth()->user()))
                    <flux:badge size="sm" color="zinc" variant="outline" class="text-[10px] uppercase font-black tracking-tighter">Follows you</flux:badge>
                @endif
            </div>
        </div>

        @if($user->bio)
            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 leading-relaxed line-clamp-2">
                {{ $user->bio }}
            </flux:text>
        @endif

        <div class="flex items-center gap-6 pt-2">
            <div class="flex items-center gap-1.5">
                <flux:heading size="sm" class="font-black">{{ number_format($user->followers->count()) }}</flux:heading>
                <flux:text size="xs" class="text-zinc-500 uppercase tracking-widest font-bold">Followers</flux:text>
            </div>
            @if($user->isAuthor())
                <div class="flex items-center gap-1.5">
                    <flux:heading size="sm" class="font-black">{{ number_format($user->products->count()) }}</flux:heading>
                    <flux:text size="xs" class="text-zinc-500 uppercase tracking-widest font-bold">Products</flux:text>
                </div>
            @else
                <div class="flex items-center gap-1.5">
                    <flux:heading size="sm" class="font-black">{{ number_format($user->following->count()) }}</flux:heading>
                    <flux:text size="xs" class="text-zinc-500 uppercase tracking-widest font-bold">Following</flux:text>
                </div>
            @endif
        </div>

        <div class="flex gap-3 pt-2">
            @if($user->isAuthor())
                <flux:button href="{{ route('authors.show', $user->username ?? $user->id) }}" variant="outline" size="sm" class="flex-1">View Shop</flux:button>
            @else
                <flux:button href="#" variant="outline" size="sm" class="flex-1">View Profile</flux:button>
            @endif
            
            @auth
                @if(auth()->id() !== $user->id)
                    <flux:button 
                        x-on:click="Livewire.dispatch('open-author-chat', { authorId: {{ $user->id }} })"
                        variant="primary" 
                        size="sm" 
                        icon="chat-bubble-left-right" 
                        class="flex-1 bg-emerald-600 border-none"
                    >
                        Message
                    </flux:button>
                @endif
            @endauth
        </div>
    </flux:popover>
</flux:dropdown>
