@props(['name'])

@php
    $techs = [
        'laravel' => ['slug' => 'laravel', 'color' => 'FF2D20'],
        'php' => ['slug' => 'php', 'color' => '777BB4'],
        'tailwind' => ['slug' => 'tailwindcss', 'color' => '06B6D4'],
        'tailwindcss' => ['slug' => 'tailwindcss', 'color' => '06B6D4'],
        'livewire' => ['slug' => 'livewire', 'color' => 'FB70A9'],
        'javascript' => ['slug' => 'javascript', 'color' => 'F7DF1E'],
        'vue' => ['slug' => 'vuedotjs', 'color' => '4FC08D'],
        'react' => ['slug' => 'react', 'color' => '61DAFB'],
        'mysql' => ['slug' => 'mysql', 'color' => '4479A1'],
        'alpine' => ['slug' => 'alpinedotjs', 'color' => '8BC0D0'],
        'css' => ['slug' => 'css3', 'color' => '1572B6'],
        'html' => ['slug' => 'html5', 'color' => 'E34F26'],
        'saas' => ['slug' => 'cloud', 'color' => 'indigo'],
        'api' => ['slug' => 'insomnia', 'color' => '5849BE'],
        'plugin' => ['slug' => 'iconify', 'color' => '1769aa'],
    ];

    $slug_raw = strtolower($name);
    $tech = $techs[$slug_raw] ?? null;
    
    // Fallback logic
    $iconUrl = $tech ? "https://cdn.simpleicons.org/{$tech['slug']}/{$tech['color']}" : null;
@endphp

<div class="flex items-center gap-2.5 px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm transition-all hover:shadow-md hover:border-emerald-500/50 group cursor-default">
    <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-900/50 p-1 group-hover:scale-110 transition-transform">
        @if($iconUrl)
            <img src="{{ $iconUrl }}" class="w-4 h-4 object-contain" alt="{{ $name }}">
        @else
            <x-lucide-tag class="w-4 h-4 text-zinc-400 group-hover:text-emerald-500 transition-colors" />
        @endif
    </div>
    <span class="text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-widest">{{ $name }}</span>
</div>
