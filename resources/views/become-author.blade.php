@extends('layouts.app')

@section('content')
    <flux:container class="py-12">
        <div class="pb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item separator="slash">Become an Author</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
        <flux:heading size="xl" class="mb-6">Become an Author</flux:heading>
        <flux:subheading class="mb-8">Join our community of elite creators and start selling your digital products.</flux:subheading>
        
        <flux:card class="max-w-2xl">
            <div class="space-y-6">
                <p>Apply to become an author on NEXACODE Marketplace and reach thousands of customers worldwide.</p>
                
                <flux:button variant="primary" href="{{ route('register') }}">
                    Start Your Application
                </flux:button>
            </div>
        </flux:card>
    </flux:container>
@endsection
