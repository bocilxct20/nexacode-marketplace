<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ $type === 'sales' ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-amber-500/10 border-amber-500/20' }} {{ $value > 0 ? '' : 'hidden' }}">
    @if($type === 'sales')
        <flux:icon.bolt variant="mini" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
            Keren! Sudah <span class="bg-emerald-600 text-white px-1 rounded">{{ $value }}</span> Unit Terjual
        </span>
    @elseif($type === 'viewers')
        <flux:icon.users variant="mini" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
        <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">
            Ada <span class="font-bold underline">{{ $value }}</span> orang sedang melihat produk ini
        </span>
    @endif
</div>
