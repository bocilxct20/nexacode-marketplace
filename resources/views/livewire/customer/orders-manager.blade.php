<div wire:init="load">
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">My Purchases</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <flux:heading size="xl">My Orders</flux:heading>
    <flux:subheading>Pantau dan kelola seluruh riwayat pesanan kamu di sini.</flux:subheading>

    <flux:separator variant="subtle" class="my-8" />

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @if(!$readyToLoad)
            @for($i=0; $i<3; $i++)
                <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 animate-pulse">
                    <div class="space-y-3">
                        <flux:skeleton class="h-3 w-20" />
                        <flux:skeleton class="h-8 w-16" />
                    </div>
                </div>
            @endfor
        @else
            <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <div class="space-y-2">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Total Orders</div>
                    <div class="text-3xl font-black tabular-nums tracking-tight">{{ $stats['total'] }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm relative overflow-hidden group">
                <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative space-y-2">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Completed</div>
                    <div class="text-3xl font-black tabular-nums tracking-tight text-emerald-600 dark:text-emerald-400">{{ $stats['completed'] }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm relative overflow-hidden group">
                <div class="absolute inset-0 bg-amber-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative space-y-2">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Pending</div>
                    <div class="text-3xl font-black tabular-nums tracking-tight text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-sm mb-8 flex items-center justify-between">
        <flux:select wire:model.live="statusFilter" class="w-full md:w-48">
            <option value="all">All Orders</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </flux:select>
    </div>

    {{-- Orders List --}}
    <div class="space-y-6">
        @if(!$readyToLoad)
            @for($i=0; $i<3; $i++)
                <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm animate-pulse">
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
                            <flux:skeleton class="w-16 h-16 rounded-xl" />
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
                </div>
            @endfor
        @else
            @forelse($orders as $order)
                <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-sm">
                    <div class="space-y-6">
                        {{-- Order Header --}}
                        <div class="flex items-start md:items-center justify-between flex-col md:flex-row gap-4">
                            <div>
                                <div class="text-sm font-bold text-zinc-900 dark:text-white mb-1">Order #{{ $order->transaction_id }}</div>
                                <div class="text-xs text-zinc-500 font-medium">{{ $order->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($order->is_in_escrow)
                                    <div class="flex items-center gap-1.5 px-2 py-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/50 rounded-lg text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                        <flux:icon name="shield-check" variant="mini" class="w-3 h-3" />
                                        Bebas Risiko
                                    </div>
                                @endif
                                <flux:badge :color="$order->status_color" size="sm" class="uppercase font-black tracking-widest text-[9px]">
                                    {{ $order->status_label }}
                                </flux:badge>
                            </div>
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Order Items --}}
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-4">
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product) }}" class="flex items-center gap-4 flex-1 min-w-0 group/item">
                                            <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl overflow-hidden flex-shrink-0 group-hover/item:opacity-80 transition-opacity relative border border-zinc-200 dark:border-zinc-700/50 shadow-sm">
                                                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-sm text-zinc-900 dark:text-white truncate group-hover/item:text-emerald-600 dark:group-hover/item:text-emerald-400 transition-colors mb-1">{{ $item->product->name }}</div>
                                                <div class="text-xs text-zinc-500 font-medium">Qty: <span class="text-zinc-900 dark:text-white font-bold">{{ $item->quantity ?? 1 }}</span></div>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    <div class="text-right">
                                        <div class="font-bold text-sm text-zinc-900 dark:text-white mb-2 tabular-nums tracking-tight">Rp {{ number_format($item->price * ($item->quantity ?? 1), 0, ',', '.') }}</div>
                                        @if($order->isCompleted() && $item->product)
                                            <flux:button href="{{ route('products.download', $item->product->slug) }}" variant="outline" size="sm" icon="arrow-down-tray" class="rounded-xl font-bold shrink-0">Download</flux:button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <flux:separator variant="subtle" />

                        {{-- Order Total --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-zinc-50 dark:bg-zinc-800/30 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800/50">
                            <div>
                                <div class="font-black text-[10px] uppercase tracking-widest text-zinc-500 mb-1">Total Amount</div>
                                <div class="text-2xl font-black tabular-nums tracking-tight text-zinc-900 dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                @if($order->isCompleted())
                                    @if($order->type === 'product')
                                        <flux:button wire:click="openRefundModal({{ $order->id }})" variant="ghost" size="sm" icon="arrow-path" class="text-xs font-bold rounded-xl text-zinc-500">Request Refund</flux:button>
                                    @endif
                                    <flux:button href="{{ route('orders.invoice', $order) }}" variant="subtle" size="sm" icon="document-text" class="text-xs font-bold rounded-xl">Invoice</flux:button>
                                    <flux:button wire:click="$dispatch('open-ticket-modal', { productId: {{ $order->items->first()->product_id ?? 'null' }} })" variant="outline" size="sm" icon="lifebuoy" class="text-xs font-bold rounded-xl">Support</flux:button>
                                @endif

                                @if($order->isPending())
                                    <flux:button href="{{ route('checkout.payment', $order) }}" variant="primary" size="sm" icon="credit-card" class="text-xs font-bold rounded-xl px-6">Pay Now</flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <flux:card class="text-center py-20 px-6">
                    <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <flux:icon.shopping-bag class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <flux:heading size="xl" class="mb-2">Pesanan tidak ditemukan</flux:heading>
                    <flux:subheading class="mb-8">Mulai belanja sekarang untuk melihat daftar pesanan kamu di sini.</flux:subheading>
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
            <flux:subheading>Silakan berikan alasan pengajuan refund kamu. Tim kami akan meninjau permintaan ini dalam waktu 24-48 jam.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <form wire:submit="submitRefundRequest" class="space-y-6">
            <flux:field>
                <flux:label>Reason for Refund</flux:label>
                <flux:textarea 
                    wire:model="refundReason" 
                    placeholder="Jelaskan alasan kamu mengajukan refund (minimal 10 karakter)..."
                    rows="5"
                    required
                />
                <flux:error name="refundReason" />
                <flux:description>Minimal 10 karakter, maksimal 500 karakter</flux:description>
            </flux:field>

            <div class="flex gap-3 justify-end">
                <flux:button type="button" wire:click="closeRefundModal" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Submit Refund Request</flux:button>
            </div>
        </form>
    </flux:modal>

    @livewire('support.create-ticket')
</div>
