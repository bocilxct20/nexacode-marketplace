<div class="space-y-6">
    <flux:card class="p-8 bg-zinc-50 dark:bg-zinc-900 shadow-inner">
        <form wire:submit="submit" class="space-y-6">
            <div class="space-y-4">
                <flux:label>Rating</flux:label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                            <flux:icon.star class="w-8 h-8" :class="$rating >= $i ? 'text-amber-400 fill-amber-400' : 'text-zinc-300 dark:text-zinc-700'" />
                        </button>
                    @endfor
                </div>
            </div>
            
            <flux:field>
                <flux:label>Your Review</flux:label>
                <flux:textarea wire:model="comment" rows="4" placeholder="Share your experience with this item..." required minlength="10" />
                <flux:error name="comment" />
            </flux:field>

            <div class="space-y-3">
                <flux:file-upload wire:model="photos" multiple label="Share implementations / screenshots">
                    <flux:file-upload.dropzone
                        heading="Drop screenshots here or click to browse"
                        text="JPG, PNG, WEBP up to 10MB per file"
                    />
                </flux:file-upload>

                @if(!empty($photos))
                    <div class="mt-4 flex flex-col gap-2">
                        @foreach($photos as $index => $photo)
                            <flux:file-item
                                :heading="$photo->getClientOriginalName()"
                                :image="in_array($photo->extension(), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']) ? $photo->temporaryUrl() : null"
                                :size="$photo->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove wire:click="removePhoto({{ $index }})" />
                                </x-slot>
                            </flux:file-item>
                        @endforeach
                    </div>
                @endif
                <flux:error name="photos.*" />
            </div>

            <div class="flex gap-2 pt-4">
                <flux:button type="submit" variant="primary">Submit Review</flux:button>
                <flux:button variant="ghost" @click="showReviewForm = false">Cancel</flux:button>
            </div>
        </form>
    </flux:card>
</div>
