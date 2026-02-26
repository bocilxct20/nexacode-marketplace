<div class="space-y-6">
    <flux:card class="bg-zinc-50 dark:bg-zinc-900 border-none">
        <div class="flex justify-between items-start">
            <div>
                <flux:heading size="xl">{{ $ticket->subject }}</flux:heading>
                <div class="flex items-center gap-2 mt-2">
                    <flux:badge size="sm" :color="$ticket->status_color" class="uppercase font-bold tracking-tighter">{{ $ticket->status_label }}</flux:badge>
                    <span class="text-xs text-zinc-400">• Created {{ $ticket->created_at->format('M d, Y') }}</span>
                </div>
            </div>
            
            @if(!$ticket->isClosed() && (Auth::id() === $ticket->user_id || Auth::user()->isAdmin()))
                <flux:button wire:click="closeTicket" variant="ghost" size="sm" color="red" icon="x-circle">Close Ticket</flux:button>
            @endif
        </div>
    </flux:card>

    <div class="space-y-4 max-h-[600px] overflow-y-auto px-1">
        @foreach($ticket->replies as $reply)
            @php
                $isMe = Auth::id() === $reply->user_id;
                $isStaff = $reply->user->isAdmin() || ($ticket->product->author_id === $reply->user_id);
            @endphp
            
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} gap-3">
                @if(!$isMe)
                    <div class="shrink-0 pt-1">
                        <div class="relative">
                            <flux:avatar 
                                :src="$reply->user->avatar" 
                                class="size-8 rounded-lg {{ $reply->user->isElite() ? 'border-2 border-amber-400 elite-glow-gold' : ($reply->user->isPro() ? 'border-2 border-indigo-400 pro-glow-indigo' : '') }}" 
                            />
                            @if($reply->user->isElite())
                                <div class="absolute -inset-1 bg-amber-400/20 blur-sm rounded-lg -z-10 animate-pulse"></div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="max-w-[85%] md:max-w-[70%] space-y-1">
                    <div class="flex items-center gap-2 px-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">{{ $reply->user->name }}</span>
                            @if($isStaff)
                                <div class="flex items-center gap-1.5">
                                    <flux:badge size="sm" variant="ghost" color="indigo" class="text-[8px] py-0 px-1 border-indigo-500/30">STAFF</flux:badge>
                                    <x-community-badge :user="$reply->user" />
                                </div>
                            @else
                                <x-community-badge :user="$reply->user" />
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-4 rounded-2xl shadow-sm {{ $isMe ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 rounded-tl-none border border-zinc-100 dark:border-zinc-700' }}">
                        <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</p>
                        <div class="mt-2 text-[9px] {{ $isMe ? 'text-indigo-200' : 'text-zinc-400' }} flex items-center gap-1">
                            <flux:icon.clock class="w-2.5 h-2.5" />
                            {{ $reply->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(!$ticket->isClosed())
        <flux:card class="mt-8 p-0 border-none shadow-xl">
            <form wire:submit.prevent="postReply" class="relative">
                <textarea 
                    wire:model="message"
                    placeholder="Type your reply here..." 
                    class="w-full h-32 p-4 text-sm bg-white dark:bg-zinc-800 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 resize-none dark:text-white"
                ></textarea>
                
                <div class="absolute bottom-4 right-4 flex items-center gap-3">
                    <flux:icon.loading wire:loading wire:target="postReply" class="w-4 h-4 text-indigo-500" />
                    <flux:button type="submit" variant="primary" icon="paper-airplane" size="sm">Send Reply</flux:button>
                </div>
            </form>
        </flux:card>
    @else
        <div class="p-6 text-center bg-zinc-100 dark:bg-zinc-900 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
            <flux:icon.lock-closed class="w-8 h-8 mx-auto text-zinc-400 mb-2" />
            <flux:heading size="sm">This ticket is closed</flux:heading>
            <p class="text-xs text-zinc-500 mt-1">If you need more help, please post a new reply to reopen it or create a new ticket.</p>
        </div>
    @endif
</div>
