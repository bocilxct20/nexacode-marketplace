<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - NEXACODE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="h-full antialiased font-sans text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950">
    <div class="flex min-h-screen">
        <div class="flex-1 flex justify-center items-center p-8">
            <div class="w-full max-w-sm space-y-8">
                <div class="flex justify-center opacity-80">
                    <flux:brand href="/" name="NEXACODE" class="font-bold text-2xl">
                        <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                            <flux:icon name="rocket-launch" variant="micro" />
                        </x-slot>
                    </flux:brand>
                </div>

                <div class="text-center space-y-2">
                    <flux:heading size="xl">Welcome back</flux:heading>
                    <flux:subheading>Log in to your author account</flux:subheading>
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

                <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-6">
                    @csrf
                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input name="email" type="email" :value="old('email')" placeholder="email@example.com" required autofocus />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <div class="mb-3 flex justify-between items-center">
                            <flux:label>Password</flux:label>
                            <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
                        </div>
                        <flux:input name="password" type="password" placeholder="Your password" required />
                    </flux:field>

                    <flux:checkbox name="remember" label="Remember me for 30 days" />

                    <flux:button type="submit" variant="primary" class="w-full h-11">Log in</flux:button>
                </form>

                <flux:subheading class="text-center">
                    First time around here? <flux:link href="{{ route('register') }}">Sign up for free</flux:link>
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
                        "NEXACODE has enabled me to design, build, and deliver premium digital assets faster than ever before. The community is unmatched."
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

    @fluxScripts
</body>
</html>
