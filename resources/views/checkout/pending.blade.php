@extends('layouts.app')

@section('content')
<flux:container class="py-12">
    <div class="max-w-2xl mx-auto text-center">
        <div class="mb-8">
            <div class="w-24 h-24 bg-amber-100 dark:bg-amber-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <flux:icon.clock class="w-16 h-16 text-amber-600 dark:text-amber-400" />
            </div>
            
            <flux:heading size="xl" class="mb-4">Payment Pending</flux:heading>
            <flux:subheading class="mb-6">Your payment is being processed. This may take a few minutes.</flux:subheading>
        </div>

        <flux:card class="mb-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400">Order ID</span>
                    <span class="font-semibold">#{{ request('order_id') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-zinc-600 dark:text-zinc-400">Status</span>
                    <flux:badge color="amber">Pending</flux:badge>
                </div>
            </div>
        </flux:card>

        <div class="p-6 bg-amber-50 dark:bg-amber-900/20 rounded-lg mb-8">
            <div class="flex items-start gap-3">
                <flux:icon.information-circle class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                <div class="text-left text-sm text-amber-900 dark:text-amber-100">
                    <p class="font-semibold mb-2">What happens next?</p>
                    <ul class="list-disc list-inside space-y-1 text-amber-800 dark:text-amber-200">
                        <li>Complete your payment using the instructions provided</li>
                        <li>We'll send you an email once payment is confirmed</li>
                        <li>Your download links will be available immediately after confirmation</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <flux:button href="{{ route('purchases.index') }}" variant="primary">
                <flux:icon.shopping-bag class="w-4 h-4" />
                View My Orders
            </flux:button>
            <flux:button href="{{ route('home') }}" variant="ghost">
                <flux:icon.home class="w-4 h-4" />
                Back to Homepage
            </flux:button>
        </div>
    </div>
</flux:container>
@endsection
