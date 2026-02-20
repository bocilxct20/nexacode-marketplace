<div class="relative">
    <div wire:loading.flex class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm items-center justify-center rounded-xl overflow-hidden p-8">
        <flux:skeleton animate="shimmer" class="aspect-2/1 size-full rounded-lg" />
    </div>

    <flux:chart wire:model="data" class="w-full aspect-2/1">
        <flux:chart.viewport class="size-full">
            <flux:chart.svg>
                <flux:chart.area field="revenue" class="text-purple-500" curve="smooth" />
                <flux:chart.line field="revenue" class="text-purple-500" curve="smooth" />
                
                <flux:chart.area field="commission" class="text-green-500" curve="smooth" />
                <flux:chart.line field="commission" class="text-green-500" curve="smooth" />

                <flux:chart.axis axis="x" field="date" position="bottom">
                    <flux:chart.axis.tick />
                    <flux:chart.axis.line />
                </flux:chart.axis>

                <flux:chart.axis axis="y" position="left">
                    <flux:chart.axis.grid />
                    <flux:chart.axis.tick />
                </flux:chart.axis>

                <flux:chart.cursor class="text-zinc-800" stroke-dasharray="4 4" />
            </flux:chart.svg>

            <flux:chart.tooltip>
                <flux:chart.tooltip.heading field="date" />

                <flux:chart.tooltip.value field="revenue" label="Total Revenue">
                    <flux:chart.legend.indicator class="bg-purple-500" />
                </flux:chart.tooltip.value>
                
                <flux:chart.tooltip.value field="commission" label="Platform Commission">
                    <flux:chart.legend.indicator class="bg-green-500" />
                </flux:chart.tooltip.value>
            </flux:chart.tooltip>
        </flux:chart.viewport>
    </flux:chart>
</div>
