<div wire:init="load">
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Refund Requests</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <flux:heading size="xl">Refund Requests</flux:heading>
    <flux:subheading>Review and manage customer refund requests.</flux:subheading>

    <flux:separator variant="subtle" class="my-8" />

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @if(!$readyToLoad)
            @for($i=0; $i<4; $i++)
                <flux:card class="animate-pulse">
                    <div class="space-y-2">
                        <flux:skeleton class="h-3 w-20" />
                        <flux:skeleton class="h-8 w-16" />
                    </div>
                </flux:card>
            @endfor
        @else
            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Requests</flux:subheading>
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                </div>
            </flux:card>

            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Pending</flux:subheading>
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</div>
                </div>
            </flux:card>

            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Approved</flux:subheading>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['approved'] }}</div>
                </div>
            </flux:card>

            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Rejected</flux:subheading>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['rejected'] }}</div>
                </div>
            </flux:card>
        @endif
    </div>

    {{-- Filter --}}
    <flux:card class="mb-8">
        <flux:select wire:model.live="statusFilter" class="w-full md:w-48">
            <option value="all">All Requests</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </flux:select>
    </flux:card>

    {{-- Refund Requests List --}}
    <div class="space-y-6">
        @if(!$readyToLoad)
            @for($i=0; $i<3; $i++)
                <flux:card class="animate-pulse">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="space-y-2">
                                <flux:skeleton class="h-6 w-32" />
                                <flux:skeleton class="h-4 w-48" />
                            </div>
                            <flux:skeleton class="h-6 w-20 rounded-full" />
                        </div>
                    </div>
                </flux:card>
            @endfor
        @else
            @forelse($refunds as $refund)
                <flux:card>
                    <div class="space-y-4">
                        {{-- Header --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-lg">Refund Request #{{ $refund->id }}</div>
                                <div class="text-sm text-zinc-500">
                                    Order #{{ $refund->order->transaction_id }} • 
                                    {{ $refund->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                            <flux:badge :color="match($refund->status->value) {
                                'approved' => 'emerald',
                                'rejected' => 'red',
                                'pending' => 'amber',
                                default => 'zinc'
                            }">
                                {{ $refund->status->label() }}
                            </flux:badge>
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Details --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs uppercase tracking-wider text-zinc-500 mb-1">Customer</div>
                                <div class="font-semibold">{{ $refund->user->name }}</div>
                                <div class="text-sm text-zinc-500">{{ $refund->user->email }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wider text-zinc-500 mb-1">Order Amount</div>
                                <div class="font-semibold text-lg">Rp {{ number_format($refund->order->total_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <div class="text-xs uppercase tracking-wider text-zinc-500 mb-2">Refund Reason</div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm">
                                {{ $refund->reason }}
                            </div>
                        </div>

                        @if($refund->admin_notes)
                            <div>
                                <div class="text-xs uppercase tracking-wider text-zinc-500 mb-2">Admin Notes</div>
                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm">
                                    {{ $refund->admin_notes }}
                                </div>
                            </div>
                        @endif

                        <flux:separator variant="subtle" />

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            @if($refund->status->value === 'pending')
                                <flux:button 
                                    wire:click="openReviewModal({{ $refund->id }})" 
                                    variant="primary" 
                                    size="sm"
                                    icon="clipboard-document-check"
                                >
                                    Review Request
                                </flux:button>
                            @else
                                    @if($refund->processed_at)
                                        Processed {{ $refund->processed_at->diffForHumans() }}
                                    @else
                                        Processed
                                    @endif
                            @endif
                        </div>
                    </div>
                </flux:card>
            @empty
                <flux:card class="text-center py-20 px-6">
                    <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <flux:icon.currency-dollar class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <flux:heading size="xl" class="mb-2">No refund requests found</flux:heading>
                    <flux:subheading>Refund requests will appear here when customers submit them.</flux:subheading>
                </flux:card>
            @endforelse
        @endif
    </div>

    {{-- Pagination --}}
    @if(method_exists($refunds, 'hasPages') && $refunds->hasPages())
        <div class="mt-6">
            {{ $refunds->links() }}
        </div>
    @endif

    {{-- Review Modal --}}
    @if($selectedRefund)
        <flux:modal name="review-refund-modal" :open="$showReviewModal" wire:model="showReviewModal" class="space-y-6 max-w-2xl">
            <div>
                <flux:heading size="lg">Review Refund Request #{{ $selectedRefund->id }}</flux:heading>
                <flux:subheading>Order #{{ $selectedRefund->order->transaction_id }}</flux:subheading>
            </div>

            <flux:separator variant="subtle" />

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500 mb-1">Customer</div>
                        <div class="font-semibold">{{ $selectedRefund->user->name }}</div>
                        <div class="text-sm text-zinc-500">{{ $selectedRefund->user->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500 mb-1">Amount</div>
                        <div class="font-semibold text-lg">Rp {{ number_format($selectedRefund->order->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wider text-zinc-500 mb-2">Customer's Reason</div>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm">
                        {{ $selectedRefund->reason }}
                    </div>
                </div>

                <flux:field>
                    <flux:label>Admin Notes (Optional for approval, Required for rejection)</flux:label>
                    <flux:textarea 
                        wire:model="adminNotes" 
                        placeholder="Add notes about this refund decision..."
                        rows="4"
                    />
                    <flux:error name="adminNotes" />
                    <flux:description>These notes will be included in the email to the customer.</flux:description>
                </flux:field>
            </div>

            <flux:separator variant="subtle" />

            <div class="flex gap-3 justify-end">
                <flux:button type="button" wire:click="closeReviewModal" variant="ghost">Cancel</flux:button>
                <flux:button 
                    type="button" 
                    wire:click="rejectRefund" 
                    variant="danger"
                    wire:confirm="Are you sure you want to reject this refund request?"
                >
                    Reject Refund
                </flux:button>
                <flux:button 
                    type="button" 
                    wire:click="approveRefund" 
                    variant="primary"
                    wire:confirm="Are you sure you want to approve this refund request?"
                >
                    Approve Refund
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>
