<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Help Center Categories</flux:heading>
            <flux:subheading>Manage groups for your help articles</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Add Category</flux:button>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Icon</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Articles</flux:table.column>
                <flux:table.column>Sort</flux:table.column>
                <flux:table.column align="right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell>
                            <flux:icon :icon="$category->icon ?: 'book-open'" variant="mini" class="text-zinc-500" />
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">{{ $category->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" variant="zinc">{{ $category->slug }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $category->articles_count ?? $category->articles()->count() }}</flux:table.cell>
                        <flux:table.cell>{{ $category->sort_order }}</flux:table.cell>
                        <flux:table.cell align="right">
                            <div class="flex justify-end gap-2">
                                <flux:button wire:click="edit({{ $category->id }})" variant="ghost" size="sm" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $category->id }})" wire:confirm="Are you sure?" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Modal for Create/Edit --}}
    <flux:modal wire:model="showModal" name="help-category-modal" class="md:w-[500px]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingCategory ? 'Edit Category' : 'Create Category' }}</flux:heading>
                <flux:subheading>Enter category details below</flux:subheading>
            </div>

            <flux:input wire:model.live="name" label="Category Name" placeholder="e.g., Getting Started" />
            <flux:input wire:model="slug" label="Slug" placeholder="getting-started" />
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="icon" label="Flux Icon Name" placeholder="book-open" />
                <flux:input wire:model="sort_order" type="number" label="Sort Order" />
            </div>

            <flux:textarea wire:model="description" label="Description (Optional)" placeholder="Short summary of this category..." />

            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="submit" variant="primary" class="flex-1">Save Category</flux:button>
                <flux:button type="button" variant="ghost" wire:click="closeModal">Cancel</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
