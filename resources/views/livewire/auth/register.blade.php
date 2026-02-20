<div class="flex min-h-screen">
    @section('title', 'Join NEXACODE')
    <div class="flex-1 flex justify-center items-center p-8 overflow-y-auto relative">
        {{-- Loading Overlay --}}
        <div wire:loading.delay wire:target="register" class="absolute inset-0 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-sm flex items-center justify-center animate-in fade-in duration-300">
            <div class="flex flex-col items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full border-2 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
                    <flux:icon name="rocket-launch" class="absolute inset-0 m-auto w-5 h-5 text-indigo-500" />
                </div>
                <flux:text size="sm" class="font-medium animate-pulse">Creating account...</flux:text>
            </div>
        </div>

        <div class="w-full max-w-sm space-y-8 py-12">
            <div class="flex justify-center opacity-80">
                <flux:brand href="/" name="NEXACODE" class="font-bold text-2xl">
                    <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                        <flux:icon name="rocket-launch" variant="micro" />
                    </x-slot>
                </flux:brand>
            </div>

            <div class="text-center space-y-2">
                <flux:heading size="xl">Create your account</flux:heading>
                <flux:subheading>Join the community of elite developers</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:button href="{{ route('social.redirect', 'google') }}" class="w-full h-11">
                    <x-slot name="icon">
                        <svg width="20" height="20" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.06 12.25C23.06 11.47 22.99 10.72 22.86 10H12.5V14.26H18.42C18.16 15.63 17.38 16.79 16.21 17.57V20.34H19.78C21.86 18.42 23.06 15.6 23.06 12.25Z" fill="#4285F4"/>
                            <path d="M12.4997 23C15.4697 23 17.9597 22.02 19.7797 20.34L16.2097 17.57C15.2297 18.23 13.9797 18.63 12.4997 18.63C9.63969 18.63 7.20969 16.7 6.33969 14.1H2.67969V16.94C4.48969 20.53 8.19969 23 12.4997 23Z" fill="#34A853"/>
                            <path d="M6.34 14.0899C6.12 13.4299 5.99 12.7299 5.99 11.9999C5.99 11.2699 6.12 10.5699 6.34 9.90995V7.06995H2.68C1.93 8.54995 1.5 10.2199 1.5 11.9999C1.5 13.7799 1.93 15.4499 2.68 16.9299L5.53 14.7099L6.34 14.0899Z" fill="#FBBC05"/>
                            <path d="M12.4997 5.38C14.1197 5.38 15.5597 5.94 16.7097 7.02L19.8597 3.87C17.9497 2.09 15.4697 1 12.4997 1C8.19969 1 4.48969 3.47 2.67969 7.07L6.33969 9.91C7.20969 7.31 9.63969 5.38 12.4997 5.38Z" fill="#EA4335"/>
                        </svg>
                    </x-slot>
                    Continue with Google
                </flux:button>

                <flux:button href="{{ route('social.redirect', 'github') }}" class="w-full h-11">
                    <x-slot name="icon">
                        <x-lucide-github class="w-5 h-5" />
                    </x-slot>
                    Continue with GitHub
                </flux:button>
            </div>

            <flux:separator text="or" />

            <form wire:submit="register" class="flex flex-col gap-6">
                <flux:field>
                    <flux:label>Full Name</flux:label>
                    <flux:input wire:model="name" type="text" placeholder="John Doe" required autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email address</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="email@example.com" required />
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
                        <flux:label>Password</flux:label>
                        <div class="relative">
                            <flux:input
                                x-bind:type="show ? 'text' : 'password'"
                                x-model="pwd"
                                placeholder="Min. 8 karakter, huruf besar & angka"
                                required
                            />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                                <flux:icon x-show="!show" name="eye" variant="micro" />
                                <flux:icon x-show="show" name="eye-slash" variant="micro" />
                            </button>
                        </div>

                        {{-- ── Strength Indicator ──────────────────────────── --}}
                        <div x-show="pwd.length > 0" x-transition class="mt-2 space-y-1.5">
                            {{-- 3 bars --}}
                            <div class="flex gap-1.5 h-1.5">
                                <div class="flex-1 rounded-full transition-all duration-300" :class="barColor(0)"></div>
                                <div class="flex-1 rounded-full transition-all duration-300" :class="barColor(1)"></div>
                                <div class="flex-1 rounded-full transition-all duration-300" :class="barColor(2)"></div>
                            </div>
                            {{-- Checklist + label --}}
                            <div class="flex justify-between items-center">
                                <div class="flex gap-3 text-[11px] text-zinc-400 dark:text-zinc-500">
                                    <span :class="pwd.length >= 8 ? 'text-emerald-500' : ''">
                                        <span x-text="pwd.length >= 8 ? '✓' : '○'"></span> 8 karakter
                                    </span>
                                    <span :class="/[A-Z]/.test(pwd) ? 'text-emerald-500' : ''">
                                        <span x-text="/[A-Z]/.test(pwd) ? '✓' : '○'"></span> Huruf besar
                                    </span>
                                    <span :class="/[0-9]/.test(pwd) ? 'text-emerald-500' : ''">
                                        <span x-text="/[0-9]/.test(pwd) ? '✓' : '○'"></span> Angka
                                    </span>
                                </div>
                                <span class="text-xs font-bold tracking-wide transition-colors duration-200" :class="labelColor" x-text="label"></span>
                            </div>
                        </div>

                        <flux:error name="password" />
                    </flux:field>

                    <flux:field class="mt-6">
                        <flux:label>Confirm Password</flux:label>
                        <flux:input wire:model="password_confirmation" x-bind:type="show ? 'text' : 'password'" placeholder="Ulangi password kamu" required />
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

                <flux:button type="submit" variant="primary" class="w-full h-11 font-bold shadow-lg">Create Account</flux:button>
            </form>

            <p class="text-xs text-center text-zinc-500 dark:text-zinc-600 px-4">
                By creating an account, you agree to our 
                <flux:link href="{{ route('terms') }}" class="text-zinc-700 dark:text-zinc-400 font-medium">Terms of Service</flux:link> 
                and 
                <flux:link href="{{ route('privacy') }}" class="text-zinc-700 dark:text-zinc-400 font-medium">Privacy Policy</flux:link>.
            </p>

            <flux:subheading class="text-center">
                Already have an account? <flux:link href="{{ route('login') }}" wire:navigate>Sign in</flux:link>
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
                    "Elevate your craft and join a community that values quality above all else. Your journey to elite status starts here."
                </div>

                <div class="flex gap-4 items-center">
                    <flux:avatar src="https://ui-avatars.com/api/?name=Elite+Author&background=0284c7&color=fff" size="xl" class="border-2 border-white/20" />

                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">Elite Community</div>
                        <div class="text-zinc-300">Join 10,000+ Verified Authors</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
