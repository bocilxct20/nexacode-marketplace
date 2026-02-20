<div class="flex min-h-screen bg-white dark:bg-zinc-950">
    <div class="flex-1 flex justify-center items-center p-8">
        <div class="w-full max-w-sm space-y-8">
            <div class="flex justify-center opacity-80">
                <flux:brand href="/" name="NEXACODE" class="font-bold text-2xl">
                    <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                        <flux:icon name="rocket-launch" variant="micro" />
                    </x-slot>
                </flux:brand>
            </div>

            <div class="text-center space-y-4">
                @if (session('registered'))
                    <flux:callout variant="secondary" icon:left="information-circle" heading="Your account has been successfully created." class="mb-4" />
                @endif
                
                <flux:heading size="xl">Verify your account</flux:heading>
                <flux:subheading>
                    We've sent a 6-digit code to <span class="font-bold text-zinc-900 dark:text-white">{{ Auth::user()->email }}</span>.
                </flux:subheading>
            </div>

            <div class="relative">
                {{-- Loading Overlay --}}
                <div wire:loading.delay wire:target="verify, resend" class="absolute inset-0 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300 rounded-xl">
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                            <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-4 h-4 text-indigo-500" />
                        </div>
                    </div>
                </div>

                <form wire:submit="verify" class="space-y-8">
                <flux:field>
                    <flux:otp wire:model="code" length="6" label="OTP Code" label:sr-only class="mx-auto" />
                    <flux:error name="code" class="text-center mt-2" />
                </flux:field>

                <div class="space-y-4">
                    <flux:button variant="primary" type="submit" class="w-full h-12 font-bold shadow-lg">
                        Verify Account
                    </flux:button>
                    <flux:button wire:click="resend" variant="ghost" class="w-full h-12 font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                        Resend code
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="flex-1 p-4 max-lg:hidden">
        <div class="text-white relative rounded-2xl h-full w-full bg-aurora flex flex-col items-start justify-end p-16 overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            
            <div class="relative z-10 w-full max-w-2xl">
                <div class="flex gap-1 mb-6 text-amber-400">
                    <flux:icon.star variant="solid" size="sm" />
                    <flux:icon.star variant="solid" size="sm" />
                    <flux:icon.star variant="solid" size="sm" />
                    <flux:icon.star variant="solid" size="sm" />
                    <flux:icon.star variant="solid" size="sm" />
                </div>

                <div class="mb-8 italic font-medium text-3xl xl:text-4xl leading-tight">
                    "Secure, fast, and incredibly well-designed. NEXACODE sets a new bar for digital marketplaces."
                </div>

                <div class="flex gap-4 items-center">
                    <x-nexacode-brand-n class="size-16" />

                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">Ahmad Dani Saputra</div>
                        <div class="text-zinc-300">Creator of NEXACODE</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
