@extends('layouts.app')

@section('title', 'My Support Tickets')

@section('content')
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" separator="slash">Dashboard</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Support Center</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="mb-8 flex justify-between items-center">
        <div>
            <flux:heading size="xl" class="font-bold">Support Center</flux:heading>
            <flux:subheading size="lg" class="mt-2 text-zinc-500">
                Manage your support inquiries and get help from our authors.
            </flux:subheading>
        </div>
        
        <flux:button x-on:click="$dispatch('open-ticket-modal')" variant="primary" icon="plus">New Ticket</flux:button>
    </div>

    <flux:separator class="mb-8" />

    @livewire('support.ticket-list', ['role' => 'buyer'])

    @livewire('support.create-ticket')
@endsection
