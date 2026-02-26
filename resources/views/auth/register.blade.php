@extends('layouts.auth')

@section('title', 'Join NEXACODE')

@section('testimonial', '"The marketplace standards here are incredibly high. Joining NEXACODE was the best decision for my software business."')

@section('content')
<div class="space-y-2 text-center">
    <flux:heading size="xl">Create your account</flux:heading>
    <flux:subheading>Bergabunglah dengan komunitas author elit di seluruh dunia</flux:subheading>
</div>

<div class="space-y-4">
    <flux:button href="{{ route('social.redirect', 'google') }}" class="w-full h-11">
        <x-slot name="icon">
            <svg width="20" height="20" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.06 12.25C23.06 11.47 22.99 10.72 22.86 10H12.5V14.26H18.42C18.16 15.63 17.38 16.79 16.21 17.57V20.34H19.78C21.86 18.42 23.06 15.6 23.06 12.25Z" fill="#4285F4"/>
                <path d="M12.4997 23C15.4697 23 17.9597 22.02 19.7797 20.34L16.2097 17.57C15.2297 18.23 13.9797 18.63 12.4997 18.63C9.63969 18.63 7.20969 16.7 6.33969 14.1H2.67969V16.94C4.48969 20.53 8.19969 23 12.4997 23Z" fill="#34A853"/>
                <path d="M6.34 14.0899C6.12 13.4299 5.99 12.7299 5.99 11.9999C5.99 11.2699 6.12 10.5699 6.34 9.90995V7.06995H2.68C1.93 8.54995 1.5 10.2199 1.5 11.9999C1.5 13.7799 1.93 15.4499 2.68 16.9299L5.53 14.7099L6.34 14.0899Z" fill="#FBBC05"/>
                <path d="M12.4997 5.38C14.1197 5.38 15.5597 5.94 16.7097 7.02L19.8597 3.87C17.9497 2.09 15.4697 1 12.4997 1C8.19969 1 4.48969 3.47 2.67969 7.07L6.33969 9.91C7.20969 7.31 9.63969 5.38 12.4997 5.38Z" fill="#EA4335"/>
            </svg>
        </x-slot>
        Continue with Google
    </flux:button>

    <flux:button href="{{ route('social.redirect', 'github') }}" class="w-full h-11">
        <x-slot name="icon">
            <x-lucide-github class="w-5 h-5" />
        </x-slot>
        Continue with GitHub
    </flux:button>
</div>

<flux:separator text="or" />

<form action="{{ route('register') }}" method="POST" class="flex flex-col gap-6">
    @csrf
    <flux:field>
        <flux:label>Full Name</flux:label>
        <flux:input name="name" type="text" :value="old('name')" placeholder="John Doe" required autofocus />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label>Email address</flux:label>
        <flux:input name="email" type="email" :value="old('email')" placeholder="email@example.com" required />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Password</flux:label>
        <flux:input name="password" type="password" placeholder="Minimum 8 characters" required />
        <flux:error name="password" />
    </flux:field>

    {{-- Honeypot field (hidden from users) --}}
    <div class="hidden">
        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
    </div>

    @if(isset($captcha_question))
        <flux:field>
            <flux:label>Security Check: What is {{ $captcha_question }}?</flux:label>
            <flux:input name="captcha_answer" type="number" placeholder="Enter answer" required />
            <flux:error name="captcha_answer" />
        </flux:field>
    @endif

    <flux:button type="submit" variant="primary" color="indigo" class="w-full h-11 rounded-xl">Create Account</flux:button>
</form>

<p class="text-xs text-center text-zinc-500 dark:text-zinc-600 px-4">
    Dengan membuat akun, berarti kamu menyetujui 
    <flux:link href="{{ route('terms') }}" class="text-zinc-700 dark:text-zinc-400 font-medium">Syarat & Ketentuan</flux:link> 
    serta 
    <flux:link href="{{ route('privacy') }}" class="text-zinc-700 dark:text-zinc-400 font-medium">Kebijakan Privasi</flux:link> kami.
</p>
@endsection
