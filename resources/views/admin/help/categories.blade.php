@extends('layouts.admin')

@section('header')
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Help Center</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Categories</flux:breadcrumbs.item>
    </flux:breadcrumbs>
@endsection

@section('content')
    <livewire:admin.help.category-manager />
@endsection
