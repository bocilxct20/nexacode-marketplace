<div>
    <flux:chart wire:model="data" class="w-full aspect-2/1">
        <flux:chart.viewport class="size-full">
            <flux:chart.svg>
                <flux:chart.area field="sales" class="text-blue-500" curve="smooth" />
                <flux:chart.line field="sales" class="text-blue-500" curve="smooth" />

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

                <flux:chart.tooltip.value field="sales" label="Sales">
                    <flux:chart.legend.indicator class="bg-blue-500" />
                </flux:chart.tooltip.value>
            </flux:chart.tooltip>
        </flux:chart.viewport>
    </flux:chart>
</div>
