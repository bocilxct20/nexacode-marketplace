@extends('layouts.app')

@section('title', $user->name . ' - NEXACODE Author')

@section('content')
<div class="space-y-16 py-12">
    <flux:container>
        {{-- Author Header/Banner Card --}}
        <flux:card class="p-0 overflow-hidden shadow-2xl relative" style="{{ ($user->isAdmin() || $user->isElite()) && $user->brand_color ? '--brand-primary: ' . $user->brand_color : '' }}">
            {{-- Banner Background --}}
            {{-- Minimalist Modern Banner --}}
            <div class="h-64 relative overflow-hidden bg-zinc-900 border-b border-white/10">
                <div class="absolute inset-0 bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-950"></div>
                @if($user->cover_image)
                    <img src="{{ asset('storage/' . $user->cover_image) }}" class="w-full h-full object-cover opacity-30">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent"></div>
            </div>
            
            <div class="px-6 md:px-12 pb-12 relative flex flex-col lg:flex-row gap-8">
                {{-- Avatar & Identity --}}
                <div class="-mt-16 flex flex-col items-center lg:items-start space-y-4">
                    <x-profile-preview :user="$user">
                    <div class="group relative">
                        <x-user-avatar 
                            :user="$user" 
                            size="xl"
                            thickness="8"
                            class="rounded-[2.5rem] shadow-2xl transition-transform group-hover:scale-105" 
                        />
                    </div>
                    </x-profile-preview>

                    <div class="flex flex-col items-center lg:items-start">
                        <div class="flex items-center gap-3">
                            <flux:heading size="xl" class="font-black text-zinc-900 dark:text-white tracking-tight">{{ $user->name }}</flux:heading>
                            <x-community-badge :user="$user" size="md" />
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <flux:text size="sm">@ {{ $user->username ?? $user->id }}</flux:text>
                            <flux:separator vertical />
                            <flux:text size="sm" class="text-zinc-400">Member since {{ $memberSince }}</flux:text>
                        </div>

                        @if($user->isElite() && $user->storefront_message)
                            <div class="mt-4 p-4 bg-zinc-50 dark:bg-white/10 backdrop-blur rounded-2xl border border-zinc-200 dark:border-white/20">
                                <flux:heading size="lg" class="text-zinc-900 dark:text-white !font-black italic tracking-tight">{{ $user->storefront_message }}</flux:heading>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info & Actions --}}
                <div class="flex-1 pt-4 lg:pt-8 flex flex-col space-y-8">
                    {{-- Bio + Social + Follow Button --}}
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                        <flux:text size="lg" class="max-w-3xl leading-relaxed flex-1">
                            {{ $user->bio ?? 'Author ini sedang sibuk menciptakan konten luar biasa untuk komunitas!' }}
                        </flux:text>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($user->website_url || $user->twitter_url || $user->github_url)
                                <flux:button.group>
                                    @if($user->website_url)
                                        <flux:button variant="ghost" square href="{{ $user->website_url }}" target="_blank" icon="globe-alt" />
                                    @endif
                                    @if($user->twitter_url)
                                        <flux:button variant="ghost" square href="{{ $user->twitter_url }}" target="_blank">
                                            <x-lucide-twitter class="w-4 h-4" />
                                        </flux:button>
                                    @endif
                                    @if($user->github_url)
                                        <flux:button variant="ghost" square href="{{ $user->github_url }}" target="_blank">
                                            <x-lucide-github class="w-4 h-4" />
                                        </flux:button>
                                    @endif
                                </flux:button.group>
                            @endif
                            @auth
                                @if(auth()->id() !== $user->id)
                                    @livewire('follow-button', ['authorId' => $user->id])
                                @endif
                            @endauth
                        </div>
                    </div>



                    {{-- Metric Bar (Minimalist - Structured) --}}
                    <div class="flex flex-wrap items-center justify-between bg-zinc-50/50 dark:bg-white/5 rounded-2xl p-6 border border-zinc-100 dark:border-zinc-800/50 shadow-sm">
                        @if(!$user->isAdmin())
                            <div class="flex flex-col items-center flex-1 min-w-[100px]">
                                <flux:icon.banknotes variant="mini" class="text-emerald-500 w-4 h-4 mb-2" />
                                <span class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums leading-none">{{ number_format($totalSales) }}</span>
                                <span class="uppercase tracking-widest text-[9px] font-black text-zinc-400 mt-2">Total Sales</span>
                            </div>

                            <div class="hidden md:block w-px h-10 bg-zinc-200 dark:bg-zinc-800"></div>

                            <div class="flex flex-col items-center flex-1 min-w-[100px]">
                                <flux:icon.star variant="mini" class="text-amber-500 w-4 h-4 mb-2" />
                                <span class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums leading-none">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</span>
                                <span class="uppercase tracking-widest text-[9px] font-black text-zinc-400 mt-2">Avg Rating</span>
                            </div>

                            <div class="hidden md:block w-px h-10 bg-zinc-200 dark:bg-zinc-800"></div>
                        @else
                            {{-- Admin Perspective: System Authority Metrics --}}
                            <div class="flex flex-col items-center flex-1 min-w-[100px]">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <flux:icon.cpu-chip variant="mini" class="text-rose-500 w-4 h-4" />
                                    <div class="size-1.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]"></div>
                                </div>
                                <span class="text-2xl font-black text-rose-600 dark:text-rose-400 leading-none tracking-tighter">14</span>
                                <span class="uppercase tracking-widest text-[9px] font-black text-zinc-400 mt-2">Access Level</span>
                            </div>

                            <div class="hidden md:block w-px h-10 bg-zinc-200 dark:bg-zinc-800"></div>

                            <div class="flex flex-col items-center flex-1 min-w-[100px]">
                                <flux:icon.eye variant="mini" class="text-sky-500 w-4 h-4 mb-2" />
                                <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none tabular-nums">1</span>
                                <span class="uppercase tracking-widest text-[9px] font-black text-zinc-400 mt-2">Observant Users</span>
                            </div>

                            <div class="hidden md:block w-px h-10 bg-zinc-200 dark:bg-zinc-800"></div>
                        @endif



                        <a href="{{ route('page.author-ranking') }}" class="flex flex-col items-center flex-1 min-w-[100px] group hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors rounded-xl p-2">
                            <flux:icon.trophy variant="mini" class="text-orange-500 w-4 h-4 mb-2" />
                            <span class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums leading-none">#{{ $user->getGlobalRankPosition() }}</span>
                            <span class="uppercase tracking-widest text-[9px] font-black text-zinc-400 mt-2">Global Rank</span>
                        </a>

                        @if(!$user->isAdmin())
                            <div class="hidden lg:block w-px h-10 bg-zinc-200 dark:bg-zinc-800"></div>

                            <div class="flex flex-col items-center flex-1 min-w-[100px]">
                                <flux:icon.academic-cap variant="mini" class="text-purple-500 w-4 h-4 mb-2" />
                                <span class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums leading-none">Lv.{{ $user->level ?? 1 }}</span>
                                <span class="uppercase tracking-widest text-[9px] font-black text-zinc-400 mt-2">Mastery</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </flux:card>
    </flux:container>



    {{-- Author Products --}}
    <div class="space-y-10">
        <flux:container>
            <flux:heading size="lg" class="uppercase tracking-widest font-black text-zinc-400 mb-8">Author Storefront</flux:heading>
        </flux:container>
        @livewire('author.storefront', ['authorId' => $user->id])
    </div>
</div>
@endsection
