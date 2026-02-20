<div class="w-full">
    <flux:card class="p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="lg">Revenue Growth</flux:heading>
                <flux:subheading>Total revenue performance over the last 30 days.</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Live Data</span>
                </div>
            </div>
        </div>

        <flux:chart wire:model="data" class="w-full aspect-[3/1]">
            <flux:chart.viewport class="size-full">
                <flux:chart.svg>
                    <defs>
                        <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="5%" stop-color="#3b82f6" stop-opacity="0.3"/>
                            <stop offset="95%" stop-color="#3b82f6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    <flux:chart.area field="revenue" class="text-blue-500 fill-[url(#revenueGradient)]" curve="smooth" />
                    <flux:chart.line field="revenue" class="text-blue-500" curve="smooth" />

                    <flux:chart.axis axis="x" field="date" position="bottom">
                        <flux:chart.axis.tick />
                    </flux:chart.axis>

                    <flux:chart.axis axis="y" position="left">
                        <flux:chart.axis.grid />
                        <flux:chart.axis.tick />
                    </flux:chart.axis>

                    <flux:chart.cursor stroke-dasharray="4 4" />
                </flux:chart.svg>

                <flux:chart.tooltip>
                    <flux:chart.tooltip.heading field="date" :format="['month' => 'short', 'day' => 'numeric']" />

                    <flux:chart.tooltip.value field="revenue" label="Revenue">
                        <flux:chart.legend.indicator class="bg-blue-500" />
                        <template #prefix>Rp </template>
                    </flux:chart.tooltip.value>
                </flux:chart.tooltip>
            </flux:chart.viewport>
        </flux:chart>
    </flux:card>
</div>
