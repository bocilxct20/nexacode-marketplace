@php
    $isPremium = Auth::user()->isPro() || Auth::user()->isElite();
@endphp

<div class="space-y-8 relative">
    {{-- Absolute overlay for non-premium users --}}
    @if(!$isPremium)
        <div class="absolute inset-0 z-50 flex flex-col items-center justify-start pt-24 backdrop-blur-[2px] bg-white/10 dark:bg-zinc-950/10 pointer-events-none">
            <flux:card class="max-w-md w-full shadow-2xl border-2 border-indigo-500/20 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl pointer-events-auto animate-in zoom-in-95 duration-300">
                <div class="flex flex-col items-center text-center p-6 space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center">
                        <flux:icon.ticket class="w-8 h-8 text-indigo-600" />
                    </div>
                    
                    <div>
                        <flux:heading size="lg" class="font-black">Fitur Premium: Kupon</flux:heading>
                        <flux:subheading class="mt-2 text-sm">
                            Tingkatkan penjualan kamu dengan kode diskon kustom. Fitur ini eksklusif untuk penulis **Pro** dan **Elite**.
                        </flux:subheading>
                    </div>

                    <div class="w-full space-y-3 pt-2">
                        <flux:button variant="primary" class="w-full bg-indigo-600 hover:bg-indigo-700" href="{{ route('author.plans') }}">
                            Coba Gratis 7 Hari
                        </flux:button>
                        <flux:button variant="ghost" class="w-full" href="{{ route('author.dashboard') }}">
                            Kembali ke Dashboard
                        </flux:button>
                    </div>

                    <div class="flex items-center gap-4 pt-2 opacity-50 grayscale">
                        <div class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                            <flux:icon.check-circle variant="mini" class="w-3 h-3" />
                            <span>Analytics</span>
                        </div>
                        <div class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                            <flux:icon.check-circle variant="mini" class="w-3 h-3" />
                            <span>Bulk Mail</span>
                        </div>
                        <div class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                            <flux:icon.check-circle variant="mini" class="w-3 h-3" />
                            <span>Low Commision</span>
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>
    @endif

    <div class="flex items-center justify-between {{ !$isPremium ? 'opacity-40 grayscale pointer-events-none select-none' : '' }}">
        <div>
            <flux:heading size="xl" class="font-bold">Manajer Kupon</flux:heading>
            <flux:subheading>Buat dan kelola kode diskon untuk produk kamu.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create" :disabled="!$isPremium">Buat Kupon</flux:button>
    </div>

    <flux:card class="p-0 overflow-hidden {{ !$isPremium ? 'opacity-30 grayscale pointer-events-none select-none' : '' }}">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Kode</flux:table.column>
                <flux:table.column>Tipe</flux:table.column>
                <flux:table.column>Nilai</flux:table.column>
                <flux:table.column>Penggunaan</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Kadaluarsa</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($coupons as $coupon)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono font-black text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded-lg border border-indigo-100 dark:border-indigo-800">{{ $coupon->code }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="capitalize">
                            {{ match($coupon->type) {
                                'percentage' => 'Persentase',
                                'fixed' => 'Tetap',
                                default => $coupon->type
                            } }}
                        </flux:table.cell>
                        <flux:table.cell class="font-bold">
                            @if($coupon->type === 'percentage')
                                {{ number_format($coupon->value, 0) }}%
                            @else
                                Rp {{ number_format($coupon->value, 0, ',', '.') }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-bold tabular-nums">{{ $coupon->usage_count }}</span>
                            @if($coupon->usage_limit)
                                <span class="text-zinc-400 text-xs">/ {{ $coupon->usage_limit }}</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$coupon->status === 'active' ? 'emerald' : ($coupon->status === 'inactive' ? 'zinc' : 'red')" size="sm" class="uppercase font-black tracking-tighter">
                                {{ match($coupon->status) {
                                    'active' => 'Aktif',
                                    'inactive' => 'Non-aktif',
                                    'expired' => 'Kadaluarsa',
                                    default => $coupon->status
                                } }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm {{ $coupon->expires_at && $coupon->expires_at->isPast() ? 'text-red-500 line-through' : '' }}">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'Selamanya' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button.group>
                                <flux:button variant="ghost" square icon="pencil-square" size="sm" wire:click="edit({{ $coupon->id }})" />
                                <flux:button variant="ghost" square icon="power" size="sm" :class="$coupon->status === 'active' ? 'text-zinc-400' : 'text-emerald-500'" wire:click="toggleStatus({{ $coupon->id }})" />
                                <flux:button variant="ghost" square icon="trash" size="sm" class="text-red-500" wire:confirm="Apakah kamu yakin ingin menghapus kupon ini?" wire:click="delete({{ $coupon->id }})" />
                            </flux:button.group>
                        </flux:table.cell>
                    </flux:row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-12 text-zinc-500">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.ticket class="w-12 h-12 text-zinc-200" />
                                <p>Belum ada kupon. Buat kode diskon pertama kamu untuk meningkatkan penjualan!</p>
                            </div>
                        </flux:table.cell>
                    </flux:row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="p-4">
            {{ $coupons->links() }}
        </div>
    </flux:card>

    {{-- Create/Edit Modal --}}
    <flux:modal profile="create-coupon-modal" class="md:w-[600px]" wire:model="showCreateModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingCouponId ? 'Edit Kupon' : 'Buat Kupon Baru' }}</flux:heading>
                <flux:subheading>Atur setelan diskon kamu di bawah ini.</flux:subheading>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Kode Kupon</flux:label>
                        <flux:input wire:model="code" placeholder="DISKONHEMAT50" class="font-mono uppercase" />
                        <flux:error for="code" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model="status">
                            <option value="active">Aktif</option>
                            <option value="inactive">Non-aktif</option>
                        </flux:select>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Deskripsi (Internal)</flux:label>
                    <flux:input wire:model="description" placeholder="Promo Kilat Januari 2026" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Tipe Diskon</flux:label>
                        <flux:select wire:model="type">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Jumlah Tetap (Rp)</option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Nilai Diskon</flux:label>
                        <flux:input wire:model="value" type="number" placeholder="{{ $type === 'percentage' ? '20' : '50000' }}" />
                        <flux:error for="value" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Minimal Pembelian (Opsional)</flux:label>
                        <flux:input wire:model="min_purchase" type="number" placeholder="100000" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Batas Penggunaan (Total)</flux:label>
                        <flux:input wire:model="usage_limit" type="number" placeholder="100" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Tanggal Kadaluarsa</flux:label>
                    <flux:input wire:model="expires_at" type="datetime-local" />
                </flux:field>

                <flux:field>
                    <flux:label>Terapkan ke Produk</flux:label>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-h-48 overflow-y-auto space-y-2">
                        @foreach($availableProducts as $product)
                            <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                <input type="checkbox" wire:model="selectedProducts" value="{{ $product->id }}" class="rounded border-zinc-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="text-sm font-medium">{{ $product->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <flux:description>Jika tidak ada produk yang dipilih, kupon berlaku untuk semua produk kamu.</flux:description>
                </flux:field>

                <div class="flex gap-2 justify-end pt-4">
                    <flux:button variant="ghost" wire:click="$set('showCreateModal', false)">Batal</flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ $editingCouponId ? 'Simpan Perubahan' : 'Buat Kupon' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
