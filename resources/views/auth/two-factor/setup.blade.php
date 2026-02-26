@extends('layouts.auth')

@section('title', 'Enable 2FA')

@section('side-testimonial')
    "Scanning a QR code takes seconds, but the protection it provides lasts forever. Secure your digital empire today."
@endsection

@section('side-author', 'Ahmad Dani Saputra')
@section('side-role', 'Creator of NEXACODE')

@section('content')
    <div class="w-full max-w-lg space-y-8">
        <div class="text-center">
            <flux:heading size="xl">2FA Activation</flux:heading>
            <flux:subheading class="mt-2">
                Secure your account with an authenticator app
            </flux:subheading>
        </div>

        <flux:card class="p-8">
            <div class="space-y-8">
                {{-- Step 1: Scan QR Code --}}
                <div>
                    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-400">1. Scan QR Code</flux:heading>
                    <flux:subheading class="mt-2">
                        Scan this code with Google Authenticator or Authy.
                    </flux:subheading>
                    
                    <div class="mt-4 flex justify-center">
                        <div class="p-4 bg-white rounded-2xl border-2 border-zinc-100 dark:border-zinc-800 shadow-xl">
                            {!! $qrCode !!}
                        </div>
                    </div>
                </div>

                {{-- Step 2: Manual Entry --}}
                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-400">2. Manual Backup</flux:heading>
                    <flux:subheading class="mt-2">
                        If scanning fails, enter this secret key manually:
                    </flux:subheading>
                    
                    <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <code class="text-lg font-mono font-bold text-zinc-900 dark:text-white">{{ $secret }}</code>
                    </div>
                </div>

                {{-- Step 3: Verify Code --}}
                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-400">3. Verification</flux:heading>
                    <flux:subheading class="mt-2">
                        Enter the 6-digit code to activate 2FA.
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
                                class="text-center text-3xl font-black tracking-[0.3em] h-16"
                            />

                            @error('code')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror

                            <div class="flex gap-3 pt-2">
                                <flux:button type="submit" variant="primary" class="flex-1 font-bold h-11 shadow-lg">
                                    Activate Security
                                </flux:button>
                                
                                <flux:button href="{{ auth()->user()->isAdmin() ? route('admin.profile') : (auth()->user()->isAuthor() ? route('author.profile') : route('profile')) }}" variant="ghost" class="h-11">
                                    Cancel
                                </flux:button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </flux:card>
    </div>
@endsection
