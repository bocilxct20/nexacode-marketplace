@extends('layouts.app')

@section('title', 'Open New Support Ticket')

@section('content')
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('support.index') }}">Support</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>New Ticket</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    @livewire('support.create-ticket')
@endsection
