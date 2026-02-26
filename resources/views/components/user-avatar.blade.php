@props(['user', 'size' => 'md', 'border' => true, 'thickness' => '2', 'src' => null])

@php
    $isAdmin = $user->isAdmin();
    $isElite = $user->isElite();
    $isPro = $user->isPro();
    
    $ringClass = '';
    if ($border) {
        if ($isAdmin) {
            $ringClass = "ring-rose-500 ring-offset-2 dark:ring-offset-zinc-950 shadow-[0_0_30px_rgba(244,63,94,0.6)]";
        } elseif ($isElite) {
            $ringClass = "ring-amber-400 shadow-[0_0_15px_rgba(251,191,36,0.3)]";
        } elseif ($isPro) {
            $ringClass = "ring-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.3)]";
        } else {
            $ringClass = "ring-zinc-100 dark:ring-zinc-800";
        }
    }

    $thicknessClass = $border ? "ring-{$thickness}" : "";
@endphp

<div class="relative inline-block">
    <flux:avatar 
        {{ $attributes->class(['rounded-xl', $thicknessClass, $ringClass]) }}
        :size="$size" 
        :src="$src ?? $user->avatar_url" 
        :initials="$user->initials" 
    />
    
    @if($isAdmin)
        <div class="absolute -top-1 -right-1 size-3 bg-rose-500 rounded-full border-2 border-white dark:border-zinc-900 animate-pulse shadow-[0_0_10px_rgba(244,63,94,0.8)]"></div>
    @endif
</div>
