<div class="relative">
    <flux:chart wire:model="data" class="w-full aspect-2/1">
        <flux:chart.viewport class="size-full">
            <flux:chart.svg>
                <flux:chart.line field="count" class="text-blue-500" />

                <flux:chart.axis axis="x" field="category" position="bottom">
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
                <flux:chart.tooltip.heading field="category" />

                <flux:chart.tooltip.value field="count" label="Products">
                    <flux:chart.legend.indicator class="bg-blue-500" />
                </flux:chart.tooltip.value>
            </flux:chart.tooltip>
        </flux:chart.viewport>
    </flux:chart>
</div>
