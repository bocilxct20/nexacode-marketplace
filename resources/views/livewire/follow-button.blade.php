<div>
    <flux:button 
        wire:click="toggleFollow"
        :variant="$isFollowing ? 'outline' : 'primary'"
        size="sm"
        class="{{ $isFollowing ? '' : 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 border-none' }} rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest transition-all duration-300 transform active:scale-95"
    >
        {{ $isFollowing ? 'Unfollow' : 'Follow' }}
    </flux:button>
</div>
