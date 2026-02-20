<div class="container mx-auto px-4 py-12">
    {{-- Header & Search Section --}}
    <div class="max-w-3xl mx-auto text-center mb-16">
        <flux:heading size="2xl" class="mb-4">Ada yang bisa kami bantu?</flux:heading>
        <flux:subheading class="mb-8 text-lg">Cari panduan, artiket teknis, dan solusi troubleshooting.</flux:subheading>
        
        <div class="max-w-2xl mx-auto">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                icon="magnifying-glass" 
                placeholder="Cari panduan, dokumentasi, atau solusi..." 
                class="w-full bg-white dark:bg-zinc-900 shadow-2xl border-zinc-200 dark:border-zinc-800 focus:ring-emerald-500 rounded-2xl" 
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
                            <flux:card class="p-6 hover:shadow-lg transition-all border-zinc-100 dark:border-zinc-800 hover:border-emerald-500/30">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                        <flux:icon name="document-text" variant="mini" class="w-5 h-5" />
                                    </div>
                                    <flux:badge size="sm" variant="zinc" class="ml-auto">{{ $result->category->name }}</flux:badge>
                                </div>
                                <flux:heading size="md" class="group-hover:text-emerald-600 transition-colors mb-2">{{ $result->title }}</flux:heading>
                                <p class="text-xs text-zinc-500 line-clamp-2">{{ Str::limit(strip_tags($result->content), 120) }}</p>
                            </flux:card>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-20 flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-900/50 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                        <flux:icon name="magnifying-glass" class="w-8 h-8 text-zinc-400" />
                    </div>
                    <flux:heading size="lg">Artikel tidak ditemukan</flux:heading>
                    <p class="text-zinc-500 dark:text-zinc-400 mt-2 max-w-md text-center">Kami tidak menemukan artikel yang sesuai dengan kata kunci kamu. Coba gunakan istilah lain.</p>
                    <flux:button wire:click="$set('search', '')" variant="ghost" class="mt-4">Kembali ke Help Center</flux:button>
                </div>
            @endif
        </div>
    @else
        {{-- Category Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
            @foreach($categories as $category)
                <a href="{{ route('help.category', $category->slug) }}" wire:navigate class="group">
                    <flux:card class="h-full p-8 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border-zinc-100 dark:border-zinc-800 group-hover:border-emerald-500/30">
                        <div class="mb-6 flex justify-between items-start">
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 text-emerald-600 dark:text-emerald-400">
                                <flux:icon :icon="$category->icon ?: 'book-open'" variant="solid" class="w-8 h-8" />
                            </div>
                            <flux:badge size="sm" variant="zinc" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $category->articles()->where('is_published', true)->count() }} Artikel
                            </flux:badge>
                        </div>
                        <flux:heading size="lg" class="mb-3 group-hover:text-emerald-600 transition-colors">{{ $category->name }}</flux:heading>
                        <p class="text-zinc-500 line-clamp-2 leading-relaxed">{{ $category->description ?: 'Find all resources related to ' . $category->name . ' here.' }}</p>
                        
                        <div class="mt-8 flex items-center text-sm font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 group-hover:translate-x-2 transition-all duration-300">
                            Jelajahi Topik <flux:icon name="arrow-long-right" class="ml-2 w-5 h-5" />
                        </div>
                    </flux:card>
                </a>
            @endforeach
        </div>

        {{-- Featured/Popular Articles --}}
        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-20">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <flux:heading size="xl">Artikel Pilihan</flux:heading>
                    <flux:subheading>Panduan terbaik yang dikurasi langsung oleh tim kami</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($featuredArticles as $article)
                    <a href="{{ route('help.article', [$article->category->slug, $article->slug]) }}" wire:navigate class="flex items-center p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl hover:bg-white dark:hover:bg-zinc-800 hover:shadow-lg border border-transparent hover:border-emerald-500/20 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 flex items-center justify-center text-zinc-400 group-hover:text-emerald-500 transition-colors mr-6 flex-shrink-0">
                            <flux:icon name="document-text" variant="mini" class="w-6 h-6" />
                        </div>
                        <div class="flex-1">
                            <flux:heading size="md" class="group-hover:text-emerald-600 transition-colors">{{ $article->title }}</flux:heading>
                            <p class="text-xs text-zinc-500 mt-1">{{ $article->category->name }} &bull; {{ $article->views_count }} dilihat</p>
                        </div>
                        <flux:icon name="chevron-right" class="w-5 h-5 text-zinc-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" />
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Contact Section --}}
    <div class="mt-24 relative overflow-hidden rounded-[3rem] bg-zinc-900 dark:bg-zinc-950 p-12 md:p-20 text-center shadow-3xl">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/20 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-500/10 blur-[120px] rounded-full"></div>

        <div class="relative z-10 max-w-2xl mx-auto">
            <flux:heading size="xl" class="text-white mb-6">Butuh bantuan personal?</flux:heading>
            <p class="text-zinc-400 text-lg mb-10 leading-relaxed">
                Jika kamu belum menemukan jawaban yang dicari, tim support kami siap membantu kamu secara langsung.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <flux:button x-on:click="Livewire.dispatch('open-admin-support')" variant="primary" icon="chat-bubble-left-right" class="px-10 h-14 bg-emerald-600 hover:bg-emerald-500 border-none shadow-lg shadow-emerald-900/20 transition-all hover:scale-105">
                    Mulai Live Chat
                </flux:button>
                @auth
                    <flux:button x-on:click="Livewire.dispatch('open-ticket-modal')" variant="ghost" icon="pencil-square" class="px-10 h-14 text-white border-white/20 hover:bg-white/10 transition-all">
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
