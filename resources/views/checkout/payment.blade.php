@extends('layouts.app')

@section('content')
<div class="relative bg-aurora overflow-hidden min-h-screen">
    <div class="absolute inset-0 bg-white/40 dark:bg-zinc-950/60 backdrop-blur-3xl"></div>
    
    <flux:container class="relative z-10 py-12">
    <div class="max-w-xl mx-auto">
        <div class="p-8 md:p-10 bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <div class="text-center mb-10">
                <flux:heading size="lg" class="mb-2 font-black uppercase tracking-tight">Complete Your Payment</flux:heading>
                <div class="text-xs uppercase tracking-widest font-bold text-zinc-500">Transaction ID: {{ $order->transaction_id }}</div>
            </div>

            {{-- Professional Checkout Stepper --}}
            <div class="relative mb-10">
                <div class="flex items-center justify-between max-w-sm mx-auto">
                    {{-- Step 1: Select Payment (Always Completed on this page) --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white font-bold shadow-sm">
                            <flux:icon.check class="w-4 h-4" />
                        </div>
                        <div class="mt-3 text-center">
                            <div class="text-[9px] uppercase tracking-widest font-black text-emerald-600 dark:text-emerald-500">Payment Selected</div>
                        </div>
                    </div>

                    {{-- Connector Line 1 (Completed) --}}
                    <div class="flex-1 h-[2px] bg-emerald-500 -mx-6 relative top-[-14px]"></div>

                    {{-- Step 2: Complete Payment --}}
                    <div class="flex flex-col items-center flex-1 px-2">
                        @if($order->isProcessing() || $order->isCompleted())
                            <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white font-bold shadow-sm">
                                <flux:icon.check class="w-4 h-4" />
                            </div>
                        @else
                            <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full border border-zinc-900 dark:border-white bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-sm ring-4 ring-zinc-50 dark:ring-zinc-900/50">
                                <span class="text-xs font-bold">2</span>
                            </div>
                        @endif
                        <div class="mt-3 text-center">
                            <div class="text-[9px] uppercase tracking-widest font-black {{ $order->isProcessing() || $order->isCompleted() ? 'text-emerald-600' : 'text-zinc-900 dark:text-white' }}">Complete Payment</div>
                        </div>
                    </div>

                    {{-- Connector Line 2 --}}
                    <div class="flex-1 h-[2px] {{ $order->isCompleted() ? 'bg-emerald-500' : 'bg-zinc-200 dark:bg-zinc-800' }} -mx-6 relative top-[-14px]"></div>

                    {{-- Step 3: Instant Access --}}
                    <div class="flex flex-col items-center flex-1">
                        @if($order->isCompleted())
                            <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white font-bold shadow-sm">
                                <flux:icon.check class="w-4 h-4" />
                            </div>
                        @else
                            <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 text-zinc-400 font-bold">
                                <span class="text-xs">3</span>
                            </div>
                        @endif
                        <div class="mt-3 text-center">
                            <div class="text-[9px] uppercase tracking-widest font-bold {{ $order->isCompleted() ? 'text-emerald-600' : 'text-zinc-400' }}">Instant Access</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/30 rounded-3xl p-6 mb-8 border border-zinc-100 dark:border-zinc-800/50"
                 x-data="{ 
                    expiresAt: '{{ $order->expires_at ? $order->expires_at->toIso8601String() : '' }}',
                    remaining: '',
                    isExpired: false,
                    init() {
                        if (!this.expiresAt) return;
                        const update = () => {
                            const now = new Date();
                            const expiration = new Date(this.expiresAt);
                            const diff = expiration - now;
                            
                            if (diff <= 0) {
                                this.remaining = 'Expired';
                                this.isExpired = true;
                                return;
                            }
                            
                            const hours = Math.floor(diff / 3600000);
                            const minutes = Math.floor((diff % 3600000) / 60000);
                            const seconds = Math.floor((diff % 60000) / 1000);
                            
                            this.remaining = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        };
                        update();
                        setInterval(update, 1000);
                    }
                 }">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left">
                        <span class="text-[10px] uppercase tracking-widest font-black text-zinc-500 mb-1 block">Total Amount</span>
                        <span class="text-3xl font-black tabular-nums text-emerald-600 dark:text-emerald-400 tracking-tight">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($order->expires_at && ($order->isPending() || $order->isPendingPayment()))
                        <div class="flex flex-col items-center md:items-end">
                            <span class="text-[10px] uppercase tracking-widest font-black text-zinc-500 mb-1 block">Time Remaining</span>
                            <div class="flex items-center gap-2 font-mono text-lg font-bold" :class="isExpired ? 'text-rose-600' : 'text-indigo-600 dark:text-indigo-400'">
                                <flux:icon.clock variant="mini" class="w-4 h-4" />
                                <span x-text="remaining">--:--:--</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center">
                @if(!$order->payment_method_id)
                    @livewire('checkout.payment-method-selector', ['order' => $order])
                @elseif($snapToken)
                    <button id="pay-button" class="inline-flex items-center gap-2 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors w-full justify-center">
                        <flux:icon.credit-card class="w-5 h-5" />
                        Pay Now with Midtrans
                    </button>
                @elseif($order->hasPaymentProof())
                    {{-- Payment proof already uploaded --}}
                    <div class="mt-8 border-t border-zinc-200 dark:border-zinc-800 pt-8 text-left">
                        <flux:heading size="lg" class="mb-4">Payment Proof Uploaded</flux:heading>
                        <flux:subheading class="mb-4">
                            Your payment proof has been uploaded and is being verified by our team.
                        </flux:subheading>
                        
                        <div class="mb-4">
                            <img src="{{ $order->payment_proof_url }}" alt="Payment Proof" class="max-w-md rounded-lg border border-zinc-200 dark:border-zinc-700">
                        </div>
                        
                        <flux:badge color="{{ $order->status_color }}" size="lg">
                            {{ $order->status_label }}
                        </flux:badge>
                        
                        <flux:subheading class="mt-2 text-xs">
                            Uploaded: {{ $order->payment_proof_uploaded_at->format('M d, Y g:i A') }}
                        </flux:subheading>
                    </div>
                @elseif($order->isPending() || $order->isPendingPayment())
                    {{-- Reactive Upload Component --}}
                    @livewire('checkout.payment-proof-upload', ['order' => $order])
                @endif
            </div>

            @if($order->payment_method_id)
                <div class="mt-8 p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border border-zinc-100 dark:border-zinc-800/50">
                    <div class="text-center">
                        <div class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">{{ $order->payment_method }}</div>
                        <div class="text-sm text-zinc-500 mb-6">
                            @if($order->paymentMethod?->type === 'bank_transfer')
                                Transfer to: <span class="font-mono font-bold text-base text-zinc-900 dark:text-white block mt-1">{{ $order->paymentMethod->account_number }}</span>
                                <div class="mt-1 text-[10px] font-black uppercase tracking-widest">a/n {{ $order->paymentMethod->account_name }}</div>
                            @elseif($order->paymentMethod?->type === 'qris')
                                @if($order->qris_dynamic)
                                    <div class="mb-4">
                                        <div class="inline-block p-4 bg-white rounded-3xl border border-zinc-200 shadow-sm">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($order->qris_dynamic) }}" 
                                                 class="w-64 h-64 mx-auto" 
                                                 alt="QRIS Code">
                                        </div>
                                        <p class="text-xs text-zinc-500 mt-4">Scan this code with any e-wallet or bank app</p>
                                    </div>
                                @endif
                            @endif
                            
                            {{-- Display payment instructions from database --}}
                            @if($order->paymentMethod?->instructions)
                                <div class="mt-6 p-4 text-xs font-medium text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-left shadow-sm">
                                    @if(is_array($order->paymentMethod->instructions))
                                        {!! nl2br(e(implode("\n", $order->paymentMethod->instructions))) !!}
                                    @else
                                        {!! nl2br(e($order->paymentMethod->instructions)) !!}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 text-center">
            @if($order->isPending() || $order->isPendingPayment())
                <flux:modal.trigger name="cancel-order">
                    <flux:button variant="ghost" size="sm" class="text-zinc-500 hover:text-red-500 transition-colors">
                        <flux:icon.x-mark variant="mini" class="mr-1" />
                        Batalkan Pesanan
                    </flux:button>
                </flux:modal.trigger>

                <flux:modal name="cancel-order" class="md:w-96">
                    <div class="text-center">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full dark:bg-red-900/30">
                            <flux:icon.exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-500" />
                        </div>
                        <flux:heading size="lg" class="mb-2">Batalkan Pesanan?</flux:heading>
                        <flux:subheading class="mb-6">
                            Apakah kamu yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan dan kamu harus memulai ulang jika berubah pikiran.
                        </flux:subheading>

                        <div class="flex flex-col gap-2">
                            <form action="{{ route('payment.cancel', $order) }}" method="POST">
                                @csrf
                                <flux:button type="submit" variant="danger" class="w-full">
                                    Ya, Batalkan Pesanan
                                </flux:button>
                            </form>
                            <flux:modal.close>
                                <flux:button variant="ghost" class="w-full">Tidak, Kembali</flux:button>
                            </flux:modal.close>
                        </div>
                    </div>
                </flux:modal>
            @endif
        </div>
    </div>
</flux:container>

@if($snapToken)
    {{-- Midtrans Snap Script --}}
    <script src="https://app.{{ config('services.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    window.location.href = '{{ route("checkout.success") }}?order_id={{ $order->id }}';
                },
                onPending: function(result){
                    window.location.href = '{{ route("checkout.pending") }}?order_id={{ $order->id }}';
                },
                onError: function(result){
                    window.location.href = '{{ route("checkout.failed") }}?order_id={{ $order->id }}';
                },
                onClose: function(){
                    console.log('Payment popup closed');
                }
            });
        };
    </script>
@endif
</div>
@endsection
