<div wire:init="load" class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Order Management</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="2xl">Order Management</flux:heading>
            <flux:subheading>Track and manage all transactions and customer orders.</flux:subheading>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 items-end">
        <flux:field class="flex-1">
            <flux:label>Search Orders</flux:label>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by Order ID, Buyer Name, or Email..." />
        </flux:field>

        <flux:field>
            <flux:label>Status Filter</flux:label>
            <flux:select wire:model.live="statusFilter" placeholder="All Status">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </flux:select>
        </flux:field>
    </div>

    <flux:card class="space-y-6">
        <flux:table :paginate="$this->readyToLoad ? $this->orders : null" container:class="max-h-80">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sort('id')">ID</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'customer'" :direction="$sortDirection" wire:click="sort('customer')">Customer</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'total_amount'" :direction="$sortDirection" wire:click="sort('total_amount')">Total Amount</flux:table.column>
                <flux:table.column>Payment</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Date</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @if(!$this->readyToLoad)
                    @foreach(range(1, 10) as $i)
                        <flux:table.row>
                            <flux:table.cell><flux:skeleton class="w-20 h-4" /></flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:skeleton class="size-8 rounded-full" />
                                    <div class="space-y-2">
                                        <flux:skeleton class="w-24 h-4" />
                                        <flux:skeleton class="w-32 h-3" />
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-20 h-5" /></flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-20 h-6 rounded-full" /></flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                            <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @else
                    @forelse($this->orders as $order)
                        <flux:table.row :key="$order->id">
                            <flux:table.cell class="font-mono" variant="strong">
                                {{ $order->transaction_id }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar :src="$order->buyer->avatar" :initials="$order->buyer->initials" />
                                    <div>
                                        <div class="font-bold text-sm">{{ $order->buyer->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $order->buyer->email }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-bold tabular-nums" variant="strong">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs text-zinc-500">{{ $order->paymentMethod->name ?? 'N/A' }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $order->status_color }}" inset="top bottom">
                                    {{ $order->status_label }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $order->created_at->format('M d, g:i A') }}
                            </flux:table.cell>
                            <flux:table.cell align="right">
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" inset="top bottom" />
                                    <flux:menu>
                                        @if($order->isPending())
                                            <flux:menu.item wire:click="markAsPaid({{ $order->id }})">
                                                Mark as Paid
                                            </flux:menu.item>
                                            <flux:menu.item wire:click="cancelOrder({{ $order->id }})" class="text-red-600">
                                                Cancel Order
                                            </flux:menu.item>
                                        @endif
                                        <flux:menu.separator />
                                        <flux:menu.item wire:click="viewOrder({{ $order->id }})" icon="eye">View Details</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-12 text-zinc-500">
                                No orders found matching your criteria.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        @if(method_exists($this->orders, 'links'))
            {{ $this->orders->links() }}
        @endif
    </div>

    {{-- Order Details Modal --}}
    <flux:modal wire:model="showModal" name="order-details" class="md:w-[700px]">
        @if($selectedOrder)
            <div class="space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">Order Details</flux:heading>
                        <flux:text class="mt-1">Transaction ID: <span class="font-mono font-bold">{{ $selectedOrder->transaction_id }}</span></flux:text>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <flux:badge color="{{ $selectedOrder->status_color }}">
                            {{ $selectedOrder->status_label }}
                        </flux:badge>
                        @if($convId = $this->getConversationId($selectedOrder->id))
                            <flux:button href="{{ route('admin.chat', ['conv' => $convId]) }}" variant="subtle" size="xs" icon="chat-bubble-left-right" class="text-[10px] uppercase font-bold tracking-wider">
                                View Chat History
                            </flux:button>
                        @endif
                    </div>
                </div>

                <flux:separator variant="subtle" />

                <div class="grid grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Customer</flux:label>
                        <div class="flex items-center gap-3 mt-2">
                            <flux:profile :avatar="$selectedOrder->buyer->avatar" :initials="$selectedOrder->buyer->initials" />
                            <div>
                                <flux:text variant="strong">{{ $selectedOrder->buyer->name }}</flux:text>
                                <flux:subheading>{{ $selectedOrder->buyer->email }}</flux:subheading>
                            </div>
                        </div>
                    </flux:field>
                    <flux:field>
                        <flux:label>Payment Method</flux:label>
                        <flux:text variant="strong" class="mt-2">
                            {{ $selectedOrder->paymentMethod->name ?? 'Manual Transfer / Other' }}
                        </flux:text>
                    </flux:field>
                </div>

                <flux:separator variant="subtle" />

                <flux:field>
                    <flux:label>Order Timeline</flux:label>
                    <div class="space-y-3 mt-2">
                        @forelse($selectedOrder->histories as $history)
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-2">
                                        <flux:text variant="strong">{{ $history->status_label }}</flux:text>
                                        <flux:subheading class="flex-shrink-0">{{ $history->created_at->format('M d, g:i A') }}</flux:subheading>
                                    </div>
                                    @if($history->note)
                                        <flux:subheading class="mt-0.5">{{ $history->note }}</flux:subheading>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <flux:subheading>No history available</flux:subheading>
                        @endforelse
                    </div>
                </flux:field>

                <flux:separator variant="subtle" />

                @if($selectedOrder->hasPaymentProof())
                    <flux:field>
                        <flux:label>Payment Proof</flux:label>
                        <div class="mt-2">
                            <img src="{{ $selectedOrder->payment_proof_url }}" 
                                 alt="Payment Proof" 
                                 class="max-w-md rounded-lg border border-zinc-200 dark:border-zinc-700 cursor-pointer hover:opacity-90 transition"
                                 onclick="window.open('{{ $selectedOrder->payment_proof_url }}', '_blank')">
                        </div>
                        <flux:subheading class="mt-2">
                            Uploaded: {{ $selectedOrder->payment_proof_uploaded_at->format('M d, Y g:i A') }}
                        </flux:subheading>
                        
                        @if($selectedOrder->isPendingVerification())
                            <div class="flex gap-2 mt-4">
                                <flux:button wire:click="approvePaymentProof({{ $selectedOrder->id }})" 
                                             variant="primary" 
                                             icon="check">
                                    Approve Payment
                                </flux:button>
                                <flux:button wire:click="rejectPaymentProof({{ $selectedOrder->id }})" 
                                             variant="ghost" 
                                             icon="x-mark">
                                    Reject Payment
                                </flux:button>
                            </div>
                        @endif
                    </flux:field>

                    <flux:separator variant="subtle" />
                @endif

                <flux:field>
                    <flux:label>Order Items</flux:label>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Product</flux:table.column>
                            <flux:table.column align="right">Price</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($selectedOrder->items as $item)
                                <flux:table.row>
                                    <flux:table.cell>
                                        @if($item->product)
                                            {{ $item->product->name }}
                                        @elseif($item->subscriptionPlan)
                                            Subscription: {{ $item->subscriptionPlan->name }}
                                        @else
                                            Unknown Item
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell align="right">Rp {{ number_format($item->price, 0, ',', '.') }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:field>

                <flux:separator variant="subtle" />

                <div class="flex justify-between items-center">
                    <flux:text variant="strong">Total</flux:text>
                    <flux:heading size="lg" class="text-indigo-600 font-black tabular-nums">Rp {{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</flux:heading>
                </div>

                <flux:separator variant="subtle" />

                <div class="flex gap-2">
                    @if($selectedOrder->isPending())
                        <flux:button wire:click="markAsPaid({{ $selectedOrder->id }})" variant="primary" color="emerald">Mark as Paid</flux:button>
                        <flux:button wire:click="cancelOrder({{ $selectedOrder->id }})" variant="ghost" color="red">Cancel Order</flux:button>
                    @endif
                    <flux:spacer />
                    <flux:button variant="ghost" x-on:click="Flux.modal('order-details').close()">Close</flux:button>
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
    $wire.on('order-status-updated', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Order Updated',
            text: 'The order status has been successfully updated.'
        });
    });
@endscript
