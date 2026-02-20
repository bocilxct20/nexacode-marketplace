<div class="mt-8 border-t border-zinc-200 dark:border-zinc-800 pt-8 text-left">
    <flux:heading size="lg" class="mb-4">Upload Payment Proof</flux:heading>
    <flux:subheading class="mb-6">
        Please upload a screenshot or photo of your transfer receipt
    </flux:subheading>

    <div class="space-y-6">
        <flux:file-upload wire:model="photos" multiple label="Upload files" accept="image/*">
            <flux:file-upload.dropzone
                heading="Drop files here or click to browse"
                text="JPG, PNG, GIF up to 10MB"
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

            <div class="mt-6">
                <flux:button wire:click="save" variant="primary" class="w-full py-4" wire:loading.attr="disabled">
                    <flux:icon.arrow-up-tray class="w-4 h-4 mr-2" wire:loading.remove />
                    <flux:icon.loading class="w-4 h-4 animate-spin mr-2" wire:loading />
                    Upload Proof
                </flux:button>
            </div>
        @endif

        @error('photos.*') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
        @error('photos') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
    </div>
</div>
