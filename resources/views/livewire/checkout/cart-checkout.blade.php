<div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Left: Order Summary & Items --}}
        <div class="lg:col-span-2 space-y-8">
            <div>
                <flux:heading size="2xl" class="mb-2 uppercase font-black tracking-tight">Checkout Keranjang</flux:heading>
                <flux:subheading>Selesaikan pembayaran untuk {{ $cartItems->count() }} asset digital pilihanmu.</flux:subheading>
            </div>

            {{-- Product List --}}
            <div class="space-y-4">
                @foreach($cartItems as $item)
                    <flux:card class="p-4 flex gap-4 transition-all hover:border-emerald-500/30">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-zinc-100 shrink-0">
                            <img src="{{ $item->product->thumbnail_url }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <div class="font-bold text-lg truncate">{{ $item->product->name }}</div>
                            <div class="text-xs text-zinc-500 flex items-center gap-1.5">
                                Oleh <span class="font-bold text-zinc-900 dark:text-white">{{ $item->product->author->name }}</span>
                                <x-community-badge :user="$item->product->author" size="sm" class="scale-75 origin-left" />
                            </div>
                        </div>
                        <div class="text-right flex flex-col justify-center">
                            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($item->product->price, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-zinc-400 uppercase font-bold tracking-widest leading-none">Lifetime License</div>
                        </div>
                    </flux:card>
                @endforeach
            </div>

            {{-- Security Badge --}}
            <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 flex items-center gap-6">
                <div class="size-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <flux:icon.shield-check class="size-6 text-emerald-600" />
                </div>
                <div class="flex-1">
                    <div class="font-bold">Transaksi Aman & Terverifikasi</div>
                    <p class="text-sm text-zinc-500">Semua file dicek manual dan dilindungi enkripsi standard industri.</p>
                </div>
            </div>
        </div>

        {{-- Right: Payment & Total --}}
        <div class="space-y-8">
            <flux:card class="p-8 sticky top-8 shadow-2xl border-zinc-200 dark:border-zinc-800">
                <flux:heading size="lg" class="mb-6 uppercase tracking-widest font-black text-zinc-400">Rincian Pembayaran</flux:heading>
                
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-zinc-500">
                        <span>Subtotal ({{ $cartItems->count() }} items)</span>
                        <span>Rp {{ number_format($cartItems->sum(fn($i) => $i->product->price), 0, ',', '.') }}</span>
                    </div>
                    
                    @if($appliedCoupon)
                        <div class="flex justify-between text-red-500 font-bold">
                            <div class="flex items-center gap-1">
                                <flux:icon.tag variant="mini" />
                                <span>Diskon ({{ $appliedCoupon->code }})</span>
                            </div>
                            <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-zinc-500">
                        <span>Biaya Layanan</span>
                        <span class="text-emerald-500 font-bold">Termasuk</span>
                    </div>
                    
                    {{-- Coupon Input --}}
                    <div class="pt-4 mt-4 border-t border-dashed border-zinc-200 dark:border-zinc-800">
                        @if(!$appliedCoupon)
                            <div class="flex gap-2">
                                <flux:input wire:model="couponCode" placeholder="Kode Kupon / Affiliate" class="flex-1" />
                                <flux:button wire:click="applyCoupon" variant="ghost" class="font-bold">Terapkan</flux:button>
                            </div>
                            @error('couponCode') <p class="mt-2 text-[10px] text-red-500 font-bold uppercase">{{ $message }}</p> @enderror
                        @else
                            <div class="flex items-center justify-between p-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                <div class="flex items-center gap-2">
                                    <flux:icon.check-badge variant="mini" class="text-emerald-600" />
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 capitalize">Kupon Terpasang!</span>
                                </div>
                                <flux:button wire:click="removeCoupon" variant="ghost" size="sm" class="text-red-500 hover:text-red-600 font-bold text-[10px] uppercase">Hapus</flux:button>
                            </div>
                        @endif
                    </div>

                    <flux:separator variant="subtle" />
                    
                    <div class="flex justify-between items-center">
                        <span class="font-bold">Total Pembayaran</span>
                        <span class="text-3xl font-black">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <flux:label class="mb-3">Pilih Metode Pembayaran</flux:label>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($paymentMethods as $method)
                                <label class="relative flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border-2 cursor-pointer transition-all {{ $selectedPaymentMethodId == $method->id ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-transparent hover:border-zinc-300 dark:hover:border-zinc-700' }}">
                                    <input type="radio" wire:model.live="selectedPaymentMethodId" value="{{ $method->id }}" class="sr-only">
                                    <div class="size-10 rounded-xl bg-white dark:bg-zinc-800 flex items-center justify-center p-2 shadow-sm">
                                        {{-- Icon or Logo placeholder --}}
                                        @if($method->isQris())
                                            <flux:icon.qr-code class="size-6 text-indigo-600" />
                                        @else
                                            <flux:icon.credit-card class="size-6 text-indigo-600" />
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-sm leading-none">{{ $method->name }}</div>
                                        <div class="text-[10px] text-zinc-500 mt-1 uppercase leading-none font-bold tracking-tighter">{{ $method->type }}</div>
                                    </div>
                                    @if($selectedPaymentMethodId == $method->id)
                                        <flux:icon.check-circle variant="solid" class="size-5 text-emerald-500" />
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('selectedPaymentMethodId') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <flux:button wire:click="process" variant="primary" color="indigo" class="w-full py-6 text-lg shadow-xl shadow-indigo-500/20" :disabled="!$selectedPaymentMethodId">
                        Bayar Sekarang
                    </flux:button>
                    
                    <p class="text-[10px] text-center text-zinc-400 uppercase font-bold tracking-widest leading-relaxed">
                        Dengan membayar, kamu menyetujui <br> Syarat & Ketentuan NexaCode.
                    </p>
                </div>
            </flux:card>
        </div>
    </div>
</div>
