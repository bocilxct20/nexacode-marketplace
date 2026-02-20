<div>
    <flux:button 
        wire:click="toggle" 
        variant="{{ $inCart ? 'primary' : 'outline' }}" 
        color="{{ $inCart ? 'emerald' : 'zinc' }}"
        class="h-12 w-12"
        :icon="$inCart ? 'check' : 'shopping-cart'"
        square
        flux:tooltip="{{ $inCart ? 'Hapus dari Keranjang' : 'Tambah ke Keranjang' }}"
    />
</div>
