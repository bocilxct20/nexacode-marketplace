<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Marketplace Categories</flux:heading>
            <flux:subheading>Manage structural categories for product items</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Add Category</flux:button>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Icon</flux:table.column>
                <flux:table.column>Category Name</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Products</flux:table.column>
                <flux:table.column>Sort</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell>
                                    @if($category->icon && (str_starts_with($category->icon, 'storage/') || str_starts_with($category->icon, 'http')))
                                        <div class="w-6 h-6 bg-current" style="mask-image: url('{{ asset($category->icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($category->icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;"></div>
                                    @elseif(str_starts_with($category->icon ?? '', 'lucide-'))
                                        <x-dynamic-component :component="$category->icon" class="w-4 h-4 text-zinc-600 dark:text-zinc-400" />
                                    @else
                                        <flux:icon :icon="$category->icon ?: 'folder'" variant="mini" class="text-zinc-500" />
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $category->name }}</span>
                                <span class="text-xs text-zinc-500">{{ $category->slug }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" variant="zinc" class="font-mono text-[10px]">{{ $category->slug }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="indigo" inset="top bottom">{{ $category->products_count ?? $category->products()->count() }} items</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-500">{{ $category->sort_order }}</flux:table.cell>
                        <flux:table.cell align="right">
                            <div class="flex justify-end gap-2">
                                <flux:button wire:click="edit({{ $category->id }})" variant="ghost" size="sm" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $category->id }})" wire:confirm="Are you sure you want to delete this category?" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12 text-zinc-500 italic">
                            No product categories found. Create your first one to organize marketplace items.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Modal for Create/Edit --}}
    <flux:modal wire:model="showModal" name="product-category-modal" class="md:w-[500px]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingCategory ? 'Edit Category' : 'Create New Category' }}</flux:heading>
                <flux:subheading>Define icons and descriptions for this category.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Category Name</flux:label>
                <flux:input wire:model.live="name" placeholder="e.g., PHP Scripts" required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>URL Slug</flux:label>
                <flux:input wire:model="slug" placeholder="php-scripts" required />
                <flux:error name="slug" />
            </flux:field>

            {{-- Single SVG Upload with Full Flux Dropzone --}}
            <flux:field>
                <flux:label>Upload Icon (SVG)</flux:label>
                <flux:subheading>Optional SVG file. Will automatically adapt to theme colors.</flux:subheading>
                
                <div class="mt-4 space-y-4">
                    @if($iconFile)
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                            <div class="w-12 h-12 rounded-lg bg-emerald-500 flex items-center justify-center overflow-hidden">
                                 <div class="w-8 h-8 bg-white" style="mask-image: url('{{ $iconFile->temporaryUrl() }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ $iconFile->temporaryUrl() }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;"></div>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">New Icon Preview</div>
                                <div class="text-xs text-zinc-500">Ready to save</div>
                            </div>
                            <flux:button wire:click="$set('iconFile', null)" variant="ghost" size="sm" icon="x-mark" />
                        </div>
                    @elseif($icon && (str_starts_with($icon, 'storage/') || str_starts_with($icon, 'http')))
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                            <div class="w-12 h-12 rounded-lg bg-emerald-500 flex items-center justify-center overflow-hidden">
                                <div class="w-8 h-8 bg-white" style="mask-image: url('{{ asset($icon) }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ asset($icon) }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;"></div>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Current SVG Icon</div>
                                <div class="text-xs text-zinc-500">Stored in system</div>
                            </div>
                            <flux:button wire:click="$set('icon', '')" variant="ghost" size="sm" icon="trash" class="text-red-500" />
                        </div>
                    @endif

                    <flux:file-upload wire:model="iconFile" accept=".svg">
                        <flux:file-upload.dropzone
                            heading="Unggah Ikon SVG"
                            text="Format .svg saja (disarankan Simple Icons/Single Color)"
                        />
                    </flux:file-upload>
                </div>
                <flux:error name="iconFile" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Icon Name Fallback</flux:label>
                    <flux:input wire:model="icon_name" placeholder="e.g. folder, shopping-cart" />
                    <flux:subheading class="mt-2 text-[10px]">Used if no SVG is uploaded</flux:subheading>
                </flux:field>

                <flux:field>
                    <flux:label>Sort Order</flux:label>
                    <flux:subheading>Ordering in list</flux:subheading>
                    <flux:input wire:model="sort_order" type="number" />
                    <flux:error name="sort_order" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Description (Optional)</flux:label>
                <flux:textarea wire:model="description" placeholder="Short summary of this category..." />
                <flux:error name="description" />
            </flux:field>

            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:spacer />
                <flux:button wire:click="closeModal" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $editingCategory ? 'Update Category' : 'Create Category' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
