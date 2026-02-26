@section('title', 'Login')

<div class="space-y-8 relative">
    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="login" class="absolute -inset-8 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300 rounded-[2rem]">
        <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
            </div>
            <flux:text size="sm" class="font-medium animate-pulse">Authenticating...</flux:text>
        </div>
    </div>

    <div class="text-center space-y-2">
        <div class="font-black uppercase tracking-tight text-3xl text-zinc-900 dark:text-white">Welcome back</div>
        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Log in to your account</p>
    </div>

    <div class="space-y-4">
        <flux:button href="{{ route('social.redirect', 'google') }}" variant="outline" class="w-full h-12 text-xs font-bold shadow-sm transition-transform hover:-translate-y-0.5 rounded-2xl">
            <x-slot name="icon">
                <x-lucide-chrome class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
            </x-slot>
            Continue with Google
        </flux:button>

        <flux:button href="{{ route('social.redirect', 'github') }}" variant="outline" class="w-full h-12 text-xs font-bold shadow-sm transition-transform hover:-translate-y-0.5 rounded-2xl">
            <x-slot name="icon">
                <x-lucide-github class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
            </x-slot>
            Continue with GitHub
        </flux:button>
    </div>

    <flux:separator text="or" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:field>
            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Email Address</flux:label>
            <flux:input wire:model="email" type="email" placeholder="email@example.com" class="h-12 rounded-2xl" required autofocus />
            <flux:error name="email" />
        </flux:field>

        <flux:field x-data="{ show: false }">
            <div class="mb-2 flex justify-between items-center">
                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400">Password</flux:label>
                <flux:link href="{{ route('password.request') }}" class="text-[10px] font-bold text-indigo-500 hover:text-indigo-600" wire:navigate>Lupa password?</flux:link>
            </div>

            <div class="relative">
                <flux:input wire:model="password" x-bind:type="show ? 'text' : 'password'" placeholder="Your password" class="h-12 rounded-2xl" required />
                
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-zinc-400 hover:text-indigo-500 transition-colors">
                    <flux:icon x-show="!show" name="eye" variant="micro" class="w-5 h-5" />
                    <flux:icon x-show="show" name="eye-slash" variant="micro" class="w-5 h-5" />
                </button>
            </div>

            <flux:error name="password" />
        </flux:field>

        <div class="flex items-center">
            <flux:checkbox wire:model="remember" label="Remember me for 30 days" class="text-xs font-medium" />
        </div>

        <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-500 w-full h-12 text-[10px] font-black uppercase tracking-widest shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5 mt-2">Log in</flux:button>
    </form>
</div>
@push('scripts')
<script src="https://openfpcdn.io/fingerprintjs/v4"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const fpPromise = FingerprintJS.load();
        const fp = await fpPromise;
        const result = await fp.get();

        const deviceId = result.visitorId;
        const meta = {
            device_name: result.components?.platform?.value || 'Unknown Device',
            browser: result.components?.vendor?.value || 'Unknown Browser',
            platform: result.components?.platform?.value || 'Unknown Platform'
        };

        @this.set('deviceId', deviceId);
        @this.set('deviceMeta', meta);
    });
</script>
@endpush
