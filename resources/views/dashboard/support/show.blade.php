@extends($layout ?? 'layouts.app')

@section('title', 'Ticket: ' . $ticket->subject)

@section('content')
    <div class="mb-8">
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" separator="slash">Dashboard</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('support.index') }}" separator="slash">Support Center</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">#{{ $ticket->id }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
        <flux:heading size="xl" class="font-bold">{{ $ticket->subject }}</flux:heading>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @livewire('support.ticket-chat', ['ticketId' => $ticket->id])
        </div>

        <div class="space-y-6">
            <flux:card>
                <flux:heading size="sm" class="uppercase tracking-widest text-zinc-400 font-bold mb-4">Ticket Details</flux:heading>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs text-zinc-500">Status</span>
                        <flux:badge size="sm" :color="$ticket->status === 'closed' ? 'zinc' : 'blue'">{{ strtoupper($ticket->status) }}</flux:badge>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs text-zinc-500">Priority</span>
                        <flux:badge size="sm" variant="ghost" :color="$ticket->priority === 'high' ? 'red' : 'zinc'">{{ strtoupper($ticket->priority) }}</flux:badge>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs text-zinc-500">Created At</span>
                        <span class="text-xs font-medium">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="sm" class="uppercase tracking-widest text-zinc-400 font-bold mb-4">Product Info</flux:heading>
                
                <div class="flex items-center gap-4">
                    <img src="{{ $ticket->product->thumbnail }}" class="w-12 h-12 rounded-lg object-cover">
                    <div class="flex flex-col">
                        <flux:link href="{{ route('products.show', $ticket->product->slug) }}" class="font-bold text-sm">{{ $ticket->product->name }}</flux:link>
                        <span class="text-xs text-zinc-500">Author: {{ $ticket->product->author->name }}</span>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
@endsection
