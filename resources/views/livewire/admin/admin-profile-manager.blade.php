<div wire:init="loadData" class="space-y-8">
    {{-- Tab Navigation --}}
    <flux:navbar class="mb-8">
        <flux:navbar.item wire:click="setTab('profile')" :current="$tab === 'profile'">Profile</flux:navbar.item>
        <flux:navbar.item wire:click="setTab('security')" :current="$tab === 'security'">Security</flux:navbar.item>
        <flux:navbar.item wire:click="setTab('notifications')" :current="$tab === 'notifications'">Notifications</flux:navbar.item>
        <flux:navbar.item wire:click="setTab('activity')" :current="$tab === 'activity'">Activity</flux:navbar.item>
    </flux:navbar>

    <div class="relative min-h-[400px]">
        {{-- Loading Skeleton Overlay (Faded but layout-preserving) --}}
        <div wire:loading wire:target="setTab, loadData" class="absolute inset-0 z-10 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-sm flex items-center justify-center rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
            <div class="space-y-6 w-full">
                <div class="flex items-center gap-4">
                    <flux:skeleton class="size-16 rounded-full" />
                    <div class="space-y-2">
                        <flux:skeleton class="h-6 w-48" />
                        <flux:skeleton class="h-4 w-32" />
                    </div>
                </div>
                <flux:separator />
                <div class="space-y-4">
                    <div class="space-y-2"><flux:skeleton class="h-4 w-24" /><flux:skeleton class="h-10 w-full" /></div>
                    <div class="space-y-2"><flux:skeleton class="h-4 w-24" /><flux:skeleton class="h-10 w-full" /></div>
                    <div class="space-y-2"><flux:skeleton class="h-4 w-24" /><flux:skeleton class="h-32 w-full" /></div>
                </div>
            </div>
        </div>

        @if(!$readyToLoad)
            {{-- Initial Load Skeleton (Matches the layout exactly) --}}
            <flux:card class="p-6 space-y-6 animate-pulse">
                <div class="flex items-center gap-4">
                    <flux:skeleton class="size-16 rounded-full" />
                    <div class="space-y-2">
                        <flux:skeleton class="h-6 w-48" />
                        <flux:skeleton class="h-4 w-32" />
                    </div>
                </div>
                <flux:separator />
                <div class="space-y-4">
                    @foreach(range(1, 3) as $i)
                        <div class="space-y-2 text-zinc-300 dark:text-zinc-700">
                            <flux:skeleton class="h-4 w-24" />
                            <flux:skeleton class="h-10 w-full" />
                        </div>
                    @endforeach
                </div>
            </flux:card>
        @else
            {{-- Profile Tab --}}
            @if($tab === 'profile')
                <flux:card class="space-y-6">
                    <div class="flex items-center gap-3">
                        <flux:icon.user-circle class="w-6 h-6 text-zinc-500" />
                        <flux:heading size="lg">Profile Information</flux:heading>
                    </div>
                    
                    <flux:separator />
                    
                    <form wire:submit="updateProfile" class="space-y-6">
                        <div class="space-y-3">
                            <flux:label>Profile Picture</flux:label>
                            <div class="flex items-start gap-4">
                                <flux:avatar 
                                    :src="$avatar ? $avatar->temporaryUrl() : ($user->avatar ? asset('storage/' . $user->avatar) : null)" 
                                    :initials="$user->initials" 
                                    size="xl" 
                                />
                                <div class="flex-1">
                                    <flux:file-upload wire:model="avatar">
                                        <flux:file-upload.dropzone
                                            heading="Drop avatar or click to browse"
                                            text="JPG, PNG or GIF. Max 2MB"
                                            inline
                                        />
                                    </flux:file-upload>
                                    <flux:error name="avatar" />
                                </div>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input wire:model="name" placeholder="Your full name" required />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Email Address</flux:label>
                            <flux:input wire:model="email" type="email" placeholder="admin@nexacode.com" required />
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Bio</flux:label>
                            <flux:textarea wire:model="bio" placeholder="I'm an admin at NEXACODE..." rows="4" />
                            <flux:description>Tell us a little bit about yourself.</flux:description>
                            <flux:error name="bio" />
                        </flux:field>

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary">Save profile</flux:button>
                        </div>
                    </form>
                </flux:card>
            @endif

            {{-- Security Tab --}}
            @if($tab === 'security')
                <div class="space-y-8">
                    <flux:card class="space-y-6">
                        <div class="flex items-center gap-3">
                            <flux:icon.shield-check class="w-6 h-6 text-zinc-500" />
                            <flux:heading size="lg">Two-Factor Authentication</flux:heading>
                        </div>
                        <flux:separator />
                        <div class="space-y-4">
                            <flux:subheading>Add an extra layer of security to your account.</flux:subheading>
                            <flux:switch 
                                :checked="$user->two_factor_enabled" 
                                label="Enable Two-Factor Authentication"
                            />
                        </div>
                    </flux:card>

                    <flux:card class="space-y-6">
                        <div class="flex items-center gap-3">
                            <flux:icon.lock-closed class="w-6 h-6 text-zinc-500" />
                            <flux:heading size="lg">Change Password</flux:heading>
                        </div>
                        <flux:separator />
                        <form wire:submit="updatePassword" class="space-y-6">
                            <flux:field>
                                <flux:label>Current Password</flux:label>
                                <flux:input type="password" wire:model="current_password" required />
                                <flux:error name="current_password" />
                            </flux:field>
                            <flux:field>
                                <flux:label>New Password</flux:label>
                                <flux:input type="password" wire:model="password" required />
                                <flux:error name="password" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Confirm New Password</flux:label>
                                <flux:input type="password" wire:model="password_confirmation" required />
                            </flux:field>
                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary">Update password</flux:button>
                            </div>
                        </form>
                    </flux:card>
                </div>
            @endif

            {{-- Notifications Tab --}}
            @if($tab === 'notifications')
                <flux:card class="space-y-6">
                    <div class="flex items-center gap-3">
                        <flux:icon.bell class="w-6 h-6 text-zinc-500" />
                        <flux:heading size="lg">Notification Preferences</flux:heading>
                    </div>
                    <flux:separator />
                    <form wire:submit="updateNotifications" class="space-y-6">
                        <div class="space-y-4">
                            @foreach(['important_activity' => 'Important Activity', 'weekly_reports' => 'Weekly Reports', 'system_updates' => 'System Updates', 'security_alerts' => 'Security Alerts'] as $key => $label)
                                <flux:switch 
                                    wire:model="notifications.{{ $key }}"
                                    :label="$label"
                                />
                                <flux:separator variant="subtle" />
                            @endforeach
                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary">Save preferences</flux:button>
                            </div>
                        </div>
                    </form>
                </flux:card>
            @endif

            {{-- Activity Tab --}}
            @if($tab === 'activity')
                <div class="space-y-8">
                    <flux:card class="space-y-6">
                        <div class="flex items-center gap-3">
                            <flux:icon.clock class="w-6 h-6 text-zinc-500" />
                            <flux:heading size="lg">Recent Activity</flux:heading>
                        </div>
                        <flux:separator />
                        @if($user->last_login_at)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                                    <flux:subheading class="text-xs uppercase tracking-wider mb-1">Last Login</flux:subheading>
                                    <div class="font-semibold">{{ $user->last_login_at->diffForHumans() }}</div>
                                    <div class="text-sm text-zinc-500">{{ $user->last_login_at->format('M d, Y g:i A') }}</div>
                                </div>
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                                    <flux:subheading class="text-xs uppercase tracking-wider mb-1">IP Address</flux:subheading>
                                    <div class="font-semibold font-mono">{{ $user->last_login_ip ?? 'N/A' }}</div>
                                </div>
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                                    <flux:subheading class="text-xs uppercase tracking-wider mb-1">Device</flux:subheading>
                                    <div class="font-semibold">{{ $user->last_login_device ?? 'Unknown' }}</div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-zinc-500 italic">No recent activity recorded</div>
                        @endif
                    </flux:card>

                    <flux:card class="space-y-6 border-red-200 dark:border-red-900">
                        <div class="flex items-center gap-3">
                            <flux:icon.exclamation-triangle class="w-6 h-6 text-red-600" />
                            <flux:heading size="lg" class="text-red-600 dark:text-red-400">Danger Zone</flux:heading>
                        </div>
                        <flux:separator variant="subtle" />
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                <div>
                                    <div class="font-semibold text-red-900 dark:text-red-100">Deactivate Account</div>
                                    <div class="text-sm text-red-700 dark:text-red-300">Temporarily disable your admin account</div>
                                </div>
                                <flux:button variant="danger" size="sm">Deactivate</flux:button>
                            </div>
                        </div>
                    </flux:card>
                </div>
            @endif
        @endif
    </div>
</div>
