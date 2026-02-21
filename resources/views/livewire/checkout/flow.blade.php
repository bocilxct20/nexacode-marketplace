<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Progress Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <flux:card class="p-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-4">
                    <flux:icon.lock-closed variant="mini" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <flux:heading size="lg">Secure Checkout</flux:heading>
                <flux:subheading size="xs">100% Guarded & Encrypted</flux:subheading>
            </flux:card>

            <flux:card class="p-6">
                <flux:heading size="sm" class="uppercase tracking-widest text-zinc-400 font-bold mb-4">Order Summary</flux:heading>
                
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ $product->thumbnail_url }}" class="w-12 h-12 rounded-xl object-cover">
                    <div class="flex flex-col">
                        <span class="font-bold text-sm line-clamp-1">{{ $product->name }}</span>
                        <span class="text-[10px] text-emerald-600 font-black uppercase tracking-tighter">Full Source Code</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Harga Produk</span>
                        <span class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($discount > 0)
                        <div class="flex justify-between text-sm text-emerald-600">
                            <span>Discount / Coupon</span>
                            <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <flux:separator variant="subtle" />
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="font-bold">Total Pay</span>
                        <span class="text-xl font-black text-indigo-600">Rp {{ number_format($this->finalPrice, 0, ',', '.') }}</span>
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            <flux:card class="p-8">
                <flux:heading size="xl" class="mb-2">Review & Complete Payment</flux:heading>
                <flux:subheading class="mb-8">Please check your order details before proceeding to payment.</flux:subheading>

                {{-- Coupon Section --}}
                <div class="mb-8">
                    <flux:heading size="sm" class="mb-4 font-bold uppercase tracking-widest text-zinc-400">Apply Promo Code</flux:heading>
                    <div class="flex gap-2">
                        <flux:input wire:model="couponCode" placeholder="Enter code here..." class="flex-1" />
                        <flux:button wire:click="applyCoupon" variant="subtle">Apply</flux:button>
                    </div>
                    @if($appliedCoupon)
                        <div class="mt-2 flex items-center gap-2">
                            <flux:badge color="emerald" size="sm" variant="solid" class="font-black uppercase tracking-tighter">Coupon Applied: {{ $appliedCoupon->code }}</flux:badge>
                            <flux:button variant="ghost" size="sm" wire:click="$set('appliedCoupon', null); $set('discount', 0)" icon="x-mark"></flux:button>
                        </div>
                    @endif
                    @error('couponCode')
                        <span class="text-xs text-rose-500 mt-2 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 mb-8">
                    <flux:heading size="sm" class="mb-4 font-bold uppercase tracking-widest text-zinc-400">Payment Gateway</flux:heading>
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <img src="https://static.okrs.ai/midtrans.png" class="h-6 opacity-80" onerror="this.src='https://ui-avatars.com/api/?name=Midtrans&background=0061A8&color=fff'">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold">Secured by Midtrans</span>
                            <span class="text-[10px] text-zinc-500 font-medium">Virtual Account, E-Wallet, QRIS, Credit Card</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:button wire:click="processPayment" variant="primary" icon="shield-check" class="w-full h-16 text-lg font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20">
                        Pay Securely Now
                    </flux:button>
                    <p class="text-[10px] text-center text-zinc-400">By clicking the button above, you agree to our Terms of Service & Refund Policy.</p>
                </div>

                @error('payment')
                    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 text-sm mt-6 font-medium animate-pulse">
                        {{ $message }}
                    </div>
                @enderror
            </flux:card>
        </div>
    </div>

    {{-- Midtrans Snap Integration --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('payment-ready', (event) => {
                const data = event[0];
                window.snap.pay(data.snapToken, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('checkout.success') }}";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('checkout.pending') }}";
                    },
                    onError: function(result) {
                        window.location.href = "{{ route('checkout.failed') }}";
                    },
                    onClose: function() {
                        alert('You closed the payment window without finishing the payment.');
                    }
                });
            });
        });
    </script>
</div>
