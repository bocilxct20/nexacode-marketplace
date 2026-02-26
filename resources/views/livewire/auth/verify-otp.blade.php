@section('title', 'Verify Account')

<div class="space-y-8 relative">
    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="verify, resend" class="absolute -inset-8 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300 rounded-[2rem]">
        <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
            </div>
        </div>
    </div>

    <div class="text-center space-y-4">
        @if (session('registered'))
            <flux:callout variant="secondary" icon:left="information-circle" heading="Your account has been successfully created." class="mb-4" />
        @endif
        
        <div class="space-y-2">
            <div class="font-black uppercase tracking-tight text-3xl text-zinc-900 dark:text-white">Verify your account</div>
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                We've sent a 6-digit code to <span class="text-indigo-500">{{ Auth::user()->email }}</span>.
            </p>
        </div>
    </div>

    <form wire:submit="verify" class="space-y-8">
        <flux:field>
            <flux:otp wire:model="code" length="6" label="OTP Code" label:sr-only class="mx-auto" />
            <flux:error name="code" class="text-center mt-2" />
        </flux:field>

        <div class="space-y-4 pt-4">
            <flux:button variant="primary" type="submit" class="bg-indigo-600 hover:bg-indigo-500 w-full h-12 text-[10px] font-black uppercase tracking-widest shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5 text-white">
                Verify Account
            </flux:button>
            <button wire:click="resend" type="button" class="w-full text-center text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-indigo-500 transition-colors">
                Resend code
            </button>
        </div>
    </form>
</div>
