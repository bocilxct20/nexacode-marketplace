<div>
    <flux:button 
        variant="{{ $variant }}" 
        @click="$wire.toggle()" 
        class="{{ $isWishlisted ? 'text-rose-500 bg-rose-50 dark:bg-rose-900/20 border-rose-100 dark:border-rose-800' : 'text-zinc-400' }} transition-all duration-300 h-12 w-12"
        icon="heart"
        square
        flux:tooltip="{{ $isWishlisted ? 'Wishlisted' : 'Add to Wishlist' }}"
    />
</div>
