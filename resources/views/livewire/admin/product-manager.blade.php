<div class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Product Management</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="2xl">Product Management</flux:heading>
            <flux:subheading>Manage all digital products, prices, and visibility on the platform.</flux:subheading>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 items-end">
        <flux:field class="flex-1">
            <flux:label>Search Products</flux:label>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by name, slug, or author..." />
        </flux:field>

        <flux:field>
            <flux:label>Status Filter</flux:label>
            <flux:select wire:model.live="statusFilter" placeholder="All Status">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </flux:select>
        </flux:field>
    </div>

    <flux:card class="space-y-6">
        <flux:table :paginate="$this->products" container:class="max-h-80">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Product</flux:table.column>
                <flux:table.column>Author</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection" wire:click="sort('price')">Price</flux:table.column>
                <flux:table.column>Stats</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                {{-- Loading Skeletons (Matching actual row height and thumbnail size) --}}
                @foreach(range(1, 10) as $i)
                    <flux:table.row wire:loading wire:target="search, statusFilter, sort">
                        <flux:table.cell>
                            <div class="flex items-center gap-4">
                                <flux:skeleton class="w-12 h-12 rounded-lg shrink-0" />
                                <div class="space-y-2 flex-1">
                                    <flux:skeleton class="w-48 h-4" />
                                    <flux:skeleton class="w-32 h-3" />
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-32 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-5" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="space-y-2">
                                <flux:skeleton class="w-16 h-3" />
                                <flux:skeleton class="w-12 h-3" />
                            </div>
                        </flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-20 h-6 rounded-full" /></flux:table.cell>
                        <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Actual Data --}}
                @forelse($this->products as $product)
                    <flux:table.row :key="$product->id" wire:loading.remove wire:target="search, statusFilter, sort">
                        <flux:table.cell variant="strong">
                            <button wire:click="viewProduct({{ $product->id }})" class="flex items-center gap-4 text-left hover:opacity-80 transition-opacity">
                                <div class="w-12 h-12 rounded-lg bg-zinc-100 dark:bg-zinc-800 shrink-0 overflow-hidden border border-zinc-200 dark:border-zinc-800">
                                    <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover" />
                                </div>
                                <div>
                                    <div class="font-bold text-sm">{{ $product->name }}</div>
                                    <div class="text-xs text-zinc-500 font-mono">{{ $product->slug }}</div>
                                </div>
                            </button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm font-medium">{{ $product->author->name }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-bold tabular-nums" variant="strong">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-xs text-zinc-500">
                                <div>Sales: <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $product->sales_count }}</span></div>
                                <div>Rating: <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($product->avg_rating, 1) }}</span></div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$product->status_color" inset="top bottom">
                                {{ $product->status_label }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="right">
                            <flux:dropdown align="end">
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" inset="top bottom" />
                                <flux:menu>
                                    <flux:menu.item wire:click="viewProduct({{ $product->id }})" icon="eye">View Details</flux:menu.item>
                                    <flux:menu.separator />
                                    @if($product->status === 'approved')
                                        <flux:menu.item wire:click="toggleFeatured({{ $product->id }})" icon="{{ $product->is_featured ? 'sparkles' : 'star' }}">
                                            {{ $product->is_featured ? 'Remove from Spotlight' : 'Add to Spotlight' }}
                                        </flux:menu.item>
                                    @endif
                                    <flux:menu.separator />
                                    @if($product->status !== 'approved')
                                        <flux:menu.item wire:click="updateStatus({{ $product->id }}, 'approved')" icon="check">Approve</flux:menu.item>
                                    @endif
                                    @if($product->status !== 'rejected')
                                        <flux:menu.item wire:click="updateStatus({{ $product->id }}, 'rejected')" icon="x-mark" class="text-red-600">Reject</flux:menu.item>
                                    @endif
                                    @if($product->status === 'approved')
                                        <flux:menu.item wire:click="updateStatus({{ $product->id }}, 'pending')" icon="clock">Move to Pending</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row wire:loading.remove wire:target="search, statusFilter, sort">
                        <flux:table.cell colspan="6" class="text-center py-12 text-zinc-500">
                            No products found matching your criteria.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
    </div>

    {{-- Product Details Modal --}}
    <flux:modal wire:model="showModal" name="product-details" class="md:w-[800px]">
        @if($selectedProduct)
            <div class="space-y-6">
                <div class="flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-20 h-20 rounded-xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden shrink-0">
                            <img src="{{ $selectedProduct->thumbnail_url }}" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <flux:heading size="xl">{{ $selectedProduct->name }}</flux:heading>
                            <flux:subheading>{{ $selectedProduct->slug }}</flux:subheading>
                            <div class="mt-2 flex gap-2">
                                <flux:badge size="sm" color="indigo">{{ $selectedProduct->author->name }}</flux:badge>
                                <flux:badge size="sm" color="zinc text-emerald-600 dark:text-emerald-400 font-black">Rp {{ number_format($selectedProduct->price, 0, ',', '.') }}</flux:badge>
                            </div>
                        </div>
                    </div>
                    <flux:badge :color="$selectedProduct->status_color">
                        {{ $selectedProduct->status_label }}
                    </flux:badge>
                </div>

                <div class="space-y-2">
                    <flux:label>Product Description</flux:label>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-600 dark:text-zinc-400">
                        {!! nl2br(e($selectedProduct->description ?? 'No description available.')) !!}
                    </div>
                </div>

                @if(count($selectedProduct->screenshots_urls) > 0)
                    <div class="space-y-2">
                        <flux:label>Screenshots</flux:label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($selectedProduct->screenshots_urls as $url)
                                <div class="aspect-video rounded-lg bg-zinc-100 dark:bg-zinc-800 overflow-hidden transition-transform hover:scale-[1.05]">
                                    <img src="{{ $url }}" class="w-full h-full object-cover" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex gap-2">
                        @if($selectedProduct->status !== \App\Enums\ProductStatus::APPROVED)
                            <flux:button wire:click="updateStatus({{ $selectedProduct->id }}, 'approved')" variant="primary" color="emerald">Approve</flux:button>
                        @endif
                        @if($selectedProduct->status !== 'rejected')
                            <flux:button wire:click="updateStatus({{ $selectedProduct->id }}, 'rejected')" variant="ghost" color="red">Reject</flux:button>
                        @endif
                    </div>
                    <div class="flex justify-end gap-2">
                        <flux:button href="{{ route('products.show', $selectedProduct->slug) }}" target="_blank" variant="ghost" icon="arrow-top-right-on-square">Live View</flux:button>
                        <flux:button variant="ghost" x-on:click="Flux.modal('product-details').close()">Close</flux:button>
                    </div>
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
    $wire.on('product-status-updated', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Product Updated',
            text: 'The product status has been successfully updated.'
        });
    });
@endscript
