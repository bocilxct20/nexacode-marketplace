@extends('layouts.author')

@section('title', 'Author Support Tickets')

@section('content')
    <div class="mb-8">
        <flux:heading size="xl" class="font-bold">Customer Support</flux:heading>
        <flux:subheading size="lg" class="mt-2 text-zinc-500">
            Manage support inquiries from your customers.
        </flux:subheading>
    </div>

    <flux:separator class="mb-8" />

    @livewire('support.ticket-list', ['role' => 'author'])
@endsection
