<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Riwayat Login</flux:heading>
            <flux:subheading>Semua aktivitas keamanan pada akun kamu</flux:subheading>
        </div>
        <flux:button href="{{ route('profile') }}" variant="ghost" icon="arrow-left">
            Kembali
        </flux:button>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2">
        <flux:button
            wire:click="setFilter('all')"
            variant="{{ $filter === 'all' ? 'primary' : 'ghost' }}"
        >
            Semua
        </flux:button>
        <flux:button
            wire:click="setFilter('success')"
            variant="{{ $filter === 'success' ? 'primary' : 'ghost' }}"
        >
            ✅ Berhasil
        </flux:button>
        <flux:button
            wire:click="setFilter('failed')"
            variant="{{ $filter === 'failed' ? 'primary' : 'ghost' }}"
        >
            ❌ Gagal
        </flux:button>
    </div>

    {{-- Logs Table --}}
    <flux:card>
        @if($logs->isEmpty())
            <div class="py-16 text-center">
                <flux:icon name="shield-check" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                <flux:heading>Belum ada aktivitas</flux:heading>
                <flux:subheading>Riwayat login kamu akan muncul di sini.</flux:subheading>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left py-3 px-4 font-semibold text-zinc-600 dark:text-zinc-400">Aktivitas</th>
                            <th class="text-left py-3 px-4 font-semibold text-zinc-600 dark:text-zinc-400">IP Address</th>
                            <th class="text-left py-3 px-4 font-semibold text-zinc-600 dark:text-zinc-400">Device</th>
                            <th class="text-left py-3 px-4 font-semibold text-zinc-600 dark:text-zinc-400">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($logs as $log)
                            @php
                                $device = $this->parseDevice($log->user_agent ?? '');
                                $badge  = $this->getActionBadge($log->action);
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                {{-- Action --}}
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ $this->getActionLabel($log->action) }}
                                    </span>
                                    @if(!empty($log->data['provider']))
                                        <span class="ml-1 text-xs text-zinc-400">via {{ ucfirst($log->data['provider']) }}</span>
                                    @endif
                                </td>
                                {{-- IP --}}
                                <td class="py-3 px-4">
                                    <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                        {{ $log->ip_address ?? '—' }}
                                    </code>
                                </td>
                                {{-- Device --}}
                                <td class="py-3 px-4">
                                    <span class="text-zinc-700 dark:text-zinc-300">
                                        {{ $device['browser'] }} · {{ $device['os'] }}
                                    </span>
                                </td>
                                {{-- Time --}}
                                <td class="py-3 px-4 text-zinc-500 dark:text-zinc-400 text-xs whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                    <br>
                                    <span class="text-zinc-400 dark:text-zinc-500">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-4 border-t border-zinc-100 dark:border-zinc-800">
                {{ $logs->links() }}
            </div>
        @endif
    </flux:card>

</div>
