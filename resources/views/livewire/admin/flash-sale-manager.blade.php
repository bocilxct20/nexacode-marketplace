<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading level="1">NexaFlash™ Manager</flux:heading>
            <flux:subheading>Manage marketplace-wide discount events and urgency banners.</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-1">
            <flux:card class="space-y-6">
                <flux:heading size="lg">Create / Edit Sale</flux:heading>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="title" label="Sale Title" placeholder="e.g. Ramadan Tech Sale" />
                    
                    <flux:input wire:model="discount_percentage" type="number" label="Discount %" placeholder="20" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="starts_at" type="datetime-local" label="Starts At" />
                        <flux:input wire:model="ends_at" type="datetime-local" label="Ends At" />
                    </div>

                    <flux:textarea wire:model="banner_message" label="Banner Message" placeholder="Flash Sale is LIVE! Get 20% OFF all premium assets now." />

                    <flux:switch wire:model="is_active" label="Mark as Active" />

                    <div class="flex space-x-2 pt-4">
                        <flux:button type="submit" variant="primary" class="w-full">Save Flash Sale</flux:button>
                        @if($editingSaleId)
                            <flux:button wire:click="$set('editingSaleId', null)" variant="ghost">Cancel</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        </div>

        <!-- List -->
        <div class="lg:col-span-2">
            <flux:card>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Sale Event</flux:table.column>
                        <flux:table.column>Discount</flux:table.column>
                        <flux:table.column>Duration</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($sales as $sale)
                            <flux:table.row :key="$sale->id">
                                <flux:table.cell>
                                    <div class="font-medium">{{ $sale->title }}</div>
                                    <div class="text-xs text-zinc-500">{{ $sale->banner_message }}</div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="cyan">{{ $sale->discount_percentage }}%</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm italic">
                                        {{ $sale->starts_at->format('d M') }} - {{ $sale->ends_at->format('d M') }}
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($sale->is_active && $sale->starts_at <= now() && $sale->ends_at >= now())
                                        <flux:badge color="green" size="sm" inset="top bottom">LIVE</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex justify-end space-x-2">
                                    <flux:button wire:click="delete({{ $sale->id }})" icon="trash" size="sm" variant="ghost" />
                                </flux:cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>
</div>
