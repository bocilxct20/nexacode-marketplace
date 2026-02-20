<div class="space-y-6">
    <div class="flex items-center gap-4 mb-2">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
            <flux:icon.credit-card />
        </div>
        <div>
            <flux:heading size="lg" class="font-black">Pilih Metode Pembayaran</flux:heading>
            <flux:subheading>Silakan pilih metode pembayaran untuk menyelesaikan pesanan kamu.</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($methods as $method)
            <button 
                wire:click="selectMethod({{ $method->id }})"
                class="relative flex items-center p-5 border-2 rounded-2xl cursor-pointer transition-all hover:border-indigo-500 hover:bg-indigo-50/10 dark:hover:bg-indigo-900/10 group {{ $selectedMethodId == $method->id ? 'border-indigo-600 bg-indigo-50/30 dark:bg-indigo-900/20' : 'border-zinc-200 dark:border-zinc-800' }}"
            >
                <div class="flex-1 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center overflow-hidden p-1.5 shadow-sm group-hover:scale-105 transition-transform">
                        @if($method->logo)
                            <img src="{{ Storage::url($method->logo) }}" alt="{{ $method->name }}" class="max-w-full max-h-full object-contain">
                        @else
                            <flux:icon.credit-card variant="mini" class="text-zinc-400" />
                        @endif
                    </div>
                    <div class="text-left">
                        <div class="font-black text-zinc-900 dark:text-white">{{ $method->name }}</div>
                        <div class="text-[10px] text-zinc-500 uppercase font-black tracking-widest mt-0.5">{{ $method->type }}</div>
                    </div>
                </div>

                @if($selectedMethodId == $method->id)
                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white">
                        <flux:icon.check variant="mini" />
                    </div>
                @else
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.chevron-right variant="mini" class="text-zinc-400" />
                    </div>
                @endif
            </button>
        @endforeach
    </div>
    
    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30 rounded-2xl flex gap-3">
        <flux:icon.information-circle variant="mini" class="text-amber-600 shrink-0 mt-0.5" />
        <flux:text size="xs" class="text-amber-800 dark:text-amber-400 leading-relaxed">
            Pilih salah satu metode di atas. Jika kamu menggunakan QRIS, kode QR akan otomatis dibuat setelah kamu memilih. Untuk Transfer Bank, kamu harus mengunggah bukti pembayaran setelah transfer.
        </flux:text>
    </div>
</div>
