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
                        <flux:navlist.item href="{{ route('help.category', $cat->slug) }}" :current="$cat->id === $category->id" icon="{{ $cat->icon ?: 'book-open' }}">
                            {{ $cat->name }}
                        </flux:navlist.item>
                    @endforeach
                </flux:navlist>

                <div class="mt-12 p-6 bg-zinc-900 rounded-3xl text-center overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/20 blur-2xl rounded-full"></div>
                    <flux:heading size="sm" class="text-white mb-2">Need Help?</flux:heading>
                    <p class="text-xs text-zinc-500 mb-4">Contact our support team anytime.</p>
                    <flux:button x-on:click="Livewire.dispatch('open-admin-support')" size="sm" variant="primary" class="w-full h-10 bg-emerald-600 border-none">Chat Now</flux:button>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1">
            <div class="mb-12">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <flux:icon :icon="$category->icon ?: 'book-open'" variant="solid" class="w-8 h-8" />
                    </div>
                    <flux:heading size="2xl">{{ $category->name }}</flux:heading>
                </div>
                <p class="text-zinc-500 text-lg leading-relaxed">{{ $category->description ?: 'Find helpful guides and documentation for ' . $category->name . '.' }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($articles as $article)
                    <a href="{{ route('help.article', [$category->slug, $article->slug]) }}" class="group">
                        <flux:card class="p-6 hover:shadow-lg hover:border-emerald-500/30 transition-all duration-300 border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:text-emerald-500 transition-colors mr-6">
                                    <flux:icon name="document-text" variant="mini" class="w-5 h-5" />
                                </div>
                                <div class="flex-1">
                                    <flux:heading size="lg" class="group-hover:text-emerald-600 transition-colors">{{ $article->title }}</flux:heading>
                                    @if($article->excerpt)
                                        <p class="text-sm text-zinc-500 mt-1 line-clamp-1 truncate">{{ $article->excerpt }}</p>
                                    @endif
                                </div>
                                <flux:icon name="chevron-right" class="w-5 h-5 text-zinc-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" />
                            </div>
                        </flux:card>
                    </a>
                @empty
                    <div class="text-center py-20 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                        <flux:icon name="document-text" class="mx-auto w-16 h-16 text-zinc-300 mb-4" />
                        <flux:heading>No articles yet</flux:heading>
                        <flux:subheading>This category doesn't have any guides published yet.</flux:subheading>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
