<div wire:init="loadData" class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item separator="slash">Home</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    {{-- Verification Banners --}}
    @if (!$user->email_verified_at)
        <flux:callout variant="warning" icon="exclamation-circle" heading="Please verify your account to unlock all features.">
            Check your inbox for the verification code we sent you. 
            <flux:link href="{{ route('verify-otp') }}" class="font-bold">Verify now &rarr;</flux:link>
        </flux:callout>
    @endif

    @if (session('just_verified'))
        <flux:callout variant="success" icon="check-circle" heading="Your account is verified and ready to use." />
    @endif
    @if (!$readyToLoad)
        {{-- Skeleton State --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @for ($i = 0; $i < 3; $i++)
                <flux:card class="animate-pulse">
                    <div class="h-4 w-24 bg-zinc-200 dark:bg-zinc-800 rounded mb-2"></div>
                    <div class="h-8 w-32 bg-zinc-200 dark:bg-zinc-800 rounded"></div>
                </flux:card>
            @endfor
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
            <flux:card class="animate-pulse">
                <div class="h-6 w-32 bg-zinc-200 dark:bg-zinc-800 rounded mb-4"></div>
                <div class="space-y-4">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="h-16 w-full bg-zinc-100 dark:bg-zinc-800/50 rounded-2xl"></div>
                    @endfor
                </div>
            </flux:card>
            <flux:card class="animate-pulse">
                <div class="h-6 w-32 bg-zinc-200 dark:bg-zinc-800 rounded mb-4"></div>
                <div class="grid grid-cols-2 gap-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-40 bg-zinc-100 dark:bg-zinc-800/50 rounded-2xl"></div>
                    @endfor
                </div>
            </flux:card>
        </div>
    @else
        {{-- Actual Content --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:card class="p-6">
                <flux:subheading class="text-xs uppercase tracking-widest font-black opacity-50 mb-2">Total Spent</flux:subheading>
                <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">
                    Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}
                </div>
            </flux:card>

            <flux:card class="p-6">
                <flux:subheading class="text-xs uppercase tracking-widest font-black opacity-50 mb-2">Items Owned</flux:subheading>
                <div class="text-3xl font-black">
                    {{ number_format($stats['total_items']) }}
                </div>
            </flux:card>

            <flux:card class="p-6">
                <flux:subheading class="text-xs uppercase tracking-widest font-black opacity-50 mb-2">My Plan</flux:subheading>
                <div class="flex items-center justify-between">
                    <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ $user->currentPlan()->name }}
                    </div>
                    <flux:badge color="emerald" variant="subtle" size="sm" class="font-bold">
                        {{ 100 - ($user->currentPlan()->commission_rate ?? 30) }}% Earning Rate
                    </flux:badge>
                </div>
            </flux:card>
        </div>

        @if(!$user->isAuthor())
            <flux:card class="p-8 bg-zinc-950 dark:bg-zinc-900 border-none relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-colors duration-700"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <flux:heading size="xl" class="text-white font-black">Monetize Your Code & Projects</flux:heading>
                        <flux:text class="text-zinc-400 max-w-xl">
                            Punya script, template, atau plugin yang keren? Bergabunglah dengan komunitas author NexaCode dan raih pendapatan dari setiap baris kode yang kamu tulis.
                        </flux:text>
                    </div>
                    <flux:button href="{{ route('author.register') }}" variant="primary" color="emerald" class="font-bold shrink-0 shadow-lg shadow-emerald-500/20">
                        Become an Author &rarr;
                    </flux:button>
                </div>
            </flux:card>

        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Recent Purchases --}}
            <flux:card class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg" class="font-bold">Recent Purchases</flux:heading>
                    <flux:button variant="ghost" size="sm" href="{{ route('purchases.index') }}">View All</flux:button>
                </div>

                <div class="space-y-4">
                    @forelse($recentPurchases as $purchase)
                        <div class="group flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800 hover:border-emerald-500/30 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-zinc-200 dark:bg-zinc-700 rounded-lg overflow-hidden shrink-0">
                                    @foreach($purchase->items as $item)
                                        @if($item->product)
                                            <img src="{{ $item->product->thumbnail_url }}" class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                        @elseif($item->subscriptionPlan)
                                            <div class="w-full h-full flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30">
                                                <flux:icon.sparkles class="w-6 h-6 text-indigo-500" />
                                            </div>
                                        @endif
                                        @break
                                    @endforeach
                                </div>
                                <div>
                                    <div class="font-bold text-sm">
                                        @foreach($purchase->items as $item)
                                            @if($item->product)
                                                {{ $item->product->name }}
                                            @elseif($item->subscriptionPlan)
                                                Subscription: {{ $item->subscriptionPlan->name }}
                                            @else
                                                Unknown Item
                                            @endif
                                            @break
                                        @endforeach
                                    </div>
                                    <div class="text-[10px] uppercase font-bold text-zinc-500 tracking-wider">
                                        Purchased on {{ $purchase->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <flux:button variant="ghost" icon="arrow-down-tray" size="sm" square />
                        </div>
                    @empty
                        <div class="text-center py-12 text-zinc-500 italic">
                            No purchases yet. <a href="{{ route('products.index') }}" class="text-indigo-600 hover:underline">Start shopping</a>
                        </div>
                    @endforelse
                </div>
            </flux:card>

            {{-- Recommended for You --}}
            <flux:card class="p-6 text-center lg:text-left">
                <flux:heading size="lg" class="font-bold mb-6">Recommended for You</flux:heading>
                
                <div class="grid grid-cols-2 gap-4">
                    @foreach($recommendations as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="group block text-left">
                            <div class="relative aspect-video rounded-xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden mb-3">
                                <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                    <div class="text-white text-xs font-bold">View Product</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="font-bold text-xs truncate flex-1">{{ $product->name }}</div>
                                @if($product->is_on_sale)
                                    <flux:badge color="cyan" variant="solid" size="sm" class="px-1 py-0 text-[8px] font-black h-4 transition-transform group-hover:scale-105">FLASH</flux:badge>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="text-[10px] text-emerald-600 font-bold uppercase">
                                    Rp {{ number_format($product->discounted_price, 0, ',', '.') }}
                                </div>
                                @if($product->is_on_sale)
                                    <div class="text-[9px] text-zinc-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </flux:card>
        </div>
    @endif
</div>
