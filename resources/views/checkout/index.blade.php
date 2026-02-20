@extends('layouts.app')

@section('title', 'Secure Checkout')

@section('content')
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('products.index') }}">Browse</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Checkout</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    @livewire('checkout.flow', ['product' => $product])
@endsection
