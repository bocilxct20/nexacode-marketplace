<div>
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Mail Manager</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <flux:heading size="xl" class="mb-1">Ultimate Mail Manager 📧</flux:heading>
            <flux:subheading>Comprehensive testing suite for all {{ count(collect($categories)->flatten(1)) }} platform mailables.</flux:subheading>
        </div>
        
        <div class="flex items-center gap-3 p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <flux:icon name="information-circle" class="size-5 text-zinc-500" />
            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">All tests will be sent to <strong>{{ auth()->user()->email }}</strong></span>
        </div>
    </div>

    <div class="space-y-12">
        @foreach($categories as $categoryName => $mailables)
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-6 w-1 rounded-full bg-emerald-500"></div>
                    <flux:heading size="lg" class="uppercase tracking-widest text-xs font-black">{{ $categoryName }}</flux:heading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($mailables as $mail)
                        <flux:card class="flex flex-col justify-between h-full bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow p-5 rounded-2xl">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="p-2.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                    <flux:icon name="{{ $mail['icon'] }}" variant="micro" class="size-4" />
                                </div>
                                <flux:heading size="sm" class="line-clamp-1">{{ $mail['name'] }}</flux:heading>
                            </div>

                            <div class="flex items-center gap-2">
                                <flux:button wire:click="sendTest('{{ $mail['id'] }}')" wire:loading.attr="disabled" variant="subtle" size="sm" class="flex-1 font-bold text-[10px] uppercase tracking-wider py-2">
                                    <span wire:loading.remove wire:target="sendTest('{{ $mail['id'] }}')">Send Test</span>
                                    <span wire:loading wire:target="sendTest('{{ $mail['id'] }}')">...</span>
                                </flux:button>
                                
                                <flux:button variant="ghost" size="sm" icon="eye" x-on:click="window.open('/admin/mail-preview/{{ $mail['id'] }}', '_blank')" class="px-2" title="Preview" />
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div class="mt-16 p-8 bg-zinc-900 text-white rounded-[2rem] border border-white/10 shadow-2xl overflow-hidden relative">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 size-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 size-40 bg-blue-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <div class="p-4 bg-white/10 rounded-2xl backdrop-blur-md">
                <flux:icon name="shield-check" class="size-10 text-emerald-400" />
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <flux:heading size="lg" class="text-white mb-2">Quality Assurance Enforcement</flux:heading>
                <p class="text-zinc-400 text-sm max-w-2xl">
                    By testing every mailable, you ensure that NEXACODE maintains a professional, spam-free, and high-converting communication loop. Always verify responsiveness and dark-mode compatibility before deploying new template changes.
                </p>
            </div>
        </div>
    </div>
</div>
