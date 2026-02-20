<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" class="mb-1">Bundle Manager</flux:heading>
            <flux:text>Kemas produk terbaikmu dalam satu paket diskon untuk meningkatkan penjualan.</flux:text>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Create New Bundle</flux:button>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card class="flex flex-col gap-1">
            <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Total Bundles</flux:text>
            <flux:heading size="xl">{{ $bundles->total() }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Active Bundles</flux:text>
            <flux:heading size="xl" color="emerald">{{ $bundles->where('status', 'active')->count() }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Drafts</flux:text>
            <flux:heading size="xl" color="amber">{{ $bundles->where('status', 'draft')->count() }}</flux:heading>
        </flux:card>
    </div>

    {{-- Bundle List Table --}}
    <flux:card class="p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Bundle</flux:table.column>
                <flux:table.column>Products</flux:table.column>
                <flux:table.column>Discount</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Sales</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($bundles as $bundle)
                    <flux:table.row :key="$bundle->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                    <img src="{{ $bundle->thumbnail_url }}" alt="{{ $bundle->name }}" class="size-full object-cover">
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $bundle->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $bundle->slug }}</div>
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge variant="subtle">{{ $bundle->products_count }} items</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($bundle->discount_amount > 0)
                                <span class="text-emerald-600 font-bold">-Rp {{ number_format($bundle->discount_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-emerald-600 font-bold">-{{ $bundle->discount_percentage }}%</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-status-badge :status="$bundle->status" />
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $bundle->sales_count }}
                        </flux:table.cell>
                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" icon="ellipsis-horizontal" square size="sm" />
                                <flux:menu>
                                    <flux:menu.item wire:click="edit({{ $bundle->id }})" icon="pencil">Edit Bundle</flux:menu.item>
                                    <flux:menu.item wire:click="delete({{ $bundle->id }})" variant="danger" icon="trash">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12">
                            <flux:text>Belum ada bundel yang dibuat.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        
        @if($bundles->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $bundles->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Bundle' : 'Create New Bundle' }}</flux:heading>
                <flux:text>Atur rincian paket produk hemat Anda.</flux:text>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <flux:input wire:model="name" label="Bundle Name" placeholder="e.g. Ultimate Web Starter Pack" />
                
                <flux:textarea wire:model="description" label="Description" placeholder="Jelaskan keuntungan membeli paket ini..." />

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Discount Type</flux:label>
                        <flux:select wire:model="discount_type">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (Rp)</option>
                        </flux:select>
                    </flux:field>

                    @if($discount_type === 'percentage')
                        <flux:input wire:model="discount_percentage" label="Discount Percentage" type="number" min="0" max="100" />
                    @else
                        <flux:input wire:model="discount_amount" label="Discount Amount (Rp)" type="number" min="0" />
                    @endif
                </div>

                <flux:field>
                    <flux:label>Included Products (Minimum 2)</flux:label>
                    <div class="mt-2 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 space-y-2 max-h-60 overflow-y-auto">
                        @foreach($authorProducts as $product)
                            <label class="flex items-center gap-3 p-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-md cursor-pointer">
                                <input type="checkbox" wire:model="selectedProducts" value="{{ $product->id }}" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-medium">{{ $product->name }}</span>
                                <span class="text-xs text-zinc-500">(Rp {{ number_format($product->price, 0, ',', '.') }})</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedProducts') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Thumbnail</flux:label>
                        <input type="file" wire:model="thumbnail" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        @if($existingThumbnail && !$thumbnail)
                            <div class="mt-2 text-xs text-zinc-500">Current: {{ basename($existingThumbnail) }}</div>
                        @endif
                    </flux:field>

                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model="status">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update Bundle' : 'Create Bundle' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
