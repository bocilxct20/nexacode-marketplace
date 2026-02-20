<div wire:init="load" class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Moderation Queue</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div>
        <flux:heading size="xl" class="font-bold">Moderation Queue</flux:heading>
        <flux:subheading size="lg">Review and approve new product submissions from authors.</flux:subheading>
    </div>

    <flux:card class="space-y-6">
        <flux:table :paginate="$this->readyToLoad ? $this->products : null" container:class="max-h-80">
            <flux:table.columns>
                <flux:table.column>Product & Author</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Submitted</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @if(!$this->readyToLoad)
                    @foreach(range(1, 10) as $i)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-4">
                                    <flux:skeleton class="w-12 h-12 rounded-lg shrink-0" />
                                    <div class="space-y-2 flex-1">
                                        <flux:skeleton class="w-48 h-4" />
                                        <flux:skeleton class="w-32 h-3" />
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-16 h-5" /></flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-24 h-3" /></flux:table.cell>
                            <flux:table.cell align="right"><flux:skeleton class="w-16 h-8 rounded-md" /></flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @else
                    @forelse($this->products as $product)
                        <flux:table.row :key="$product->id">
                            <flux:table.cell variant="strong">
                                <button wire:click="viewProduct({{ $product->id }})" class="flex items-center gap-4 text-left hover:opacity-80 transition-opacity">
                                    <div class="w-12 h-12 rounded-lg bg-zinc-100 dark:bg-zinc-800 shrink-0 overflow-hidden">
                                        @if($product->thumbnail)
                                            <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                                <flux:icon.photo class="w-6 h-6 text-zinc-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <flux:heading size="sm" class="font-bold">{{ $product->name }}</flux:heading>
                                        <flux:subheading size="xs">by {{ $product->author->name }}</flux:subheading>
                                    </div>
                                </button>
                            </flux:table.cell>
                            <flux:table.cell class="font-bold tabular-nums">Rp {{ number_format($product->price, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-500 tabular-nums">{{ $product->created_at->format('M d, g:i A') }}</flux:table.cell>
                            <flux:table.cell align="right">
                                <flux:button wire:click="viewProduct({{ $product->id }})" variant="subtle" size="sm" icon="pencil-square" inset="top bottom">
                                    Review
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center py-12 text-zinc-500">
                                The moderation queue is empty. Good job!
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-8">
    </div>

    {{-- Product Review Modal --}}
    <flux:modal name="review-product" class="md:w-[800px]">
        @if($selectedProduct)
            <div class="space-y-6">
                <div class="flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-20 h-20 rounded-xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden shrink-0">
                            <img src="{{ $selectedProduct->thumbnail_url ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=200&auto=format&fit=crop' }}" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <flux:heading size="xl">{{ $selectedProduct->name }}</flux:heading>
                            <flux:subheading>{{ $selectedProduct->slug }}</flux:subheading>
                            <div class="mt-2 flex gap-2">
                                <flux:badge size="sm" color="indigo">Author: {{ $selectedProduct->author->name }}</flux:badge>
                                <flux:badge size="sm" color="zinc">${{ number_format($selectedProduct->price, 2) }}</flux:badge>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:label>Admin Review Action</flux:label>
                    <flux:textarea wire:model="rejectionReason" label="Alasan Penolakan (Hanya jika ditolak)" placeholder="Contoh: Screenshot tidak lengkap atau deskripsi kurang jelas..." />
                </div>

                <div class="flex gap-2 pt-4">
                    <flux:button wire:click="approve({{ $selectedProduct->id }})" variant="primary" color="emerald" class="flex-1">Approve Product</flux:button>
                    <flux:button wire:click="reject({{ $selectedProduct->id }})" variant="ghost" color="red">Reject</flux:button>
                    <flux:spacer />
                    <flux:button href="{{ route('products.show', $selectedProduct->slug) }}" target="_blank" variant="ghost" icon="eye">Preview Page</flux:button>
                    <flux:button variant="ghost" x-on:click="Flux.modal('review-product').close()">Close</flux:button>
                </div>
            </div>
        @else
            <div class="py-12 flex justify-center">
                <flux:icon.loading class="w-8 h-8" />
            </div>
        @endif
    </flux:modal>
</div>

@script
    $wire.on('product-moderated', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Moderation Complete',
            text: 'Product status has been updated successfully.'
        });
        Flux.modal('review-product').hide();
    });

    $wire.on('modal-opened', (event) => {
        Flux.modal(event.name).show();
    });

    $wire.on('modal-closed', (event) => {
        const modal = Flux.modal(event.name);
        if (typeof modal.close === 'function') modal.close();
        else if (typeof modal.hide === 'function') modal.hide();
    });
@endscript
