<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" class="mb-1">Notification Center</flux:heading>
            <flux:subheading>Manage your activity and alerts across the platform.</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
             @if($notifications->count() > 0)
                <flux:button wire:click="markAllAsRead" variant="subtle" size="sm" icon="check-circle">
                    Mark all read
                </flux:button>
                <flux:button wire:click="clearAll" variant="subtle" size="sm" icon="trash" color="red">
                    Clear all
                </flux:button>
            @endif
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse($notifications as $notification)
                <div class="p-6 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors {{ $notification->read_at ? 'opacity-60' : '' }} {{ ($notification->data['type'] ?? '') === 'elite' ? 'bg-amber-500/5 border-l-4 border-amber-500' : '' }}">
                    <div class="flex gap-4">
                        <div class="mt-1">
                            @php
                                $type = $notification->data['type'] ?? 'info';
                                $icon = match($type) {
                                    'sale' => 'banknotes',
                                    'level' => 'academic-cap',
                                    'info' => 'information-circle',
                                    'payment' => 'check-circle',
                                    'warning' => 'exclamation-triangle',
                                    'price_drop' => 'bolt',
                                    default => 'bell',
                                };
                                $colorClass = match($type) {
                                    'sale' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
                                    'level' => 'bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400',
                                    'payment' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400',
                                    'warning' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
                                    'price_drop' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
                                    'elite' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
                                    default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-500/20 dark:text-zinc-400',
                                };
                            @endphp
                            <div class="size-10 rounded-xl {{ $colorClass }} flex items-center justify-center shadow-sm">
                                <flux:icon name="{{ $icon }}" variant="solid" class="size-5" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-1">
                                <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 italic">
                                    {{ $notification->data['title'] ?? 'System Update' }}
                                </p>
                                <span class="text-[10px] tabular-nums text-zinc-400 font-medium">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            
                            @if(isset($notification->data['action_url']))
                                <div class="mt-4">
                                    <flux:button :href="$notification->data['action_url']" size="sm" :variant="$notification->read_at ? 'subtle' : 'primary'" class="text-[10px] px-3 font-bold uppercase tracking-wider">
                                        {{ $notification->data['action_text'] ?? 'View Details' }}
                                    </flux:button>
                                </div>
                            @endif

                            @unless($notification->read_at)
                                <div class="mt-4 flex justify-end">
                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 transition-colors">
                                        Mark as read
                                    </button>
                                </div>
                            @endunless
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <div class="inline-flex size-16 rounded-full bg-zinc-50 dark:bg-zinc-900 items-center justify-center mb-4">
                        <flux:icon.bell class="size-8 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <flux:heading size="lg">Quiet in here...</flux:heading>
                    <flux:subheading>You have no notifications yet. We'll let you know when something happens.</flux:subheading>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/30">
                {{ $notifications->links() }}
            </div>
        @endif
    </flux:card>
</div>
