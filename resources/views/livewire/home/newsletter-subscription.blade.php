<flux:card class="py-12 px-8 bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-3xl relative overflow-hidden group">
    {{-- Background Decorative Glow --}}
    <div class="absolute -top-24 -right-24 size-96 bg-emerald-500/10 blur-[100px] rounded-full group-hover:bg-emerald-500/20 transition-all duration-1000"></div>
    <div class="absolute -bottom-24 -left-24 size-96 bg-blue-500/10 blur-[100px] rounded-full group-hover:bg-blue-500/20 transition-all duration-1000"></div>

    <div class="relative flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-20">
        <div class="max-w-xl text-center lg:text-left">
            <flux:badge color="emerald" variant="solid" class="flex items-center gap-2 mb-6 w-fit mx-auto lg:mx-0">
                <flux:icon name="envelope" variant="micro" class="size-4" />
                <span class="text-[10px] uppercase font-black tracking-widest">NexaCode Weekly</span>
            </flux:badge>
            
            <flux:heading size="2xl" class="text-zinc-900 dark:text-white mb-6 !text-4xl lg:!text-5xl font-black leading-tight">
                Dapatkan kurasi aset terbaik <span class="text-emerald-500">setiap Senin</span> langsung di emailmu.
            </flux:heading>
            
            <flux:text class="text-zinc-600 dark:text-zinc-400 text-lg lg:text-xl">
                Jangan lewatkan update script, theme, dan template premium pilihan editor kami. Gratis, selamanya.
            </flux:text>
        </div>

        <div class="w-full max-w-2xl">
            <form wire:submit="subscribe" class="flex flex-col md:flex-row gap-3">
                <div class="grow group/input">
                    <input 
                        wire:model="email" 
                        type="email" 
                        placeholder="Masukkan alamat email kamu..." 
                        class="w-full h-16 px-6 bg-white dark:bg-zinc-800 border-none rounded-2xl text-zinc-900 dark:text-white placeholder:text-zinc-500 focus:ring-2 focus:ring-emerald-500/30 transition-all shadow-none outline-none appearance-none"
                    />
                    @error('email')
                        <span class="mt-2 block text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span>
                    @enderror
                </div>
                
                <flux:button type="submit" variant="primary" color="emerald" class="h-16 px-8 !text-lg !font-black transition-all !rounded-2xl !shadow-none hover:shadow-none !border-none !outline-none whitespace-nowrap">
                    Langganan Sekarang
                </flux:button>
            </form>
            
            <flux:text size="xs" class="text-center md:text-left uppercase tracking-widest font-bold mt-4 text-zinc-500 dark:text-zinc-400 opacity-60">
                Join {{ number_format($subscriberCount, 0, ',', '.') }}+ elite developers
            </flux:text>
        </div>
    </div>
</flux:card>
