<div class="flex min-h-screen">
    @section('title', 'Forgot Password')
    <div class="flex-1 flex justify-center items-center p-8 relative">
        {{-- Loading Overlay --}}
        <div wire:loading.delay wire:target="sendResetLink" class="absolute inset-0 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300">
            <div class="flex flex-col items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                    <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
                </div>
                <flux:text size="sm" class="font-medium animate-pulse">Sending link...</flux:text>
            </div>
        </div>

        <div class="w-full max-w-sm space-y-8">
            <div class="flex justify-center opacity-80 mb-6">
                <flux:brand href="/" name="NEXACODE" class="font-bold text-2xl">
                    <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                        <flux:icon name="rocket-launch" variant="micro" />
                    </x-slot>
                </flux:brand>
            </div>

            <div class="text-center space-y-2">
                <flux:heading size="xl">Forgot password?</flux:heading>
                <flux:subheading>Enter your email and we'll send you a link to reset your password.</flux:subheading>
            </div>

            <form wire:submit="sendResetLink" class="flex flex-col gap-6">
                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="email@example.com" required autofocus />
                    <flux:error name="email" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full h-11 font-bold shadow-lg">
                    Send Reset Link
                </flux:button>
            </form>

            <flux:subheading class="text-center">
                Remember your password? <flux:link href="{{ route('login') }}" wire:navigate>Back to login</flux:link>
            </flux:subheading>
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
                    "Security and ease of use are our top priorities. Resetting your password is fast and secure."
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
