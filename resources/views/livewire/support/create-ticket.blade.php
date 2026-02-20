<flux:modal name="create-ticket-modal" class="md:w-[600px]">
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl">
                <flux:icon.plus-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <flux:heading size="xl">Buat Tiket Bantuan Baru</flux:heading>
                <flux:subheading>Butuh bantuan dengan produk yang kamu beli? Isi formulir di bawah ini.</flux:subheading>
            </div>
        </div>

        <flux:separator variant="subtle" />

        <form wire:submit.prevent="saveTicket" class="space-y-6">
            @error('general')
                <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 text-sm mb-4">
                    {{ $message }}
                </div>
            @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Pilih Produk</flux:label>
                    <flux:select wire:model="product_id" placeholder="Pilih produk yang bermasalah...">
                        @foreach($products as $product)
                            <flux:select.option value="{{ $product->id }}">{{ $product->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error for="product_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Prioritas</flux:label>
                    <flux:select wire:model="priority">
                        <flux:select.option value="low">Rendah - Pertanyaan Umum</flux:select.option>
                        <flux:select.option value="medium">Sedang - Masalah Teknis</flux:select.option>
                        <flux:select.option value="high">Tinggi - Error Fatal / Bug</flux:select.option>
                    </flux:select>
                    <flux:error for="priority" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Subjek</flux:label>
                <flux:input wire:model="subject" placeholder="Ringkasan masalah (misal: Error saat instalasi)" />
                <flux:error for="subject" />
            </flux:field>

            <flux:field>
                <flux:label>Pesan</flux:label>
                <flux:textarea wire:model="message" placeholder="Jelaskan masalah kamu secara detail..." rows="5" />
                <flux:error for="message" />
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-6">
                <flux:button variant="ghost" x-on:click="Flux.modal('create-ticket-modal').close()">Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-10" wire:loading.attr="disabled">
                    Kirim Tiket
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
