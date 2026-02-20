@php
    $layout = 'layouts.app';
    $profileRoute = 'profile';
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            $layout = 'layouts.admin';
            $profileRoute = 'admin.profile';
        } elseif (auth()->user()->isAuthor()) {
            $layout = 'layouts.author';
            $profileRoute = 'author.profile';
        }
    }
@endphp

@extends($layout)

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <flux:heading size="xl" class="text-center">Enable Two-Factor Authentication</flux:heading>
            <flux:subheading class="text-center mt-2">
                Secure your account with an authenticator app
            </flux:subheading>
        </div>

        <flux:card class="p-8">
            <div class="space-y-6">
                {{-- Step 1: Scan QR Code --}}
                <div>
                    <flux:heading size="lg">1. Scan QR Code</flux:heading>
                    <flux:subheading class="mt-2">
                        Use an authenticator app like Google Authenticator, Microsoft Authenticator, or Authy
                    </flux:subheading>
                    
                    <div class="mt-4 flex justify-center">
                        <div class="p-4 bg-white rounded-lg">
                            {!! $qrCode !!}
                        </div>
                    </div>
                </div>

                {{-- Step 2: Manual Entry --}}
                <div>
                    <flux:heading size="lg">2. Or Enter Manually</flux:heading>
                    <flux:subheading class="mt-2">
                        If you can't scan the QR code, enter this secret key manually:
                    </flux:subheading>
                    
                    <div class="mt-4 p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <code class="text-lg font-mono">{{ $secret }}</code>
                    </div>
                </div>

                {{-- Step 3: Verify Code --}}
                <div>
                    <flux:heading size="lg">3. Verify Code</flux:heading>
                    <flux:subheading class="mt-2">
                        Enter the 6-digit code from your authenticator app
                    </flux:subheading>

                    <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-4">
                        @csrf

                        <div class="space-y-4">
                            <flux:input
                                name="code"
                                type="text"
                                placeholder="000000"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required
                                autofocus
                                class="text-center text-2xl tracking-widest"
                            />

                            @error('code')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror

                            <div class="flex gap-3">
                                <flux:button type="submit" variant="primary" class="flex-1">
                                    Enable 2FA
                                </flux:button>
                                
                                <flux:button href="{{ route($profileRoute) }}" variant="ghost">
                                    Cancel
                                </flux:button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Security Notice --}}
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex gap-3">
                        <div class="text-blue-600 dark:text-blue-400">ℹ️</div>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Important:</strong> After enabling 2FA, you'll receive backup codes. 
                            Save them in a secure location. You'll need them if you lose access to your authenticator app.
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
</div>
@endsection
