@section('title', 'Forgot Password')

<div class="space-y-8 relative">
    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="sendResetLink" class="absolute -inset-8 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300 rounded-[2rem]">
        <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
            </div>
            <flux:text size="sm" class="font-medium animate-pulse">Sending link...</flux:text>
        </div>
    </div>

    <div class="text-center space-y-2">
        <div class="font-black uppercase tracking-tight text-3xl text-zinc-900 dark:text-white">Forgot password?</div>
        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Enter your email and we'll send you a link to reset your password.</p>
    </div>

    <form wire:submit="sendResetLink" class="flex flex-col gap-6">
        <flux:field>
            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Email Address</flux:label>
            <flux:input wire:model="email" type="email" placeholder="email@example.com" class="h-12 rounded-2xl" required autofocus />
            <flux:error name="email" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-500 w-full h-12 text-[10px] font-black uppercase tracking-widest shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5 mt-2 text-white">
            Send Reset Link
        </flux:button>
    </form>
</div>
