<div class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Profile Settings</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    {{-- Success/Toast notification logic is handled via dispatching from component --}}
    
    {{-- Tab Navigation --}}
    <div class="flex items-center">
        <flux:tabs class="w-full">
            <flux:tab :current="$tab === 'profile'" wire:click="setTab('profile')" class="cursor-pointer">Profile</flux:tab>
            <flux:tab :current="$tab === 'security'" wire:click="setTab('security')" class="cursor-pointer">Security</flux:tab>
            @if($user->isAuthor())
                <flux:tab :current="$tab === 'payout'" wire:click="setTab('payout')" class="cursor-pointer">Payout Settings</flux:tab>
            @endif
            <flux:tab :current="$tab === 'notifications'" wire:click="setTab('notifications')" class="cursor-pointer">Notifications</flux:tab>
            <flux:tab :current="$tab === 'activity'" wire:click="setTab('activity')" class="cursor-pointer">Activity</flux:tab>
        </flux:tabs>
    </div>

    {{-- Profile Tab --}}
    @if($tab === 'profile')
        <flux:card class="p-8 space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon.user-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="lg">Profile Information</flux:heading>
                    <flux:subheading>Update your basic account details and public bio.</flux:subheading>
                </div>
            </div>
            
            <flux:separator variant="subtle" />
            
            <form wire:submit.prevent="updateProfile" class="space-y-8">
                {{-- Avatar Upload --}}
                <div class="space-y-4">
                    <flux:label>Profile Picture</flux:label>
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <x-user-avatar 
                                :user="$user"
                                :src="$avatar ? $avatar->temporaryUrl() : ($currentAvatar ? asset('storage/' . $currentAvatar) : null)" 
                                size="xl" 
                                thickness="4"
                                class="rounded-[2.5rem] shadow-xl"
                            />
                            <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/40 rounded-[2.5rem] flex items-center justify-center">
                                <flux:icon.loading class="w-8 h-8 text-white animate-spin" />
                            </div>
                        </div>
                        <div class="flex-1 max-w-sm">
                            <flux:file-upload wire:model="avatar" label:sr-only>
                                <flux:file-upload.dropzone
                                    heading="Change avatar"
                                    text="Tap to browse"
                                    inline
                                />
                            </flux:file-upload>
                            <flux:error for="avatar" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Full Name</flux:label>
                        <flux:input wire:model="name" placeholder="John Doe" autocomplete="name" />
                        <flux:error for="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="john@example.com" autocomplete="email" />
                        <flux:error for="email" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Bio</flux:label>
                    <flux:textarea wire:model="bio" placeholder="Tell us about yourself..." rows="4" />
                    <flux:error for="bio" />
                    <p class="text-xs text-zinc-500 mt-2">Maximum 1000 characters.</p>
                </flux:field>

                {{-- Elite Branding Section --}}
                @if($user->isElite())
                    <flux:separator variant="subtle" />

                    <div class="space-y-6">
                        <div class="p-6 bg-amber-500 rounded-2xl border border-white dark:border-zinc-900 shadow-xl flex items-center gap-4 relative overflow-hidden group">
                            <div class="p-3 bg-white/20 rounded-xl border border-white/30">
                                <flux:icon.sparkles class="w-8 h-8 text-white" />
                            </div>
                            <div>
                                <flux:heading size="lg" class="text-white font-black">Elite Storefront Branding</flux:heading>
                                <flux:subheading class="text-white/80 font-bold">Exclusive perks for Elite authors. Stand out from the crowd.</flux:subheading>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>Storefront Welcome Message</flux:label>
                                <flux:input wire:model="storefront_message" placeholder="Premium Scripts & Themes by {{ $user->name }}" />
                                <flux:error for="storefront_message" />
                                <flux:description>Displayed as a prominent headline on your profile.</flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label>Brand Primary Color</flux:label>
                                <div class="flex items-center gap-3">
                                    <flux:input wire:model="brand_color" type="color" class="w-12 h-10 p-1" />
                                    <flux:input wire:model="brand_color" placeholder="#10b981" class="flex-1" />
                                </div>
                                <flux:error for="brand_color" />
                            </flux:field>
                        </div>

                        <div class="space-y-4">
                            <flux:label>Author Banner / Cover Image</flux:label>
                            <div class="space-y-4">
                                @if($cover_image)
                                    <div class="relative h-40 w-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                                        <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-full object-cover">
                                    </div>
                                @elseif($currentCoverImage)
                                    <div class="relative h-40 w-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                                        <img src="{{ asset('storage/' . $currentCoverImage) }}" class="w-full h-full object-cover">
                                    </div>
                                @endif

                                <flux:file-upload wire:model="cover_image" label:sr-only>
                                    <flux:file-upload.dropzone
                                        heading="Upload your banner"
                                        text="Recommended size: 1200x400px (Max 5MB)"
                                        inline
                                    />
                                </flux:file-upload>
                                <flux:error for="cover_image" />
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary" class="px-8" wire:loading.attr="disabled">
                        <flux:icon.check variant="mini" class="mr-2" /> Save Changes
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Security Tab --}}
    @if($tab === 'security')
        <div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
            {{-- Password Change --}}
            <flux:card class="p-8 space-y-8">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
                        <flux:icon.lock-closed class="w-6 h-6 text-rose-600 dark:text-rose-400" />
                    </div>
                    <div>
                        <flux:heading size="lg">Update Password</flux:heading>
                        <flux:subheading>Ensure your account is using a long, random password to stay secure.</flux:subheading>
                    </div>
                </div>
                
                <flux:separator variant="subtle" />
                
                <form wire:submit.prevent="updatePassword" class="max-w-xl space-y-6">
                    <flux:field>
                        <flux:label>Current Password</flux:label>
                        <flux:input wire:model="current_password" type="password" placeholder="••••••••" />
                        <flux:error for="current_password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>New Password</flux:label>
                        <flux:input wire:model="password" type="password" placeholder="••••••••" />
                        <flux:error for="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Confirm New Password</flux:label>
                        <flux:input wire:model="password_confirmation" type="password" placeholder="••••••••" />
                    </flux:field>

                    <div class="flex justify-end pt-4">
                        <flux:button type="submit" variant="primary" class="bg-rose-600 hover:bg-rose-700">Update Password</flux:button>
                    </div>
                </form>
            </flux:card>

            {{-- 2FA Placeholder (Logic handled elsewhere but integrated here for UX) --}}
            @if($user->isAdmin())
                <flux:card class="p-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                <flux:icon.shield-check class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <flux:heading size="lg">Two-Factor Authentication</flux:heading>
                                <flux:subheading>Add an extra layer of security to your account.</flux:subheading>
                            </div>
                        </div>
                        <flux:button variant="outline" href="{{ route('two-factor.setup') }}">Configure 2FA</flux:button>
                    </div>
                    @if($user->two_factor_enabled)
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800 flex items-center gap-3">
                            <flux:icon.check-circle variant="mini" class="text-emerald-600" />
                            <span class="text-sm font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-tighter">Two-factor authentication is currently active</span>
                        </div>
                    @endif
                </flux:card>
            @endif
        </div>
    @endif

    {{-- Payout Tab --}}
    @if($tab === 'payout' && $user->isAuthor())
        <flux:card class="p-8 space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.building-library class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="lg">Payout Settings</flux:heading>
                    <flux:subheading>Manage your bank account details for earnings withdrawals.</flux:subheading>
                </div>
            </div>
            
            <flux:separator variant="subtle" />
            
            <form wire:submit.prevent="updatePayoutSettings" class="max-w-xl space-y-6">
                <flux:field>
                    <flux:label>Bank Name</flux:label>
                    <flux:input wire:model="bank_name" placeholder="e.g. BCA, Mandiri, BNI" />
                    <flux:error for="bank_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Account Number</flux:label>
                    <flux:input wire:model="bank_account_number" placeholder="e.g. 1234567890" />
                    <flux:error for="bank_account_number" />
                </flux:field>

                <flux:field>
                    <flux:label>Account Holder Name</flux:label>
                    <flux:input wire:model="bank_account_name" placeholder="e.g. John Doe" />
                    <flux:error for="bank_account_name" />
                </flux:field>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-start gap-3">
                        <flux:icon.information-circle variant="mini" class="text-zinc-500 mt-0.5" />
                        <p class="text-xs text-zinc-500 leading-relaxed">
                            Please ensure your bank information is accurate. Errors in bank details may lead to delayed or failed payout processing.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary" class="bg-blue-600 hover:bg-blue-700">Save Payout Settings</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Notifications Tab --}}
    @if($tab === 'notifications')
        <flux:card class="p-8 space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.bell class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:heading size="lg">Notification Preferences</flux:heading>
                    <flux:subheading>Customize how and when we reach out to you.</flux:subheading>
                </div>
            </div>
            
            <flux:separator variant="subtle" />
            
            <form wire:submit.prevent="updateNotifications" class="space-y-2">
                <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-colors">
                    <flux:switch wire:model="pref_order_confirmations" label="Konfirmasi Pesanan" description="Terima detail transaksi dan bukti pembayaran setiap kali kamu membeli produk." />
                </div>
                
                <flux:separator variant="subtle" />
                
                @if($user->isAuthor())
                    <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-colors">
                        <flux:switch wire:model="pref_sale_notifications" label="Notifikasi Penjualan" description="Dapatkan kabar gembira langsung di inbox kamu setiap ada produk yang terjual." />
                    </div>

                    <flux:separator variant="subtle" />

                    <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-colors">
                        <flux:switch wire:model="pref_withdrawal_notifications" label="Status Penarikan Dana" description="Update mengenai proses pengiriman uang dari dashboard pendapatan kamu." />
                    </div>

                    <flux:separator variant="subtle" />
                @endif

                <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-colors">
                    <flux:switch wire:model="pref_product_updates" label="Pembaruan Produk (NexaUpdate)" description="Notifikasi otomatis jika produk yang kamu beli merilis versi baru atau perbaikan bug." />
                </div>

                <flux:separator variant="subtle" />

                <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-colors">
                    <flux:switch wire:model="pref_review_notifications" label="Pengingat Ulasan" description="Kami akan mengirimkan pengingat ramah untuk memberikan rating pada produk yang telah kamu beli." />
                </div>

                <flux:separator variant="subtle" />

                <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-xl transition-colors">
                    <flux:switch wire:model="pref_marketing_emails" label="Promo & Rekomendasi" description="Dapatkan informasi diskon eksklusif dan rekomendasi aset digital terbaik untuk kamu." />
                </div>

                <div class="flex justify-end pt-8">
                    <flux:button type="submit" variant="primary">Simpan Preferensi</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Activity Tab --}}
    @if($tab === 'activity')
        <flux:card class="p-8 space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
                </div>
                <div>
                    <flux:heading size="lg">Login Activity</flux:heading>
                    <flux:subheading>Monitor where and when you've accessed your account.</flux:subheading>
                </div>
            </div>
            
            <flux:separator variant="subtle" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Last Login --}}
                <flux:card class="p-6 space-y-4 group hover:ring-1 hover:ring-zinc-200 dark:hover:ring-zinc-700 transition-all">
                    <flux:subheading size="xs" class="font-black uppercase tracking-widest">Last Login</flux:subheading>
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl shadow-inner">
                            <flux:icon.calendar variant="mini" class="text-zinc-500" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="leading-none mb-1">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</flux:heading>
                            <flux:text size="xs" class="text-zinc-500">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y @ H:i') : '-' }}</flux:text>
                        </div>
                    </div>
                </flux:card>

                {{-- IP Address --}}
                <flux:card class="p-6 space-y-4 group hover:ring-1 hover:ring-zinc-200 dark:hover:ring-zinc-700 transition-all">
                    <flux:subheading size="xs" class="font-black uppercase tracking-widest">IP Address</flux:subheading>
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl shadow-inner">
                            <flux:icon.globe-alt variant="mini" class="text-zinc-500" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="font-mono leading-none mb-1">{{ $user->last_login_ip ?? 'N/A' }}</flux:heading>
                            <flux:text size="xs" class="uppercase font-bold tracking-tighter text-zinc-400">Current Session</flux:text>
                        </div>
                    </div>
                </flux:card>

                {{-- Device & OS --}}
                <flux:card class="p-6 space-y-4 group hover:ring-1 hover:ring-zinc-200 dark:hover:ring-zinc-700 transition-all">
                    <flux:subheading size="xs" class="font-black uppercase tracking-widest">Device & OS</flux:subheading>
                    @php $os = $user->device_os_info; @endphp
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl shadow-inner group-hover:bg-zinc-200 dark:group-hover:bg-zinc-700 transition-colors">
                            <x-dynamic-component :component="$os->icon" class="w-5 h-5 {{ $os->color }}" />
                        </div>
                        <div class="overflow-hidden">
                            <flux:heading size="lg" class="leading-none mb-1">{{ $os->name }}</flux:heading>
                            <flux:text size="xs" class="truncate text-zinc-500 block" title="{{ $user->last_login_device }}">
                                {{ $user->last_login_device ?? 'Unknown Device' }}
                            </flux:text>
                        </div>
                    </div>
                </flux:card>
            </div>

            {{-- Security Log Export --}}
            <div class="pt-4 flex justify-between items-center bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <flux:text size="xs" class="font-medium">Active session tracking enabled</flux:text>
                </div>
                <flux:button wire:click="exportSecurityLog" variant="ghost" size="sm" icon="document-arrow-down" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="exportSecurityLog">Export Security Log</span>
                    <span wire:loading wire:target="exportSecurityLog">Generating CSV...</span>
                </flux:button>
            </div>
        </flux:card>
    @endif

</div>
