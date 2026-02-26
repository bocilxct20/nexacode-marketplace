<div wire:init="loadData" class="space-y-12">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Author Hub</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="font-bold">Analitik Penulis</flux:heading>
            <flux:subheading size="lg">Pantau performa toko dan pertumbuhan pendapatan kamu secara real-time.</flux:subheading>
        </div>
        <div class="flex gap-2">
            @if($user->isTrialing())
                <div class="flex items-center gap-2 px-4 py-2 bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/20 rounded-xl mr-4 animate-pulse">
                    <flux:icon.clock variant="mini" class="text-amber-600 dark:text-amber-400" />
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-black tracking-widest text-amber-600 dark:text-amber-400">Trial Pro Berakhir Dalam</span>
                        <span class="text-[10px] text-amber-600/80 dark:text-amber-400/80 font-bold">{{ $user->trial_ends_at->diffForHumans(['parts' => 1]) }}</span>
                    </div>
                </div>
            @endif
            @php $currentPlan = $user->currentPlan(); @endphp
            <flux:button variant="ghost" icon="calendar">30 Hari Terakhir</flux:button>
            <div class="flex items-center gap-3 px-4 py-2 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                <x-community-badge :user="$user" size="sm" />
                <div class="flex flex-col min-w-[100px]">
                    <div class="flex items-center gap-2">
                        @if($levelInfo['discount'] > 0)
                            <flux:badge color="indigo" size="sm" class="py-0 px-1 text-[8px] uppercase font-black" icon="sparkles" tooltip="Bonus Level: -{{ $levelInfo['discount'] }}% Fee">Level Advantage</flux:badge>
                        @endif
                        @if(!$currentPlan->is_default && $subscriptionDaysLeft !== null)
                            <flux:badge :color="$subscriptionDaysLeft <= 5 ? 'red' : 'emerald'" size="sm" class="py-0 px-1 text-[8px] uppercase font-black">
                                {{ $subscriptionDaysLeft }} Hari Tersisa
                            </flux:badge>
                        @endif
                    </div>
                    <flux:text size="xs" class="font-bold tabular-nums text-zinc-500">Bagi Hasil {{ 100 - ($finalCommissionRate) }}%</flux:text>
                </div>
            </div>
            <flux:button variant="primary" icon="wallet">Tarik Pendapatan</flux:button>
        </div>
    </div>

    @if (!$readyToLoad)
        {{-- Skeleton State --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @for ($i = 0; $i < 3; $i++)
                <flux:card class="p-6 space-y-4">
                    <div class="h-3 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                    <div class="h-8 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                    <div class="h-3 w-40 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                </flux:card>
            @endfor
        </div>

        <div class="h-80 bg-zinc-100 dark:bg-zinc-900/50 animate-pulse rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
            <flux:icon.chart-bar class="w-12 h-12 text-zinc-300 dark:text-zinc-700" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-6">
                <div class="h-6 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                <flux:card class="p-0">
                    <div class="p-6 space-y-4">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="flex justify-between items-center">
                                <div class="space-y-2">
                                    <div class="h-4 w-48 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                                    <div class="h-3 w-24 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                                </div>
                                <div class="h-4 w-20 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                            </div>
                        @endfor
                    </div>
                </flux:card>
            </div>
            <div class="space-y-6">
                <div class="h-6 w-32 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                @for ($i = 0; $i < 3; $i++)
                    <flux:card class="p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-zinc-200 dark:bg-zinc-800 animate-pulse"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-3/4 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                            <div class="h-3 w-1/2 bg-zinc-200 dark:bg-zinc-800 animate-pulse rounded"></div>
                        </div>
                    </flux:card>
                @endfor
            </div>
        </div>
    @else
        {{-- Real Content --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:card class="p-6">
                <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Saldo Tersedia</flux:subheading>
                <flux:heading size="xl" class="font-black tabular-nums text-emerald-600 dark:text-emerald-400">Rp {{ number_format($availableBalance ?? 0, 0, ',', '.') }}</flux:heading>
                <flux:subheading size="xs" class="font-bold italic text-zinc-500">Siap ditarik</flux:subheading>
                
                @if(($pendingBalance ?? 0) > 0)
                    <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <flux:text size="xs" class="uppercase tracking-widest font-bold text-amber-600 dark:text-amber-400">Saldo Pending</flux:text>
                            <flux:text size="sm" class="font-black tabular-nums text-amber-600 dark:text-amber-400">Rp {{ number_format($pendingBalance, 0, ',', '.') }}</flux:text>
                        </div>
                        <flux:text size="xs" class="text-zinc-500 italic mt-1">Menunggu 24 jam sebelum tersedia</flux:text>
                    </div>
                @endif
                
                @if($earningsGrowth != 0)
                    <div class="mt-2 text-xs {{ $earningsGrowth > 0 ? 'text-emerald-500' : 'text-red-500' }} font-bold flex items-center gap-1">
                        <flux:icon.{{ $earningsGrowth > 0 ? 'arrow-trending-up' : 'arrow-trending-down' }} class="w-3 h-3" />
                        <span>{{ $earningsGrowth > 0 ? '+' : '' }}{{ number_format($earningsGrowth, 1) }}% vs bulan lalu</span>
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6">
                <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Produk Aktif</flux:subheading>
                <flux:heading size="xl" class="font-black tabular-nums">{{ $productsCount ?? 0 }}</flux:heading>
                <flux:subheading size="xs" class="font-bold italic">{{ $productsCount > 0 ? 'Terbit' : 'Belum ada produk' }}</flux:subheading>
            </flux:card>

            <flux:card class="p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <flux:icon.academic-cap class="w-16 h-16" />
                </div>
                <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Pengalaman Penulis</flux:subheading>
                <div class="flex items-end justify-between mb-4">
                    <flux:heading size="xl" class="font-black tabular-nums">Level {{ $levelInfo['level'] }}</flux:heading>
                    <flux:text size="xs" class="font-bold text-zinc-500">{{ number_format($levelInfo['xp']) }} / {{ number_format($levelInfo['next_level_xp']) }} XP</flux:text>
                </div>
                <div class="space-y-2">
                    <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-1000" style="width: {{ $levelInfo['percentage'] }}%"></div>
                    </div>
                    <flux:text size="xs" class="italic text-zinc-500">Kurang {{ number_format($levelInfo['remaining_xp']) }} XP untuk naik ke Level {{ $levelInfo['level'] + 1 }}</flux:text>
                </div>
            </flux:card>
        </div>

        {{-- Growth Insights Card (Elite Only) --}}
@if($eliteMetrics)
            <flux:card class="p-8 bg-amber-500/5 border-amber-500/20 dark:bg-amber-500/10 rounded-3xl">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <flux:heading size="lg" class="font-bold">Wawasan Pertumbuhan Elite</flux:heading>
                        <flux:subheading>Metrik performa real-time dan pelacakan konversi.</flux:subheading>
                    </div>
                    <flux:badge color="amber" size="sm" class="uppercase text-[10px] font-black tracking-widest px-3">Wawasan Langsung DB</flux:badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    {{-- Item Views --}}
                    <div class="space-y-2">
                        <flux:text size="xs" class="uppercase tracking-widest font-bold text-zinc-500">Tayangan Item (30h)</flux:text>
                        <div class="flex items-baseline gap-2">
                            <flux:text size="xl" class="text-3xl font-black tabular-nums">{{ number_format($eliteMetrics['itemViews']) }}</flux:text>
                            @if($eliteMetrics['itemViewsGrowth'] != 0)
                                <div class="flex items-center gap-1 {{ $eliteMetrics['itemViewsGrowth'] > 0 ? 'text-emerald-600' : 'text-red-600' }} font-bold text-xs">
                                    <flux:icon.{{ $eliteMetrics['itemViewsGrowth'] > 0 ? 'arrow-small-up' : 'arrow-small-down' }} variant="mini" class="w-4 h-4" />
                                    <flux:text size="xs" class="font-bold">{{ number_format(abs($eliteMetrics['itemViewsGrowth']), 1) }}%</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Sales --}}
                    <div class="space-y-2">
                        <flux:text size="xs" class="uppercase tracking-widest font-bold text-zinc-500">Kecepatan Pembelian</flux:text>
                        <div class="flex items-baseline gap-2">
                            <flux:text size="xl" class="text-3xl font-black tabular-nums">{{ number_format($eliteMetrics['purchases']) }}</flux:text>
                            <flux:text size="xs" class="text-zinc-400 font-medium">Penjualan Terbaru</flux:text>
                        </div>
                    </div>

                    {{-- Conversion --}}
                    <div class="space-y-2">
                        <flux:text size="xs" class="uppercase tracking-widest font-bold text-zinc-500">Kekuatan Konversi</flux:text>
                        <div class="flex items-baseline gap-2">
                            <flux:text size="xl" class="text-3xl font-black tabular-nums">{{ number_format($eliteMetrics['conversionRate'], 2) }}%</flux:text>
                            <flux:badge size="sm" color="emerald" variant="subtle" class="text-[10px] uppercase font-black">Terverifikasi</flux:badge>
                        </div>
                    </div>
                </div>

                {{-- Competitor Benchmarking --}}
                @if(isset($eliteMetrics['primaryCategory']))
                    <flux:separator variant="subtle" class="mb-8" />
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <flux:icon.chart-bar-square class="w-4 h-4 text-zinc-400" />
                            <flux:heading size="sm" class="uppercase tracking-widest font-black text-zinc-400">Posisi Pasar vs Rata-rata {{ $eliteMetrics['primaryCategory'] }}</flux:heading>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            {{-- Conversion Benchmarking --}}
                            <div class="space-y-4">
                                <div class="flex justify-between items-end">
                                    <flux:text size="sm" class="font-bold">Tingkat Konversi</flux:text>
                                    <div class="text-right">
                                        <flux:text size="sm" class="text-xl font-black {{ $eliteMetrics['conversionRate'] >= $eliteMetrics['categoryAvgConversion'] ? 'text-emerald-500' : 'text-amber-500' }}">
                                            {{ number_format($eliteMetrics['conversionRate'], 1) }}%
                                        </flux:text>
                                        <flux:text size="xs">vs {{ number_format($eliteMetrics['categoryAvgConversion'], 1) }}% rata-rata</flux:text>
                                    </div>
                                </div>
                                <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                                    <div class="h-full bg-emerald-500" style="width: {{ min(100, ($eliteMetrics['conversionRate'] / max(1, $eliteMetrics['categoryAvgConversion'] * 1.5)) * 100) }}%"></div>
                                </div>
                                @if($eliteMetrics['conversionRate'] >= $eliteMetrics['categoryAvgConversion'])
                                    <div class="flex items-center gap-2 text-xs text-emerald-600 font-bold italic">
                                        <flux:icon.check-badge variant="mini" />
                                        <flux:text size="xs" class="font-bold">Mengungguli rata-rata kategori sebesar {{ number_format($eliteMetrics['conversionRate'] - $eliteMetrics['categoryAvgConversion'], 1) }}%</flux:text>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-xs text-amber-600 font-bold italic">
                                        <flux:icon.information-circle variant="mini" />
                                        <flux:text size="xs" class="font-bold">Di bawah rata-rata kategori. Coba optimalkan deskripsi atau tangkapan layar kamu.</flux:text>
                                    </div>
                                @endif
                            </div>

                            {{-- Rating Benchmarking --}}
                            <div class="space-y-4">
                                <div class="flex justify-between items-end">
                                    <flux:text size="sm" class="font-bold">Kepuasan Pelanggan</flux:text>
                                    <div class="text-right">
                                        <flux:text size="sm" class="text-xl font-black {{ $averageRating >= $eliteMetrics['categoryAvgRating'] ? 'text-emerald-500' : 'text-amber-500' }}">
                                            {{ number_format($averageRating, 1) }} / 5.0
                                        </flux:text>
                                        <flux:text size="xs">vs {{ number_format($eliteMetrics['categoryAvgRating'], 1) }} rata-rata kategori</flux:text>
                                    </div>
                                </div>
                                <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                                    <div class="h-full bg-emerald-500" style="width: {{ ($averageRating / 5) * 100 }}%"></div>
                                </div>
                                @if($averageRating >= $eliteMetrics['categoryAvgRating'])
                                    <div class="flex items-center gap-2 text-xs text-emerald-600 font-bold italic">
                                        <flux:icon.check-badge variant="mini" />
                                        <flux:text size="xs" class="font-bold">Kualitas produk lebih unggul dibandingkan rata-rata.</flux:text>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-xs text-amber-600 font-bold italic">
                                        <flux:icon.information-circle variant="mini" />
                                        <flux:text size="xs" class="font-bold">Fokus pada respon bantuan untuk meningkatkan rating kamu.</flux:text>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </flux:card>

            {{-- Dedicated Manager Concierge Card --}}
            <flux:card class="mt-8 p-6 bg-indigo-600 dark:bg-indigo-700 text-white relative overflow-hidden group border-none shadow-xl shadow-indigo-500/20">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                    <flux:icon.user-group class="w-32 h-32" />
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="px-2 py-0.5 bg-white/20 rounded text-[10px] font-black uppercase tracking-widest text-white border border-white/20">
                            Dedicated Concierge
                        </div>
                        <div class="flex -space-x-2">
                            <img src="https://ui-avatars.com/api/?name=Admin+Nexa&background=0284c7&color=fff" class="w-6 h-6 rounded-full border-2 border-indigo-600">
                            <img src="https://ui-avatars.com/api/?name=Support+Elite&background=059669&color=fff" class="w-6 h-6 rounded-full border-2 border-indigo-600">
                        </div>
                    </div>
                    <flux:heading class="text-white font-black text-2xl mb-2">Dedicated Manager Aktif</flux:heading>
                    <flux:text class="text-indigo-100 text-sm mb-6 max-w-lg leading-relaxed">
                        Sebagai partner Elite, kamu memiliki akses prioritas langsung ke tim manajemen NexaCode untuk bantuan strategis, moderasi cepat, dan solusi personal.
                    </flux:text>
                    <div class="flex gap-3">
                        <flux:button variant="filled" class="bg-white text-indigo-600 hover:bg-indigo-50 border-none font-bold" icon="lifebuoy" wire:click="$dispatch('open-ticket-modal', { subject: 'Konsultasi Dedicated Manager' })">
                            Hubungi Manager
                        </flux:button>
                        <flux:button variant="ghost" class="text-white hover:bg-white/10 border-white/20 font-bold" icon="sparkles" wire:click="$dispatch('open-ticket-modal', { subject: 'Permintaan Early Access Fitur Baru' })">
                            Ajukan Early Access
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        @endif

        {{-- Sales Analytics Chart --}}
        @livewire('author.author-sales-chart')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-8">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg" class="font-bold">Penjualan Terkini</flux:heading>
                    <flux:button variant="ghost" size="sm">Ekspor CSV</flux:button>
                </div>

                <flux:card class="p-0">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Item</flux:table.column>
                            <flux:table.column>Tanggal</flux:table.column>
                            <flux:table.column>Harga</flux:table.column>
                            <flux:table.column>Pendapatan</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>
                        
                        <flux:table.rows>
                            @forelse($recentSales ?? [] as $sale)
                                <flux:table.row :key="$sale->id">
                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-zinc-900 dark:text-white truncate max-w-[200px]">{{ $sale->product->name }}</span>
                                            <span class="text-[10px] text-zinc-500 font-mono">#{{ $sale->order->transaction_id ?? substr($sale->order->id, 0, 8) }}</span>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell class="text-zinc-500 tabular-nums">
                                        {{ $sale->created_at->format('d M Y') }}
                                    </flux:table.cell>
                                    <flux:table.cell class="tabular-nums font-medium">
                                        Rp {{ number_format($sale->product->price, 0, ',', '.') }}
                                    </flux:table.cell>
                                    <flux:table.cell class="font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($sale->amount, 0, ',', '.') }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" :color="match($sale->status) {
                                            'completed' => 'emerald',
                                            'pending' => 'amber',
                                            'failed' => 'red',
                                            default => 'zinc'
                                        }" inset="top bottom" class="uppercase text-[10px] font-bold">
                                            {{ match($sale->status) {
                                                'completed' => 'Berhasil',
                                                'pending' => 'Tertunda',
                                                'failed' => 'Gagal',
                                                default => ucfirst($sale->status)
                                            } }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center py-12 text-zinc-500 italic font-medium">
                                        Belum ada rekaman penjualan terbaru.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            </div>

            <div class="space-y-8">
                <flux:heading size="lg" class="font-bold">Performa Terbaik</flux:heading>
                
                <div class="space-y-4">
                    @forelse($topProducts as $index => $product)
                        <flux:card class="p-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-500 font-black">#{{ $index + 1 }}</div>
                            <div class="flex-1 overflow-hidden">
                                <flux:heading size="sm" class="truncate">{{ $product->name }}</flux:heading>
                                <flux:subheading size="xs">{{ $product->sales_count }} {{ Str::plural('Penjualan', $product->sales_count) }}</flux:subheading>
                            </div>
                            <flux:badge color="emerald" size="sm">{{ number_format($product->avg_rating, 1) }} ★</flux:badge>
                        </flux:card>
                    @empty
                        <div class="text-center text-zinc-500 text-sm py-8 italic">Belum ada produk</div>
                    @endforelse
                </div>

                <div class="pt-8">
                    <flux:heading size="lg" class="font-bold mb-4">Manfaat Langganan</flux:heading>
                    <flux:card class="p-6 bg-zinc-50 dark:bg-zinc-900/50 border-dashed">
                        <div class="space-y-3">
                            @foreach($user->currentPlan()->features as $feature)
                                <div class="flex items-center gap-3">
                                    <flux:icon.check-circle variant="mini" class="text-emerald-500 shrink-0" />
                                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                        {{ $feature }}
                                    </flux:text>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            <flux:button variant="subtle" class="w-full font-bold" icon="arrow-up-circle" href="{{ route('author.plans') }}">Upgrade Tier</flux:button>
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>
    @endif
</div>
