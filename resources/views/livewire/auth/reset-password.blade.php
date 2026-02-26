@section('title', 'Reset Password')

@section('testimonial')
    "A strong password is your first line of defense. Use a mix of letters, numbers, and symbols for maximum security."
@endsection

<div class="space-y-8 relative">
    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="resetPassword" class="absolute -inset-8 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300 rounded-[2rem]">
        <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
            </div>
            <flux:text size="sm" class="font-medium animate-pulse">Resetting password...</flux:text>
        </div>
    </div>

    <div class="text-center space-y-2">
        <div class="font-black uppercase tracking-tight text-3xl text-zinc-900 dark:text-white">Reset Password</div>
        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Choose a strong and secure new password.</p>
    </div>

    <form wire:submit="resetPassword" class="flex flex-col gap-6">
        <input type="hidden" wire:model="token">

        <flux:field>
            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Email Address</flux:label>
            <flux:input wire:model="email" type="email" placeholder="email@example.com" class="h-12 rounded-2xl" required readonly />
            <flux:error name="email" />
        </flux:field>

        <div
            x-data="{
                show: false,
                pwd: '',
                get score() {
                    let s = 0;
                    if (this.pwd.length >= 8)        s++;
                    if (/[A-Z]/.test(this.pwd))      s++;
                    if (/[0-9]/.test(this.pwd))      s++;
                    return s;
                },
                get label() {
                    if (this.pwd.length === 0) return '';
                    if (this.score === 3) return 'Kuat';
                    if (this.score === 2) return 'Sedang';
                    return 'Lemah';
                },
                get labelColor() {
                    if (this.score === 3) return 'text-emerald-500';
                    if (this.score === 2) return 'text-amber-500';
                    return 'text-red-500';
                },
                barColor(idx) {
                    if (this.pwd.length === 0) return 'bg-zinc-200 dark:bg-zinc-700';
                    if (idx < this.score) {
                        if (this.score === 3) return 'bg-emerald-500';
                        if (this.score === 2) return 'bg-amber-500';
                        return 'bg-red-500';
                    }
                    return 'bg-zinc-200 dark:bg-zinc-700';
                }
            }"
            x-init="$watch('pwd', v => $wire.set('password', v))"
        >
            <flux:field>
                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">New Password</flux:label>
                <div class="relative">
                    <flux:input
                        x-bind:type="show ? 'text' : 'password'"
                        x-model="pwd"
                        placeholder="Min. 8 karakter, huruf besar & angka"
                        class="h-12 rounded-2xl"
                        required
                        autofocus
                    />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-zinc-400 hover:text-indigo-500 transition-colors">
                        <flux:icon x-show="!show" name="eye" variant="micro" class="w-5 h-5"/>
                        <flux:icon x-show="show" name="eye-slash" variant="micro" class="w-5 h-5" />
                    </button>
                </div>

                {{-- ── Strength Indicator ──────────────────────────── --}}
                <div x-show="pwd.length > 0" x-transition class="mt-2 space-y-2">
                    {{-- 3 bars --}}
                    <div class="flex gap-1.5 h-1.5 opacity-80">
                        <div class="flex-1 rounded-full transition-all duration-300" :class="barColor(0)"></div>
                        <div class="flex-1 rounded-full transition-all duration-300" :class="barColor(1)"></div>
                        <div class="flex-1 rounded-full transition-all duration-300" :class="barColor(2)"></div>
                    </div>
                    {{-- Checklist + label --}}
                    <div class="flex justify-between items-center">
                        <div class="flex gap-3 text-[10px] uppercase font-bold tracking-widest text-zinc-400 dark:text-zinc-500">
                            <span :class="pwd.length >= 8 ? 'text-emerald-500' : ''">
                                <span x-text="pwd.length >= 8 ? '✓' : '○'"></span> 8 char
                            </span>
                            <span :class="/[A-Z]/.test(pwd) ? 'text-emerald-500' : ''">
                                <span x-text="/[A-Z]/.test(pwd) ? '✓' : '○'"></span> 1 big
                            </span>
                            <span :class="/[0-9]/.test(pwd) ? 'text-emerald-500' : ''">
                                <span x-text="/[0-9]/.test(pwd) ? '✓' : '○'"></span> 1 num
                            </span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest transition-colors duration-200" :class="labelColor" x-text="label"></span>
                    </div>
                </div>

                <flux:error name="password" />
            </flux:field>

            <flux:field class="mt-6">
                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Confirm Password</flux:label>
                <flux:input wire:model="password_confirmation" x-bind:type="show ? 'text' : 'password'" placeholder="Ulangi password baru kamu" class="h-12 rounded-2xl" required />
            </flux:field>
        </div>

        <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-500 w-full h-12 text-[10px] font-black uppercase tracking-widest shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5 mt-2 text-white">
            Reset Password
        </flux:button>
    </form>
</div>
