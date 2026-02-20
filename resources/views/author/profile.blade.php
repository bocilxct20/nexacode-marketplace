@extends('layouts.author')

@section('content')
<div class="py-8">
    <flux:heading size="xl">Profile Settings</flux:heading>
    <flux:subheading>Manage your author account settings and preferences.</flux:subheading>
    
    <flux:separator variant="subtle" class="my-8" />

    @livewire('account.profile-manager')
</div>
@endsection
