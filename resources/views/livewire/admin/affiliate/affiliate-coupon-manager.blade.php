<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="font-bold">Affiliate Coupons (Vanity Codes)</flux:heading>
            <flux:subheading>Create special coupon codes linked directly to affiliates.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">Create Vanity Code</flux:button>
    </div>

    <flux:card class="p-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Code</flux:table.column>
                <flux:table.column>Affiliate</flux:table.column>
                <flux:table.column>Discount</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($coupons as $coupon)
                    <flux:table.row :key="$coupon->id">
                        <flux:table.cell class="font-black text-indigo-600 dark:text-indigo-400">{{ $coupon->code }}</flux:table.cell>
                        <flux:table.cell>{{ $coupon->affiliate->name }}</flux:table.cell>
                        <flux:table.cell>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : 'Rp '.number_format($coupon->value) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $coupon->status->value === 'active' ? 'emerald' : 'zinc' }}">
                                {{ $coupon->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $coupon->id }})" />
                                <flux:button size="sm" variant="ghost" color="danger" icon="trash" wire:click="delete({{ $coupon->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="py-12 text-center text-zinc-500 italic">No vanity codes created yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal wire:model="showModal" name="coupon-modal" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingCouponId ? 'Edit' : 'Create' }} Vanity Code</flux:heading>
                <flux:subheading>Link this code to a specific affiliate for attribution.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Coupon Code" placeholder="e.g. DANI10" wire:model="code" />
                <flux:select label="Assign to Affiliate" wire:model="affiliate_id">
                    <option value="">Select Affiliate</option>
                    @foreach($affiliates as $aff)
                        <option value="{{ $aff->id }}">{{ $aff->name }} ({{ $aff->affiliate_code }})</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select label="Discount Type" wire:model="type">
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                </flux:select>
                <flux:input label="Value" type="number" wire:model="value" />
            </div>

            <flux:select label="Status" wire:model="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </flux:select>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="save">Save Coupon</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
