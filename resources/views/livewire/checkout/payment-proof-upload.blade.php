<div class="mt-10 pt-8 text-left">
    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-900 dark:text-white mb-2">Upload Payment Proof</div>
    <div class="text-xs text-zinc-500 mb-6 font-medium">
        Please upload a screenshot or photo of your transfer receipt
    </div>

    <div class="space-y-6">
        <flux:file-upload wire:model="photos" multiple accept="image/*">
            <flux:file-upload.dropzone
                heading="Drop files here or click to browse"
                text="JPG, PNG, GIF up to 10MB"
                class="bg-zinc-50/50 dark:bg-zinc-800/30 border-2 border-dashed border-zinc-200 dark:border-zinc-700/50 hover:border-emerald-500/50 transition-colors"
            />
        </flux:file-upload>

        @if (!empty($photos))
            <div class="mt-4 flex flex-col gap-2">
                @foreach ($photos as $index => $photo)
                    <flux:file-item
                        :heading="$photo->getClientOriginalName()"
                        :image="$photo->temporaryUrl()"
                        :size="$photo->getSize()"
                    >
                        <x-slot name="actions">
                            <flux:file-item.remove wire:click="removePhoto({{ $index }})" />
                        </x-slot>
                    </flux:file-item>
                @endforeach
            </div>

            <div class="mt-8">
                <flux:button wire:click="save" variant="primary" class="w-full py-5 text-base font-black uppercase tracking-widest rounded-2xl" wire:loading.attr="disabled">
                    <flux:icon.arrow-up-tray variant="mini" class="w-4 h-4 mr-2" wire:loading.remove />
                    <flux:icon.loading class="w-4 h-4 animate-spin mr-2" wire:loading />
                    Submit Proof
                </flux:button>
            </div>
        @endif

        @error('photos.*') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
        @error('photos') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
    </div>
</div>
