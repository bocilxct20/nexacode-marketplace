<div>
    @if ($count > 0)
        <flux:dropdown align="end">
            <flux:button variant="subtle" square class="relative" aria-label="Payment Notifications">
                <flux:icon.bell variant="mini" class="text-zinc-500 dark:text-zinc-400" />
                
                <span class="absolute top-1 right-1 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 border border-white dark:border-zinc-900"></span>
                </span>
            </flux:button>

            <flux:menu class="w-80">
                <flux:menu.heading>Pending Payments ({{ $count }})</flux:menu.heading>
                <flux:menu.separator />
                
                @foreach ($unpaidOrders as $order)
                    <flux:menu.item href="{{ route('checkout.payment', $order) }}" class="flex flex-col items-start gap-1 py-3 group">
                        <div class="flex justify-between w-full items-center">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200 group-hover:text-emerald-500 transition-colors">
                                {{ $order->type === 'subscription' ? 'Plan Upgrade' : 'Product Order' }}
                            </span>
                            <span class="text-xs font-mono text-zinc-500">#{{ $order->transaction_id }}</span>
                        </div>
                        <div class="flex justify-between w-full items-center text-xs">
                            <span class="font-medium text-emerald-600 dark:text-emerald-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            <span class="text-zinc-400 italic">Expires {{ $order->expires_at->diffForHumans() }}</span>
                        </div>
                    </flux:menu.item>
                @endforeach

                <flux:menu.separator />
                <flux:menu.item href="{{ route('purchases.index') }}" icon="credit-card" class="text-xs justify-center py-2 bg-zinc-50 dark:bg-zinc-800/50">
                    View All Orders
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    @else
        <flux:button variant="subtle" square disabled aria-label="No Payment Notifications">
            <flux:icon.bell variant="mini" class="text-zinc-500 dark:text-zinc-400 opacity-50" />
        </flux:button>
    @endif
</div>
