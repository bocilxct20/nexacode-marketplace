<div 
    x-data="{ 
        isOpen: @entangle('isOpen'),
        next() { $wire.next() },
        prev() { $wire.prev() },
        close() { $wire.close() }
    }"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="close()"
    @keydown.right.window="next()"
    @keydown.left.window="prev()"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 backdrop-blur-xl"
    style="display: none;"
>
    {{-- Close Button --}}
    <button @click="close()" class="absolute top-6 right-6 p-3 text-white/50 hover:text-white transition-colors">
        <x-lucide-x class="w-8 h-8" />
    </button>

    {{-- Main Image Container --}}
    <div class="relative w-full max-w-6xl max-h-[90vh] flex items-center justify-center p-4">
        {{-- Navigation - Prev --}}
        @if(count($images) > 1)
            <button @click="prev()" class="absolute left-4 z-10 p-4 text-white/30 hover:text-white transition-colors bg-white/5 hover:bg-white/10 rounded-full">
                <x-lucide-chevron-left class="w-8 h-8" />
            </button>
        @endif

        {{-- Active Image --}}
        @if(!empty($images) && isset($images[$activeIndex]))
            <img 
                src="{{ Storage::url($images[$activeIndex]) }}" 
                class="max-w-full max-h-[85vh] object-contain shadow-2xl rounded-lg"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
            >
        @endif

        {{-- Navigation - Next --}}
        @if(count($images) > 1)
            <button @click="next()" class="absolute right-4 z-10 p-4 text-white/30 hover:text-white transition-colors bg-white/5 hover:bg-white/10 rounded-full">
                <x-lucide-chevron-right class="w-8 h-8" />
            </button>
        @endif
    </div>

    {{-- Counter & Info --}}
    @if(count($images) > 1)
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-4 px-6 py-2 bg-white/5 rounded-full border border-white/10 backdrop-blur-md">
            <span class="text-sm font-black text-white tracking-widest uppercase">{{ $activeIndex + 1 }} / {{ count($images) }}</span>
        </div>
    @endif
</div>
