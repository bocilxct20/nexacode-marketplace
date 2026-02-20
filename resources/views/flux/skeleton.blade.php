@props([
    'variant' => 'default',
])

@php
$classes = match ($variant) {
    'avatar' => 'h-10 w-10 rounded-full',
    'button' => 'h-9 w-20 rounded-md',
    'heading' => 'h-6 w-1/2 rounded-md',
    'text' => 'h-4 w-full rounded-md',
    default => 'h-4 w-full rounded-md',
};
@endphp

<div {{ $attributes->class([
    'bg-zinc-200 dark:bg-zinc-800 animate-pulse',
    $classes,
]) }}></div>
