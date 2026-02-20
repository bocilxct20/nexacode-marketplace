@pushonce('head')
    <link href="{{ asset('css/quill.snow.css') }}" rel="stylesheet">
    <script src="{{ asset('js/quill.js') }}"></script>
@endpushonce

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ $isEdit ? 'Edit Artikel' : 'Buat Artikel Baru' }}</flux:heading>
            <flux:subheading>Kelola panduan dan bantuan untuk pengguna platform</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.help.articles') }}" variant="ghost" icon="arrow-left" wire:navigate>
            Kembali ke Daftar
        </flux:button>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            {{-- Left Side: Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                <flux:tabs variant="segmented">
                    <flux:tab :current="$tab === 'content'" wire:click="$set('tab', 'content')">Konten Utama</flux:tab>
                    <flux:tab :current="$tab === 'seo'" wire:click="$set('tab', 'seo')">SEO & Rich Snippets</flux:tab>
                    <flux:tab :current="$tab === 'notes'" wire:click="$set('tab', 'notes')">Catatan & Admin</flux:tab>
                    @if($isEdit)
                        <flux:tab :current="$tab === 'versions'" wire:click="$set('tab', 'versions')">Riwayat Versi</flux:tab>
                    @endif
                </flux:tabs>

                @if($tab === 'content')
                    <div class="mt-6 space-y-6">
                        <flux:card class="space-y-6">
                            <flux:field>
                                <flux:label>Judul Artikel</flux:label>
                                <flux:input wire:model.live="title" placeholder="Contoh: Cara mengatur profil kamu" required />
                                <flux:error name="title" />
                            </flux:field>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:field>
                                    <flux:label>Slug (URL)</flux:label>
                                    <flux:input wire:model="slug" placeholder="cara-mengatur-profil" required />
                                    <flux:error name="slug" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Kategori</flux:label>
                                    <flux:select wire:model="help_category_id" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="help_category_id" />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>Ringkasan Singkat (Excerpt)</flux:label>
                                <flux:textarea wire:model="excerpt" rows="3" placeholder="Ringkasan pendek untuk hasil pencarian..." />
                                <flux:error name="excerpt" />
                            </flux:field>

                            <flux:separator />

                            <flux:field>
                                <flux:label>Isi Artikel</flux:label>
                                
                                <div 
                                    x-data="{
                                        content: @entangle('content'),
                                        quill: null,
                                        init() {
                                            if (typeof window.Quill === 'undefined') {
                                                console.error('Quill is not defined. Retrying in 100ms...');
                                                setTimeout(() => this.init(), 100);
                                                return;
                                            }

                                            this.quill = new window.Quill($refs.quillEditor, {
                                                theme: 'snow',
                                                modules: {
                                                    toolbar: [
                                                        [{ 'header': [1, 2, 3, false] }],
                                                        ['bold', 'italic', 'underline', 'strike'],
                                                        ['blockquote', 'code-block'],
                                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                        ['link', 'clean']
                                                    ]
                                                }
                                            });
                                            this.quill.root.innerHTML = this.content || '';
                                            this.quill.on('text-change', () => {
                                                this.content = this.quill.root.innerHTML;
                                            });
                                            this.$watch('content', value => {
                                                if (value !== this.quill.root.innerHTML) {
                                                    this.quill.root.innerHTML = value || '';
                                                }
                                            });
                                        }
                                    }"
                                    wire:ignore
                                    class="block w-full shadow-xs border rounded-lg bg-white dark:bg-white/5 border-zinc-200 dark:border-white/10 overflow-hidden"
                                >
                                    <div x-ref="quillEditor" class="min-h-[500px] prose dark:prose-invert max-w-none text-zinc-900 border-none !bg-white dark:!bg-zinc-900"></div>
                                </div>
                                <flux:error name="content" />
                            </flux:field>
                        </flux:card>
                    </div>
                @endif

                @if($tab === 'seo')
                    <div class="mt-6">
                        <flux:card class="space-y-6">
                            <flux:field>
                                <flux:label>Tipe Schema (Rich Snippets)</flux:label>
                                <flux:select wire:model.live="schema_type">
                                    <option value="none">Tanpa Schema Khusus</option>
                                    <option value="faq">FAQ (Tanya Jawab)</option>
                                    <option value="howto">How-To (Langkah Kerja)</option>
                                </flux:select>
                                <flux:description>Pilih tipe data terstruktur untuk meningkatkan tampilan di Google.</flux:description>
                            </flux:field>

                            @if($schema_type === 'faq')
                                <div class="space-y-4">
                                    <flux:heading size="sm">Daftar Pertanyaan & Jawaban</flux:heading>
                                    @foreach($schema_data as $index => $item)
                                        <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-4 relative">
                                            <flux:button wire:click="removeSchemaItem({{ $index }})" variant="ghost" icon="trash" size="sm" class="absolute top-2 right-2 text-red-500" />
                                            <flux:field>
                                                <flux:label>Pertanyaan #{{ $index + 1 }}</flux:label>
                                                <flux:input wire:model="schema_data.{{ $index }}.question" placeholder="Apa itu..." />
                                            </flux:field>
                                            <flux:field>
                                                <flux:label>Jawaban</flux:label>
                                                <flux:textarea wire:model="schema_data.{{ $index }}.answer" rows="2" placeholder="Jawaban singkat..." />
                                            </flux:field>
                                        </div>
                                    @endforeach
                                    <flux:button wire:click="addSchemaItem" variant="outline" icon="plus" size="sm">Tambah Pertanyaan</flux:button>
                                </div>
                            @elseif($schema_type === 'howto')
                                <div class="space-y-4">
                                    <flux:heading size="sm">Langkah-Langkah (How-To)</flux:heading>
                                    @foreach($schema_data as $index => $item)
                                        <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-4 relative">
                                            <flux:button wire:click="removeSchemaItem({{ $index }})" variant="ghost" icon="trash" size="sm" class="absolute top-2 right-2 text-red-500" />
                                            <flux:field>
                                                <flux:label>Judul Langkah #{{ $index + 1 }}</flux:label>
                                                <flux:input wire:model="schema_data.{{ $index }}.title" placeholder="Langkah 1..." />
                                            </flux:field>
                                            <flux:field>
                                                <flux:label>Deskripsi Langkah</flux:label>
                                                <flux:textarea wire:model="schema_data.{{ $index }}.step" rows="2" placeholder="Detail cara melakukannya..." />
                                            </flux:field>
                                        </div>
                                    @endforeach
                                    <flux:button wire:click="addSchemaItem" variant="outline" icon="plus" size="sm">Tambah Langkah</flux:button>
                                </div>
                            @endif
                        </flux:card>
                    </div>
                @endif

                @if($tab === 'notes')
                    <div class="mt-6">
                        <flux:card class="space-y-6">
                            <flux:field>
                                <flux:label>Catatan Internal (Khusus Admin)</flux:label>
                                <flux:textarea wire:model="internal_notes" rows="6" placeholder="Beri catatan untuk admin lain tentang artikel ini..." />
                                <flux:description>Catatan ini tidak akan tampil di sisi publik/pembeli.</flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label>Custom Read Time (Menit)</flux:label>
                                <flux:input type="number" wire:model="read_time_minutes" placeholder="Kosongkan untuk hitung otomatis" />
                                <flux:description>Jika diisi, sistem akan menggunakan angka ini daripada menghitung kata.</flux:description>
                            </flux:field>
                        </flux:card>
                    </div>
                @endif

                @if($tab === 'versions' && $isEdit)
                    <div class="mt-6">
                        <flux:card>
                            <flux:heading size="lg" class="mb-6">Riwayat Perubahan</flux:heading>
                            <div class="space-y-4">
                                @forelse($article->versions as $version)
                                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                                                <flux:icon name="clock" variant="mini" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold">{{ $version->created_at->format('d M N, H:i') }}</p>
                                                <p class="text-[10px] text-zinc-500 uppercase">Oleh {{ $version->updater->name ?? 'System' }}</p>
                                            </div>
                                        </div>
                                        <flux:button variant="ghost" size="sm" icon="eye" disabled>Lihat Snapshot</flux:button>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-zinc-500">
                                        Belum ada riwayat perubahan yang tercatat.
                                    </div>
                                @endforelse
                            </div>
                        </flux:card>
                    </div>
                @endif
            </div>

            {{-- Right Side: Stats & Options --}}
            <div class="space-y-6">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">Pengaturan</flux:heading>
                    
                    <flux:field>
                        <flux:label>Urutan Tampil</flux:label>
                        <flux:input type="number" wire:model="sort_order" min="0" />
                        <flux:error name="sort_order" />
                    </flux:field>

                    <flux:separator />

                    <div class="space-y-4">
                        <flux:checkbox wire:model="is_published" label="Terbitkan Sekarang" />
                        <flux:checkbox wire:model="is_featured" label="Jadikan Unggulan" />
                    </div>
                </flux:card>

                @if($isEdit && $article)
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Statistik Real-time</flux:heading>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-white/5 border border-zinc-200 dark:border-white/10">
                                <span class="text-sm text-zinc-500">Total Dilihat</span>
                                <span class="font-bold text-zinc-900 dark:text-white">{{ number_format($article->views_count) }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20">
                                <span class="text-sm text-emerald-700 dark:text-emerald-400">Terbantu (Helpful)</span>
                                <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ number_format($article->helpful_count) }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20">
                                <span class="text-sm text-red-700 dark:text-red-400">Tidak Terbantu</span>
                                <span class="font-bold text-red-700 dark:text-red-400">{{ number_format($article->unhelpful_count) }}</span>
                            </div>
                        </div>
                    </flux:card>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 justify-end">
            <flux:button href="{{ route('admin.help.articles') }}" variant="ghost" wire:navigate>Batalkan</flux:button>
            <flux:button type="submit" variant="primary" class="px-8">
                {{ $isEdit ? 'Simpan Perubahan' : 'Buat Artikel' }}
            </flux:button>
        </div>
    </form>
</div>
