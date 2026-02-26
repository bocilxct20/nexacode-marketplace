<div class="py-20">
    <flux:container>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Sales Stat --}}
            <div class="group relative p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <flux:icon.banknotes class="w-32 h-32 rotate-12" />
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-6 group-hover:scale-110 transition-transform">
                        <flux:icon.banknotes variant="mini" class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums tracking-tighter" x-data="{ count: 0, target: {{ $stats['total_sales'] }} }" x-init="setTimeout(() => { let interval = setInterval(() => { if(count >= target) clearInterval(interval); else count += Math.ceil((target - count) / 10) }, 50) }, 500)">
                            <span x-text="count.toLocaleString()">0</span>+
                        </div>
                        <div class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Transactions</div>
                    </div>
                </div>
            </div>

            {{-- Members Stat --}}
            <div class="group relative p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <flux:icon.users class="w-32 h-32 rotate-12" />
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 mb-6 group-hover:scale-110 transition-transform">
                        <flux:icon.users variant="mini" class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums tracking-tighter" x-data="{ count: 0, target: {{ $stats['total_members'] }} }" x-init="setTimeout(() => { let interval = setInterval(() => { if(count >= target) clearInterval(interval); else count += Math.ceil((target - count) / 10) }, 50) }, 500)">
                            <span x-text="count.toLocaleString()">0</span>+
                        </div>
                        <div class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Community Members</div>
                    </div>
                </div>
            </div>

            {{-- Authors Stat --}}
            <div class="group relative p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <flux:icon.pencil-square class="w-32 h-32 rotate-12" />
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 mb-6 group-hover:scale-110 transition-transform">
                        <flux:icon.pencil-square variant="mini" class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums tracking-tighter" x-data="{ count: 0, target: {{ $stats['active_authors'] }} }" x-init="setTimeout(() => { let interval = setInterval(() => { if(count >= target) clearInterval(interval); else count += Math.ceil((target - count) / 10) }, 50) }, 500)">
                            <span x-text="count.toLocaleString()">0</span>+
                        </div>
                        <div class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Talented Authors</div>
                    </div>
                </div>
            </div>

            {{-- Product Stat --}}
            <div class="group relative p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <flux:icon.cube class="w-32 h-32 rotate-12" />
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 flex items-center justify-center text-sky-500 mb-6 group-hover:scale-110 transition-transform">
                        <flux:icon.cube variant="mini" class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-4xl font-black text-zinc-900 dark:text-white tabular-nums tracking-tighter" x-data="{ count: 0, target: {{ $stats['total_products'] }} }" x-init="setTimeout(() => { let interval = setInterval(() => { if(count >= target) clearInterval(interval); else count += Math.ceil((target - count) / 10) }, 50) }, 500)">
                            <span x-text="count.toLocaleString()">0</span>+
                        </div>
                        <div class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Premium Items</div>
                    </div>
                </div>
            </div>
        </div>
    </flux:container>
</div>
