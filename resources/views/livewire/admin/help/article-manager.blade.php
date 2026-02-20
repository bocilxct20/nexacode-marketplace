<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Artikel Pusat Bantuan</flux:heading>
            <flux:subheading>Kelola panduan dan bantuan untuk pengguna platform</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.help.articles.create') }}" variant="primary" icon="plus" wire:navigate>Buat Artikel</flux:button>
    </div>

    <div class="flex gap-4 mb-6">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari artikel..." />
        </div>
        <div class="w-64">
            <flux:select wire:model.live="categoryFilter" placeholder="Semua Kategori">
                <flux:select.option value="">Semua Kategori</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Judul</flux:table.column>
                <flux:table.column>Kategori</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Dilihat</flux:table.column>
                <flux:table.column>Umpan Balik</flux:table.column>
                <flux:table.column>Urutan</flux:table.column>
                <flux:table.column align="right">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($articles as $article)
                    <flux:table.row :key="$article->id">
                        <flux:table.cell class="font-medium">
                            <div class="flex flex-col">
                                <span>{{ $article->title }}</span>
                                <span class="text-xs text-zinc-500">{{ $article->slug }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" variant="zinc">{{ $article->category->name }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($article->is_published)
                                <flux:badge size="sm" variant="success">Terbit</flux:badge>
                            @else
                                <flux:badge size="sm" variant="zinc">Draf</flux:badge>
                            @endif
                            @if ($article->is_featured)
                                <flux:badge size="sm" variant="primary" icon="star" class="ml-1" />
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $article->views_count }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-600 text-xs font-bold">+{{ $article->helpful_count }}</span>
                                <span class="text-zinc-300">/</span>
                                <span class="text-red-500 text-xs font-bold">-{{ $article->unhelpful_count }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $article->sort_order }}</flux:table.cell>
                        <flux:table.cell align="right">
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('admin.help.articles.edit', $article) }}" variant="ghost" size="sm" icon="pencil-square" wire:navigate />
                                <flux:button wire:click="delete({{ $article->id }})" wire:confirm="Apakah kamu yakin?" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </flux:table.cell>
                        </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $articles->links() }}
        </div>
    </flux:card>
</div>
