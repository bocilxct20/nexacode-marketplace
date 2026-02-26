@extends('layouts.app')

@section('title', 'Browse All Items')

@section('content')
    <div class="pt-4 pb-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('products.index') }}" separator="slash">Marketplace</flux:breadcrumbs.item>
            @if(isset($category))
                <flux:breadcrumbs.item separator="slash">{{ $category->name }}</flux:breadcrumbs.item>
            @endif
        </flux:breadcrumbs>
    </div>

    @livewire('product-catalog', ['selectedCategory' => isset($category) ? $category->id : null])
@endsection
