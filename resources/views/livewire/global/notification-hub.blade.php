<div class="relative">
    <flux:dropdown align="end">
        <flux:button variant="subtle" square class="relative">
            <flux:icon.bell variant="mini" class="text-zinc-500 dark:text-zinc-400" />
            
            @if($unreadCount > 0)
                <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
            @endif
        </flux:button>

        <flux:menu class="min-w-[320px] p-0 overflow-hidden">
            <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-900/50">
                <flux:heading size="sm" class="font-bold">Notifications</flux:heading>
                <div class="flex items-center gap-4">
                    @if($notifications->count() > 0)
                        <button wire:click="clearAll" class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                            Clear all
                        </button>
                    @endif
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                            Mark all read
                        </button>
                    @endif
                </div>
            </div>

            <div class="max-h-[400px] overflow-y-auto">
                {{-- Pending Payments Section --}}
                @if($unpaidOrders->count() > 0)
                    <div class="bg-amber-500/5 dark:bg-amber-500/10 border-b border-amber-500/20">
                        <div class="p-3 bg-amber-500/10 dark:bg-amber-500/20 px-4 py-2 flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Pending Payments</span>
                            <flux:badge color="amber" size="sm" class="text-[9px] font-black">{{ $unpaidOrders->count() }}</flux:badge>
                        </div>
                        @foreach($unpaidOrders as $order)
                            <a href="{{ route('payment.show', $order) }}" class="flex items-center gap-3 p-4 hover:bg-amber-500/10 transition-colors border-b border-amber-500/10 last:border-0">
                                <div class="size-9 rounded-xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                                    <flux:icon name="credit-card" variant="solid" class="size-5 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                            {{ $order->type === 'subscription' ? 'Plan Upgrade' : 'Product Order' }}
                                        </p>
                                        <span class="text-[10px] font-mono text-zinc-500">#{{ $order->transaction_id }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                        <span class="text-zinc-400 italic font-medium">Expires {{ $order->expires_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @forelse($notifications as $notification)
                    <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 last:border-0 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                        <div class="flex gap-3">
                            <div class="mt-1">
                                @php
                                    $icon = match($notification->data['type'] ?? 'info') {
                                        'sale' => 'banknotes',
                                        'level' => 'academic-cap',
                                        'info' => 'information-circle',
                                        'payment' => 'check-circle',
                                        'warning' => 'exclamation-triangle',
                                        'price_drop' => 'bolt',
                                        default => 'bell',
                                    };
                                    $color = match($notification->data['type'] ?? 'info') {
                                        'sale' => 'text-emerald-500',
                                        'level' => 'text-purple-500',
                                        'payment' => 'text-indigo-500',
                                        'warning' => 'text-amber-500',
                                        'price_drop' => 'text-emerald-500',
                                        default => 'text-zinc-400',
                                    };
                                @endphp
                                <flux:icon name="{{ $icon }}" variant="mini" class="{{ $color }}" />
                            </div>
                            <div class="flex-1 space-y-1">
                                <p class="text-sm font-medium leading-tight text-zinc-900 dark:text-zinc-100">
                                    {{ $notification->data['title'] ?? 'New Notification' }}
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-[10px] text-zinc-400 tabular-nums">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @unless($notification->read_at)
                                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">
                                            Mark read
                                        </button>
                                    @endunless
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <flux:icon.bell class="w-12 h-12 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                        <flux:text size="sm" class="text-zinc-400">No notifications yet.</flux:text>
                    </div>
                @endforelse

                @if(!auth()->user()->isAuthor() && !auth()->user()->isAdmin() && !request()->routeIs('admin.*'))
                    <div class="p-4 bg-emerald-500/5 dark:bg-emerald-500/10 border-y border-emerald-500/20">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                                <flux:icon name="sparkles" variant="solid" class="size-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-0.5">Ready to sell?</p>
                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-tight line-clamp-2">Ubah script kamu menjadi penghasilan pasif di NexaCode.</p>
                            </div>
                        </div>
                        <flux:button href="{{ route('author.register') }}" variant="primary" class="w-full mt-3 text-[10px] uppercase font-bold tracking-wider py-2" icon="presentation-chart-line">
                            Become an Author
                        </flux:button>
                    </div>
                @endif
            </div>

            @if($notifications->count() > 0)
                <div class="p-3 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-800 text-center">
                    <a href="{{ route('notifications') }}" class="text-xs font-bold text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">
                        View All Activity
                    </a>
                </div>
            @endif
        </flux:menu>
    </flux:dropdown>
</div>
