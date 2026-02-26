<div class="space-y-6">
    <div class="flex items-center gap-3 animate-pulse">
        <div class="h-1 w-8 bg-indigo-500 rounded-full"></div>
        <div class="h-6 w-48 bg-zinc-200 dark:bg-zinc-800 rounded-lg"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach(range(1, 4) as $i)
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm h-full">
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 animate-pulse"></div>
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="h-4 w-16 bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                        <div class="h-4 w-10 bg-zinc-50 dark:bg-zinc-900/50 rounded-md animate-pulse"></div>
                    </div>

                    <div class="h-5 w-full bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="h-6 w-24 bg-zinc-100 dark:bg-zinc-900 rounded-md animate-pulse"></div>
                        <div class="size-8 bg-zinc-100 dark:bg-zinc-900 rounded-lg animate-pulse"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
