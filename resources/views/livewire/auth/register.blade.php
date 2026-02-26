@section('title', 'Join NEXACODE')

<div class="space-y-8 relative">
    {{-- Loading Overlay --}}
    <div wire:loading.delay wire:target="register" class="absolute -inset-8 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300 rounded-[2rem]">
        <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
            </div>
            <flux:text size="sm" class="font-medium animate-pulse">Creating account...</flux:text>
        </div>
    </div>

    <div class="text-center space-y-2">
        <div class="font-black uppercase tracking-tight text-3xl text-zinc-900 dark:text-white">Create Account</div>
        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Join the NexaCode community</p>
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

    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:field>
            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Full Name</flux:label>
            <flux:input wire:model="name" type="text" placeholder="John Doe" class="h-12 rounded-2xl" required autofocus />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Email Address</flux:label>
            <flux:input wire:model="email" type="email" placeholder="email@example.com" class="h-12 rounded-2xl" required />
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
                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Password</flux:label>
                <div class="relative">
                    <flux:input
                        x-bind:type="show ? 'text' : 'password'"
                        x-model="pwd"
                        placeholder="Min. 8 karakter, huruf besar & angka"
                        class="h-12 rounded-2xl"
                        required
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

            <flux:field class="mt-4">
                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Confirm Password</flux:label>
                <flux:input wire:model="password_confirmation" x-bind:type="show ? 'text' : 'password'" placeholder="Ulangi password kamu" class="h-12 rounded-2xl" required />
            </flux:field>
        </div>

        {{-- Honeypot field (hidden from users) --}}
        <div class="hidden">
            <input type="text" wire:model="website_url" tabindex="-1" autocomplete="off">
        </div>

        {{-- ── Image Captcha ─────────────────────────────────────────── --}}
        <flux:field
            x-data="{
                refreshing: false,
                captchaKey: '{{ $captchaKey }}',
                imageUrl() {
                    return '{{ route('captcha.image') }}?key=' + this.captchaKey + '&t=' + Date.now();
                },
                reload(newKey) {
                    if (newKey) this.captchaKey = newKey;
                    this.refreshing = true;
                    const img = document.getElementById('captcha-img');
                    img.src = this.imageUrl();
                    img.onload  = () => this.refreshing = false;
                    img.onerror = () => this.refreshing = false;
                }
            }"
            x-on:captcha-refresh.window="reload($event.detail.key)"
            x-init="document.getElementById('captcha-img').src = imageUrl()"
        >
            <flux:label class="flex items-center gap-1">
                <flux:icon name="shield-check" variant="micro" class="text-indigo-500" />
                Security Check — ketik 6 karakter di gambar
            </flux:label>

            {{-- Captcha image + refresh button --}}
            <div class="flex items-center gap-3 mb-2">
                <div class="relative rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-sm bg-white">
                    <img
                        id="captcha-img"
                        src="{{ route('captcha.image') }}?key={{ $captchaKey }}"
                        alt="Captcha"
                        class="h-14 block select-none"
                        draggable="false"
                    />
                    {{-- Shimmer overlay while refreshing --}}
                    <div
                        x-show="refreshing"
                        class="absolute inset-0 bg-zinc-100 dark:bg-zinc-800 animate-pulse flex items-center justify-center"
                    >
                        <div class="w-5 h-5 border-2 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin"></div>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="refreshCaptcha"
                    wire:loading.attr="disabled"
                    x-bind:class="refreshing ? 'opacity-50 cursor-wait' : 'hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-950'"
                    class="p-2 rounded-lg text-zinc-400 dark:text-zinc-500 transition-all duration-200"
                    title="Refresh captcha"
                >
                    <flux:icon
                        name="arrow-path"
                        variant="micro"
                        class="w-5 h-5"
                        x-bind:class="refreshing ? 'animate-spin' : ''"
                    />
                </button>

                <flux:text size="xs" class="text-zinc-400 dark:text-zinc-600 leading-tight max-w-[90px]">
                    Tidak bisa dibaca? Klik 🔄
                </flux:text>
            </div>

            <flux:input
                wire:model="captcha_input"
                type="text"
                placeholder="Contoh: A3BK9X"
                maxlength="6"
                autocomplete="off"
                spellcheck="false"
                class="tracking-[0.4em] font-mono uppercase"
                required
            />
            <flux:error name="captcha_input" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-500 w-full h-12 text-[10px] font-black uppercase tracking-widest shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5 mt-4 text-white">Create Account</flux:button>
    </form>

    <p class="text-[10px] text-center text-zinc-500 dark:text-zinc-600 px-4 leading-relaxed uppercase tracking-widest font-bold">
        By creating an account, you agree to our 
        <flux:link href="{{ route('terms') }}" class="text-indigo-500">Terms</flux:link> 
        and 
        <flux:link href="{{ route('privacy') }}" class="text-indigo-500">Privacy</flux:link>.
    </p>
</div>
