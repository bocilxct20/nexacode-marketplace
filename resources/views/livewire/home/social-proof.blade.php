<div 
    x-data="{ 
        show: false,
        timeout: null,
        init() {
            setTimeout(() => { this.show = true; }, 5000);
            setInterval(() => {
                if (this.show) {
                    this.show = false;
                    setTimeout(() => {
                        $wire.fetchRecentSale().then(() => {
                            this.show = true;
                        });
                    }, 10000);
                }
            }, 30000);
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 -translate-x-full"
    class="fixed bottom-6 left-6 z-[100] max-w-sm pointer-events-none"
    style="display: none;"
>
    @if($recentSale && $recentSale->product)
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-2xl flex items-center gap-4 pointer-events-auto">
            <div class="size-12 rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border border-zinc-100 dark:border-zinc-800">
                <img src="{{ $recentSale->product->thumbnail_url }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400 mb-0.5">Recent Activity</p>
                <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                    Seseorang baru saja membeli
                </p>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate font-medium italic">
                    {{ $recentSale->product->name }}
                </p>
            </div>
            <button @click="show = false" class="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors ml-2">
                <flux:icon name="x-mark" variant="micro" class="size-3 text-zinc-400" />
            </button>
        </div>
    @endif
</div>
