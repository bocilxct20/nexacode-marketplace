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
        </div>
    </div>
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
