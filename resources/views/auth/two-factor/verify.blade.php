@extends('layouts.auth')

@section('title', 'Two-Factor Authentication')

@section('side-testimonial')
    "Two-factor authentication adds an extra layer of security to your account. Your digital assets deserve the best protection."
@endsection

@section('side-author', 'Ahmad Dani Saputra')
@section('side-role', 'Creator of NEXACODE')

@section('content')
    <div class="w-full max-w-sm space-y-8">
        <div>
            <flux:heading size="xl" class="text-center">2FA Security</flux:heading>
            <flux:subheading class="text-center mt-2">
                Enter your authentication code to continue
            </flux:subheading>
        </div>

        <flux:card class="p-8">
            <form method="POST" action="{{ route('two-factor.verify.submit') }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <flux:label>Authentication Code</flux:label>
                        <flux:input
                            name="code"
                            type="text"
                            placeholder="000000"
                            maxlength="8"
                            required
                            autofocus
                            class="text-center text-2xl tracking-widest"
                        />
                        <flux:description class="text-center">
                            Enter the 6-digit code from your app or a backup code.
                        </flux:description>

                        @error('code')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    <div>
                        <flux:checkbox name="remember" label="Remember this device for 30 days" />
                    </div>

                    <flux:button type="submit" variant="primary" class="w-full font-bold shadow-lg">
                        Verify Identity
                    </flux:button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 text-center">
                <p class="text-xs text-zinc-500 dark:text-zinc-600 leading-relaxed font-medium">
                    Lost your device? Use a backup code to login, then disable and re-enable 2FA.
                </p>
            </div>
        </flux:card>
    </div>
@endsection
