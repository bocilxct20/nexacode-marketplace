@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Security Logs</flux:heading>
            <flux:subheading>View your account activity history</flux:subheading>
        </div>
        
        <flux:button href="{{ route('security.logs.export') }}" variant="outline">
            📥 Export Logs
        </flux:button>
    </div>

    {{-- Filters --}}
    <flux:card class="p-6">
        <form method="GET" action="{{ route('security.logs') }}" class="flex gap-4">
            <div class="flex-1">
                <flux:label>Action Type</flux:label>
                <flux:select name="action">
                    <option value="">All Actions</option>
                    <option value="login_success" {{ request('action') === 'login_success' ? 'selected' : '' }}>Login Success</option>
                    <option value="login_failed" {{ request('action') === 'login_failed' ? 'selected' : '' }}>Login Failed</option>
                    <option value="2fa_enabled" {{ request('action') === '2fa_enabled' ? 'selected' : '' }}>2FA Enabled</option>
                    <option value="2fa_disabled" {{ request('action') === '2fa_disabled' ? 'selected' : '' }}>2FA Disabled</option>
                    <option value="2fa_verified" {{ request('action') === '2fa_verified' ? 'selected' : '' }}>2FA Verified</option>
                    <option value="password_changed" {{ request('action') === 'password_changed' ? 'selected' : '' }}>Password Changed</option>
                </flux:select>
            </div>

            <div class="flex-1">
                <flux:label>From Date</flux:label>
                <flux:input type="date" name="from" value="{{ request('from') }}" />
            </div>

            <div class="flex-1">
                <flux:label>To Date</flux:label>
                <flux:input type="date" name="to" value="{{ request('to') }}" />
            </div>

            <div class="flex items-end gap-2">
                <flux:button type="submit" variant="primary">
                    Filter
                </flux:button>
                
                <flux:button href="{{ route('security.logs') }}" variant="ghost">
                    Clear
                </flux:button>
            </div>
        </form>
    </flux:card>

    {{-- Logs Table --}}
    <flux:card class="p-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800/50">
                        <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">Date & Time</th>
                        <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">Security Action</th>
                        <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">IP Address</th>
                        <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">User Agent</th>
                        <th class="text-right py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($logs as $log)
                    <tr class="group hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="py-4 px-4 text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y • H:i:s') }}
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tight
                                {{ str_contains($log->action, 'failed') ? 'bg-rose-50 text-rose-600 border border-rose-200/50 dark:bg-rose-900/20 dark:border-rose-800/50' : 
                                   (str_contains($log->action, 'success') || str_contains($log->action, 'enabled') ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/50 dark:bg-emerald-900/20 dark:border-emerald-800/50' : 
                                   'bg-blue-50 text-blue-600 border border-blue-200/50 dark:bg-blue-900/20 dark:border-blue-800/50') }}">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm font-mono text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                            {{ $log->ip_address }}
                        </td>
                        <td class="py-4 px-4 text-xs text-zinc-500 max-w-xs truncate" title="{{ $log->user_agent }}">
                            {{ $log->user_agent }}
                        </td>
                        <td class="py-4 px-4 text-right">
                            @if($log->response_status)
                                <flux:badge :color="$log->response_status >= 200 && $log->response_status < 300 ? 'emerald' : 'rose'" size="sm" class="font-black tabular-nums">
                                    {{ $log->response_status }}
                                </flux:badge>
                            @else
                                <span class="text-zinc-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-zinc-400 italic">
                            No security logs found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="mt-6 px-4">
            {{ $logs->links() }}
        </div>
        @endif
    </flux:card>
</div>
@endsection
