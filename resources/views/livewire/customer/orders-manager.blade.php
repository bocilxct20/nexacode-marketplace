<div wire:init="load">
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">My Purchases</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <flux:heading size="xl">My Orders</flux:heading>
    <flux:subheading>View and manage your order history.</flux:subheading>

    <flux:separator variant="subtle" class="my-8" />

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @if(!$readyToLoad)
            @for($i=0; $i<3; $i++)
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
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Orders</flux:subheading>
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                </div>
            </flux:card>

            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Completed</flux:subheading>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['completed'] }}</div>
                </div>
            </flux:card>

            <flux:card>
                <div class="space-y-2">
                    <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Pending</flux:subheading>
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</div>
                </div>
            </flux:card>
        @endif
    </div>

    {{-- Filter --}}
    <flux:card class="mb-8">
        <flux:select wire:model.live="statusFilter" class="w-full md:w-48">
            <option value="all">All Orders</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </flux:select>
    </flux:card>

    {{-- Orders List --}}
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
                        <flux:separator variant="subtle" />
                        <div class="flex items-center gap-3">
                            <flux:skeleton class="w-16 h-16 rounded-lg" />
                            <div class="flex-1 space-y-2">
                                <flux:skeleton class="h-5 w-48" />
                                <flux:skeleton class="h-4 w-20" />
                            </div>
                            <flux:skeleton class="h-6 w-24" />
                        </div>
                        <flux:separator variant="subtle" />
                        <div class="flex items-center justify-between">
                            <flux:skeleton class="h-5 w-16" />
                            <flux:skeleton class="h-8 w-32" />
                        </div>
                    </div>
                </flux:card>
            @endfor
        @else
            @forelse($orders as $order)
                <flux:card>
                    <div class="space-y-4">
                        {{-- Order Header --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-lg">Order #{{ $order->transaction_id }}</div>
                                <div class="text-sm text-zinc-500">{{ $order->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                            <flux:badge :color="$order->status_color">
                                {{ $order->status_label }}
                            </flux:badge>
                            
                            @if($order->is_in_escrow)
                                <div class="ml-3 flex items-center gap-1.5 px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 rounded text-[10px] font-bold text-emerald-600 dark:text-emerald-400 animate-pulse">
                                    <flux:icon name="shield-check" class="size-3" />
                                    SECURED BY ESCROW
                                </div>
                            @endif
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Order Items --}}
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-3">
                                    @if($order->isCompleted() && $item->product)
                                        <a href="{{ route('products.show', $item->product) }}" class="flex items-center gap-3 flex-1 min-w-0 group/item">
                                            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden flex-shrink-0 group-hover/item:opacity-80 transition-opacity">
                                                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold truncate group-hover/item:text-emerald-600 dark:group-hover/item:text-emerald-400 transition-colors">{{ $item->product->name }}</div>
                                                <div class="text-sm text-zinc-500">Qty: {{ $item->quantity ?? 1 }}</div>
                                            </div>
                                        </a>
                                    @elseif($item->product)
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold truncate">{{ $item->product->name }}</div>
                                                <div class="text-sm text-zinc-500">Qty: {{ $item->quantity ?? 1 }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="font-semibold text-indigo-600 dark:text-indigo-400">
                                        <div class="mt-1 flex items-center justify-between">
                                            <span>Rp {{ number_format($item->price * ($item->quantity ?? 1), 0, ',', '.') }}</span>
                                            @if($order->isCompleted() && $item->product)
                                                <flux:button href="{{ route('products.download', $item->product->slug) }}" variant="subtle" size="xs" icon="arrow-down-tray" class="text-[9px] uppercase font-bold tracking-wider">Download</flux:button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Order Total --}}
                        <div class="flex items-center justify-between">
                            <div class="font-black text-xs uppercase tracking-widest text-zinc-500">Total Amount</div>
                            <div class="flex items-center gap-4">
                                <div class="text-xl font-black tabular-nums">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                
                                <div class="flex items-center gap-2">
                                    @if($order->isCompleted())
                                        @if($order->type === 'product')
                                            <flux:button wire:click="openRefundModal({{ $order->id }})" variant="ghost" size="sm" icon="arrow-path" class="text-[10px] uppercase font-bold tracking-wider">Request Refund</flux:button>
                                        @endif
                                        <flux:button href="{{ route('orders.invoice', $order) }}" variant="ghost" size="sm" icon="document-text" class="text-[10px] uppercase font-bold tracking-wider">Invoice</flux:button>
                                        <flux:button wire:click="$dispatch('open-ticket-modal', { productId: {{ $order->items->first()->product_id ?? 'null' }} })" variant="ghost" size="sm" icon="lifebuoy" class="text-[10px] uppercase font-bold tracking-wider">Support</flux:button>
                                    @endif

                                    @if($order->isPending())
                                        <flux:button href="{{ route('checkout.payment', $order) }}" variant="primary" size="sm" icon="credit-card" class="text-[10px] uppercase font-bold tracking-wider">Pay Now</flux:button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </flux:card>
            @empty
                <flux:card class="text-center py-20 px-6">
                    <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <flux:icon.shopping-bag class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <flux:heading size="xl" class="mb-2">No orders found</flux:heading>
                    <flux:subheading class="mb-8">Start shopping to see your orders here.</flux:subheading>
                    <flux:button href="{{ route('products.index') }}" variant="primary" icon="sparkles">Browse Marketplace</flux:button>
                </flux:card>
            @endforelse
        @endif
    </div>

    {{-- Pagination --}}
    @if(method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

    @if(!auth()->user()->isAuthor())
        <div class="mt-16 relative overflow-hidden rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-8 md:p-12">
            {{-- Background Decals --}}
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 size-64 bg-emerald-500/10 blur-[80px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 size-64 bg-indigo-500/10 blur-[80px] rounded-full"></div>

            <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] uppercase font-black tracking-widest mb-4">
                        <flux:icon name="sparkles" variant="solid" class="size-3" />
                        Join the Elite
                    </div>
                    <flux:heading size="xl" class="text-3xl font-black mb-4">Siap untuk mulai menjual kode kamu?</flux:heading>
                    <flux:text class="text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed mb-0">
                        Bergabunglah dengan komunitas kreator NexaCode. Ubah script, tema, atau template kamu menjadi penghasilan pasif dengan komisi yang transparan.
                    </flux:text>
                </div>

                <div class="flex flex-col items-center gap-4 shrink-0">
                    <flux:button href="{{ route('author.register') }}" variant="primary" class="px-8 py-4 text-lg shadow-xl shadow-emerald-500/20" icon="presentation-chart-line">
                        Become an Author
                    </flux:button>
                    <div class="flex items-center gap-6 text-xs text-zinc-400 font-bold uppercase tracking-widest">
                        <div class="flex items-center gap-1.5">
                            <flux:icon name="check-circle" variant="solid" class="size-3 text-emerald-500" />
                            Fast Review
                        </div>
                        <div class="flex items-center gap-1.5">
                            <flux:icon name="check-circle" variant="solid" class="size-3 text-emerald-500" />
                            Low Fee
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Refund Request Modal --}}
    <flux:modal name="refund-modal" :open="$showRefundModal" wire:model="showRefundModal" class="space-y-6">
        <div>
            <flux:heading size="lg">Request Refund</flux:heading>
            <flux:subheading>Please provide a reason for your refund request. Our team will review it within 24-48 hours.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <form wire:submit="submitRefundRequest" class="space-y-6">
            <flux:field>
                <flux:label>Reason for Refund</flux:label>
                <flux:textarea 
                    wire:model="refundReason" 
                    placeholder="Please describe why you're requesting a refund (minimum 10 characters)..."
                    rows="5"
                    required
                />
                <flux:error name="refundReason" />
                <flux:description>Minimum 10 characters, maximum 500 characters</flux:description>
            </flux:field>

            <div class="flex gap-3 justify-end">
                <flux:button type="button" wire:click="closeRefundModal" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Submit Refund Request</flux:button>
            </div>
        </form>
    </flux:modal>

    @livewire('support.create-ticket')
</div>
