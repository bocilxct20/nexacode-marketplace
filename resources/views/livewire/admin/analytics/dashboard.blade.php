<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Platform Analytics</flux:heading>
            <flux:subheading>Monitor platform performance and growth</flux:subheading>
        </div>
        
        <div class="flex gap-3">
            {{-- Period Selector --}}
            <flux:select wire:model.live="period" class="w-40">
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="90days">Last 90 Days</option>
                <option value="1year">Last Year</option>
            </flux:select>
            
            {{-- Export Button --}}
            <flux:button variant="outline" href="{{ route('admin.analytics.export', ['type' => 'revenue', 'period' => $period]) }}">
                📊 Export Data
            </flux:button>
        </div>
    </div>

    {{-- Platform Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Revenue</div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                    Rp {{ number_format($platformMetrics['total_revenue'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">All time</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Platform Commission</div>
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($platformMetrics['platform_commission'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ number_format($platformMetrics['effective_commission_rate'], 1) }}% effective rate
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Users</div>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($platformMetrics['total_users']) }}
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ number_format($platformMetrics['total_authors']) }} authors
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="space-y-2">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Products</div>
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ number_format($platformMetrics['total_products']) }}
                </div>
                <div class="text-sm text-yellow-600 dark:text-yellow-400">
                    {{ number_format($platformMetrics['pending_products']) }} pending
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue Chart --}}
        <flux:card>
            <div class="space-y-4">
                <flux:heading size="lg">Revenue Trend</flux:heading>
                <livewire:admin.analytics.revenue-chart :period="$period" :key="'rev-'.$period" />
            </div>
        </flux:card>

        {{-- User Growth Chart --}}
        <flux:card>
            <div class="space-y-4">
                <flux:heading size="lg">User Growth</flux:heading>
                <livewire:admin.analytics.user-growth-chart :period="$period" :key="'user-'.$period" />
            </div>
        </flux:card>
    </div>

    {{-- Products by Category --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <flux:card>
            <div class="space-y-4">
                <flux:heading size="lg">Products by Category</flux:heading>
                <livewire:admin.analytics.category-chart />
            </div>
        </flux:card>

        {{-- Product Stats --}}
        <flux:card>
            <div class="space-y-4">
                <flux:heading size="lg">Product Statistics</flux:heading>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Approval Rate</span>
                        <span class="text-lg font-semibold text-green-600 dark:text-green-400">
                            {{ number_format($productAnalytics['approval_rate'], 1) }}%
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Approved Products</span>
                        <span class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ number_format($productAnalytics['approved_products']) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Pending Review</span>
                        <span class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">
                            {{ number_format($productAnalytics['pending_products']) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Rejected</span>
                        <span class="text-lg font-semibold text-red-600 dark:text-red-400">
                            {{ number_format($productAnalytics['rejected_products']) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Average Price</span>
                        <span class="text-lg font-semibold text-zinc-900 dark:text-white">
                            Rp {{ number_format($productAnalytics['average_price'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
</div>
