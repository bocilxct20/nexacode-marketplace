<div wire:init="loadData">
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Partner Portal</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    @if (!$readyToLoad)
        {{-- Skeleton loading --}}
        <div class="space-y-12">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <flux:skeleton class="h-8 w-48" />
                    <flux:skeleton class="h-4 w-64" />
                </div>
                <flux:skeleton class="h-10 w-48 rounded-xl" />
            </div>
            <flux:skeleton class="h-48 w-full rounded-3xl" />
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach(range(1, 6) as $i)
                    <flux:skeleton class="h-32 rounded-2xl" />
                @endforeach
            </div>
        </div>
    @elseif (!$isAffiliate)
        {{-- Onboarding Screen --}}
        <div class="max-w-3xl mx-auto py-12 text-center space-y-12">
            <div class="space-y-4">
                <div class="flex justify-center">
                    <div class="w-24 h-24 rounded-3xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                        <flux:icon.megaphone class="w-12 h-12" />
                    </div>
                </div>
                <flux:heading size="xl" class="font-black">Join the NexaAffiliate Program</flux:heading>
                <flux:subheading size="lg">Earn up to **10% commission** on every sale you refer to NexaCode Marketplace.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <flux:card class="p-6 space-y-3">
                    <flux:icon.currency-dollar class="text-emerald-500" />
                    <flux:heading class="font-bold">Passive Income</flux:heading>
                    <flux:text size="xs">Share links on your blog or social media and earn while you sleep.</flux:text>
                </flux:card>
                <flux:card class="p-6 space-y-3">
                    <flux:icon.chart-bar class="text-indigo-500" />
                    <flux:heading class="font-bold">Real-time Analytics</flux:heading>
                    <flux:text size="xs">Track every click and conversion with our advanced analytics dashboard.</flux:text>
                </flux:card>
                <flux:card class="p-6 space-y-3">
                    <flux:icon.gift class="text-orange-500" />
                    <flux:heading class="font-bold">Vanity Coupons</flux:heading>
                    <flux:text size="xs">Get custom coupon codes that track sales even without cookies.</flux:text>
                </flux:card>
            </div>

            <div class="pt-8">
                <flux:button variant="primary" wire:click="joinProgram" class="rounded-2xl px-12 py-6 text-lg font-bold">
                    Join Now & Start Earning
                </flux:button>
                <flux:text class="mt-4 text-xs">By joining, you agree to our Affiliate Terms of Service.</flux:text>
            </div>
        </div>
    @else
        {{-- The actual dashboard --}}
        <div class="space-y-12">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl" class="font-bold">Affiliate Hub</flux:heading>
                    <flux:subheading size="lg">Earn money by sharing NexaCode with your network.</flux:subheading>
                </div>
                <div class="flex gap-2">
                    <flux:button 
                        variant="primary" 
                        icon="wallet" 
                        x-on:click="$flux.modal('withdrawal-modal').show()"
                    >
                        <flux:spacer />
                        Withdraw Balance (Rp {{ number_format($stats['balance'], 0, ',', '.') }})
                    </flux:button>
                </div>
            </div>

            {{-- Referral Link Card --}}
            <flux:card class="p-8 bg-indigo-500/5 border-indigo-500/20 dark:bg-indigo-500/10 rounded-3xl relative overflow-hidden">
                <div class="absolute -right-12 -top-12 opacity-5 pointer-events-none">
                    <flux:icon.megaphone class="w-64 h-64 rotate-12" />
                </div>
                
                <div class="max-w-2xl">
                    <flux:heading size="lg" class="font-bold mb-2">Your Magic Link 🪄</flux:heading>
                    <flux:subheading class="mb-6">Share this link anywhere. When someone signs up or buys using your link, you get **10%** of the platform commission!</flux:subheading>
                    
                    <flux:input.group>
                        <flux:input 
                            value="{{ $referralLink }}" 
                            readonly 
                            id="referral-link-input"
                            class="font-medium"
                        />
                        <x-slot name="append">
                            <flux:button 
                                variant="primary" 
                                icon="document-duplicate"
                                class="font-bold whitespace-nowrap"
                                onclick="copyReferralLink()"
                            >
                                Copy Link
                            </flux:button>
                        </x-slot>
                    </flux:input.group>
                </div>
            </flux:card>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <flux:card class="p-6 bg-indigo-500/5 border-indigo-500/20">
                    <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Available Balance</flux:subheading>
                    <flux:heading size="xl" class="font-black tabular-nums text-indigo-600 dark:text-indigo-400">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</flux:heading>
                    <flux:subheading size="xs" class="font-bold text-zinc-500 italic text-[10px]">Ready to withdraw</flux:subheading>
                </flux:card>

                <flux:card class="p-6">
                    <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Total Earned</flux:subheading>
                    <flux:heading size="xl" class="font-black tabular-nums text-emerald-500">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</flux:heading>
                    <flux:subheading size="xs" class="font-bold text-zinc-500 italic text-[10px]">All time earnings</flux:subheading>
                </flux:card>

                <flux:card class="p-6">
                    <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Total Sales</flux:subheading>
                    <flux:heading size="xl" class="font-black tabular-nums">{{ number_format($stats['sales_count']) }}</flux:heading>
                    <flux:subheading size="xs" class="font-bold text-zinc-500 italic text-[10px]">Referred purchases</flux:subheading>
                </flux:card>

                <flux:card class="p-6">
                    <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Unique Clicks</flux:subheading>
                    <flux:heading size="xl" class="font-black tabular-nums">{{ number_format($stats['click_count']) }}</flux:heading>
                    <flux:subheading size="xs" class="font-bold text-zinc-500 italic text-[10px]">Total traffic</flux:subheading>
                </flux:card>

                <flux:card class="p-6">
                    <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">CTR (Conv.)</flux:subheading>
                    <flux:heading size="xl" class="font-black tabular-nums text-indigo-500">{{ $stats['ctr'] }}%</flux:heading>
                    <flux:subheading size="xs" class="font-bold text-zinc-500 italic text-[10px]">Conversion rate</flux:subheading>
                </flux:card>

                <flux:card class="p-6">
                    <flux:subheading size="xs" class="font-bold uppercase tracking-widest mb-2">Pending Earning</flux:subheading>
                    <flux:heading size="xl" class="font-black tabular-nums text-amber-500">Rp {{ number_format($stats['pending_earnings'], 0, ',', '.') }}</flux:heading>
                    <flux:subheading size="xs" class="font-bold text-zinc-500 italic text-[10px]">Being processed</flux:subheading>
                </flux:card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-4">
                {{-- Table: Recent Earnings --}}
                <flux:card>
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="lg">Recent Referral Sales</flux:heading>
                    </div>

                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Product</flux:table.column>
                            <flux:table.column>Earnings</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column align="right"></flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @if(!$readyToLoad)
                                @foreach(range(1, 5) as $i)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div class="flex items-center gap-4">
                                                <flux:skeleton class="w-12 h-12 rounded-lg shrink-0" />
                                                <div class="space-y-2 flex-1">
                                                    <flux:skeleton class="w-24 h-4" />
                                                    <flux:skeleton class="w-16 h-3" />
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell><flux:skeleton class="w-20 h-5" /></flux:table.cell>
                                        <flux:table.cell><flux:skeleton class="w-16 h-6 rounded-full" /></flux:table.cell>
                                        <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            @else
                                @forelse($recentEarnings as $earning)
                                    <flux:table.row :key="'earning-'.$earning->id">
                                        <flux:table.cell variant="strong">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-lg bg-zinc-100 dark:bg-zinc-800 shrink-0 overflow-hidden border border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
                                                    @if($earning->product->thumbnail_url)
                                                        <img src="{{ $earning->product->thumbnail_url }}" alt="{{ $earning->product->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <flux:icon.cube variant="mini" class="text-zinc-500" />
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-sm text-zinc-800 dark:text-zinc-200 truncate max-w-[140px]">{{ $earning->product->name }}</div>
                                                    <div class="text-[10px] text-zinc-500 font-mono">{{ $earning->created_at->format('M d, Y') }}</div>
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($earning->amount, 0, ',', '.') }}
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge size="sm" :color="$earning->status_color" inset="top bottom">
                                                {{ $earning->status_label }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell align="right">
                                            <flux:button variant="ghost" icon="chevron-right" size="sm" inset="top bottom" />
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="text-center py-12 text-zinc-500 italic">No sales recorded yet.</flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            @endif
                        </flux:table.rows>
                    </flux:table>
                </flux:card>

                {{-- Table: Payout History --}}
                <flux:card>
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="lg">Payout History</flux:heading>
                    </div>

                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Summary</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column align="right"></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @if(!$readyToLoad)
                                @foreach(range(1, 3) as $i)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div class="space-y-2">
                                                <flux:skeleton class="w-32 h-4" />
                                                <flux:skeleton class="w-16 h-3" />
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell><flux:skeleton class="w-20 h-6 rounded-full" /></flux:table.cell>
                                        <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            @else
                                @forelse($recentPayouts as $payout)
                                    <flux:table.row :key="'payout-'.$payout->id">
                                        <flux:table.cell>
                                            <div class="font-bold text-zinc-800 dark:text-zinc-200 tabular-nums">Rp {{ number_format($payout->amount, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-zinc-500 font-mono">{{ $payout->created_at->format('M d, Y') }}</div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge size="sm" :color="$payout->status_color" inset="top bottom">
                                                {{ $payout->status_label }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell align="right">
                                            <flux:button variant="ghost" icon="eye" size="sm" inset="top bottom" />
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="3" class="text-center py-12 text-zinc-500 italic">No payout history found.</flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            @endif
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            </div>

            @if($activeCoupons->count() > 0)
                <div class="pt-8">
                    <flux:heading size="lg" class="font-bold mb-6">Your Vanity Coupons 🏷️</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($activeCoupons as $coupon)
                            <flux:card class="p-6 relative overflow-hidden group">
                                <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <flux:icon.ticket class="w-20 h-20" />
                                </div>
                                <div class="flex items-start justify-between mb-4">
                                    <div class="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-3 py-1 rounded-lg font-mono font-bold text-sm tracking-widest uppercase">
                                        {{ $coupon->code }}
                                    </div>
                                    <flux:badge size="sm" color="emerald" class="font-bold uppercase text-[10px]">Active</flux:badge>
                                </div>
                                <flux:heading size="sm" class="font-bold">{{ $coupon->description }}</flux:heading>
                                <div class="mt-4 flex items-end justify-between">
                                    <div>
                                        <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-tighter">Discount Value</div>
                                        <div class="text-lg font-black text-emerald-600 tabular-nums">
                                            {{ $coupon->type === 'percentage' ? $coupon->value . '%' : 'Rp ' . number_format($coupon->value, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <flux:button 
                                        variant="subtle" 
                                        size="xs" 
                                        icon="document-duplicate"
                                        onclick="copyText('{{ $coupon->code }}', 'Coupon Copied!')"
                                    />
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8">
                <flux:card class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                            <flux:icon.question-mark-circle />
                        </div>
                        <flux:heading class="font-bold">Affiliate FAQ</flux:heading>
                    </div>
                    <div class="space-y-4 pt-4">
                        <div class="space-y-1">
                            <flux:text size="sm" class="font-bold text-zinc-900 dark:text-white">When do I get paid?</flux:text>
                            <flux:text size="xs">Earnings are processed after the 7-day refund window expires.</flux:text>
                        </div>
                        <div class="space-y-1">
                            <flux:text size="sm" class="font-bold text-zinc-900 dark:text-white">Is there a minimum withdrawal?</flux:text>
                            <flux:text size="xs">Yes, the minimum withdrawal amount is Rp 50.000.</flux:text>
                        </div>
                        <div class="space-y-1">
                            <flux:text size="sm" class="font-bold text-zinc-900 dark:text-white">How is the commission calculated?</flux:text>
                            <flux:text size="xs">You receive 10% of the platform's cut from every sale you refer.</flux:text>
                        </div>
                    </div>
                </flux:card>

                <flux:card class="p-6 space-y-4 bg-emerald-500/5 border-emerald-500/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <flux:icon.rocket-launch />
                        </div>
                        <flux:heading class="font-bold">Promotion Tips</flux:heading>
                    </div>
                    <div class="space-y-4 pt-4">
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2">
                                <flux:icon.check-circle variant="mini" class="text-emerald-500 mt-0.5" />
                                <flux:text size="xs">Share on social media (Twitter/X, LinkedIn) with a personal review.</flux:text>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon.check-circle variant="mini" class="text-emerald-500 mt-0.5" />
                                <flux:text size="xs">Embed links in your technical blog posts or newsletters.</flux:text>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon.check-circle variant="mini" class="text-emerald-500 mt-0.5" />
                                <flux:text size="xs">Use the "Share & Earn" button on product pages for clean links.</flux:text>
                            </li>
                        </ul>
                    </div>
                </flux:card>
            {{-- Withdrawal Modal --}}
            <flux:modal name="withdrawal-modal" class="md:w-[450px]">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg" class="font-bold">Withdraw Balance</flux:heading>
                        <flux:subheading>Enter the amount you wish to withdraw to your bank account.</flux:subheading>
                    </div>

                    <div class="p-4 bg-indigo-500/5 border border-indigo-500/10 rounded-2xl flex items-center justify-between">
                        <flux:text size="sm" class="font-bold">Available Balance</flux:text>
                        <flux:text size="sm" class="tabular-nums font-black text-indigo-600">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</flux:text>
                    </div>

                    <form wire:submit="requestWithdrawal" class="space-y-6">
                        <flux:field>
                            <flux:label>Withdrawal Amount</flux:label>
                            <flux:input.group>
                                <flux:input.group.prefix>Rp</flux:input.group.prefix>
                                <flux:input 
                                    wire:model="withdrawalAmount" 
                                    type="number" 
                                    placeholder="Min. 50.000" 
                                    class="font-bold tabular-nums"
                                />
                            </flux:input.group>
                            <flux:error name="withdrawalAmount" />
                        </flux:field>

                        <div class="flex gap-3">
                            <flux:modal.close>
                                <flux:button variant="ghost" class="flex-1">Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button 
                                type="submit"
                                variant="primary" 
                                class="flex-1 font-bold" 
                                wire:loading.attr="disabled"
                            >
                                Submit Request
                            </flux:button>
                        </div>
                    </form>

                    <flux:text size="xs" class="text-center italic text-zinc-500">
                        *Min. withdrawal Rp 50.000. Processed within 1-3 business days.
                    </flux:text>
                </div>
            </flux:modal>
        </div>
    @endif

    <script>
        window.addEventListener('close-modal', event => {
            $flux.modal(event.detail.name).hide();
        });
        function copyReferralLink() {
            const input = document.getElementById('referral-link-input');
            if (!input) return;
            copyText(input.value, 'Link Copied! 📋');
        }

        function copyText(text, heading) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            
            Flux.toast({
                variant: 'success',
                heading: heading,
                text: 'Ready to share with your network.'
            });
        }
    </script>
</div>
