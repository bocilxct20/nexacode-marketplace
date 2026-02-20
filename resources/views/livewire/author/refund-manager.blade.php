<div wire:init="loadData">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Permintaan</flux:subheading>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Menunggu</flux:subheading>
                <div class="text-2xl font-bold text-amber-500">{{ $stats['pending'] }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div class="space-y-1">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Disetujui</flux:subheading>
                <div class="text-2xl font-bold text-emerald-500">{{ $stats['approved'] }}</div>
            </div>
        </flux:card>
    </div>

    <flux:card>
        <flux:table :paginate="$refunds">
            <flux:table.columns>
                <flux:table.column>Pesanan</flux:table.column>
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
                            <flux:table.cell><div class="h-4 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-40 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-12 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell><div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                            <flux:table.cell align="right"><div class="h-8 w-16 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div></flux:table.cell>
                        </flux:table.row>
                    @endfor
                @else

                    @forelse ($refunds as $refund)
                        <flux:table.row :key="$refund->id">
                            <flux:table.cell variant="strong">#{{ substr($refund->order->id, 0, 8) }}</flux:table.cell>
                            <flux:table.cell>{{ $refund->order->user->name }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="match($refund->status) {
                                    'pending' => 'amber',
                                    'approved' => 'emerald',
                                    'rejected' => 'red',
                                    default => 'zinc'
                                }" class="uppercase text-[10px] font-bold">
                                    {{ match($refund->status) {
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => $refund->status
                                    } }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $refund->created_at->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell align="right">
                                @if($refund->status === 'pending')
                                    <div class="flex gap-2 justify-end">
                                        <flux:button variant="ghost" size="sm" icon="check" wire:click="approveRefund({{ $refund->id }})" class="text-emerald-500" inset="top bottom" />
                                        <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="rejectRefund({{ $refund->id }})" class="text-red-500" inset="top bottom" />
                                    </div>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500 py-12 italic">
                                Tidak ada permintaan pengembalian dana ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
