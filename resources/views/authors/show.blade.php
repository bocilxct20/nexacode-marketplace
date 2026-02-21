@extends('layouts.app')

@section('title', $user->name . ' - NEXACODE Author')

@section('content')
<div class="space-y-16 py-12">
    <flux:container>
        {{-- Author Header/Banner Card --}}
        <flux:card class="p-0 overflow-hidden shadow-2xl relative" style="{{ $user->isElite() && $user->brand_color ? '--brand-primary: ' . $user->brand_color : '' }}">
            {{-- Banner Background --}}
            @if($user->isElite() && $user->cover_image)
                <div class="h-64 bg-zinc-900 relative">
                    <img src="{{ asset('storage/' . $user->cover_image) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
            @else
                <div class="h-64 md:h-72 bg-indigo-950 relative overflow-hidden">
                    {{-- Deep Space Base --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-zinc-900 to-purple-900"></div>
                    
                    {{-- The "Bulet-bulet" Aurora (Vibrant & Animated) --}}
                    <div class="absolute inset-0 overflow-hidden pointer-events-none">
                        {{-- Purple Bulet --}}
                        <div class="absolute -top-20 -left-20 w-80 h-80 bg-purple-500/40 blur-[60px] rounded-full animate-aurora-1"></div>
                        
                        {{-- Emerald Bulet --}}
                        <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-emerald-500/40 blur-[70px] rounded-full animate-aurora-2"></div>
                        
                        {{-- Indigo/Cyan Bulet --}}
                        <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-cyan-400/30 blur-[50px] rounded-full animate-aurora-3"></div>
                        
                        {{-- Pink Ghost --}}
                        <div class="absolute bottom-1/4 left-1/3 w-48 h-48 bg-pink-500/20 blur-[60px] rounded-full animate-aurora-slow-move"></div>
                    </div>

                    {{-- Glassy Overlay for Shine --}}
                    <div class="absolute inset-0 bg-white/5 backdrop-blur-[2px]"></div>
                    
                    {{-- Bottom Gradient for Transition --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/40 via-transparent to-transparent"></div>
                </div>

                <style>
                    @keyframes aurora-1 {
                        0%, 100% { transform: translate(0, 0) scale(1.2) rotate(0deg); filter: hue-rotate(0deg); }
                        50% { transform: translate(25%, 20%) scale(1) rotate(15deg); filter: hue-rotate(30deg); }
                    }
                    @keyframes aurora-2 {
                        0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); filter: hue-rotate(0deg); }
                        50% { transform: translate(-35%, -25%) scale(1.4) rotate(-20deg); filter: hue-rotate(-45deg); }
                    }
                    @keyframes aurora-3 {
                        0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; filter: hue-rotate(0deg); }
                        50% { transform: translate(20%, -15%) scale(1.3); opacity: 0.6; filter: hue-rotate(60deg); }
                    }
                    @keyframes aurora-slow-move {
                        0%, 100% { transform: translate(0, 0) rotate(0deg); }
                        50% { transform: translate(-25%, 45%) rotate(45deg); }
                    }
                    .animate-aurora-1 { animation: aurora-1 15s ease-in-out infinite; }
                    .animate-aurora-2 { animation: aurora-2 18s ease-in-out infinite; }
                    .animate-aurora-3 { animation: aurora-3 12s ease-in-out infinite; }
                    .animate-aurora-slow-move { animation: aurora-slow-move 25s ease-in-out infinite; }
                </style>
            @endif
            
            <div class="px-6 md:px-12 pb-12 relative flex flex-col lg:flex-row gap-8">
                {{-- Avatar & Identity --}}
                <div class="-mt-16 flex flex-col items-center lg:items-start space-y-4">
                    <x-profile-preview :user="$user">
                        <flux:avatar 
                            :src="$user->avatar" 
                            :initials="$user->initials" 
                            size="xl"
                            class="rounded-[2.5rem] border-8 border-white dark:border-zinc-900 shadow-2xl transition-transform group-hover:scale-105" 
                        />
                    </x-profile-preview>

                    <div class="flex flex-col items-center lg:items-start">
                        <div class="flex items-center gap-3">
                            <flux:heading size="xl" class="font-black !text-white tracking-tight">{{ $user->name }}</flux:heading>
                            @if($user->isElite())
                                <flux:badge size="sm" color="amber" class="bg-amber-500 text-white border-none shadow-lg shadow-amber-500/30 uppercase font-black text-[10px] tracking-widest px-3 py-1">Verified Elite Partner</flux:badge>
                            @elseif($user->isPro())
                                <flux:badge size="sm" color="indigo" class="bg-indigo-600 text-white border-none shadow-lg shadow-indigo-600/30 uppercase font-black text-[10px] tracking-widest px-3 py-1">Pro Developer</flux:badge>
                            @endif

                            @if($user->isElite())
                                <div class="relative group/elite">
                                    <flux:icon.check-badge variant="solid" class="w-6 h-6 text-amber-500 animate-pulse" />
                                    <div class="absolute inset-0 bg-amber-400 blur-md opacity-40 animate-pulse rounded-full"></div>
                                </div>
                            @elseif($user->isPro())
                                <div class="relative group/pro">
                                    <flux:icon.check-badge variant="solid" class="w-6 h-6 text-indigo-500" />
                                    <div class="absolute inset-0 bg-indigo-400 blur-sm opacity-20 rounded-full"></div>
                                </div>
                            @else
                                <flux:icon.check-badge variant="solid" class="w-6 h-6 text-zinc-400" />
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <flux:text size="sm">@ {{ $user->username ?? $user->id }}</flux:text>
                            <flux:separator vertical />
                            <flux:badge :color="$user->isElite() ? 'amber' : ($user->isPro() ? 'indigo' : 'zinc')" size="sm" class="px-2 uppercase font-black tracking-tighter">{{ $user->currentPlan()?->name ?? 'Basic' }}</flux:badge>
                            <flux:separator vertical />
                            <flux:text size="sm" class="text-zinc-400">Member since {{ $memberSince }}</flux:text>
                        </div>

                        @if($user->isElite() && $user->storefront_message)
                            <div class="mt-4 p-4 bg-white/10 backdrop-blur rounded-2xl border border-white/20">
                                <flux:heading size="lg" class="text-white !font-black italic tracking-tight">{{ $user->storefront_message }}</flux:heading>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info & Actions --}}
                <div class="flex-1 pt-4 lg:pt-8 flex flex-col space-y-8">
                    {{-- Bio + Social + Follow Button --}}
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                        <flux:text size="lg" class="max-w-3xl leading-relaxed flex-1">
                            {{ $user->bio ?? 'This author is busy creating amazing content for the community!' }}
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

                    <flux:separator />

                    {{-- Metric Grid --}}
                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                        <flux:callout color="emerald" icon="banknotes">
                            <flux:subheading class="uppercase tracking-widest text-[10px] font-black">Total Sales</flux:subheading>
                            <flux:heading size="xl" class="tabular-nums">{{ number_format($totalSales) }}</flux:heading>
                        </flux:callout>

                        <flux:callout color="amber" icon="star">
                            <flux:subheading class="uppercase tracking-widest text-[10px] font-black">Avg Rating</flux:subheading>
                            <flux:heading size="xl" class="tabular-nums">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</flux:heading>
                        </flux:callout>

                        <flux:callout color="indigo" icon="users">
                            <flux:subheading class="uppercase tracking-widest text-[10px] font-black">Followers</flux:subheading>
                            <flux:heading size="xl" class="tabular-nums">{{ number_format($followerCount) }}</flux:heading>
                        </flux:callout>

                        <flux:callout color="zinc" icon="cube">
                            <flux:subheading class="uppercase tracking-widest text-[10px] font-black">Public Items</flux:subheading>
                            <flux:heading size="xl" class="tabular-nums">{{ number_format($productCount) }}</flux:heading>
                        </flux:callout>

                        <flux:callout color="sky" icon="shield-check">
                            <flux:subheading class="uppercase tracking-widest text-[10px] font-black">Trust Score</flux:subheading>
                            <flux:heading size="xl" class="tabular-nums">{{ number_format($user->ranking_score, 0, ',', '.') }}</flux:heading>
                        </flux:callout>

                        <flux:callout color="purple" icon="academic-cap">
                            <flux:subheading class="uppercase tracking-widest text-[10px] font-black">Mastery</flux:subheading>
                            <flux:heading size="xl" class="tabular-nums">Lv. {{ $user->level ?? 1 }}</flux:heading>
                        </flux:callout>
                    </div>
                </div>
            </div>
        </flux:card>
    </flux:container>

    {{-- Author Products --}}
    <div class="space-y-10">
        @livewire('author.storefront', ['authorId' => $user->id])
    </div>
</div>
@endsection
