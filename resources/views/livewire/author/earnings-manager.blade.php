<div>
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Beranda</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Pendapatan</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <flux:heading size="xl">Pendapatan</flux:heading>
    <flux:subheading>Pantau pendapatan kamu dan ajukan penarikan dana.</flux:subheading>

    <flux:separator variant="subtle" class="my-8" />

    {{-- Earnings Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Pendapatan</flux:subheading>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    Rp {{ number_format($totalEarnings, 0, ',', '.') }}
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Tertunda</flux:subheading>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                    Rp {{ number_format($pendingEarnings, 0, ',', '.') }}
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Saldo Tersedia</flux:subheading>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Telah Ditarik</flux:subheading>
                <div class="text-2xl font-bold">
                    Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Filters and Actions --}}
    <flux:card class="mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <flux:input 
                    wire:model="dateFrom" 
                    type="date" 
                    label="Dari Tanggal"
                />
            </div>
            <div class="flex-1">
                <flux:input 
                    wire:model="dateTo" 
                    type="date" 
                    label="Sampai Tanggal"
                />
            </div>
            <flux:button wire:click="filterByDate" variant="primary">
                Terapkan Filter
            </flux:button>
            <flux:button wire:click="requestWithdrawal" variant="filled" class="bg-emerald-600 hover:bg-emerald-700">
                Tarik Pendapatan
            </flux:button>
        </div>
    </flux:card>

    {{-- Earnings Table --}}
    <flux:card>
        <flux:table :paginate="$earnings">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'order_id'" :direction="$sortDirection" wire:click="sort('order_id')">ID Pesanan</flux:table.column>
                <flux:table.column>Produk</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Tanggal</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection" wire:click="sort('price')">Jumlah</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Loading State --}}
                @foreach (range(1, 8) as $i)
                    <flux:table.row wire:loading wire:target="filterByDate, sort, gotoPage, nextPage, previousPage">
                        <flux:table.cell>
                            <div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="h-4 w-48 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="h-4 w-16 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Content State --}}
                @forelse($earnings as $item)
                    <flux:table.row :key="$item->id" wire:loading.remove wire:target="filterByDate, sort, gotoPage, nextPage, previousPage">
                        <flux:table.cell class="font-mono text-xs" variant="strong">
                            <a href="{{ route('author.products') }}" class="hover:underline">
                                #{{ $item->order->transaction_id ?? $item->order_id }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $item->product->name }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-500 tabular-nums">
                            {{ $item->order->created_at->format('d M Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$item->order->status === 'completed' ? 'emerald' : 'amber'" inset="top bottom">
                                {{ match($item->order->status) {
                                    'completed' => 'Berhasil',
                                    'pending' => 'Tertunda',
                                    default => ucfirst($item->order->status)
                                } }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row wire:loading.remove wire:target="filterByDate, sort, gotoPage, nextPage, previousPage">
                        <flux:table.cell colspan="5" class="text-center text-zinc-500 py-12">
                            Tidak ada pendapatan ditemukan untuk periode ini.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:separator variant="subtle" class="my-12" />

    {{-- Withdrawal History --}}
    <div class="mb-6">
        <flux:heading size="lg">Riwayat Penarikan</flux:heading>
        <flux:subheading>Pantau status permintaan pembayaran kamu dan catatan dari admin.</flux:subheading>
    </div>

    <flux:card>
        <flux:table :paginate="$payouts">
            <flux:table.columns>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Jumlah</flux:table.column>
                <flux:table.column>Metode</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Catatan Admin</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($payouts as $payout)
                    <flux:table.row :key="$payout->id">
                        <flux:table.cell class="text-zinc-500 tabular-nums">
                            {{ $payout->created_at->format('d M Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="font-bold tabular-nums">
                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $payout->payment_method }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="match($payout->status) {
                                'pending' => 'amber',
                                'paid' => 'emerald',
                                'rejected' => 'red',
                                default => 'zinc'
                            }" inset="top bottom" class="uppercase text-[10px] font-bold">
                                {{ match($payout->status) {
                                    'pending' => 'Menunggu',
                                    'paid' => 'Dibayar',
                                    'rejected' => 'Ditolak',
                                    default => $payout->status
                                } }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs overflow-hidden text-ellipsis whitespace-nowrap">
                            @if($payouts->count() > 0)
                                @if($payout->admin_note)
                                    <flux:tooltip position="top" :content="$payout->admin_note">
                                        <flux:text size="sm" class="italic !text-zinc-500">{{ Str::limit($payout->admin_note, 40) }}</flux:text>
                                    </flux:tooltip>
                                @else
                                    <flux:text size="sm" class="italic !text-zinc-400">Tidak ada catatan</flux:text>
                                @endif
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-zinc-500 py-12 italic">
                            Belum ada riwayat penarikan.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Withdrawal Confirmation Modal --}}
    <flux:modal name="confirm-withdrawal" wire:model="showModal" class="min-w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Penarikan</flux:heading>
                <flux:subheading>Harap verifikasi detail pembayaran kamu sebelum melanjutkan.</flux:subheading>
            </div>

            <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-2xl space-y-4">
                {{-- Dynamic Amount Input --}}
                <flux:field>
                    <flux:label>Jumlah Penarikan (Rp)</flux:label>
                    <flux:input 
                        type="number" 
                        wire:model.live="withdrawalAmount" 
                        placeholder="min 10.000" 
                        min="10000" 
                        :max="$availableBalance"
                        class="text-lg font-bold text-blue-600 dark:text-blue-400"
                    />
                    <div class="flex justify-between items-center mt-2 px-1">
                        <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-tighter">Saldo Tersedia</span>
                        <span class="text-[10px] font-bold text-zinc-700 dark:text-zinc-300 tabular-nums">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span>
                    </div>
                    <flux:error name="withdrawalAmount" />
                </flux:field>

                <flux:separator variant="subtle" />

                {{-- Bank Details --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <flux:label size="sm" class="uppercase tracking-wider">Bank</flux:label>
                        <flux:text variant="strong" size="sm">{{ auth()->user()->bank_name }}</flux:text>
                    </div>
                    <div class="flex justify-between items-center">
                        <flux:label size="sm" class="uppercase tracking-wider">Nomor Rekening</flux:label>
                        <flux:text variant="strong" size="sm" class="font-mono">{{ auth()->user()->bank_account_number }}</flux:text>
                    </div>
                    <div class="flex justify-between items-center">
                        <flux:label size="sm" class="uppercase tracking-wider">Nama Pemilik</flux:label>
                        <flux:text variant="strong" size="sm">{{ auth()->user()->bank_account_name }}</flux:text>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800">
                <flux:icon.information-circle variant="mini" class="text-amber-600 dark:text-amber-400 mt-0.5" />
                <flux:text size="sm" class="!text-amber-800 dark:!text-amber-400 leading-relaxed">
                    Penarikan diproses secara manual oleh tim keuangan kami dan biasanya memakan waktu 1-3 hari kerja.
                </flux:text>
            </div>

            <div class="flex gap-3 justify-end pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="confirmWithdrawal" variant="primary" class="bg-emerald-600 hover:bg-emerald-700">
                    Kirim Permintaan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
