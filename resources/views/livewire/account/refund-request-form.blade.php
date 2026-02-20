<flux:card class="p-8 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl">
            <flux:icon.arrow-path class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
        </div>
        <div>
            <flux:heading size="xl">Request Refund</flux:heading>
            <flux:subheading>Order #{{ $order->transaction_id }} • Rp {{ number_format($order->total_amount, 0, ',', '.') }}</flux:subheading>
        </div>
    </div>

    <flux:separator variant="subtle" class="mb-8" />

    <div class="mb-8 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-100 dark:border-zinc-800">
        <flux:heading size="sm" class="mb-4">Order Items</flux:heading>
        <ul class="space-y-2">
            @foreach($order->items as $item)
                <li class="flex justify-between text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">{{ $item->product->name }}</span>
                    <span class="font-bold">${{ number_format($item->price, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <form wire:submit.prevent="submitRefund" class="space-y-6">
        <flux:field>
            <flux:label>Reason for Refund</flux:label>
            <flux:textarea wire:model="reason" placeholder="Please describe exactly why you are requesting a refund. Include details about any technical issues or unmet expectations." rows="6" />
            <flux:error for="reason" />
            <flux:description>Minimum 20 characters required. Our team will review your request based on our refund policy.</flux:description>
        </flux:field>

        <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 text-xs mb-4">
            Refund akan ditinjau secara manual. Akses download produk akan dicabut jika refund disetujui.
        </div>

        <div class="flex items-center justify-between pt-6">
            <flux:button href="{{ route('purchases.index') }}" variant="ghost">Cancel</flux:button>
            <flux:button type="submit" variant="primary" class="px-10" wire:loading.attr="disabled">
                Submit Request
            </flux:button>
        </div>
    </form>
</flux:card>
