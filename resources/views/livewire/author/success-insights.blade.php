<div wire:init="loadData" class="space-y-6">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Insights</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Success Insights</flux:heading>
            <flux:subheading>Understand what drives your sales and how to optimize for more.</flux:subheading>
        </div>
    </div>

    @if(!$readyToLoad)
        <div class="flex items-center justify-center h-64">
            <flux:icon.loading class="size-8 animate-spin text-zinc-400" />
        </div>
    @else
        {{-- Success Score Header --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:card class="bg-gradient-to-br from-emerald-500/10 to-transparent border-emerald-500/20">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <flux:icon name="presentation-chart-line" class="size-5 text-emerald-500" />
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Market Standard</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums">
                            {{ number_format($platformAverageRate, 1) }}%
                        </div>
                        <flux:subheading>Platform average conversion</flux:subheading>
                    </div>
                </div>
            </flux:card>

            <flux:card class="bg-gradient-to-br from-cyan-500/10 to-transparent border-cyan-500/20">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <flux:icon name="eye" class="size-5 text-cyan-500" />
                        <div class="text-xs font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-widest">Total Impressions</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums">
                            {{ number_format($totalViews) }}
                        </div>
                        <flux:subheading>Unique product views</flux:subheading>
                    </div>
                </div>
            </flux:card>

            <flux:card class="bg-gradient-to-br from-indigo-500/10 to-transparent border-indigo-500/20">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <flux:icon name="shopping-bag" class="size-5 text-indigo-500" />
                        <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Conversions</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums">
                            {{ number_format($totalSales) }}
                        </div>
                        <flux:subheading>Total items sold</flux:subheading>
                    </div>
                </div>
            </flux:card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Search Intelligence --}}
            <flux:card class="relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-cyan-500/10 blur-3xl rounded-full"></div>
                <div class="space-y-4 relative">
                    <div class="flex items-center gap-2">
                        <flux:icon name="magnifying-glass-circle" class="size-5 text-cyan-500" />
                        <flux:heading size="lg">Marketplace Demand</flux:heading>
                    </div>
                    <flux:subheading>What buyers are searching for across NEXACODE right now.</flux:subheading>

                    <div class="space-y-3 mt-6">
                        @foreach($trendingKeywords as $keyword)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black text-zinc-400">#{{ $loop->iteration }}</span>
                                    <span class="font-bold text-zinc-700 dark:text-zinc-300">"{{ $keyword->query }}"</span>
                                </div>
                                <span class="text-xs font-black text-white bg-zinc-900 px-2 py-0.5 rounded shadow-sm">{{ number_format($keyword->hits ?? $keyword->results_count) }} Hits</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 p-4 bg-cyan-500/5 rounded-2xl border border-cyan-500/20">
                        <p class="text-xs text-cyan-700 dark:text-cyan-400 font-medium leading-relaxed">
                             💡 **PRO TIP:** If a search term has many results but low conversion, there might be a gap in quality. Build something better!
                        </p>
                    </div>
                </div>
            </flux:card>

            {{-- Product Breakdown --}}
            <flux:card>
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <flux:icon name="chart-bar" class="size-5 text-emerald-500" />
                        <flux:heading size="lg">Success Breakdown</flux:heading>
                    </div>
                    <flux:subheading>Optimizing individual product performance.</flux:subheading>

                    <div class="space-y-4 mt-6">
                        @foreach($productPerformance->take(5) as $item)
                            <div class="space-y-2">
                                <div class="flex justify-between items-end">
                                    <span class="text-sm font-bold text-zinc-900 dark:text-white truncate max-w-[200px]">{{ $item['name'] }}</span>
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded {{ $item['conversion_rate'] >= 5 ? 'bg-emerald-500 text-white' : 'bg-zinc-100 text-zinc-500' }}">
                                        {{ $item['status'] }}
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: {{ min($item['conversion_rate'] * 10, 100) }}%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                    <span>{{ $item['views'] }} Views</span>
                                    <span class="text-emerald-500">{{ number_format($item['conversion_rate'], 2) }}% Conv.</span>
                                </div>
                            </div>
                        @endforeach

                        @if($productPerformance->isEmpty())
                            <div class="py-12 text-center">
                                <flux:icon name="rocket-launch" class="size-12 text-zinc-300 mx-auto mb-4" />
                                <div class="text-zinc-500 text-sm">No product data available yet.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>
        </div>
    @endif
</div>
