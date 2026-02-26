<div class="py-20 animate-pulse">
    <flux:container>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach(range(1, 4) as $i)
                <div class="p-8 bg-zinc-100/50 dark:bg-zinc-900/50 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 h-48 flex flex-col justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-200 dark:bg-zinc-800 animate-pulse"></div>
                    <div class="space-y-3">
                        <div class="h-10 w-24 bg-zinc-200 dark:bg-zinc-800 rounded-lg animate-pulse"></div>
                        <div class="h-4 w-32 bg-zinc-200 dark:bg-zinc-800 rounded-md animate-pulse"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </flux:container>
</div>
