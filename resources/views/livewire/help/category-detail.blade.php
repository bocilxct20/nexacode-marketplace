<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('help.index') }}">Help Center</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $category->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="flex flex-col lg:flex-row gap-12">
        {{-- Sidebar Navigation --}}
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="sticky top-24">
                <flux:heading size="sm" class="mb-6 uppercase tracking-wider text-zinc-400 font-bold">Categories</flux:heading>
                <flux:navlist variant="sidebar">
                    @foreach($allCategories as $cat)
                        <flux:navlist.item href="{{ route('help.category', $cat->slug) }}" :current="$cat->id === $category->id">
                            <x-slot name="icon">
                                @if($cat->icon && (str_starts_with($cat->icon, 'storage/') || str_starts_with($cat->icon, 'http')))
                                    <div class="w-4 h-4 bg-current {{ $category->id == $cat->id ? 'text-white' : 'text-zinc-500 dark:text-zinc-400' }}" 
                                         style="mask-image: url('{{ asset($cat->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($cat->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                                    </div>
                                @elseif(str_starts_with($cat->icon ?? '', 'lucide-'))
                                    <x-dynamic-component :component="$cat->icon" class="w-4 h-4" />
                                @else
                                    <flux:icon :icon="$cat->icon ?: 'book-open'" variant="mini" />
                                @endif
                            </x-slot>
                            {{ $cat->name }}
                        </flux:navlist.item>
                    @endforeach
                </flux:navlist>

                <div class="mt-12 p-6 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl text-center overflow-hidden relative shadow-sm">
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/10 blur-2xl rounded-full"></div>
                    <div class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight mb-2">Need Help?</div>
                    <p class="text-[10px] font-medium uppercase tracking-widest text-zinc-500 mb-4">Contact our support team anytime.</p>
                    <flux:button wire:click="contactSupport" size="sm" variant="primary" class="w-full text-[10px] font-black uppercase tracking-widest h-10 shadow-sm hover:-translate-y-0.5 transition-transform">Chat Now</flux:button>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1">
            <div class="mb-12">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center shadow-sm">
                        @if($category->icon && (str_starts_with($category->icon, 'storage/') || str_starts_with($category->icon, 'http')))
                            <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-400" 
                                 style="mask-image: url('{{ asset($category->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($category->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
                            </div>
                        @elseif(str_starts_with($category->icon ?? '', 'lucide-'))
                            <x-dynamic-component :component="$category->icon" class="w-12 h-12 text-emerald-600 dark:text-emerald-400" />
                        @else
                            <flux:icon :icon="$category->icon ?: 'book-open'" class="w-12 h-12 text-emerald-600 dark:text-emerald-400" />
                        @endif
                    </div>
                    <flux:heading size="2xl">{{ $category->name }}</flux:heading>
                </div>
                <p class="text-zinc-500 text-lg leading-relaxed">{{ $category->description ?: 'Find helpful guides and documentation for ' . $category->name . '.' }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($articles as $article)
                    <a href="{{ route('help.article', [$category->slug, $article->slug]) }}" class="group">
                        <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm hover:border-emerald-500/30 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all duration-300">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/50 flex items-center justify-center text-zinc-400 group-hover:text-emerald-500 transition-colors mr-6 flex-shrink-0">
                                    <flux:icon name="document-text" variant="mini" class="w-5 h-5" />
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-lg text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors tracking-tight">{{ $article->title }}</div>
                                    @if($article->excerpt)
                                        <p class="text-xs font-medium text-zinc-500 mt-1 line-clamp-1 truncate">{{ $article->excerpt }}</p>
                                    @endif
                                </div>
                                <flux:icon name="chevron-right" variant="mini" class="w-5 h-5 text-zinc-300 group-hover:text-emerald-500 transition-all" />
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-20 bg-zinc-50 dark:bg-zinc-900/30 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <flux:icon name="document-text" class="mx-auto w-12 h-12 text-zinc-300 mb-4" />
                        <div class="font-black uppercase tracking-tight text-lg mb-1">No articles yet</div>
                        <div class="text-xs font-medium text-zinc-500">This category doesn't have any guides published yet.</div>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
