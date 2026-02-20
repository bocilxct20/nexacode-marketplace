@props([
    'position' => 'bottom end',
])

{{-- Custom Alpine.js-based toast that replaces the broken Flux Pro ui-toast custom element --}}
<div
    x-data="{
        toasts: [],
        show(detail) {
            const id = Date.now();
            const toast = {
                id,
                variant: detail.variant || 'success',
                heading: detail.heading || '',
                text: detail.text || (typeof detail === 'string' ? detail : ''),
                timeout: null,
            };
            this.toasts.push(toast);
            toast.timeout = setTimeout(() => this.dismiss(id), 4000);
        },
        dismiss(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast-show.window="show($event.detail)"
    style="{{ match($position) {
        'top start' => 'top: 1rem; left: 1rem;',
        'top end' => 'top: 1rem; right: 1rem;',
        'bottom start' => 'bottom: 1rem; left: 1rem;',
        default => 'bottom: 1rem; right: 1rem;',
    } }}"
    class="fixed z-[9999] flex flex-col gap-2 max-w-sm w-full"
    wire:ignore
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="flex items-start gap-3 p-3 rounded-xl shadow-lg bg-white border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700"
        >
            {{-- Success icon --}}
            <template x-if="toast.variant === 'success'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="shrink-0 mt-0.5 size-5 text-lime-600 dark:text-lime-400">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.043l4.25-5.5Z" clip-rule="evenodd" />
                </svg>
            </template>

            {{-- Warning icon --}}
            <template x-if="toast.variant === 'warning'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="shrink-0 mt-0.5 size-5 text-amber-500 dark:text-amber-400">
                    <path fill-rule="evenodd" d="M6.701 2.25c.577-1 2.02-1 2.598 0l5.196 9a1.5 1.5 0 0 1-1.299 2.25H2.804a1.5 1.5 0 0 1-1.3-2.25l5.197-9ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 1 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>
            </template>

            {{-- Danger/Error icon --}}
            <template x-if="toast.variant === 'danger' || toast.variant === 'error'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="shrink-0 mt-0.5 size-5 text-rose-500 dark:text-rose-400">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>
            </template>

            {{-- Default/info icon --}}
            <template x-if="!['success','warning','danger','error'].includes(toast.variant)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="shrink-0 mt-0.5 size-5 text-blue-500 dark:text-blue-400">
                    <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0Zm-6 3.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM8 3a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 8 3Z" clip-rule="evenodd" />
                </svg>
            </template>

            <div class="flex-1 min-w-0">
                <p x-show="toast.heading" x-text="toast.heading" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100"></p>
                <p x-show="toast.text" x-text="toast.text" class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5"></p>
            </div>

            <button
                @click="dismiss(toast.id)"
                class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-md text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        </div>
    </template>
</div>
