<div class="space-y-12">
    <div class="space-y-4">
        <flux:heading size="xl" class="font-black">Elevate Your Presence</flux:heading>
        <flux:text class="text-zinc-500">Choose a tier that fits your growth. Scale your earnings and reach with NEXACODE Premium.</flux:text>
    </div>

    {{-- Premium Tiers Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <flux:card class="flex flex-col p-6 {{ $plan->is_elite ? 'border-emerald-500/50 bg-emerald-500/[0.02]' : '' }}">
                <div class="mb-6">
                    <flux:heading size="md" class="font-black">{{ $plan->name }}</flux:heading>
                    <div class="mt-2 flex items-baseline gap-1">
                        <flux:text size="lg" class="text-2xl font-black">Rp {{ number_format($plan->price, 0, ',', '.') }}</flux:text>
                        <flux:text size="xs" class="text-zinc-500 font-bold">/bln</flux:text>
                    </div>
                </div>

                <div class="flex-1 space-y-3 mb-6">
                    @foreach(array_slice($plan->features, 0, 4) as $feature)
                        <div class="flex items-start gap-2">
                            <flux:icon.check-circle variant="mini" class="text-emerald-500 shrink-0 mt-0.5" />
                            <flux:text size="xs" class="text-zinc-600 dark:text-zinc-400 leading-tight">
                                {{ $feature }}
                            </flux:text>
                        </div>
                    @endforeach
                </div>
                
                <flux:badge color="{{ $plan->price > 0 ? 'emerald' : 'zinc' }}" size="sm" class="w-full justify-center py-1 uppercase font-black tracking-widest">
                    {{ $plan->commission_rate }}% Commission
                </flux:badge>
            </flux:card>
        @endforeach
    </div>

    <flux:separator variant="subtle" />

    <div class="space-y-6">
        <div>
            <flux:heading size="lg" class="font-black">Begin Your Application</flux:heading>
            <flux:text class="text-zinc-500">Fill out the details below to apply for your author account.</flux:text>
        </div>

    @if ($submitted)
        <flux:card class="p-12 border-emerald-500/10 bg-emerald-500/5 dark:bg-emerald-500/5 items-center text-center">
            <div class="w-20 h-20 rounded-3xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-6 mx-auto border border-emerald-200 dark:border-emerald-800 shadow-sm">
                <flux:icon.check-circle class="w-10 h-10 text-emerald-600 dark:text-emerald-400" />
            </div>
            
            <flux:heading size="xl" class="font-black mb-2">Request Received!</flux:heading>
            <flux:text class="max-w-md mx-auto mb-8 text-zinc-500 dark:text-zinc-400">
                Your application is currently being reviewed by our moderation team. We'll notify you via email as soon as we've made a decision.
            </flux:text>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <flux:button href="{{ route('home') }}" variant="ghost">Browse Marketplace</flux:button>
            </div>
        </flux:card>
    @else
        <flux:card class="p-8">
            <form wire:submit="submit" class="space-y-6">
                <flux:field>
                    <flux:label>Portfolio or Github URL (Optional)</flux:label>
                    <flux:input wire:model="portfolio_url" type="url" placeholder="https://github.com/your-username" class="h-11" />
                    <flux:error name="portfolio_url" />
                </flux:field>

                <flux:field>
                    <flux:label>Tell us about your experience</flux:label>
                    <flux:textarea wire:model="message" placeholder="Describe what kind of products you intend to sell..." rows="5" />
                    <flux:description>Please provide at least 20 characters.</flux:description>
                    <flux:error name="message" />
                </flux:field>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" color="emerald" class="w-full h-12 font-bold shadow-lg shadow-emerald-500/20" icon-trailing="arrow-right">
                        Submit Application
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif
</div>
