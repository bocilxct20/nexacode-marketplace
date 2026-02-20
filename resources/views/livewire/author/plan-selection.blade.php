<div class="space-y-12 py-12">
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <flux:heading size="xl" class="font-black tracking-tight">Tingkatkan Performa Kamu</flux:heading>
        <flux:subheading size="lg">Pilih level yang sesuai dengan pertumbuhan kamu. Skalakan pendapatan dan jangkauan produk kamu dengan NEXACODE Premium.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto px-4">
        @foreach($plans as $plan)
            @php 
                $isCurrent = $currentPlanId == $plan->id || (!$currentPlanId && $plan->is_default);
                $isElite = $plan->is_elite;
                $canTrial = $plan->allow_trial;
            @endphp
            
            <flux:card class="relative flex flex-col p-8 {{ $isElite ? 'border-emerald-500 shadow-2xl shadow-emerald-500/10' : '' }}">
                @if($isElite)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                        <flux:badge size="sm" color="emerald" class="px-4 py-1 uppercase font-black tracking-widest shadow-lg">Paling Populer</flux:badge>
                    </div>
                @endif

                <div class="mb-8">
                    <flux:heading size="lg" class="font-black">{{ $plan->name }}</flux:heading>
                    <div class="mt-4 flex items-baseline gap-1">
                        <flux:text size="xl" class="text-4xl font-black tabular-nums">Rp {{ number_format($plan->price, 0, ',', '.') }}</flux:text>
                        <flux:text size="sm" class="text-zinc-500 font-bold">/bln</flux:text>
                    </div>
                </div>

                <div class="flex-1 space-y-6 mb-10">
                    <flux:heading size="xs" class="font-black uppercase tracking-widest text-zinc-400">Apa Saja Yang Termasuk</flux:heading>
                    
                    <div class="space-y-3">
                        @foreach($plan->features as $feature)
                            <div class="flex items-start gap-4">
                                <flux:icon.check-circle variant="mini" class="text-emerald-500 shrink-0 mt-0.5" />
                                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 leading-tight">
                                    {{ $feature }}
                                </flux:text>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    @if($canTrial && !$isCurrent && !$user->hasUsedTrial())
                        <flux:button 
                            wire:click="startTrial({{ $plan->id }})" 
                            variant="primary" 
                            class="w-full h-12 font-bold shadow-lg shadow-emerald-500/20"
                        >
                            Mulai Trial Gratis 7 Hari
                        </flux:button>
                        <flux:button 
                            wire:click="selectPlan({{ $plan->id }})" 
                            variant="subtle" 
                            class="w-full h-10 font-bold"
                        >
                            Lewati Trial & Bayar Sekarang
                        </flux:button>
                    @else
                        @php 
                            $canSelect = !$isCurrent || !$user->isSubscriptionActive() || $user->isExpiringSoon();
                            $buttonText = $isCurrent ? ($user->isSubscriptionActive() ? ($user->isExpiringSoon() ? 'Perbarui' : 'Paket Saat Ini') : 'Perbarui') : 'Pilih ' . $plan->name;
                        @endphp

                        @if($canTrial && $user->hasUsedTrial() && !$isCurrent)
                            <div class="flex items-center justify-center gap-2 mb-2 text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-900/20 py-1 rounded-lg border border-amber-200 dark:border-amber-800">
                                <flux:icon.information-circle variant="mini" class="w-3.5 h-3.5" />
                                <span>Trial Sudah Digunakan</span>
                            </div>
                        @endif

                        <flux:button 
                            wire:click="selectPlan({{ $plan->id }})" 
                            variant="{{ $isElite ? 'primary' : 'subtle' }}" 
                            class="w-full h-12 font-bold"
                            :disabled="!$canSelect"
                        >
                            {{ $buttonText }}
                        </flux:button>
                    @endif
                </div>
            </flux:card>
        @endforeach
    </div>

    <div class="text-center max-w-2xl mx-auto">
        <flux:text size="sm" class="text-zinc-500 italic leading-relaxed">
            Semua paket termasuk fitur standar marketplace. Komisi dipotong otomatis dari setiap penjualan. 
            Harga dalam Rupiah (IDR).
        </flux:text>
    </div>

    {{-- Checkout Modal --}}
    <flux:modal name="checkout-modal" wire:model="showCheckoutModal" class="md:w-[500px]">
        @if($checkoutPlan)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="font-black">Konfirmasi Paket</flux:heading>
                    <flux:subheading>Selesaikan pendaftaran paket premium untuk meningkatkan jangkauan produk kamu.</flux:subheading>
                </div>

                {{-- Plan Details Card (Premium Admin Style) --}}
                <div class="p-6 bg-emerald-500/5 border border-emerald-500/20 rounded-3xl flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 size-24 bg-emerald-500/10 blur-2xl rounded-full group-hover:bg-emerald-500/20 transition-colors"></div>
                    
                    <div class="relative">
                        <div class="text-[10px] uppercase font-black tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">Paket Terpilih</div>
                        <flux:heading size="sm" class="font-bold">{{ $checkoutPlan->name }}</flux:heading>
                    </div>
                    <div class="text-right relative">
                        <div class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums">Rp {{ number_format($checkoutPlan->price, 0, ',', '.') }}</div>
                        <flux:text size="xs" class="text-zinc-500 font-medium">Tagihan per bulan</flux:text>
                    </div>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                    <div class="flex gap-3">
                        <flux:icon.information-circle variant="mini" class="text-zinc-400 shrink-0 mt-0.5" />
                        <flux:text size="xs" class="leading-relaxed">
                            Dengan menekan tombol di bawah, kamu akan diarahkan ke halaman checkout untuk memilih metode pembayaran dan menyelesaikan transaksi.
                        </flux:text>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button wire:click="resetCheckout" variant="ghost" class="flex-1">Batal</flux:button>
                    <flux:button wire:click="processPayment" variant="primary" class="flex-[2] font-black h-12 shadow-xl shadow-emerald-500/20" icon="chevron-right" icon-trailing>
                        Bayar & Aktifkan
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
