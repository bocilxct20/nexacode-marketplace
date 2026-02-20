<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div class="w-full md:w-1/3">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                type="search" 
                placeholder="Search ticket subject..." 
                icon="magnifying-glass"
            />
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <flux:select wire:model.live="status" class="w-40">
                <option value="all">All Status</option>
                <option value="open">Open</option>
                <option value="answered">Answered</option>
                <option value="closed">Closed</option>
            </flux:select>
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Ticket</flux:table.column>
                <flux:table.column>Product</flux:table.column>
                @if($userRole === 'author')
                    <flux:table.column>Buyer</flux:table.column>
                @endif
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Priority</flux:table.column>
                <flux:table.column>Last Activity</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($tickets as $ticket)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-900 dark:text-white line-clamp-1">{{ $ticket->subject }}</span>
                                <span class="text-xs text-zinc-500">ID: #{{ $ticket->id }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <img src="{{ $ticket->product->thumbnail }}" class="w-6 h-6 rounded object-cover">
                                <span class="text-sm truncate max-w-[150px]">{{ $ticket->product->name }}</span>
                            </div>
                        </flux:table.cell>
                        @if($userRole === 'author')
                            <flux:table.cell>
                                <span class="text-sm font-medium">{{ $ticket->user->name }}</span>
                            </flux:table.cell>
                        @endif
                        <flux:table.cell>
                            @php
                                $statusColor = match($ticket->status) {
                                    'open' => 'blue',
                                    'answered' => 'emerald',
                                    'closed' => 'zinc',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge :color="$statusColor" size="sm" class="uppercase text-[10px] font-bold">{{ $ticket->status }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                                $priorityColor = match($ticket->priority) {
                                    'high' => 'red',
                                    'medium' => 'amber',
                                    'low' => 'zinc',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge :color="$priorityColor" variant="ghost" size="sm" class="uppercase text-[10px] font-bold">{{ $ticket->priority }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-xs text-zinc-500">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('support.show', $ticket) }}" variant="ghost" size="sm" icon="chat-bubble-left-right"></flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ $userRole === 'author' ? 7 : 6 }}" class="py-12 text-center text-zinc-500 italic">
                            No support tickets found matching your criteria.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</div>
