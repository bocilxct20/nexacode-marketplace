@extends('layouts.app')

@section('content')
    <flux:container class="py-12">
        @livewire('customer.orders-manager')
    </flux:container>
@endsection
