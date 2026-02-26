@section('title', $article->title . ' - Help Center')
@section('meta_description', $article->excerpt ?? Str::limit(strip_tags($article->content), 160))
@section('og_image', asset('images/help-og.png'))

@push('head')
    @if($this->seoSchema)
        <script type="application/ld+json">
        {!! json_encode($this->seoSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
@endpush

<div class="container mx-auto px-4 py-8" x-data="{ 
    toc: [],
    generateTOC() {
        const headings = document.querySelectorAll('.article-content h2, .article-content h3');
        this.toc = Array.from(headings).map((el, i) => {
            if (!el.id) el.id = 'heading-' + i;
            return {
                id: el.id,
                text: el.innerText,
                level: el.tagName
            };
        });
    }
}" x-init="generateTOC()">
    {{-- QuillJS Styles for Content Rendering (Offline) --}}
    <link href="{{ asset('css/quill.snow.css') }}" rel="stylesheet">

    <div class="mb-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Beranda</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('help.index') }}">Pusat Bantuan</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('help.category', $category->slug) }}">{{ $category->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $article->title }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="flex flex-col lg:flex-row gap-12">
        {{-- Sidebar Navigation --}}
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="sticky top-24">
                {{-- Table of Contents --}}
                <template x-if="toc.length > 0">
                    <div class="mb-12">
                        <div class="mb-6 font-black uppercase tracking-tight text-sm text-zinc-900 dark:text-white">Daftar Isi</div>
                        <nav class="space-y-2">
                            <template x-for="item in toc" :key="item.id">
                                <a :href="'#' + item.id" 
                                   class="block text-sm transition-colors hover:text-emerald-500"
                                   :class="item.level === 'H3' ? 'pl-4 text-zinc-500' : 'font-medium text-zinc-700 dark:text-zinc-300'"
                                   x-text="item.text"></a>
                            </template>
                        </nav>
                    </div>
                </template>

                <div class="mb-6 font-black uppercase tracking-tight text-sm text-zinc-900 dark:text-white">Dalam Kategori Ini</div>
                <flux:navlist variant="sidebar">
                    @foreach($relatedArticles as $rel)
                        <flux:navlist.item href="{{ route('help.article', [$category->slug, $rel->slug]) }}" :current="$rel->id === $article->id">
                            {{ $rel->title }}
                        </flux:navlist.item>
                    @endforeach
                    <flux:navlist.item href="{{ route('help.category', $category->slug) }}" class="mt-4 font-bold text-emerald-600">
                        Lihat Semua Artikel
                    </flux:navlist.item>
                </flux:navlist>

                <div class="mt-12 p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <flux:heading size="sm" class="mb-4 font-black uppercase tracking-tight">Tautan Cepat</flux:heading>
                    <flux:navlist variant="sidebar">
                        @foreach($allCategories->take(5) as $cat)
                            <flux:navlist.item href="{{ route('help.category', $cat->slug) }}" icon="{{ $cat->icon ?: 'book-open' }}">
                                {{ $cat->name }}
                            </flux:navlist.item>
                        @endforeach
                    </flux:navlist>
                </div>
            </div>
        </aside>

        {{-- Main Article Content --}}
        <main class="flex-1 max-w-4xl">
            <div class="mb-12">
                <div class="flex items-center gap-2 mb-4">
                    <flux:badge size="sm" variant="zinc">{{ $category->name }}</flux:badge>
                    <span class="text-zinc-300">&bull;</span>
                    <span class="text-xs text-zinc-400">Diperbarui {{ $article->updated_at->diffForHumans() }}</span>
                    <span class="text-zinc-300">&bull;</span>
                    <span class="text-xs text-emerald-500 font-bold">{{ $article->read_time }} menit baca</span>
                </div>
                <flux:heading size="2xl" class="mb-8 leading-tight">{{ $article->title }}</flux:heading>
                
                @if($article->excerpt)
                    <div class="p-6 bg-zinc-50 dark:bg-zinc-800 rounded-2xl border-l-4 border-emerald-500 mb-10 text-zinc-600 dark:text-zinc-400 italic">
                        {{ $article->excerpt }}
                    </div>
                @endif

                <div class="article-content prose dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed text-lg space-y-6 ql-editor !p-0">
                    {!! $article->content !!}
                </div>
            </div>

            {{-- Article Feedback --}}
            <div class="mt-20 pt-10 border-t border-zinc-100 dark:border-zinc-800" x-data="{ showFeedbackForm: false }">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <div class="font-black uppercase tracking-tight text-lg text-zinc-900 dark:text-white mb-2">Apakah artikel ini membantu?</div>
                        <p class="text-zinc-500 text-xs font-medium">
                            @if($article->helpful_count > 0)
                                {{ $article->helpful_count }} orang merasa ini membantu
                            @else
                                Jadilah yang pertama memberikan bantuan!
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-3">
                        @if($hasVoted)
                            <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-2xl">
                                <flux:icon name="check-circle" variant="solid" class="w-4 h-4" />
                                Terima kasih atas feedback kamu!
                            </div>
                        @else
                            <flux:button wire:click="voteAction('helpful')" variant="outline" icon="hand-thumb-up" class="px-6 h-10 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">Ya</flux:button>
                            <flux:button x-on:click="showFeedbackForm = true" variant="outline" icon="hand-thumb-down" class="px-6 h-10 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-red-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">Tidak</flux:button>
                        @endif
                    </div>
                </div>

                {{-- Detailed Feedback Form (Insight & Optimization) --}}
                <div x-show="showFeedbackForm" x-collapse aria-hidden="true" class="mt-8 p-8 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <div class="mb-4 font-black uppercase tracking-tight text-zinc-900 dark:text-white text-md">Bantu kami meningkatkan artikel ini</div>
                    <flux:textarea wire:model="feedbackComment" placeholder="Apa yang kurang jelas atau ingin kamu tambahkan..." rows="3" />
                    <div class="mt-6 flex justify-end gap-3">
                        <flux:button x-on:click="showFeedbackForm = false" variant="ghost" class="text-[10px] font-black uppercase tracking-widest h-10 rounded-2xl">Batal</flux:button>
                        <flux:button wire:click="submitDetailedFeedback" variant="primary" class="bg-emerald-600 hover:bg-emerald-500 text-[10px] font-black uppercase tracking-widest px-8 h-10 shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5">Kirim Feedback</flux:button>
                    </div>
                </div>
            </div>

            {{-- Related Reading --}}
            @if(count($relatedArticles) > 0)
                <div class="mt-24">
                    <div class="font-black uppercase tracking-tight text-xl mb-8 text-zinc-900 dark:text-white">Artikel Terkait</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($relatedArticles->take(4) as $rel)
                            <a href="{{ route('help.article', [$category->slug, $rel->slug]) }}" class="group">
                                <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm hover:border-emerald-500/30 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all h-full">
                                    <div class="font-bold text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors tracking-tight mb-1">{{ $rel->title }}</div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Lanjut membaca</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
