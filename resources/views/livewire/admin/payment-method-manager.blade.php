<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="2xl">Payment Methods</flux:heading>
            <p class="text-zinc-500 mt-1">Manage available payment options for customers</p>
        </div>
        <flux:button variant="primary" wire:click="create">
            Add Payment Method
        </flux:button>
    </div>

    <flux:card class="space-y-6">
        <flux:table container:class="max-h-80">
            <flux:table.columns>
                <flux:table.column text-zinc-500>Payment Method</flux:table.column>
                <flux:table.column text-zinc-500>Type</flux:table.column>
                <flux:table.column text-zinc-500>Details</flux:table.column>
                <flux:table.column text-zinc-500>Orders</flux:table.column>
                <flux:table.column text-zinc-500>Status</flux:table.column>
                <flux:table.column align="right"></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                {{-- Loading Skeletons (Matching actual row height and logo size) --}}
                @foreach(range(1, 5) as $i)
                    <flux:table.row wire:loading wire:target="toggleStatus, delete">
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-4">
                                <flux:skeleton class="w-10 h-10 rounded-lg shrink-0" />
                                <flux:skeleton class="w-32 h-4" />
                            </div>
                        </flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-48 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-12 h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-16 h-6 rounded-full" /></flux:table.cell>
                        <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Actual Data --}}
                @forelse($paymentMethods as $method)
                    <flux:table.row wire:loading.remove wire:target="toggleStatus, delete">
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-4">
                                @if($method->logo)
                                    <img src="{{ Storage::url($method->logo) }}" class="w-10 h-10 object-contain rounded-lg border border-zinc-200 dark:border-zinc-800" alt="{{ $method->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        @if($method->type === 'qris')
                                            <flux:icon.qr-code class="w-6 h-6 text-zinc-500" />
                                        @else
                                            <flux:icon.building-library class="w-6 h-6 text-zinc-500" />
                                        @endif
                                    </div>
                                @endif
                                <span class="font-bold text-sm">{{ $method->name }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-xs uppercase font-black text-zinc-500">{{ str_replace('_', ' ', $method->type) }}</span>
                        </flux:table.cell>

                        <flux:table.cell class="max-w-xs overflow-hidden text-ellipsis whitespace-nowrap">
                            @if($method->account_number)
                                <div class="text-xs font-mono text-zinc-600 dark:text-zinc-400">{{ $method->account_number }} ({{ $method->account_name }})</div>
                            @elseif($method->isQris())
                                <div class="text-xs text-zinc-600 dark:text-zinc-400">QRIS Configured</div>
                            @else
                                <span class="text-zinc-400 text-xs">N/A</span>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-bold tabular-nums text-sm">{{ $method->orders()->count() }}</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($method->is_active)
                                <flux:badge color="emerald" size="sm" inset="top bottom">Active</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown align="end">
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" inset="top bottom" />
                                <flux:menu>
                                    <flux:menu.item wire:click="edit({{ $method->id }})" icon="pencil-square">Edit</flux:menu.item>
                                    <flux:menu.item wire:click="toggleStatus({{ $method->id }})" icon="{{ $method->is_active ? 'eye-slash' : 'eye' }}">
                                        {{ $method->is_active ? 'Deactivate' : 'Activate' }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="delete({{ $method->id }})" wire:confirm="Are you sure?" icon="trash" class="text-red-500">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row wire:loading.remove wire:target="toggleStatus, delete">
                        <flux:table.cell colspan="6" class="text-center py-12 text-zinc-500">
                            No payment methods configured yet.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Modal --}}
    {{-- Modal --}}
    <flux:modal wire:model="showModal" name="payment-method-modal" class="md:w-[600px]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Payment Method' : 'Add Payment Method' }}</flux:heading>
                <flux:subheading>Configure payment options for customers</flux:subheading>
            </div>

            {{-- Type --}}
            <flux:field>
                <flux:label>Payment Type</flux:label>
                <flux:select wire:model.live="type" required>
                    <option value="bank_transfer" selected>Bank Transfer</option>
                    <option value="qris">QRIS</option>
                    <option value="ewallet">E-Wallet</option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            {{-- Name --}}
            <flux:field>
                <flux:label>Payment Method Name</flux:label>
                <flux:input type="text" wire:model="name" placeholder="e.g., BCA, Mandiri, QRIS" required />
                <flux:error name="name" />
                <flux:description>Display name shown to customers</flux:description>
            </flux:field>

            {{-- Bank Transfer Fields --}}
            @if($type === 'bank_transfer')
                <flux:field>
                    <flux:label>Account Number</flux:label>
                    <flux:input type="text" wire:model="account_number" placeholder="1234567890" />
                    <flux:error name="account_number" />
                </flux:field>

                <flux:field>
                    <flux:label>Account Name</flux:label>
                    <flux:input type="text" wire:model="account_name" placeholder="NEXACODE" />
                    <flux:error name="account_name" />
                </flux:field>
            @endif

            {{-- QRIS Fields --}}
            @if($type === 'qris')
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>QRIS Static Code</flux:label>
                        <flux:textarea wire:model="qris_static" id="qris_static_input" rows="4" placeholder="Paste your static QRIS code here"></flux:textarea>
                        <flux:error name="qris_static" />
                        <flux:description>The system will automatically convert this to dynamic QRIS with order amount</flux:description>
                    </flux:field>

                    <div class="p-4 bg-indigo-50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <div class="flex items-center gap-3 mb-3">
                            <flux:icon.qr-code class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            <div class="text-sm font-bold text-indigo-900 dark:text-indigo-100">Quick Extraction</div>
                        </div>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300 mb-4">Upload your QRIS image to automatically extract the static code.</p>
                        
                        <input type="file" id="qris_scanner_input" accept="image/*" class="hidden" onchange="extractQris(this)">
                        <flux:button type="button" variant="outline" size="sm" onclick="document.getElementById('qris_scanner_input').click()" class="w-full">
                            Upload & Scan QRIS
                        </flux:button>
                    </div>

                    <canvas id="qr-canvas" class="hidden"></canvas>
                </div>
            @endif

            {{-- Logo --}}
            <flux:field>
                <flux:label>Logo (Optional)</flux:label>

                <flux:file-upload wire:model="logo" accept="image/*">
                    <flux:file-upload.dropzone
                        heading="Drop logo here or click to browse"
                        text="JPG, PNG, GIF up to 1MB"
                        with-progress
                        inline
                    />
                </flux:file-upload>

                @if ($logo || ($editingId && $method = \App\Models\PaymentMethod::find($editingId)) && $method->logo)
                    <div class="mt-4 flex flex-col gap-2">
                        @if ($logo && is_object($logo) && method_exists($logo, 'temporaryUrl'))
                            <flux:file-item 
                                :heading="$logo->getClientOriginalName()" 
                                :image="$logo->temporaryUrl()" 
                                :size="$logo->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove wire:click="$set('logo', null)" />
                                </x-slot>
                            </flux:file-item>
                        @elseif ($editingId && ($method = \App\Models\PaymentMethod::find($editingId)) && $method->logo)
                            <flux:file-item 
                                :heading="basename($method->logo)" 
                                :image="Storage::url($method->logo)"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove wire:click="deleteLogo({{ $method->id }})" />
                                </x-slot>
                            </flux:file-item>
                        @endif
                    </div>
                @endif
                
                <flux:error name="logo" />
            </flux:field>

            {{-- Instructions --}}
            <flux:field>
                <flux:label>Payment Instructions (Optional)</flux:label>
                <div class="space-y-3">
                    @foreach($instructions as $index => $instruction)
                        <div class="flex gap-2">
                            <flux:input type="text" wire:model="instructions.{{ $index }}" placeholder="Step-by-step instruction" class="flex-1" />
                            <flux:button type="button" variant="ghost" wire:click="removeInstruction({{ $index }})">
                                <flux:icon.trash class="w-4 h-4" />
                            </flux:button>
                        </div>
                    @endforeach
                    <flux:button type="button" variant="outline" size="sm" wire:click="addInstruction">
                        Add Instruction
                    </flux:button>
                </div>
                <flux:description>Step-by-step instructions shown to customers</flux:description>
            </flux:field>

            {{-- Active Status --}}
            <flux:field>
                <div class="flex items-center gap-3">
                    <flux:checkbox wire:model="is_active" />
                    <flux:label>Active (visible to customers)</flux:label>
                </div>
                <flux:error name="is_active" />
            </flux:field>

            {{-- Sort Order --}}
            <flux:field>
                <flux:label>Sort Order</flux:label>
                <flux:input type="number" wire:model="sort_order" min="0" />
                <flux:error name="sort_order" />
                <flux:description>Lower numbers appear first</flux:description>
            </flux:field>

            {{-- Actions --}}
            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ $editingId ? 'Update Payment Method' : 'Create Payment Method' }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:click="closeModal">
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        window.extractQris = function(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.getElementById('qr-canvas');
                    const ctx = canvas.getContext('2d');
                    
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                    
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });

                    if (code) {
                        window.Livewire.find(input.closest('[wire\\:id]').getAttribute('wire:id')).set('qris_static', code.data);
                        
                        Flux.toast({
                            variant: 'success',
                            heading: 'QRIS Scanned',
                            text: 'Static code extracted successfully.'
                        });
                    } else {
                        Flux.toast({
                            variant: 'danger',
                            heading: 'Scan Failed',
                            text: 'Could not find a valid QR code in this image.'
                        });
                    }
                    
                    input.value = '';
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };
    </script>
@endonce

@script
    $wire.on('payment-method-saved', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Success',
            text: 'Payment method saved successfully'
        });
    });

    $wire.on('payment-method-deleted', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Success',
            text: 'Payment method deleted successfully'
        });
    });

    $wire.on('payment-method-toggled', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Success',
            text: 'Payment method status updated'
        });
    });
@endscript
