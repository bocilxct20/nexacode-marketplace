@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 md:px-0">
            <flux:heading size="xl" class="mb-2">Profile Settings</flux:heading>
            <flux:subheading class="mb-8">Manage your account information and security.</flux:subheading>
            
            @livewire('account.profile-manager')
        </div>
    </div>
@endsection
