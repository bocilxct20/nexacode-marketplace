@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<main class="min-h-[70vh] flex items-center justify-center container">
    <div class="text-center space-y-8 max-w-lg mx-auto">
        <div class="relative inline-block">
            <h1 class="text-[12rem] font-black text-zinc-100 dark:text-zinc-900 leading-none">403</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <flux:heading size="xl" class="font-bold tracking-tight">Access Denied.</flux:heading>
            </div>
        </div>

        <div class="space-y-4">
            <flux:subheading size="lg" class="text-zinc-500 max-w-sm mx-auto">
                You don't have permission to access this area. If you believe this is a mistake, please contact administration.
            </flux:subheading>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <flux:button href="/" variant="primary" icon="home">Back to Homepage</flux:button>
            <flux:button href="{{ route('login') }}" variant="ghost" icon="user">Switch Account</flux:button>
        </div>
        
        <div class="pt-12">
            <flux:separator variant="subtle" />
            <p class="mt-8 text-xs text-zinc-400 uppercase tracking-[0.3em] font-bold">Error Code: NX-403</p>
        </div>
    </div>
</main>
@endsection
