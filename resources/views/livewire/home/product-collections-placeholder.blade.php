<div class="space-y-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6 animate-pulse">
        <div class="space-y-3">
            <div class="h-8 w-64 bg-zinc-200 dark:bg-zinc-800 rounded-lg"></div>
            <div class="h-4 w-96 bg-zinc-100 dark:bg-zinc-900 rounded-lg"></div>
        </div>
        <div class="h-10 w-48 bg-zinc-100 dark:bg-zinc-900 rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach(range(1, 6) as $i)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm h-full flex flex-col">
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 animate-pulse"></div>
                <div class="p-6 space-y-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-center">
                        <div class="h-4 w-16 bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                        <div class="h-4 w-12 bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-6 w-full bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                        <div class="h-4 w-3/4 bg-zinc-50 dark:bg-zinc-900/50 rounded-md animate-pulse"></div>
                    </div>
                    <div class="mt-auto pt-6 border-t border-zinc-50 dark:border-zinc-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-900 animate-pulse"></div>
                            <div class="h-4 w-20 bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                        </div>
                        <div class="h-6 w-24 bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
