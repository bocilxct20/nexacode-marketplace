<div class="mt-4">
    @if($review->author_reply && !$isEditing)
        <div class="p-6 bg-emerald-50 dark:bg-emerald-900/10 border-l-4 border-emerald-500 rounded-r-2xl animate-in fade-in duration-300">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                    <flux:icon.user variant="mini" class="w-3.5 h-3.5 text-emerald-600" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-tighter text-emerald-600">Your Response</span>
                <span class="text-[10px] text-zinc-400 font-medium ml-auto">{{ $review->author_replied_at->diffForHumans() }}</span>
                <button wire:click="$set('isEditing', true)" class="text-[10px] text-zinc-400 hover:text-emerald-600 flex items-center gap-1 transition-colors">
                    <flux:icon.pencil variant="mini" class="w-2.5 h-2.5" /> Edit
                </button>
            </div>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 italic font-medium leading-relaxed">"{{ $review->author_reply }}"</p>
        </div>
    @else
        <div class="animate-in slide-in-from-top-2 duration-300">
            <form wire:submit.prevent="saveReply" class="space-y-4">
                <flux:field>
                    <flux:textarea 
                        wire:model="reply" 
                        placeholder="Write a professional response to this review..." 
                        rows="3" 
                        class="bg-white dark:bg-zinc-900"
                    />
                    <flux:error for="reply" />
                </flux:field>
                <div class="flex justify-end gap-2">
                    @if($isEditing)
                        <flux:button variant="ghost" size="sm" wire:click="$set('isEditing', false)">Cancel</flux:button>
                    @endif
                    <flux:button type="submit" variant="primary" size="sm" class="px-6">
                        {{ $review->author_reply ? 'Update Response' : 'Post Response' }}
                    </flux:button>
                </div>
            </form>
        </div>
    @endif
</div>
