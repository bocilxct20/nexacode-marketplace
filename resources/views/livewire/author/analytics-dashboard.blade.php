<div class="space-y-8" wire:init="loadData">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="2xl">Author Intelligence Hub</flux:heading>
            <flux:subheading>Monitor pergerakan produk, pendapatan, dan strategi pemasaran kamu secara real-time.</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:dropdown>
                <flux:button icon="calendar" variant="ghost">Filter: {{ $days }} Hari Terakhir</flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="updateRange(7)">7 Hari Terakhir</flux:menu.item>
                    <flux:menu.item wire:click="updateRange(30)">30 Hari Terakhir</flux:menu.item>
                    <flux:menu.item wire:click="updateRange(90)">90 Hari Terakhir</flux:menu.item>
                    <flux:menu.item wire:click="updateRange(365)">1 Tahun Terakhir</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    {{-- Reputation & Experience --}}
    <flux:card class="bg-gradient-to-br from-indigo-500/5 to-transparent border-indigo-500/20 overflow-hidden relative">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/10 blur-3xl rounded-full"></div>
        <div class="flex flex-col md:flex-row items-center gap-8 relative">
            <div class="shrink-0 flex flex-col items-center">
                <div class="relative">
                    <div class="size-20 rounded-[2rem] bg-indigo-600 flex items-center justify-center shadow-xl shadow-indigo-500/20 border-4 border-white dark:border-zinc-900 text-white">
                        <span class="text-3xl font-black">{{ $levelProgress['level'] }}</span>
                    </div>
                    <div class="absolute -top-2 -right-2 bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-lg shadow-lg uppercase tracking-widest">Lvl</div>
                </div>
            </div>

            <div class="flex-1 w-full space-y-4 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <flux:heading size="lg" class="font-black italic uppercase tracking-tighter">Reputation Score</flux:heading>
                        <flux:text size="sm" class="text-zinc-500">Your total influence and trust in the community.</flux:text>
                    </div>
                    <div class="text-center md:text-right">
                        <div class="text-xs font-black text-zinc-400 uppercase tracking-widest leading-none mb-1">Global Rank</div>
                        <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 leading-none">#{{ number_format($globalRank) }}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">{{ number_format($levelProgress['xp']) }} XP</span>
                        <span class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-widest">Goal: {{ number_format($levelProgress['next_level_xp']) }} XP</span>
                    </div>
                    <div class="w-full h-3 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700">
                        <div class="h-full bg-indigo-600 transition-all duration-1000 shadow-[0_0_10px_rgba(79,70,229,0.5)]" style="width: {{ $levelProgress['percentage'] }}%"></div>
                    </div>
                    <div class="text-[10px] text-zinc-400 font-medium">
                        Tips: Reach Level {{ $levelProgress['level'] + (5 - ($levelProgress['level'] % 5 == 0 ? 5 : $levelProgress['level'] % 5)) }} to unlock a **{{ $levelProgress['discount'] + 0.5 }}%** platform fee discount!
                    </div>
                </div>
            </div>
        </div>
    </flux:card>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <flux:card class="p-4 space-y-2">
            <div class="text-zinc-500 text-sm flex items-center gap-2">
                <flux:icon.banknotes class="size-4" />
                Total Pendapatan
            </div>
            <div class="text-2xl font-bold">Rp {{ number_format($summaryStats['total_earnings'], 0, ',', '.') }}</div>
            <div class="text-xs text-emerald-600 font-medium">Estimasi pendapatan bersih</div>
        </flux:card>

        <flux:card class="p-4 space-y-2">
            <div class="text-zinc-500 text-sm flex items-center gap-2">
                <flux:icon.shopping-cart class="size-4" />
                Item Terjual
            </div>
            <div class="text-2xl font-bold">{{ number_format($summaryStats['total_sales']) }}</div>
            <div class="text-xs text-zinc-400 font-medium">akumulasi semua produk</div>
        </flux:card>

        <flux:card class="p-4 space-y-2">
            <div class="text-zinc-500 text-sm flex items-center gap-2">
                <flux:icon.eye class="size-4" />
                Total Kunjungan
            </div>
            <div class="text-2xl font-bold">{{ number_format($summaryStats['total_views']) }}</div>
            <div class="text-xs text-zinc-400 font-medium">unique & repeat visitors</div>
        </flux:card>

        <flux:card class="p-4 space-y-2">
            <div class="text-zinc-500 text-sm flex items-center gap-2">
                <flux:icon.arrow-trending-up class="size-4" />
                Conversion Rate
            </div>
            <div class="text-2xl font-bold">{{ number_format($summaryStats['conversion_rate'], 1) }}%</div>
            <div class="text-xs text-indigo-600 font-medium">rata-rata performa toko</div>
        </flux:card>
    </div>

    {{-- Main Chart --}}
    <flux:card class="p-6">
        <div class="flex items-center justify-between mb-6">
            <flux:heading>Tren Performa</flux:heading>
            <div class="flex gap-4">
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="size-2 rounded-full bg-emerald-500"></span> Pendapatan
                </div>
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="size-2 rounded-full bg-indigo-500"></span> Kunjungan
                </div>
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="size-2 rounded-full bg-amber-500"></span> Penjualan
                </div>
            </div>
        </div>

        <div class="h-[350px] relative">
            @if(!$readyToLoad)
                <div class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-zinc-900/50 z-10 rounded-xl">
                    <flux:icon.loading class="size-8" />
                </div>
            @endif
            <canvas id="performanceChart" wire:ignore></canvas>
        </div>
    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Top Products Table --}}
        <div class="lg:col-span-2 space-y-4">
            <flux:heading>Produk Berperforma Tinggi</flux:heading>
            <flux:card class="p-0 overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Produk</flux:table.column>
                        <flux:table.column>Terjual</flux:table.column>
                        <flux:table.column>Conversion</flux:table.column>
                        <flux:table.column align="right">Revenue</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($topProducts as $product)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->thumbnail_url }}" class="size-10 rounded-lg object-cover" />
                                        <div class="font-medium text-sm">{{ $product->name }}</div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>{{ $product->sales_count }}</flux:table.cell>
                                <flux:table.cell>
                                    @php $rate = $product->views_count > 0 ? ($product->sales_count / $product->views_count) * 100 : 0; @endphp
                                    <flux:badge size="sm" :color="$rate > 2 ? 'emerald' : 'zinc'">{{ number_format($rate, 1) }}%</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell align="right" class="font-bold">
                                    Rp {{ number_format($product->sales_count * $product->price * 0.8, 0, ',', '.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>

        {{-- Insights Panel --}}
        <div class="space-y-4">
            <flux:heading>Strategic Insights</flux:heading>
            <div class="space-y-3">
                <flux:card class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800">
                    <div class="flex gap-3">
                        <flux:icon.light-bulb class="size-5 text-indigo-600" />
                        <div class="space-y-1">
                            <div class="text-sm font-bold text-indigo-900 dark:text-indigo-100">Tips Optimasi</div>
                            <p class="text-xs text-indigo-700 dark:text-indigo-300 leading-relaxed">
                                Produk dengan konversi di bawah 1.5% mungkin membutuhkan thumbnail yang lebih menarik atau deskripsi yang lebih detail.
                            </p>
                        </div>
                    </div>
                </flux:card>

                <flux:card class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800">
                    <div class="flex gap-3">
                        <flux:icon.sparkles class="size-5 text-emerald-600" />
                        <div class="space-y-1">
                            <div class="text-sm font-bold text-emerald-900 dark:text-emerald-100">Elite Advantage</div>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed">
                                Manfaatkan fitur "Spotlight" untuk produk terbaikmu guna meningkatkan visibilitas hingga 300%.
                            </p>
                        </div>
                    </div>
                </flux:card>
            </div>
                </flux:card>
            </div>
        </div>
    </div>

    {{-- XP Guide Disclosure --}}
    <flux:card class="p-6 bg-zinc-900 border-none shadow-2xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 via-indigo-500/10 to-amber-500/10 opacity-50"></div>
        <div class="relative flex flex-col md:flex-row items-center gap-8">
            <div class="size-20 shrink-0 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center text-white">
                <flux:icon.information-circle class="size-10" />
            </div>
            <div class="flex-1 space-y-4">
                <div>
                    <flux:heading size="xl" class="text-white font-black uppercase">How Smart XP Works?</flux:heading>
                    <flux:text class="text-zinc-400">Nexacode rewarding quality, activity, and expertise. Here's how to level up your reputation.</flux:text>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex gap-3">
                        <div class="size-8 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                            <flux:icon.shopping-cart class="size-4 text-emerald-500" />
                        </div>
                        <div>
                            <div class="text-xs font-black text-white uppercase tracking-widest">Sales (Volume)</div>
                            <p class="text-[10px] text-zinc-500 leading-tight mt-1">Earn **1 XP** for every Rp 1.000 earned from product sales.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="size-8 rounded-lg bg-amber-500/20 flex items-center justify-center shrink-0">
                            <flux:icon.star class="size-4 text-amber-500" />
                        </div>
                        <div>
                            <div class="text-xs font-black text-white uppercase tracking-widest">Quality (Rating)</div>
                            <p class="text-[10px] text-zinc-500 leading-tight mt-1">Get massive bonuses: **50 XP** for 5-star reviews, **20 XP** for 4-stars.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="size-8 rounded-lg bg-indigo-500/20 flex items-center justify-center shrink-0">
                            <flux:icon.bolt class="size-4 text-indigo-500" />
                        </div>
                        <div>
                            <div class="text-xs font-black text-white uppercase tracking-widest">Smart Rank</div>
                            <p class="text-[10px] text-zinc-500 leading-tight mt-1">Ranking considers Revenue Impact, average rating, and account loyalty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:card>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let chart = null;

        const initChart = (data) => {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.4
                        },
                        point: {
                            radius: 2,
                            hoverRadius: 6
                        }
                    }
                }
            });
        };

        $wire.on('chartDataUpdated', (data) => {
            initChart(data[0]);
        });

        // Initial load check
        @if($readyToLoad)
            initChart(@json($chartData));
        @endif
    });
</script>
@endpush
