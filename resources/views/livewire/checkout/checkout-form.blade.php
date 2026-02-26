<div class="relative">

    <div class="p-6 bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm mt-6">
        <flux:radio.group label="Select Payment Method" wire:model="selectedPaymentMethodId" class="space-y-3">
            @forelse($paymentMethods as $method)
                @php
                    $paymentType = match($method->type) {
                        'bank_transfer' => 'Bank Transfer',
                        'qris' => 'QRIS Payment',
                        default => 'E-Wallet'
                    };
                @endphp
                <flux:radio wire:key="payment-method-{{ $method->id }}" value="{{ $method->id }}" label="{{ $method->name }}" description="{{ $paymentType }}">
                    {{-- Payment Instructions Preview --}}
                    <div x-show="$wire.selectedPaymentMethodId == '{{ $method->id }}'" x-collapse class="mt-3 p-4 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800/50 rounded-xl">
                        <flux:subheading class="text-sm">
                            @if($method->type === 'bank_transfer')
                                Transfer to: <span class="font-bold">{{ $method->account_number }}</span> ({{ $method->account_name }})
                            @elseif($method->type === 'qris')
                                Scan QRIS code on the next page
                            @endif
                        </flux:subheading>
                    </div>
                </flux:radio>
            @empty
                <div class="text-center py-8">
                    <flux:icon.exclamation-circle class="w-12 h-12 mx-auto mb-3 text-zinc-400" />
                    <flux:subheading>No payment methods available. Please contact support.</flux:subheading>
                </div>
            @endforelse
        </flux:radio.group>

        <flux:error name="selectedPaymentMethodId" />

        {{-- Coupon Section --}}
        <div class="mt-8 pt-8 border-t border-zinc-100 dark:border-zinc-800">
            <flux:label class="mb-3">Have a discount code?</flux:label>
            @if(!$appliedCoupon)
                <div class="flex gap-2">
                    <flux:input wire:model="couponCode" placeholder="Enter code" class="flex-1 uppercase font-mono" />
                    <flux:button wire:click="applyCoupon" variant="subtle">Apply</flux:button>
                </div>
                <flux:error name="couponCode" />
            @else
                <div class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/20">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center shrink-0">
                            <flux:icon.ticket variant="mini" class="text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-widest text-emerald-800 dark:text-emerald-300">Code: {{ $appliedCoupon->code }}</div>
                            <div class="text-[10px] font-bold text-emerald-600/70 dark:text-emerald-400/70">-Rp {{ number_format($discountAmount, 0, ',', '.') }} off</div>
                        </div>
                    </div>
                    <flux:button variant="ghost" square size="sm" icon="x-mark" wire:click="removeCoupon" class="text-emerald-600 hover:text-emerald-700 hover:bg-emerald-100" />
                </div>
            @endif
        </div>

        {{-- Order Summary --}}
        <div class="mt-8 p-6 bg-zinc-50 dark:bg-zinc-800/30 rounded-3xl border border-zinc-100 dark:border-zinc-800/50">
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Subtotal</span>
                    <span class="font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
                @if($discountAmount > 0)
                    <div class="flex justify-between text-sm text-emerald-600 dark:text-emerald-500">
                        <span>Discount</span>
                        <span class="font-bold">-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <flux:separator variant="subtle" class="my-2" />
                <div class="flex justify-between items-center pt-2">
                    <span class="font-bold">Total to Pay</span>
                    <span class="text-2xl font-black tabular-nums">Rp {{ number_format($product->price - $discountAmount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Proceed Button --}}
        <div class="pt-8">
            <flux:button 
                type="button"
                wire:click="process"
                variant="primary" 
                class="w-full py-5 text-base font-black uppercase tracking-widest rounded-2xl"
                :disabled="!$selectedPaymentMethodId"
                wire:loading.attr="disabled"
            >
                <flux:icon.loading class="w-5 h-5 animate-spin mr-2" wire:loading wire:target="process" />
                <span wire:loading.remove wire:target="process">Complete Purchase</span>
                <span wire:loading wire:target="process">Processing...</span>
            </flux:button>
        </div>
    </div>
</div>
