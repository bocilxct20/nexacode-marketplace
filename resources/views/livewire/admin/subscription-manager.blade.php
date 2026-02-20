<div class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Subscriptions</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="2xl">Subscription Manager</flux:heading>
            <flux:subheading>Manage author subscription tiers, pricing, and platform commission rates.</flux:subheading>
        </div>
    </div>

    <flux:card class="space-y-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Plan Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection" wire:click="sort('price')">Monthly Price</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'commission_rate'" :direction="$sortDirection" wire:click="sort('commission_rate')">Commission</flux:table.column>
                <flux:table.column>Status & Attributes</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Loading Skeletons --}}
                @foreach(range(1, 3) as $i)
                    <flux:table.row wire:loading wire:target="sort">
                        <flux:table.cell><flux:skeleton class="w-32 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-16 h-4" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:skeleton class="w-16 h-5 rounded-full" />
                                <flux:skeleton class="w-16 h-5 rounded-full" />
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Actual Data --}}
                @foreach($plans as $plan)
                    <flux:table.row :key="$plan->id" wire:loading.remove wire:target="sort">
                        <flux:table.cell variant="strong" class="text-zinc-900 dark:text-white font-bold">
                            {{ $plan->name }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                            Rp {{ number_format($plan->price, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell class="font-medium text-indigo-600 dark:text-indigo-400">
                            {{ $plan->commission_rate }}%
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <flux:badge color="{{ $plan->is_active ? 'emerald' : 'zinc' }}" size="sm" inset="top bottom">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </flux:badge>
                                @if($plan->is_default) <flux:badge color="blue" size="sm" inset="top bottom">Default</flux:badge> @endif
                                @if($plan->allow_trial) <flux:badge color="purple" size="sm" inset="top bottom">Trial</flux:badge> @endif
                                @if($plan->is_elite) <flux:badge color="amber" size="sm" inset="top bottom">Elite</flux:badge> @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="right">
                            <flux:dropdown align="end">
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" inset="top bottom" />
                                <flux:menu>
                                    <flux:menu.item wire:click="edit({{ $plan->id }})" icon="pencil-square">Edit Plan</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="magnifying-glass" disabled>View Details</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal name="edit-plan-modal" class="md:w-[600px]">
        @if($editingPlan)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Plan: {{ $planForm['name'] }}</flux:heading>
                    <flux:subheading>Update pricing, commission, and specific tier benefits.</flux:subheading>
                </div>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Plan Name</flux:label>
                        <flux:input wire:model="planForm.name" />
                        <flux:error name="planForm.name" />
                    </flux:field>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Monthly Price (Rp)</flux:label>
                            <flux:input wire:model="planForm.price" type="number" icon="banknotes" />
                            <flux:error name="planForm.price" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Commission Rate (%)</flux:label>
                            <flux:input wire:model="planForm.commission_rate" type="number" step="0.01" icon="receipt-percent" />
                            <flux:error name="planForm.commission_rate" />
                        </flux:field>
                    </div>

                    <div class="space-y-3 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <flux:label>Plan Attributes</flux:label>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-6">
                            <flux:checkbox wire:model="planForm.is_active" label="Plan is public & active" />
                            <flux:checkbox wire:model="planForm.is_default" label="Mark as Default fallback" />
                            <flux:checkbox wire:model="planForm.allow_trial" label="Enable 7-day Free Trial" />
                            <flux:checkbox wire:model="planForm.is_elite" label="Elite Author Tier Perk" />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:label>Premium Features</flux:label>
                        <div class="flex gap-2">
                            <flux:input wire:model="newFeature" placeholder="Add a new perk..." class="flex-1" wire:keydown.enter.prevent="addFeature" />
                            <flux:button wire:click="addFeature" variant="subtle" icon="plus" />
                        </div>
                        
                        <div class="max-h-40 overflow-y-auto space-y-2 mt-2 pr-2">
                            @foreach($planForm['features'] as $index => $feature)
                                <div class="flex items-center gap-2 group transition-all">
                                    <flux:input wire:model="planForm.features.{{ $index }}" placeholder="Feature description..." class="flex-1" />
                                    <flux:button wire:click="removeFeature({{ $index }})" variant="ghost" icon="trash" size="xs" class="text-zinc-400 hover:text-red-500 transition-colors" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel Changes</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="save" variant="primary" color="indigo" class="px-8">Save Plan Updates</flux:button>
                </div>
            </div>
        @else
            <div class="py-12 flex justify-center">
                <flux:icon.loading class="w-8 h-8" />
            </div>
        @endif
    </flux:modal>
</div>
