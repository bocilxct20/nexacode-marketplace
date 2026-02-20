<div class="fixed bottom-8 left-12 z-[9998]" x-data="{ 
    open: @entangle('isOpen').live,
    proactive: @entangle('proactiveArticle').live,
    search: @entangle('search').live,
    closeProactive() {
        this.proactive = null;
    }
}">
    {{-- Trigger Button --}}
    <flux:button 
        x-on:click="open = !open"
        variant="primary"
        class="!w-14 !h-14 !rounded-full shadow-lg shadow-emerald-500/20 flex items-center justify-center transition-all duration-300 hover:scale-110 group relative p-0"
        x-bind:class="proactive ? 'animate-pulse' : ''"
    >
        <div x-show="!open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100">
            <flux:icon name="question-mark-circle" variant="solid" class="w-7 h-7" />
        </div>
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="absolute">
            <flux:icon name="x-mark" variant="solid" class="w-7 h-7" />
        </div>
        
        {{-- Notification Dot --}}
        <template x-if="proactive">
            <span class="absolute top-3 right-3 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-emerald-600"></span>
            </span>
        </template>
    </flux:button>

    {{-- Proactive Suggestion Popup --}}
    <div 
        x-show="proactive && !open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-x-12"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-12"
        class="absolute bottom-0 left-24 w-64 p-5 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200 dark:border-emerald-500/20 shadow-2xl rounded-3xl pointer-events-auto"
        x-cloak
    >
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                <flux:icon name="light-bulb" variant="mini" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1.5">Butuh Bantuan?</p>
                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 leading-snug mb-3" x-text="proactive?.title"></h4>
                <flux:link 
                    x-bind:href="'/id/help/' + proactive?.category?.slug + '/' + proactive?.slug"
                    wire:navigate
                    variant="subtle"
                    class="text-[10px] font-black uppercase tracking-widest"
                >
                    Baca Sekarang <flux:icon name="arrow-right" variant="mini" class="ml-1 w-3 h-3" />
                </flux:link>
            </div>
        </div>
        {{-- Close Suggestion --}}
        <flux:button x-on:click="closeProactive()" icon="x-mark" variant="ghost" size="sm" class="absolute top-3 right-3 !text-zinc-400 hover:!text-zinc-600" inset />
    </div>

    {{-- Widget Panel --}}
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300 origin-bottom-left"
        x-transition:enter-start="opacity-0 scale-95 translate-y-10"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 origin-bottom-left"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        class="absolute bottom-20 left-0 w-[380px] h-[550px] bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
        @click.away="open = false"
        x-cloak
    >
        {{-- Header --}}
        <div class="p-6 bg-emerald-500/5 dark:bg-emerald-500/10 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <flux:icon name="academic-cap" variant="solid" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <flux:heading size="xl" class="!text-zinc-900 dark:!text-white">Pusat Bantuan</flux:heading>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Sentiasa tersedia</span>
                    </div>
                </div>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">Punya pertanyaan? Cari solusi instan atau buka panduan lengkap kami di bawah ini.</p>
        </div>

        {{-- Search Input --}}
        <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-900">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                icon="magnifying-glass" 
                placeholder="Cari solusi (misal: 'cara bayar')..." 
                class="!bg-zinc-100/50 dark:!bg-zinc-800/50 !border-none !rounded-2xl !h-12 !text-sm" 
            />
        </div>

        {{-- Content Area --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-8 scroll-smooth custom-scrollbar">
            @if(strlen($search) >= 2)
                {{-- Search Results --}}
                <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-widest text-zinc-400 text-[10px] font-black">Hasil Pencarian</flux:heading>
                    <div class="space-y-3">
                        @forelse($results as $result)
                            <flux:link href="{{ route('help.article', [$result->category->slug, $result->slug]) }}" wire:navigate class="block p-5 bg-white dark:bg-zinc-900/50 hover:bg-emerald-500 hover:text-white border border-zinc-100 dark:border-zinc-800 rounded-3xl transition-all group active:scale-[0.98]">
                                <flux:heading size="sm" class="group-hover:text-white transition-colors">{{ $result->title }}</flux:heading>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[9px] font-black uppercase tracking-tighter opacity-60 group-hover:opacity-100">{{ $result->category->name }}</span>
                                    <flux:icon name="chevron-right" class="w-3 h-3 translate-x-0 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </flux:link>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <flux:icon name="magnifying-glass" class="w-6 h-6 text-zinc-300" />
                                </div>
                                <p class="text-sm text-zinc-500 font-medium font-inter">Maaf, kami tidak menemukan artikel itu.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                {{-- Suggested Articles --}}
                <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4">
                    <flux:heading size="sm" class="mb-6 uppercase tracking-widest text-zinc-400 text-[10px] font-black">Panduan Populer</flux:heading>
                    <div class="space-y-5">
                        @foreach($suggestedArticles as $article)
                            <flux:link href="{{ route('help.article', [$article->category->slug, $article->slug]) }}" wire:navigate class="flex items-center gap-5 group">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center text-zinc-400 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm group-hover:shadow-emerald-500/20">
                                    <flux:icon name="document-text" variant="mini" class="w-6 h-6" />
                                </div>
                                <div class="flex-1 border-b border-zinc-50 dark:border-zinc-800/50 pb-4 group-last:border-0 border-none">
                                    <h4 class="text-sm font-bold text-zinc-700 dark:text-zinc-300 group-hover:text-emerald-600 transition-colors leading-tight">{{ $article->title }}</h4>
                                    <p class="text-[10px] text-zinc-400 mt-1 font-medium uppercase tracking-wider">{{ $article->category->name }}</p>
                                </div>
                            </flux:link>
                        @endforeach
                    </div>
                </div>

                {{-- Direct Support Callout --}}
                <div class="p-8 bg-emerald-500/10 dark:bg-emerald-500/5 border border-emerald-500/20 rounded-3xl text-center space-y-6">
                    <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 flex items-center justify-center mx-auto relative">
                        <flux:icon name="chat-bubble-left-right" class="w-10 h-10 text-emerald-600" />
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center border-2 border-white dark:border-zinc-900">
                            <flux:icon name="sparkles" variant="mini" class="w-3 h-3 text-white" />
                        </div>
                    </div>
                    <div>
                        <flux:heading size="lg">Masih Bingung?</flux:heading>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">
                            Hubungi support NexaCode secara langsung untuk bantuan personal.
                        </p>
                    </div>
                    <flux:button x-on:click="Livewire.dispatch('open-admin-support')" variant="primary" class="w-full rounded-2xl shadow-lg shadow-emerald-500/20">
                        Chat Admin Sekarang
                    </flux:button>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-900/50 border-t border-zinc-200/50 dark:border-zinc-800/50 text-center">
            <flux:link href="{{ route('help.index') }}" wire:navigate variant="subtle" class="text-[10px] font-black uppercase tracking-[0.2em]">
                Buka Pusat Bantuan Lengkap &rarr;
            </flux:link>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.4);
        }
    </style>
</div>
