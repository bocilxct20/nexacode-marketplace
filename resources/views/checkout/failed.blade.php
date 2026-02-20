@extends('layouts.app')

@section('content')
<flux:container class="py-12">
    <div class="max-w-2xl mx-auto text-center">
        <div class="mb-8">
            <div class="w-24 h-24 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <flux:icon.x-circle class="w-16 h-16 text-red-600 dark:text-red-400" />
            </div>
            
            <flux:heading size="xl" class="mb-4">Payment Failed</flux:heading>
            <flux:subheading class="mb-6">We couldn't process your payment. Please try again.</flux:subheading>
        </div>

        <flux:card class="mb-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400">Order ID</span>
                    <span class="font-semibold">#{{ request('order_id') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-zinc-600 dark:text-zinc-400">Status</span>
                    <flux:badge color="red">Failed</flux:badge>
                </div>
            </div>
        </flux:card>

        <div class="p-6 bg-red-50 dark:bg-red-900/20 rounded-lg mb-8">
            <div class="flex items-start gap-3">
                <flux:icon.exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                <div class="text-left text-sm text-red-900 dark:text-red-100">
                    <p class="font-semibold mb-2">Common reasons for payment failure:</p>
                    <ul class="list-disc list-inside space-y-1 text-red-800 dark:text-red-200">
                        <li>Insufficient balance in your account</li>
                        <li>Incorrect card details or expired card</li>
                        <li>Payment was declined by your bank</li>
                        <li>Network connection issues</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <flux:button href="{{ route('checkout.index', ['product_id' => request('product_id')]) }}" variant="primary">
                <flux:icon.arrow-path class="w-4 h-4" />
                Try Again
            </flux:button>
            <flux:button href="{{ route('support.index') }}" variant="ghost">
                <flux:icon.chat-bubble-left-right class="w-4 h-4" />
                Contact Support
            </flux:button>
        </div>
    </div>
</flux:container>
@endsection
