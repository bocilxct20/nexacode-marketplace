<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="font-bold">Affiliate Payouts</flux:heading>
            <flux:subheading>Manage and process withdrawal requests from affiliates.</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card class="p-6">
            <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Pending Requests</flux:subheading>
            <flux:heading size="xl" class="font-black tabular-nums text-amber-500">{{ $stats['pending_count'] }}</flux:heading>
        </flux:card>

        <flux:card class="p-6">
            <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Pending Amount</flux:subheading>
            <flux:heading size="xl" class="font-black tabular-nums text-amber-500">Rp {{ number_format($stats['pending_amount'], 0, ',', '.') }}</flux:heading>
        </flux:card>

        <flux:card class="p-6">
            <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Total Paid Out</flux:subheading>
            <flux:heading size="xl" class="font-black tabular-nums text-emerald-500">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</flux:heading>
        </flux:card>
    </div>

    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="flex gap-2 w-full md:w-auto">
            <flux:radio.group wire:model.live="statusOrder" variant="segmented" size="sm">
                <flux:radio label="Pending" value="pending" />
                <flux:radio label="Approved" value="approved" />
                <flux:radio label="Paid" value="paid" />
                <flux:radio label="Rejected" value="rejected" />
            </flux:radio.group>
        </div>

        <div class="w-full md:w-64">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search affiliate..." icon="magnifying-glass" />
        </div>
    </div>

    <flux:card class="p-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Affiliate</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Bank Details</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($payouts as $payout)
                    <flux:table.row :key="$payout->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:avatar :src="$payout->user->avatar" :initials="$payout->user->initials" size="sm" />
                                <div>
                                    <div class="font-bold text-sm">{{ $payout->user->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $payout->user->email }}</div>
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="font-bold text-zinc-900 dark:text-white">
                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-xs">
                                <div class="font-bold">{{ $payout->bank_name }}</div>
                                <div>{{ $payout->account_number }}</div>
                                <div class="text-zinc-500">{{ $payout->account_name }}</div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $payout->created_at->format('M d, Y') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="ghost" icon="eye" wire:click="selectPayout({{ $payout->id }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="py-12 text-center text-zinc-500 italic">No payout requests found.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
            {{ $payouts->links() }}
        </div>
    </flux:card>

    @if($selectedPayout)
        <flux:modal name="payout-details" :open="true" @close="$wire.selectedPayout = null">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Payout Request #{{ $selectedPayout->id }}</flux:heading>
                    <flux:subheading>Reviewing withdrawal for {{ $selectedPayout->user->name }}</flux:subheading>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl space-y-4">
                    <div class="flex justify-between">
                        <flux:text size="sm">Amount:</flux:text>
                        <flux:text size="sm" class="font-bold">Rp {{ number_format($selectedPayout->amount, 0, ',', '.') }}</flux:text>
                    </div>
                    <div class="flex justify-between border-t border-zinc-200 dark:border-zinc-800 pt-4">
                        <flux:text size="sm">Bank Name:</flux:text>
                        <flux:text size="sm" class="font-bold">{{ $selectedPayout->bank_name }}</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text size="sm">Account Number:</flux:text>
                        <flux:text size="sm" class="font-bold">{{ $selectedPayout->account_number }}</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text size="sm">Account Name:</flux:text>
                        <flux:text size="sm" class="font-bold">{{ $selectedPayout->account_name }}</flux:text>
                    </div>
                </div>

                <flux:textarea label="Admin Notes" placeholder="Add a note (e.g., Transfer reference ID)..." wire:model="adminNote" />

                <div class="flex flex-col gap-2">
                    @if($selectedPayout->status === 'pending')
                        <flux:button variant="primary" icon="check" wire:click="updateStatus({{ $selectedPayout->id }}, 'approved')">Approve Request</flux:button>
                    @elseif($selectedPayout->status === 'approved')
                        <flux:button variant="primary" color="emerald" icon="banknotes" wire:click="updateStatus({{ $selectedPayout->id }}, 'paid')">Mark as Paid</flux:button>
                    @endif
                    
                    @if($selectedPayout->status !== 'rejected')
                        <flux:button variant="danger" icon="x-mark" wire:click="updateStatus({{ $selectedPayout->id }}, 'rejected')">Reject Request</flux:button>
                    @endif
                    
                    <flux:modal.close>
                        <flux:button variant="ghost" class="w-full">Cancel</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
