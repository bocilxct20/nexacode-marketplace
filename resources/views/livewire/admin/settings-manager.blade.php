<div class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Platform Settings</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div>
        <flux:heading size="2xl">Platform Settings</flux:heading>
        <flux:subheading>Manage your site configuration, commerce rules, branding, and social presence.</flux:subheading>
    </div>

    <flux:navbar variant="pills" scrollable>
        <flux:navbar.item wire:click="$set('tab', 'general')" :current="$tab === 'general'" icon="cog-6-tooth">General</flux:navbar.item>
        <flux:navbar.item wire:click="$set('tab', 'branding')" :current="$tab === 'branding'" icon="photo">Branding</flux:navbar.item>
        <flux:navbar.item wire:click="$set('tab', 'marketplace')" :current="$tab === 'marketplace'" icon="shopping-bag">Marketplace</flux:navbar.item>
        <flux:navbar.item wire:click="$set('tab', 'social')" :current="$tab === 'social'" icon="share">Social Media</flux:navbar.item>
        <flux:navbar.item wire:click="$set('tab', 'seo')" :current="$tab === 'seo'" icon="globe-alt">SEO</flux:navbar.item>
        <flux:navbar.item wire:click="$set('tab', 'advanced')" :current="$tab === 'advanced'" icon="beaker">Advanced</flux:navbar.item>
    </flux:navbar>

    {{-- General Settings --}}
    @if($tab === 'general')
        <flux:card class="p-6 relative min-h-[500px]">
            {{-- Tab-Specific Skeleton: General --}}
            <div wire:loading wire:target="tab, saveCategorizedSettings" class="absolute inset-0 z-10 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md flex items-center justify-center rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
                <div class="space-y-8 w-full h-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3"><flux:skeleton class="h-4 w-24" /><flux:skeleton class="h-10 w-full rounded-lg" /><flux:skeleton class="h-3 w-48" /></div>
                        <div class="space-y-3"><flux:skeleton class="h-4 w-24" /><flux:skeleton class="h-10 w-full rounded-lg" /><flux:skeleton class="h-3 w-48" /></div>
                    </div>
                    <div class="space-y-3"><flux:skeleton class="h-4 w-32" /><flux:skeleton class="h-24 w-full rounded-lg" /><flux:skeleton class="h-3 w-48" /></div>
                    <div class="flex justify-end pt-4"><flux:skeleton class="h-10 w-48 rounded-lg" /></div>
                </div>
            </div>
            <form wire:submit="saveCategorizedSettings" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Site Name</flux:label>
                        <flux:input wire:model="site_name" placeholder="e.g. NexaCode Marketplace" />
                        <flux:description>The main title of your marketplace.</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Support Email</flux:label>
                        <flux:input type="email" wire:model="support_email" placeholder="support@nexacode.id" />
                        <flux:description>Used for system notifications and support links.</flux:description>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Physical Address / Office</flux:label>
                    <flux:textarea wire:model="site_address" rows="2" placeholder="Enter your office address..." />
                    <flux:description>Displayed in footer or contact pages.</flux:description>
                </flux:field>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary">Save General Settings</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Branding Settings --}}
    @if($tab === 'branding')
        <flux:card class="p-6 relative min-h-[500px]">
            {{-- Tab-Specific Skeleton: Branding --}}
            <div wire:loading wire:target="tab, saveCategorizedSettings" class="absolute inset-0 z-10 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md flex items-center justify-center rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 w-full h-full">
                    <div class="space-y-6">
                        <flux:skeleton class="h-4 w-24" />
                        <div class="space-y-4">
                            <flux:skeleton class="h-24 w-full rounded-xl" />
                            <flux:skeleton class="h-32 w-full rounded-xl" />
                        </div>
                    </div>
                    <div class="space-y-6">
                        <flux:skeleton class="h-4 w-24" />
                        <div class="space-y-4">
                            <flux:skeleton class="h-24 w-full rounded-xl" />
                            <flux:skeleton class="h-32 w-full rounded-xl" />
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit="saveCategorizedSettings" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    {{-- Logo --}}
                    <flux:field>
                        <flux:label>Site Logo</flux:label>
                        <div class="mt-2 space-y-4">
                            @if($existing_logo)
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
                                    <img src="{{ Storage::url($existing_logo) }}" alt="Logo" class="h-12 object-contain">
                                </div>
                            @endif
                            
                            <flux:file-upload wire:model="site_logo" accept="image/*">
                                <flux:file-upload.dropzone
                                    heading="Drop new logo here"
                                    text="PNG or SVG recommended"
                                    with-progress
                                    inline
                                />
                            </flux:file-upload>
                        </div>
                    </flux:field>

                    {{-- Favicon --}}
                    <flux:field>
                        <flux:label>Site Favicon</flux:label>
                        <div class="mt-2 space-y-4">
                            @if($existing_favicon)
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
                                    <img src="{{ Storage::url($existing_favicon) }}" alt="Favicon" class="h-8 w-8 object-contain">
                                </div>
                            @endif
                            
                            <flux:file-upload wire:model="site_favicon" accept="image/x-icon,image/png">
                                <flux:file-upload.dropzone
                                    heading="Drop favicon here"
                                    text="ICO or PNG, 32x32 recommended"
                                    with-progress
                                    inline
                                />
                            </flux:file-upload>
                        </div>
                    </flux:field>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary">Update Branding</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Marketplace Settings --}}
    @if($tab === 'marketplace')
        <flux:card class="p-6 relative min-h-[500px]">
            {{-- Tab-Specific Skeleton: Marketplace --}}
            <div wire:loading wire:target="tab, saveCategorizedSettings" class="absolute inset-0 z-10 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md flex items-center justify-center rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
                <div class="space-y-10 w-full h-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(range(1, 4) as $i)<div class="space-y-3"><flux:skeleton class="h-4 w-32" /><flux:skeleton class="h-10 w-full rounded-lg" /><flux:skeleton class="h-3 w-48" /></div>@endforeach
                    </div>
                    <flux:separator />
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach(range(1, 3) as $i)
                        <div class="flex items-center justify-between p-2">
                            <div class="space-y-2"><flux:skeleton class="h-4 w-36" /><flux:skeleton class="h-3 w-32" /></div>
                            <flux:skeleton class="h-6 w-10 rounded-full" />
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-end pt-6"><flux:skeleton class="h-10 w-48 rounded-lg" /></div>
                </div>
            </div>
            <form wire:submit="saveCategorizedSettings" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Minimum Withdrawal Amount</flux:label>
                        <flux:input type="number" wire:model="min_withdrawal" min="0" />
                        <flux:description>Threshold for authors to request a withdrawal.</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Currency Code</flux:label>
                        <flux:input wire:model="currency_code" placeholder="e.g. IDR, USD" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Currency Symbol</flux:label>
                        <flux:input wire:model="currency_symbol" placeholder="e.g. Rp, $" />
                    </flux:field>
                </div>

                <flux:separator variant="subtle" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <flux:field>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:label>Auto-approve Authors</flux:label>
                                <p class="text-xs text-zinc-500">Fast-track new authors.</p>
                            </div>
                            <flux:switch wire:model="auto_approve_authors" />
                        </div>
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:label>Auto-approve Products</flux:label>
                                <p class="text-xs text-zinc-500">Skip product review.</p>
                            </div>
                            <flux:switch wire:model="auto_approve_products" />
                        </div>
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:label>Maintenance Mode</flux:label>
                                <p class="text-xs text-zinc-500">Disable customer access.</p>
                            </div>
                            <flux:switch wire:model="maintenance_mode" color="red" />
                        </div>
                    </flux:field>
                </div>

                <div class="flex justify-end pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary">Save Marketplace Rules</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Social Media Settings --}}
    @if($tab === 'social')
        <flux:card class="p-6 relative min-h-[400px]">
            {{-- Tab-Specific Skeleton: Social --}}
            <div wire:loading wire:target="tab, saveCategorizedSettings" class="absolute inset-0 z-10 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md flex items-center justify-center rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
                <div class="space-y-8 w-full h-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(range(1, 4) as $i)<div class="space-y-3"><flux:skeleton class="h-4 w-24" /><flux:skeleton class="h-10 w-full rounded-lg" /></div>@endforeach
                    </div>
                    <div class="flex justify-end pt-4"><flux:skeleton class="h-10 w-48 rounded-lg" /></div>
                </div>
            </div>
            <form wire:submit="saveCategorizedSettings" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Twitter URL</flux:label>
                        <flux:input wire:model="social_twitter" icon="share" placeholder="https://twitter.com/..." />
                    </flux:field>

                    <flux:field>
                        <flux:label>GitHub URL</flux:label>
                        <flux:input wire:model="social_github" icon="share" placeholder="https://github.com/..." />
                    </flux:field>

                    <flux:field>
                        <flux:label>Facebook URL</flux:label>
                        <flux:input wire:model="social_facebook" icon="share" placeholder="https://facebook.com/..." />
                    </flux:field>

                    <flux:field>
                        <flux:label>Instagram URL</flux:label>
                        <flux:input wire:model="social_instagram" icon="share" placeholder="https://instagram.com/..." />
                    </flux:field>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary">Save Social Links</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- SEO Settings --}}
    @if($tab === 'seo')
        <flux:card class="p-6 relative min-h-[400px]">
            {{-- Tab-Specific Skeleton: SEO --}}
            <div wire:loading wire:target="tab, saveCategorizedSettings" class="absolute inset-0 z-10 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md flex items-center justify-center rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
                <div class="space-y-8 w-full h-full">
                    <div class="space-y-3"><flux:skeleton class="h-4 w-32" /><flux:skeleton class="h-10 w-full rounded-lg" /><flux:skeleton class="h-3 w-64" /></div>
                    <div class="space-y-3"><flux:skeleton class="h-4 w-48" /><flux:skeleton class="h-24 w-full rounded-lg" /><flux:skeleton class="h-3 w-64" /></div>
                    <div class="flex justify-end pt-4"><flux:skeleton class="h-10 w-48 rounded-lg" /></div>
                </div>
            </div>
            <form wire:submit="saveCategorizedSettings" class="space-y-6">
                <flux:field>
                    <flux:label>Global Meta Title</flux:label>
                    <flux:input wire:model="meta_title" placeholder="NexaCode - Digital Marketplace" />
                    <flux:description>Appears in search results and browser tabs.</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Global Meta Description</flux:label>
                    <flux:textarea wire:model="meta_description" rows="3" placeholder="Premium source code and digital products..." />
                    <flux:description>Provides a brief summary for search engines.</flux:description>
                </flux:field>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary">Save SEO Config</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Advanced Settings (Table) --}}
    @if($tab === 'advanced')
        <div class="space-y-6 relative">
            {{-- Tab-Specific Skeleton: Advanced --}}
            <div wire:loading wire:target="tab, addSetting, deleteSetting, updateSetting" class="absolute inset-0 z-10 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md flex flex-col gap-8 rounded-xl overflow-hidden p-8 animate-in fade-in duration-200">
                <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-4">
                    <flux:skeleton class="h-6 w-48" />
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2"><flux:skeleton class="h-4 w-20" /><flux:skeleton class="h-10 w-full rounded-lg" /></div>
                        <div class="space-y-2"><flux:skeleton class="h-4 w-20" /><flux:skeleton class="h-10 w-full rounded-lg" /></div>
                    </div>
                    <div class="flex justify-end"><flux:skeleton class="h-10 w-32 rounded-lg" /></div>
                </div>
                <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-6">
                    <div class="space-y-2"><flux:skeleton class="h-6 w-56" /><flux:skeleton class="h-4 w-80" /></div>
                    <div class="space-y-3">
                        @foreach(range(1, 5) as $i)<flux:skeleton class="h-12 w-full rounded-lg" />@endforeach
                    </div>
                </div>
            </div>
            <flux:card class="p-6 bg-zinc-50 dark:bg-zinc-900/50">
                <form wire:submit="addSetting" class="space-y-4">
                    <flux:heading size="lg">Add Custom Setting</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Key (slug format)</flux:label>
                            <flux:input wire:model="newKey" placeholder="e.g. twitter_url" required />
                            <flux:error name="newKey" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Value</flux:label>
                            <flux:input wire:model="newValue" placeholder="Enter setting value..." required />
                            <flux:error name="newValue" />
                        </flux:field>
                    </div>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="plus">Add Setting</flux:button>
                    </div>
                </form>
            </flux:card>

            <flux:card class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="font-bold">Raw Configuration Data</flux:heading>
                        <flux:subheading>Direct access to the underlying platform settings.</flux:subheading>
                    </div>
                </div>

                <flux:table container:class="max-h-96">
                    <flux:table.columns>
                        <flux:table.column>Setting Key</flux:table.column>
                        <flux:table.column>Value</flux:table.column>
                        <flux:table.column align="right"></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($settings as $setting)
                            <flux:table.row :key="$setting['id']">
                                <flux:table.cell font-mono text-xs variant="strong">
                                    {{ $setting['key'] }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:input 
                                        type="text" 
                                        value="{{ $setting['value'] }}" 
                                        wire:change="updateSetting({{ $setting['id'] }}, $event.target.value)" 
                                        class="bg-transparent border-none focus:bg-white dark:focus:bg-zinc-900"
                                    />
                                </flux:table.cell>
                                <flux:table.cell align="right">
                                    <flux:button 
                                        variant="ghost" 
                                        icon="trash" 
                                        size="sm" 
                                        wire:click="deleteSetting({{ $setting['id'] }})"
                                        wire:confirm="Are you sure?"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    @endif
</div>

@script
    $wire.on('setting-updated', () => {
        Flux.toast({
            variant: 'success',
            heading: 'Settings Saved',
            text: 'Platform configuration has been updated successfully.'
        });
    });
@endscript
