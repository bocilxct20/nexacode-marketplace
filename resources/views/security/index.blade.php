@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <flux:heading size="xl">Security Dashboard</flux:heading>
        <flux:subheading>Manage your account security settings</flux:subheading>
    </div>

    {{-- Security Status Cards --}}
    {{-- Security Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- 2FA Status --}}
        <flux:card class="p-6">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center border {{ $twoFactorEnabled ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-600' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-600' }}">
                        @if($twoFactorEnabled)
                            <flux:icon.shield-check class="w-6 h-6" />
                        @else
                            <flux:icon.exclamation-triangle class="w-6 h-6" />
                        @endif
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">Two-Factor Auth</flux:heading>
                        <flux:badge :color="$twoFactorEnabled ? 'emerald' : 'amber'" size="sm" class="uppercase font-black text-[9px] tracking-tighter">
                            {{ $twoFactorEnabled ? 'Protected' : 'At Risk' }}
                        </flux:badge>
                    </div>
                </div>

                <flux:text class="text-xs leading-relaxed">
                    {{ $twoFactorEnabled ? 'Your account is secured with a secondary verification code.' : 'Add an extra layer of security to your account by enabling 2FA.' }}
                </flux:text>

                <flux:button href="{{ route('two-factor.setup') }}" :variant="$twoFactorEnabled ? 'outline' : 'primary'" class="w-full">
                    {{ $twoFactorEnabled ? 'Manage Security' : 'Enable Now' }}
                </flux:button>
            </div>
        </flux:card>

        {{-- Password Status --}}
        <flux:card class="p-6">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center border {{ !$passwordNeedsChange ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-600' : 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800 text-rose-600' }}">
                        @if($passwordNeedsChange)
                            <flux:icon.key class="w-6 h-6" />
                        @else
                            <flux:icon.lock-closed class="w-6 h-6" />
                        @endif
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">Account Password</flux:heading>
                        <flux:badge :color="!$passwordNeedsChange ? 'emerald' : 'rose'" size="sm" class="uppercase font-black text-[9px] tracking-tighter">
                            {{ $passwordNeedsChange ? 'Update Needed' : 'Strong' }}
                        </flux:badge>
                    </div>
                </div>

                <flux:text class="text-xs leading-relaxed">
                    {{ $passwordNeedsChange ? 'Your password has not been changed recently. Update it for better security.' : 'Your password meets our security requirements and is up to date.' }}
                </flux:text>

                <flux:button href="{{ route('profile') }}" variant="outline" class="w-full">
                    Update Password
                </flux:button>
            </div>
        </flux:card>

        {{-- Activity Log --}}
        <flux:card class="p-6">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-600">
                        <flux:icon.chart-bar class="w-6 h-6" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">Recent Activity</flux:heading>
                        <flux:subheading class="text-[10px] uppercase font-black tracking-widest">
                            {{ $recentActivity->count() }} Events Recorded
                        </flux:subheading>
                    </div>
                </div>

                <flux:text class="text-xs leading-relaxed">
                    Monitor logins, security changes, and other sensitive account activities.
                </flux:text>

                <flux:button href="{{ route('security.logs') }}" variant="outline" class="w-full" icon="list-bullet">
                    View Full History
                </flux:button>
            </div>
        </flux:card>
    </div>

    {{-- Recent Activity --}}
    <flux:card class="p-8">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <flux:heading size="lg" class="font-bold">Security Audit Trail</flux:heading>
                    <flux:subheading>Monitor the most recent sensitive events on your account.</flux:subheading>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800/50">
                            <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">Event Action</th>
                            <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">IP Connection</th>
                            <th class="text-left py-4 px-4 text-[10px] font-black uppercase tracking-widest text-zinc-500 text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                        @forelse($recentActivity as $log)
                        <tr class="group hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tight
                                    {{ str_contains($log->action, 'failed') 
                                        ? 'bg-rose-50 text-rose-600 border border-rose-200/50 dark:bg-rose-900/20 dark:border-rose-800/50' 
                                        : 'bg-emerald-50 text-emerald-600 border border-emerald-200/50 dark:bg-emerald-900/20 dark:border-emerald-800/50' }}">
                                    @if(str_contains($log->action, 'failed'))
                                        <flux:icon.x-circle variant="mini" class="mr-1.5 w-3 h-3" />
                                    @else
                                        <flux:icon.check-circle variant="mini" class="mr-1.5 w-3 h-3" />
                                    @endif
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $log->ip_address }}</span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="text-sm text-zinc-500">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-zinc-400 italic">
                                No security events recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </flux:card>
</div>
@endsection
