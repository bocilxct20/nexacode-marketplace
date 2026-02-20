@extends('layouts.author')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Analytics Dashboard</flux:heading>
            <flux:subheading>Track your sales, revenue, and performance</flux:subheading>
        </div>
        
        <div class="flex gap-3">
            {{-- Period Selector --}}
            <form method="GET" action="{{ route('author.analytics') }}">
                <flux:select name="period" onchange="this.form.submit()" class="w-40">
                    <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="1year" {{ $period === '1year' ? 'selected' : '' }}>Last Year</option>
                </flux:select>
            </form>
            
            {{-- Export Button --}}
            <flux:button variant="outline" href="{{ route('author.analytics.export', ['type' => 'sales', 'period' => $period]) }}">
                📊 Export Data
            </flux:button>
        </div>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Sales --}}
        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Sales</div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ number_format($salesAnalytics['total_sales']) }}
                </div>
                <div class="text-sm text-green-600 dark:text-green-400">
                    📈 {{ $period }}
                </div>
            </div>
        </flux:card>

        {{-- Total Revenue --}}
        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Revenue</div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                    Rp {{ number_format($salesAnalytics['total_revenue'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Gross sales
                </div>
            </div>
        </flux:card>

        {{-- Your Earnings --}}
        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Your Earnings (80%)</div>
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($revenueAnalytics['total_earnings'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    After platform fee
                </div>
            </div>
        </flux:card>

        {{-- Average Order Value --}}
        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Avg. Order Value</div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                    Rp {{ number_format($salesAnalytics['average_order_value'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Per transaction
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Sales Trend Chart --}}
        <flux:card>
            <div class="space-y-4">
                <flux:heading size="lg">Sales Trend</flux:heading>
                <livewire:author.analytics.sales-chart :period="$period" />
            </div>
        </flux:card>

        {{-- Revenue Chart --}}
        <flux:card>
            <div class="space-y-4">
                <flux:heading size="lg">Revenue & Earnings</flux:heading>
                <livewire:author.analytics.revenue-chart :period="$period" />
            </div>
        </flux:card>
    </div>

    {{-- Customer Insights --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Customers</div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ number_format($customerInsights['total_customers']) }}
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">New This Month</div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($customerInsights['new_customers_this_month']) }}
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Repeat Rate</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format($customerInsights['repeat_rate'], 1) }}%
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Top Products Table --}}
    <flux:card>
        <div class="space-y-4">
            <flux:heading size="lg">Top Performing Products</flux:heading>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Product</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Sales</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Revenue</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Earnings</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Views</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Conv. Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $product['name'] }}</div>
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ number_format($product['sales']) }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300">
                                Rp {{ number_format($product['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-green-600 dark:text-green-400 font-semibold">
                                Rp {{ number_format($product['earnings'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ number_format($product['views']) }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $product['conversion_rate'] >= 5 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    {{ number_format($product['conversion_rate'], 2) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                No sales data available yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </flux:card>
</div>
@endsection
