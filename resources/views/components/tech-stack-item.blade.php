@props(['name'])

@php
    $techs = [
        'laravel' => ['slug' => 'laravel'],
        'php' => ['slug' => 'php'],
        'tailwind' => ['slug' => 'tailwindcss'],
        'tailwindcss' => ['slug' => 'tailwindcss'],
        'livewire' => ['slug' => 'livewire'],
        'javascript' => ['slug' => 'javascript'],
        'js' => ['slug' => 'javascript'],
        'vue' => ['slug' => 'vuedotjs'],
        'react' => ['slug' => 'react'],
        'mysql' => ['slug' => 'mysql'],
        'alpine' => ['slug' => 'alpinedotjs'],
        'css' => ['slug' => 'css3'],
        'html' => ['slug' => 'html5'],
        'saas' => ['slug' => 'cloud'],
        'api' => ['slug' => 'insomnia'],
        'plugin' => ['slug' => 'iconify'],
        'plugins' => ['slug' => 'iconify'],
        'wordpress' => ['slug' => 'wordpress'],
        'wp' => ['slug' => 'wordpress'],
        'woocommerce' => ['slug' => 'woocommerce'],
        'bootstrap' => ['slug' => 'bootstrap'],
        'sass' => ['slug' => 'sass'],
        'scss' => ['slug' => 'sass'],
        'jquery' => ['slug' => 'jquery'],
        'nodejs' => ['slug' => 'nodedotjs'],
        'node' => ['slug' => 'nodedotjs'],
        'flutter' => ['slug' => 'flutter'],
        'dart' => ['slug' => 'dart'],
        'firebase' => ['slug' => 'firebase'],
        'aws' => ['slug' => 'amazonwebservices'],
        'docker' => ['slug' => 'docker'],
        'python' => ['slug' => 'python'],
        'django' => ['slug' => 'django'],
    ];

    $slug_raw = strtolower($name);
    $tech = $techs[$slug_raw] ?? null;
    
    // Use the mask URL
    $maskUrl = $tech ? "https://cdn.simpleicons.org/{$tech['slug']}" : null;
@endphp

<div class="flex items-center gap-2.5 px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm transition-all hover:shadow-md hover:border-emerald-500/50 group cursor-default">
    <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-900/50 p-1 group-hover:scale-110 transition-transform">
        @if($maskUrl)
            <div class="w-4 h-4 bg-zinc-400 group-hover:bg-emerald-500 transition-colors duration-300" 
                 style="mask-image: url('{{ $maskUrl }}'); mask-size: contain; mask-repeat: no-repeat; mask-position: center; -webkit-mask-image: url('{{ $maskUrl }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; -webkit-mask-position: center;">
            </div>
        @else
            <x-lucide-tag class="w-4 h-4 text-zinc-400 group-hover:text-emerald-500 transition-colors" />
        @endif
    </div>
    <span class="text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-widest">{{ $name }}</span>
</div>
