@php
    $layout = 'layouts.app';
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            $layout = 'layouts.admin';
        } elseif (auth()->user()->isAuthor()) {
            $layout = 'layouts.author';
        }
    }
@endphp

@extends($layout)

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <flux:heading size="xl" class="text-center">Two-Factor Authentication</flux:heading>
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
                        <flux:description>
                            Enter the 6-digit code from your authenticator app or an 8-character backup code
                        </flux:description>

                        @error('code')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    <div>
                        <flux:checkbox name="remember" label="Remember this device for 30 days" />
                    </div>

                    <flux:button type="submit" variant="primary" class="w-full">
                        Verify
                    </flux:button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <flux:subheading>
                    Lost your authenticator device?
                </flux:subheading>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Use one of your backup codes to login, then disable and re-enable 2FA.
                </p>
            </div>
        </flux:card>
    </div>
</div>
@endsection
