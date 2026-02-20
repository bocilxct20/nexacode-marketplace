@extends('layouts.app')

@section('title', 'Page Expired')

@section('content')
<main class="min-h-[70vh] flex items-center justify-center container">
    <div class="text-center space-y-8 max-w-lg mx-auto">
        <div class="relative inline-block">
            <h1 class="text-[12rem] font-black text-zinc-100 dark:text-zinc-900 leading-none">419</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <flux:heading size="xl" class="font-bold tracking-tight">Session Expired.</flux:heading>
            </div>
        </div>

        <div class="space-y-4">
            <flux:subheading size="lg" class="text-zinc-500 max-w-sm mx-auto">
                Your session has timed out due to inactivity. Please refresh the page and try again.
            </flux:subheading>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <flux:button onclick="window.location.reload()" variant="primary" icon="arrow-path">Refresh Page</flux:button>
            <flux:button href="/" variant="ghost" icon="home">Back to Homepage</flux:button>
        </div>
        
        <div class="pt-12">
            <flux:separator variant="subtle" />
            <p class="mt-8 text-xs text-zinc-400 uppercase tracking-[0.3em] font-bold">Error Code: NX-419</p>
        </div>
    </div>
</main>
@endsection
