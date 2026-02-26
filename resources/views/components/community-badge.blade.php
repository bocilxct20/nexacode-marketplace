@props(['badge', 'size' => 'md', 'user' => null, 'href' => null])

@php
    $badge = $badge ?? ($user?->tierBadge ?? null);
    
    if (!$badge || ($badge->name ?? '') === 'BASIC') {
        // Option to hide BASIC if requested, but user provided a style for it.
        // For now, if no badge and no user, we can't render.
        if (!$badge) return;
    }

    $badgeName = strtoupper($badge->name ?? '');
    
    $baseClasses = "inline-flex items-center font-semibold tracking-wider uppercase rounded-full transition-all duration-200 hover:scale-[1.03]";
    
    $tierStyles = match($badgeName) {
        'ADMIN' => 'bg-red-900 text-white ring-1 ring-red-800/40 dark:bg-red-700 dark:ring-red-600/40',
        'ELITE' => 'bg-gradient-to-r from-amber-400 to-yellow-500 text-gray-900 ring-1 ring-amber-400/40 hover:shadow-md',
        'PRO' => 'bg-blue-600 text-white ring-1 ring-blue-500/40 dark:bg-blue-500',
        'BASIC' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700 font-medium',
        default => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 font-medium',
    };

    $sizeClasses = $size === 'sm' ? 'px-2 py-0.5 text-[9px]' : 'px-2.5 py-0.5 text-xs';
    $iconSize = $size === 'sm' ? 'size-3' : 'size-3.5';
    
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} 
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "$baseClasses $tierStyles $sizeClasses cursor-default"]) }}
>
    @if($badge->icon)
        <flux:icon :icon="$badge->icon" variant="mini" class="{{ $iconSize }} shrink-0 mr-1.5" />
    @endif
    <span>{{ $badgeName }}</span>
</{{ $tag }}>
