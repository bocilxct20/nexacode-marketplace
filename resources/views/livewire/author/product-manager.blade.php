<div wire:init="loadData">
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Beranda</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Produk Saya</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl">Kelola Produk</flux:heading>
            <flux:subheading>Atur produk digital, harga, dan ketersediaan item kamu.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Item</flux:button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Produk</flux:subheading>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Disetujui</flux:subheading>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['approved'] }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Menunggu Review</flux:subheading>
                <div class="text-2xl font-bold text-amber-500">{{ $stats['pending'] }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Penjualan</flux:subheading>
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['total_sales']) }}</div>
            </div>
        </flux:card>
    </div>

    {{-- Filters --}}
    <flux:card class="mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari produk..."
                    icon="magnifying-glass"
                />
            </div>
            <flux:select wire:model.live="statusFilter" class="w-full md:w-48">
                <option value="all">Semua Status</option>
                <option value="approved">Disetujui</option>
                <option value="pending">Menunggu</option>
                <option value="draft">Draft</option>
                <option value="rejected">Ditolak</option>
            </flux:select>
        </div>
    </flux:card>

    {{-- Products Table --}}
    <flux:card>
        <flux:table :paginate="$products">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Item</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'regular_price'" :direction="$sortDirection" wire:click="sort('regular_price')">Harga</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'sales_count'" :direction="$sortDirection" wire:click="sort('sales_count')">Penjualan</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'avg_rating'" :direction="$sortDirection" wire:click="sort('avg_rating')">Rating</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Skeleton Rows --}}
                @if (!$readyToLoad)
                    @for ($i = 0; $i < 5; $i++)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded-lg"></div>
                                    <div class="space-y-2">
                                        <div class="h-4 w-48 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                                        <div class="h-3 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell><div class="h-4 w-20 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-12 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-8 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-12 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell align="right"><div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                        </flux:table.row>
                    @endfor
                @else


                    @forelse ($products as $product)
                        <flux:table.row :key="$product->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    @if($product->thumbnail)
                                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded border border-zinc-200 dark:border-zinc-800">
                                    @else
                                        <div class="w-10 h-10 rounded bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                                            <flux:icon.photo class="w-5 h-5 text-zinc-400" />
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-zinc-900 dark:text-white leading-tight">{{ $product->name }}</div>
                                        <div class="text-[10px] uppercase tracking-tighter text-zinc-500 font-bold mt-0.5">
                                            Ditambah {{ $product->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="tabular-nums font-medium">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" :color="match($product->status) {
                                    'approved' => 'emerald',
                                    'pending' => 'amber',
                                    'draft' => 'zinc',
                                    'rejected' => 'red',
                                    default => 'zinc'
                                }" inset="top bottom" class="uppercase text-[10px] font-bold">
                                    {{ match($product->status) {
                                        'approved' => 'Disetujui',
                                        'pending' => 'Menunggu',
                                        'draft' => 'Draft',
                                        'rejected' => 'Ditolak',
                                        default => $product->status
                                    } }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="tabular-nums">
                                {{ number_format($product->sales_count) }}
                            </flux:table.cell>

                            <flux:table.cell class="tabular-nums">
                                <div class="flex items-center gap-1">
                                    <span class="font-bold">{{ number_format($product->avg_rating, 1) }}</span>
                                    <flux:icon.star variant="mini" class="w-3 h-3 text-amber-400 fill-amber-400" />
                                </div>
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item wire:click="edit({{ $product->id }})" icon="pencil-square">Edit Item</flux:menu.item>
                                        <flux:menu.item href="{{ route('products.show', $product->slug) }}" icon="eye" target="_blank">Lihat Etalase</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item wire:click="deleteProduct({{ $product->id }})" wire:confirm="Apakah kamu yakin ingin menghapus produk ini?" icon="trash" variant="danger">
                                            Hapus Produk
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-zinc-500 py-12 italic">
                                Tidak ada produk ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Product Modal --}}
    <flux:modal name="product-manager-modal" wire:model="showModal" class="md:w-[700px]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Produk' : 'Tambah Produk Baru' }}</flux:heading>
                <flux:subheading>Isi detail item digital yang ingin kamu jual.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <flux:field>
                    <flux:label>Nama Produk</flux:label>
                    <flux:input wire:model.live.debounce.300ms="name" placeholder="misal. Tema WordPress Premium" required />
                    <flux:error name="name" />
                </flux:field>

                {{-- Slug --}}
                <flux:field>
                    <flux:label>URL Slug</flux:label>
                    <flux:input wire:model="slug" placeholder="tema-wordpress-premium" required />
                    <flux:error name="slug" />
                </flux:field>
            </div>

            {{-- Description --}}
            <flux:field>
                <div class="flex justify-between items-center mb-3">
                    <flux:label>Deskripsi</flux:label>
                </div>
                <flux:textarea wire:model="description" rows="5" placeholder="Jelaskan fitur dan keunggulan produk kamu..." required />
                <flux:error name="description" />
            </flux:field>

            <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <flux:field>
                    <flux:label>Harga Source Code (Rp)</flux:label>
                    <flux:subheading>Atur harga untuk akses kode sumber penuh.</flux:subheading>
                    <flux:input type="number" wire:model="price" placeholder="50000" min="0" required class="text-lg font-bold" />
                    <flux:error name="price" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Category (Tags) --}}
                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach(\App\Models\Category::orderBy('sort_order')->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                {{-- Demo URL --}}
                <flux:field>
                    <flux:label>Link Demo (Opsional)</flux:label>
                    <flux:input type="url" wire:model="demo_url" placeholder="https://demo.nexacode.com/item-saya" />
                    <flux:error name="demo_url" />
                </flux:field>
            </div>

            {{-- Video URL --}}
            <flux:field>
                <flux:label>Link Video (Opsional - misal. YouTube/Vimeo)</flux:label>
                <flux:input type="url" wire:model="video_url" placeholder="https://youtube.com/watch?v=..." />
                <flux:error name="video_url" />
            </flux:field>

            {{-- Thumbnail Upload --}}
            <flux:field>
                <flux:label>Thumbnail Produk</flux:label>
                
                <flux:file-upload wire:model="thumbnail" accept="image/*">
                    <flux:file-upload.dropzone
                        heading="Lepas thumbnail di sini atau klik untuk cari"
                        text="JPG, PNG, GIF sampai 2MB. Rekomendasi: 800x600px"
                        with-progress
                        inline
                    />
                </flux:file-upload>

                @if ($thumbnail || ($editingId && $existingThumbnail))
                    <div class="mt-4 flex flex-col gap-2">
                        @if ($thumbnail && is_object($thumbnail) && method_exists($thumbnail, 'temporaryUrl'))
                            <flux:file-item 
                                :heading="$thumbnail->getClientOriginalName()" 
                                :image="$thumbnail->temporaryUrl()" 
                                :size="$thumbnail->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="$set('thumbnail', null)" class="!p-1" inset="top bottom" />
                                </x-slot>
                            </flux:file-item>
                        @elseif ($editingId && $existingThumbnail)
                            <flux:file-item 
                                :heading="basename($existingThumbnail)" 
                                :image="str_starts_with($existingThumbnail, 'http') ? $existingThumbnail : Storage::url($existingThumbnail)"
                            >
                                <x-slot name="actions">
                                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="deleteThumbnail({{ $editingId }})" class="!p-1" inset="top bottom" />
                                </x-slot>
                            </flux:file-item>
                        @endif
                    </div>
                @endif
                <flux:error name="thumbnail" />
            </flux:field>

            {{-- Screenshots Upload --}}
            <flux:field>
                <flux:label>Screenshots Produk (Opsional)</flux:label>
                <flux:subheading>Tambahkan beberapa gambar untuk detail lebih lanjut.</flux:subheading>
                
                <flux:file-upload wire:model="screenshot_uploads" multiple accept="image/*">
                    <flux:file-upload.dropzone
                        heading="Lepas gambar di sini atau klik untuk cari"
                        text="JPG, PNG, GIF sampai 2MB per file"
                        with-progress
                        inline
                    />
                </flux:file-upload>

                {{-- Existing & New Screenshots Preview --}}
                @if (count($existingScreenshots) > 0 || count($screenshot_uploads) > 0)
                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                        {{-- Existing Screenshots --}}
                        @foreach ($existingScreenshots as $index => $path)
                            <div class="relative group aspect-video rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-800">
                                <img src="{{ str_starts_with($path, 'http') ? $path : Storage::url($path) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <flux:button variant="danger" size="sm" icon="trash" wire:click="removeScreenshot({{ $index }})" class="!p-1.5" />
                                </div>
                            </div>
                        @endforeach

                        {{-- New Uploads Preview --}}
                        @foreach ($screenshot_uploads as $index => $file)
                            @if (method_exists($file, 'temporaryUrl'))
                                <div class="relative group aspect-video rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-800 opacity-70">
                                    <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <div class="absolute top-1 right-1">
                                        <flux:badge size="sm" color="amber" class="text-[8px]">NEW</flux:badge>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                <flux:error name="screenshot_uploads.*" />
            </flux:field>

            {{-- Version & File --}}
            <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-6">
                <flux:field>
                    <flux:label>Nomor Versi</flux:label>
                    <flux:subheading>Contoh: 1.0.0, 2.1.4-stable, dst.</flux:subheading>
                    <flux:input wire:model="version_number" placeholder="1.0.0" class="w-full md:w-1/3" />
                    <flux:error name="version_number" />
                </flux:field>

                <flux:field>
                    <flux:label>File Proyek (ZIP)</flux:label>
                    <flux:subheading>Ini adalah file asli yang akan diunduh pembeli setelah bayar.</flux:subheading>
                
                <flux:file-upload wire:model="project_file" accept=".zip,.rar,.7z">
                    <flux:file-upload.dropzone
                        heading="Lepas file ZIP di sini atau klik untuk cari"
                        text="ZIP, RAR, 7Z sampai 100MB"
                        with-progress
                        inline
                    />
                </flux:file-upload>

                @if ($project_file)
                    <div class="mt-4 space-y-4">
                        <flux:file-item 
                            :heading="$project_file->getClientOriginalName()" 
                            :size="$project_file->getSize()"
                            icon="archive-box"
                        >
                            <x-slot name="actions">
                                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="$set('project_file', null)" class="!p-1" inset="top bottom" />
                            </x-slot>
                        </flux:file-item>

                        {{-- Changelog field --}}
                        <flux:field>
                            <flux:label>Apa yang baru di versi ini?</flux:label>
                            <flux:textarea wire:model="changelog" rows="3" placeholder="misal. Perbaikan bug minor, penambahan fitur X..." />
                            <flux:error name="changelog" />
                        </flux:field>
                    </div>
                @elseif ($editingId)
                    <div class="mt-4">
                        <flux:badge size="sm" color="zinc" inset="top bottom">
                            File versi lama tetap ada kecuali kamu unggah file baru.
                        </flux:badge>
                    </div>
                @endif
                <flux:error name="project_file" />
            </flux:field>
        </div>

                <div class="flex flex-col gap-4">
                    {{-- Featured Spotlight --}}
                    <div class="p-4 rounded-xl border {{ $stats['featured_limit'] === 0 ? 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 opacity-60' : 'bg-indigo-50/30 dark:bg-indigo-500/5 border-indigo-100 dark:border-indigo-500/20' }}">
                        <div class="flex items-start gap-3">
                            <flux:checkbox wire:model="is_featured" :disabled="$stats['featured_limit'] === 0" />
                            <div class="flex-1 space-y-1">
                                <flux:label class="font-bold flex items-center gap-2">
                                    Promosikan di Beranda (Spotlight)
                                    @if($stats['featured_limit'] > 0)
                                        <flux:badge size="sm" color="indigo" inset="top bottom" class="text-[10px] font-bold">
                                            Slot: {{ $stats['featured_count'] }}/{{ $stats['featured_limit'] }}
                                        </flux:badge>
                                    @elseif($stats['featured_limit'] === -1)
                                        <flux:badge size="sm" color="amber" inset="top bottom" class="text-[10px] font-bold uppercase">Unlimited Elite</flux:badge>
                                    @endif
                                </flux:label>
                                <flux:subheading size="xs" class="leading-relaxed">
                                    @if($stats['featured_limit'] === 0)
                                        Fitur ini hanya tersedia untuk member <strong class="text-zinc-900 dark:text-zinc-100">Pro/Elite</strong>. 
                                        <a href="{{ route('author.plans') }}" class="text-indigo-600 dark:text-indigo-400 font-bold underline decoration-indigo-300 dark:decoration-indigo-700 underline-offset-2">Upgrade sekarang</a>.
                                    @else
                                        Menampilkan produk kamu di bagian "Spotlight" halaman depan untuk trafik maksimal.
                                    @endif
                                </flux:subheading>
                            </div>
                        </div>
                        <flux:error name="is_featured" />
                    </div>

                    @if(auth()->user()->isElite())
                        <flux:field>
                            <div class="flex items-start gap-3 p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-xl">
                                <flux:checkbox wire:model="is_elite_marketed" />
                                <div class="flex-1">
                                    <flux:label class="font-bold text-emerald-800 dark:text-emerald-400 flex items-center gap-2 leading-none">
                                        Elite Marketing Push
                                        <flux:badge size="sm" color="emerald" inset="top bottom" class="text-[10px] font-bold uppercase">Eksklusif</flux:badge>
                                    </flux:label>
                                    <flux:subheading size="xs" class="text-emerald-700/70 dark:text-emerald-400/60 text-balance mt-1">Prioritas algoritma pencarian dan penempatan khusus di newsletter komunitas.</flux:subheading>
                                </div>
                            </div>
                            <flux:error name="is_elite_marketed" />
                        </flux:field>
                    @endif
                </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ $editingId ? 'Perbarui Produk' : 'Buat Produk' }}
                </flux:button>
                <flux:button type="button" variant="ghost" x-on:click="Flux.modal('product-manager-modal').close()">
                    Batal
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
