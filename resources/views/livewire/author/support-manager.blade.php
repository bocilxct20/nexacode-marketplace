<div wire:init="loadData">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Tiket</flux:subheading>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Tiket Terbuka</flux:subheading>
                <div class="text-2xl font-bold text-amber-500">{{ $stats['open'] }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Diselesaikan</flux:subheading>
                <div class="text-2xl font-bold text-emerald-500">{{ $stats['resolved'] }}</div>
            </div>
        </flux:card>
    </div>

    @if(auth()->user()->isElite())
        <flux:card class="mb-8 p-4 bg-amber-500/10 border-amber-500/20 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-500 rounded-xl shadow-lg shadow-amber-500/20">
                    <flux:icon.lifebuoy class="w-6 h-6 text-white" />
                </div>
                <div>
                    <flux:heading size="sm" class="font-black text-amber-700 dark:text-amber-500">Priority Elite Support Active</flux:heading>
                    <flux:text size="xs" class="text-amber-600 dark:text-amber-400 font-medium">Tiket kamu akan otomatis diprioritaskan dan ditangani langsung oleh Dedicated Manager.</flux:text>
                </div>
            </div>
            <flux:badge color="amber" size="sm" class="uppercase font-black animate-pulse">Ultra Fast Response</flux:badge>
        </flux:card>
    @endif

    <flux:card>
        <div class="mb-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari tiket bantuan..." icon="magnifying-glass" />
        </div>

        <flux:table :paginate="$tickets">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'subject'" :direction="$sortDirection" wire:click="sort('subject')">Subjek</flux:table.column>
                <flux:table.column>Pelanggan</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Tanggal</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Skeleton Rows --}}
                @if (!$readyToLoad)
                    @for ($i = 0; $i < 5; $i++)
                        <flux:table.row>
                            <flux:table.cell><div class="h-4 w-64 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-12 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell align="right"><div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                        </flux:table.row>
                    @endfor
                @else

                    @forelse ($tickets as $ticket)
                        <flux:table.row :key="$ticket->id">
                            <flux:table.cell variant="strong">{{ $ticket->subject }}</flux:table.cell>
                            <flux:table.cell>{{ $ticket->user->name }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="match($ticket->status) {
                                    'open' => 'amber',
                                    'pending' => 'indigo',
                                    'resolved' => 'emerald',
                                    'closed' => 'zinc',
                                    default => 'zinc'
                                }" class="uppercase text-[10px] font-bold">
                                    {{ match($ticket->status) {
                                        'open' => 'Terbuka',
                                        'pending' => 'Menunggu',
                                        'resolved' => 'Selesai',
                                        'closed' => 'Ditutup',
                                        default => $ticket->status
                                    } }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $ticket->created_at->format('M d, Y') }}</flux:table.cell>
                            <flux:table.cell align="right">
                                <flux:button variant="ghost" size="sm" icon="chat-bubble-left-right" inset="top bottom" />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500 py-12 italic">
                                Tidak ada tiket dukungan ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
