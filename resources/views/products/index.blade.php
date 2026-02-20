@extends('layouts.app')

@section('title', 'Browse All Items')

@section('content')
    <div class="pt-4 pb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('products.index') }}" separator="slash">Products</flux:breadcrumbs.item>
            @if(isset($category))
                <flux:breadcrumbs.item separator="slash">{{ $category->name }}</flux:breadcrumbs.item>
            @endif
        </flux:breadcrumbs>
    </div>

    <div class="mb-8">
        <flux:heading size="xl" class="font-bold">{{ isset($category) ? 'Category: ' . $category->name : 'Explore Marketplace' }}</flux:heading>
        <flux:subheading size="lg" class="mt-2 text-zinc-500">
            {{ isset($category) ? 'Browsing items in ' . $category->name : 'Discover thousands of premium scripts, themes, and templates from our global community.' }}
        </flux:subheading>
    </div>

    <flux:separator class="mb-8" />

    @livewire('product-catalog', ['selectedCategory' => isset($category) ? $category->id : null])
@endsection
