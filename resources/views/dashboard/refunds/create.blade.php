@extends('layouts.app')

@section('title', 'Request Refund')

@section('content')
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('dashboard.orders') }}" separator="slash">Orders</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Refund Request</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    @livewire('account.refund-request-form', ['orderId' => $order->id])
@endsection
