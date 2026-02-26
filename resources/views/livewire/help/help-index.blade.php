<div class="container mx-auto px-4 py-12">
    {{-- Header & Search Section --}}
    <div class="max-w-3xl mx-auto text-center mb-16">
        <flux:heading size="2xl" class="mb-4 font-black uppercase tracking-tight">Ada yang bisa kami bantu?</flux:heading>
        <div class="mb-8 text-zinc-500 font-medium tracking-wide">Cari panduan, artiket teknis, dan solusi troubleshooting.</div>
        
        <div class="max-w-2xl mx-auto">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                icon="magnifying-glass" 
                placeholder="Cari panduan, dokumentasi, atau solusi..." 
                class="w-full bg-white dark:bg-zinc-900/50 shadow-sm border-zinc-200 dark:border-zinc-800 rounded-3xl" 
                clearable
            />
        </div>
    </div>

    @if($search)
        {{-- Search Results Display --}}
        <div class="mb-20">
            <div class="flex items-center justify-between mb-8">
                <flux:heading size="xl">Hasil pencarian untuk "{{ $search }}"</flux:heading>
                <flux:button variant="ghost" size="sm" wire:click="$set('search', '')" icon="x-mark">Hapus Pencarian</flux:button>
            </div>

            @if(count($searchResults) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($searchResults as $result)
                        <a href="{{ route('help.article', [$result->category->slug, $result->slug]) }}" wire:navigate class="group">
                            <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm hover:border-emerald-500/30 transition-all h-full">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center text-zinc-400 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-500/10 group-hover:text-emerald-500 transition-colors">
                                        <flux:icon name="document-text" variant="mini" class="w-5 h-5" />
                                    </div>
                                    <flux:badge size="sm" variant="zinc" class="ml-auto text-[9px] uppercase font-black tracking-widest">{{ $result->category->name }}</flux:badge>
                                </div>
                                <div class="font-bold text-zinc-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors mb-2">{{ $result->title }}</div>
                                <p class="text-xs text-zinc-500 font-medium line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($result->content), 120) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-white dark:bg-zinc-900/50 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mb-6 mx-auto border border-zinc-100 dark:border-zinc-700/50">
                        <flux:icon name="magnifying-glass" class="w-8 h-8 text-zinc-400" />
                    </div>
                    <div class="font-black uppercase tracking-tight text-xl mb-2 text-zinc-900 dark:text-white">Artikel tidak ditemukan</div>
                    <p class="text-xs font-medium text-zinc-500 mb-6 max-w-md mx-auto leading-relaxed">Kami tidak menemukan artikel yang sesuai dengan kata kunci kamu. Coba gunakan istilah lain.</p>
                    <flux:button wire:click="$set('search', '')" variant="subtle" class="text-[10px] font-black uppercase tracking-widest px-6 h-10 rounded-2xl">Kembali ke Help Center</flux:button>
                </div>
            @endif
        </div>
    @else
        {{-- Category Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-20">
            @foreach($categories as $category)
                <a href="{{ route('help.category', $category->slug) }}" wire:navigate class="group">
                    <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-sm h-full hover:border-emerald-500/30 transition-all flex flex-col">
                        <div class="mb-6 flex justify-between items-start">
                            <div class="w-12 h-12 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl group-hover:bg-emerald-50 dark:group-hover:bg-emerald-500/10 text-zinc-400 group-hover:text-emerald-500 transition-colors duration-300 shadow-sm border border-zinc-100 dark:border-zinc-700/50">
                                @if($category->icon && (str_starts_with($category->icon, 'storage/') || str_starts_with($category->icon, 'http')))
                                    <div class="w-6 h-6 bg-current transition-colors duration-300" 
                                         style="mask-image: url('{{ asset($category->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($category->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                                    </div>
                                @elseif(str_starts_with($category->icon ?? '', 'lucide-'))
                                    <x-dynamic-component :component="$category->icon" class="w-6 h-6 transition-colors duration-300" />
                                @else
                                    <flux:icon :icon="$category->icon ?: 'book-open'" variant="outline" class="w-6 h-6 transition-colors duration-300" />
                                @endif
                            </div>
                            <flux:badge size="sm" variant="zinc" class="opacity-0 group-hover:opacity-100 transition-opacity text-[9px] uppercase font-black tracking-widest">
                                {{ $category->articles()->where('is_published', true)->count() }} Artikel
                            </flux:badge>
                        </div>
                        <div class="font-bold text-lg mb-2 text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors tracking-tight">{{ $category->name }}</div>
                        <div class="text-xs font-medium text-zinc-500 leading-relaxed mb-8 flex-1">{{ $category->description ?: 'Find all resources related to ' . $category->name . ' here.' }}</div>
                        
                        <div class="flex items-center text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Jelajahi Topik <flux:icon name="arrow-long-right" variant="mini" class="ml-1 w-4 h-4" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Featured/Popular Articles --}}
        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <flux:heading size="lg" class="font-black uppercase tracking-tight">Artikel Pilihan</flux:heading>
                    <div class="text-xs font-medium text-zinc-500 mt-1">Panduan terbaik yang dikurasi langsung oleh tim kami</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($featuredArticles as $article)
                    <a href="{{ route('help.article', [$article->category->slug, $article->slug]) }}" wire:navigate class="flex items-center p-5 bg-white dark:bg-zinc-900/50 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/30 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/50 flex items-center justify-center text-zinc-400 group-hover:text-emerald-500 transition-colors mr-4 flex-shrink-0">
                            <flux:icon name="document-text" variant="mini" class="w-5 h-5" />
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-sm text-zinc-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">{{ $article->title }}</div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500 mt-1">{{ $article->category->name }} &bull; {{ $article->views_count }} views</p>
                        </div>
                        <flux:icon name="chevron-right" variant="mini" class="w-4 h-4 text-zinc-300 group-hover:text-emerald-500 transition-colors" />
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Contact Section --}}
    <div class="mt-20 relative overflow-hidden rounded-[3rem] bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 p-12 md:p-16 text-center shadow-sm">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-500/10 dark:bg-emerald-500/5 blur-[80px] rounded-full"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-500/10 dark:bg-indigo-500/5 blur-[80px] rounded-full"></div>

        <div class="relative z-10 max-w-xl mx-auto">
            <div class="font-black text-2xl text-zinc-900 dark:text-white mb-4 uppercase tracking-tight">Butuh bantuan personal?</div>
            <p class="text-xs font-medium text-zinc-500 mb-8 leading-relaxed max-w-md mx-auto">
                Jika kamu belum menemukan jawaban yang dicari, tim support kami siap membantu kamu secara langsung.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <flux:button wire:click="contactSupport" variant="primary" class="px-8 py-5 text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-sm hover:-translate-y-0.5 transition-transform">
                    Mulai Live Chat
                </flux:button>
                @auth
                    <flux:button x-on:click="Livewire.dispatch('open-ticket-modal')" variant="ghost" class="px-8 py-5 text-[10px] font-black uppercase tracking-widest rounded-2xl">
                        Buat Tiket Support
                    </flux:button>
                @endauth
            </div>
        </div>
    </div>

    @auth
        <livewire:support.create-ticket />
    @endauth
</div>
