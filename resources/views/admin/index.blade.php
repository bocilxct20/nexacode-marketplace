@extends('layouts.admin')

@section('content')
<flux:heading size="xl" level="1">Good afternoon, {{ Auth::user()->name }}</flux:heading>

<flux:text class="mb-6 mt-2 text-base">Here's what's new today</flux:text>

<flux:separator variant="subtle" class="mb-8" />

<livewire:admin.admin-dashboard />
@endsection
