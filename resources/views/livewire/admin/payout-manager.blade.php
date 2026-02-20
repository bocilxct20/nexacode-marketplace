<div class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Payout Management</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="2xl">Payout Management</flux:heading>
            <flux:subheading>Review and process author withdrawal requests.</flux:subheading>
        </div>
    </div>

    {{-- Filters --}}
    <flux:card class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search by author name or email..."
                    icon="magnifying-glass"
                />
            </div>
            <flux:select wire:model.live="statusFilter" class="w-full md:w-48">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="rejected">Rejected</option>
            </flux:select>
        </div>
    </flux:card>

    {{-- Payouts Table --}}
    <flux:card>
        <flux:table :paginate="$payouts">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'author_id'" :direction="$sortDirection" wire:click="sort('author_id')">Author</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'amount'" :direction="$sortDirection" wire:click="sort('amount')">Amount</flux:table.column>
                <flux:table.column>Payment Method</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Requested</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Loading Skeletons --}}
                @foreach(range(1, 5) as $i)
                    <flux:table.row wire:loading wire:target="search, statusFilter, sort">
                        <flux:table.cell><flux:skeleton class="w-32 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-32 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-16 h-5 rounded-full" /></flux:table.cell>
                        <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Actual Data --}}
                @forelse($payouts as $payout)
                    <flux:table.row :key="$payout->id" wire:loading.remove wire:target="search, statusFilter, sort">
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-3">
                                <flux:avatar :src="$payout->author->avatar_url" :initials="$payout->author->initials" size="sm" />
                                <div>
                                    <div class="font-bold text-zinc-900 dark:text-white leading-tight">{{ $payout->author->name }}</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-[10px] text-zinc-500 font-medium lowercase">{{ $payout->author->email }}</div>
                                        @if($payout->author->isPro())
                                            <flux:badge size="sm" color="amber" variant="subtle" class="text-[8px] h-4 px-1 font-black uppercase tracking-tighter">Pro</flux:badge>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $payout->payment_method }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 tabular-nums text-sm">
                            {{ $payout->created_at->format('M d, Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" :color="$payout->status_color" inset="top bottom" class="uppercase text-[10px] font-bold">
                                {{ $payout->status_label }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown align="end">
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" inset="top bottom" />
                                <flux:menu>
                                    @if($payout->status === 'pending')
                                        <flux:menu.item icon="check" wire:click="openApproveModal({{ $payout->id }})" class="hover:text-emerald-600">Approve Payout</flux:menu.item>
                                        <flux:menu.item icon="x-mark" wire:click="openRejectModal({{ $payout->id }})" class="hover:text-red-600">Reject Payout</flux:menu.item>
                                    @endif
                                    <flux:menu.separator />
                                    <flux:menu.item icon="user" href="{{ route('admin.users') }}?search={{ $payout->author->email }}">View Author</flux:menu.item>
                                    @if($payout->admin_note)
                                        <flux:menu.separator />
                                        <flux:menu.item icon="information-circle" disabled>{{ $payout->admin_note }}</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row wire:loading.remove wire:target="search, statusFilter, sort">
                        <flux:table.cell colspan="6" class="text-center text-zinc-500 py-12 italic">
                            No payout requests found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Approval/Rejection Modal --}}
    <flux:modal name="payout-action-modal" wire:model="showModal" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $modalType === 'approve' ? 'Approve Payout' : 'Reject Payout' }}</flux:heading>
                <flux:subheading>
                    {{ $modalType === 'approve' ? 'Record processing details for this withdrawal.' : 'State the reason why this withdrawal request is rejected.' }}
                </flux:subheading>
            </div>

            @if($selectedPayout)
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-2xl space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Author</span>
                        <span class="font-medium">{{ $selectedPayout->author->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Amount</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($selectedPayout->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif

            <flux:field>
                <flux:label>{{ $modalType === 'approve' ? 'Admin Note (Optional)' : 'Rejection Reason (Required)' }}</flux:label>
                <flux:textarea wire:model="adminNote" rows="3" placeholder="e.g. Reference number or reason for rejection..." />
                <flux:error name="adminNote" />
            </flux:field>

            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="ghost" class="w-full">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="processPayout" variant="primary" class="flex-1 {{ $modalType === 'reject' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ $modalType === 'approve' ? 'Confirm Approval' : 'Confirm Rejection' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
