<div wire:init="loadData">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item separator="slash">Admin</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    @if (!$readyToLoad)
        {{-- Skeleton State --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @for ($i = 0; $i < 4; $i++)
                <flux:card class="animate-pulse">
                    <div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 rounded mb-2"></div>
                    <div class="h-8 w-32 bg-zinc-200 dark:bg-zinc-800 rounded"></div>
                </flux:card>
            @endfor
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <flux:card class="animate-pulse">
                <div class="h-6 w-32 bg-zinc-200 dark:bg-zinc-800 rounded mb-4"></div>
                <div class="space-y-4">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="h-12 w-full bg-zinc-100 dark:bg-zinc-800/50 rounded-xl"></div>
                    @endfor
                </div>
            </flux:card>
            <flux:card class="animate-pulse">
                <div class="h-6 w-32 bg-zinc-200 dark:bg-zinc-800 rounded mb-4"></div>
                <div class="space-y-4">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="h-12 w-full bg-zinc-100 dark:bg-zinc-800/50 rounded-xl"></div>
                    @endfor
                </div>
            </flux:card>
        </div>
    @else
        {{-- Actual Content --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <flux:card>
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Revenue</flux:subheading>
                <div class="text-2xl font-bold mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            </flux:card>

            <flux:card>
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Users</flux:subheading>
                <div class="text-2xl font-bold mt-1">{{ number_format($stats['total_users']) }}</div>
            </flux:card>

            <flux:card>
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Total Products</flux:subheading>
                <div class="text-2xl font-bold mt-1">{{ number_format($stats['total_products']) }}</div>
            </flux:card>

            <flux:card>
                <flux:subheading class="text-xs uppercase tracking-wider text-zinc-500">Pending Review</flux:subheading>
                <div class="text-2xl font-bold mt-1 text-amber-500">{{ number_format($stats['pending_products']) }}</div>
            </flux:card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Recent Orders --}}
            <flux:card>
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg">Recent Orders</flux:heading>
                    <flux:button variant="ghost" size="sm" href="{{ route('admin.orders') }}">View All</flux:button>
                </div>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($recentOrders as $order)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:avatar :name="$order->buyer->name" size="xs" />
                                        <span class="font-medium">{{ $order->buyer->name }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="tabular-nums font-bold">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" :color="match($order->status) {
                                        'completed' => 'emerald',
                                        'pending' => 'amber',
                                        'failed' => 'red',
                                        default => 'zinc'
                                    }" class="uppercase text-[10px] font-bold">
                                        {{ $order->status }}
                                    </flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            {{-- Top Authors --}}
            <flux:card>
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg">Recent Authors</flux:heading>
                    <flux:button variant="ghost" size="sm" href="{{ route('admin.users') }}">View All</flux:button>
                </div>

                <div class="space-y-4">
                    @foreach($top_authors as $author)
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex items-center justify-between group hover:border-emerald-500/30 transition-all">
                            <div class="flex items-center gap-3">
                                <flux:avatar :name="$author->name" size="sm" />
                                <div>
                                    <div class="font-bold text-zinc-900 dark:text-white">{{ $author->name }}</div>
                                    <div class="text-[10px] uppercase font-bold text-zinc-500 tracking-wider">
                                        {{ $author->products_count }} Products Listed
                                    </div>
                                </div>
                            </div>
                            <flux:button variant="ghost" size="sm" icon="chevron-right" square />
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('readyToLoad', () => {
            setTimeout(() => {
                const revenueCtx = document.getElementById('dashboardRevenueChart');
                if (revenueCtx) {
                    const revenueData = @js($revenueData);
                    new Chart(revenueCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: revenueData.map(d => d.date),
                            datasets: [{
                                label: 'Revenue',
                                data: revenueData.map(d => d.total_revenue),
                                borderColor: '#10b981',
                                tension: 0.4,
                                fill: true,
                                backgroundColor: 'rgba(16, 185, 129, 0.1)'
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                const userCtx = document.getElementById('dashboardUserChart');
                if (userCtx) {
                    const userData = @js($userData);
                    new Chart(userCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: userData.map(d => d.date),
                            datasets: [{
                                label: 'New Users',
                                data: userData.map(d => d.total_users),
                                backgroundColor: '#6366f1'
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }
            }, 100);
        });
    });
</script>
@endpush
