<div class="flex min-h-screen">
    @section('title', 'Setup Your Profile')
    <div class="flex-1 flex justify-center items-center p-8 relative">
        <div class="w-full max-w-sm space-y-8">
            <div class="flex justify-center opacity-80">
                <flux:brand href="/" name="NEXACODE" class="font-bold text-2xl">
                    <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                        <flux:icon name="rocket-launch" variant="micro" />
                    </x-slot>
                </flux:brand>
            </div>

            <div class="text-center space-y-2">
                <div class="font-black uppercase tracking-tight text-3xl text-zinc-900 dark:text-white">Profile Setup</div>
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Just a few more details to get you started</p>
            </div>

            <form wire:submit="save" class="flex flex-col gap-6">
                {{-- Avatar Upload --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="relative group">
                        <div class="relative">
                            <flux:avatar 
                                src="{{ $avatar ? $avatar->temporaryUrl() : Auth::user()->avatar_url }}" 
                                :initials="Auth::user()->initials"
                                size="xl" 
                                class="rounded-[2.5rem] border-4 border-white dark:border-zinc-900 shadow-xl"
                            />
                            
                            {{-- Uploading State --}}
                            <div wire:loading wire:target="avatar" class="absolute inset-0 z-10 bg-black/40 backdrop-blur-sm rounded-[2.5rem] flex items-center justify-center">
                                <div class="w-6 h-6 border-2 border-white/20 border-t-white animate-spin rounded-full"></div>
                            </div>
                        </div>

                        <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white opacity-0 group-hover:opacity-100 transition-opacity rounded-[2.5rem] cursor-pointer">
                            <flux:icon name="camera" variant="micro" />
                            <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <flux:text size="sm" class="text-zinc-500">Click to change avatar</flux:text>
                    <flux:error name="avatar" />
                </div>

                {{-- Username --}}
                <flux:field>
                    <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Username</flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>@</flux:input.group.prefix>
                        <flux:input wire:model.live.debounce.500ms="username" placeholder="username" class="h-12" required />
                        
                        <x-slot name="append">
                            <div class="flex items-center px-3 border-l border-zinc-200 dark:border-zinc-800">
                                <div wire:loading wire:target="username" class="w-4 h-4 border-2 border-indigo-500/20 border-t-indigo-500 animate-spin rounded-full"></div>
                                
                                <div wire:loading.remove wire:target="username">
                                    @if($username && !$errors->has('username'))
                                        <flux:icon name="check-circle" variant="micro" class="text-emerald-500" />
                                    @endif
                                </div>
                            </div>
                        </x-slot>
                    </flux:input.group>
                    <flux:error name="username" />
                </flux:field>

                {{-- Bio --}}
                <flux:field>
                    <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 mb-2">Short Bio</flux:label>
                    <flux:textarea wire:model="bio" placeholder="Tell us a bit about yourself..." rows="3" class="rounded-2xl" />
                    <flux:description class="text-[10px] font-bold uppercase tracking-widest mt-2">Max 160 chars.</flux:description>
                    <flux:error name="bio" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-500 w-full h-12 text-[10px] font-black uppercase tracking-widest shadow-sm rounded-2xl transition-transform hover:-translate-y-0.5 mt-2 text-white" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Complete Setup</span>
                    <span wire:loading wire:target="save">Finalizing...</span>
                </flux:button>
            </form>
        </div>
    </div>

    <div class="flex-1 p-4 max-lg:hidden whitespace-normal">
        <div class="text-white relative rounded-2xl h-full w-full bg-aurora flex flex-col items-start justify-end p-16 overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            
            <div class="relative z-10 w-full max-w-2xl">
                <div class="flex gap-1 mb-6 text-emerald-400">
                    <flux:icon.check-circle variant="solid" size="sm" />
                    <flux:text class="font-bold text-white">Account Verified</flux:text>
                </div>

                <div class="mb-8 italic font-medium text-3xl xl:text-4xl leading-tight">
                    "Welcome to the community! We're excited to see what you'll discover on NEXACODE today."
                </div>

                <div class="flex gap-4 items-center">
                    <x-nexacode-brand-n class="size-16" />

                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">NEXACODE Welcome Team</div>
                        <div class="text-zinc-300">Supporting your digital journey</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
