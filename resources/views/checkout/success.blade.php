@extends('layouts.app')

@section('content')
<flux:container class="py-12">
    <div class="max-w-2xl mx-auto text-center">
        <div class="mb-8">
            <div class="w-24 h-24 bg-emerald-100 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <flux:icon.check-circle class="w-16 h-16 text-emerald-600 dark:text-emerald-400" />
            </div>
            
            <flux:heading size="xl" class="mb-4">Payment Successful!</flux:heading>
            <flux:subheading class="mb-6">Thank you for your purchase. Your order has been confirmed.</flux:subheading>
        </div>

        <flux:card class="mb-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400">Order ID</span>
                    <span class="font-semibold">#{{ request('order_id') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-zinc-600 dark:text-zinc-400">Status</span>
                    <flux:badge color="emerald">Completed</flux:badge>
                </div>
            </div>
        </flux:card>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <flux:button href="{{ route('downloads.index') }}" variant="primary">
                <flux:icon.arrow-down-tray class="w-4 h-4" />
                Download Your Products
            </flux:button>
            <flux:button href="{{ route('purchases.index') }}" variant="ghost">
                <flux:icon.shopping-bag class="w-4 h-4" />
                View Orders
            </flux:button>
        </div>

        <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex items-start gap-3">
                <flux:icon.envelope class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                <div class="text-left text-sm text-blue-900 dark:text-blue-100">
                    <p class="font-semibold mb-1">Confirmation Email Sent</p>
                    <p class="text-blue-800 dark:text-blue-200">
                        We've sent a confirmation email with your order details and download links to your registered email address.
                    </p>
                </div>
            </div>
        </div>
    </div>
</flux:container>
@endsection
