<div class="fixed bottom-6 right-6 z-[9999]" x-data="{ 
    open: @entangle('isOpen').live,
    convId: @entangle('conversationId'),
    isTyping: false,
    typingTimeout: null,
    otherTyping: false,
    uploading: false,
    previewImage: null,
    isAdminOnline: false,
    playSound() {
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
        audio.play().catch(e => console.log('Audio play failed:', e));
    },
    setTyping() {
        const id = this.convId;
        if (!id) return;
        window.Echo.private('chat.' + id)
            .whisper('typing', { typing: true });
        
        if (this.typingTimeout) clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            window.Echo.private('chat.' + id)
                .whisper('typing', { typing: false });
        }, 3000);
    },
    scrollToBottom() {
        $nextTick(() => {
            const container = document.getElementById('chat-messages-container');
            if (container) container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        });
    }
}" x-init="
    @php
        $now = now()->timezone('Asia/Jakarta');
        $fixedStatus = ($now->dayOfWeek >= 1 && $now->dayOfWeek <= 5) && ($now->hour >= 9 && $now->hour < 18);
    @endphp
    
    this.isAdminOnline = @js($fixedStatus);

    @auth
        window.Echo.join('presence-support-team')
            .here((users) => { 
                const adminCount = users.filter(u => u.is_admin).length;
                this.isAdminOnline = adminCount > 0 || @js($fixedStatus); 
            })
            .joining((user) => { 
                if (user.is_admin) this.isAdminOnline = true; 
            })
            .leaving((user) => { 
                setTimeout(() => {
                    window.Echo.join('presence-support-team').here((users) => {
                        const adminCount = users.filter(u => u.is_admin).length;
                        this.isAdminOnline = adminCount > 0 || @js($fixedStatus);
                    });
                }, 1000);
            });
    @endauth

    $watch('open', value => { if (value) scrollToBottom() });
    
    window.addEventListener('scroll-to-bottom', () => {
        scrollToBottom();
        playSound();
    });

    $watch('convId', id => {
        if (id) {
            window.Echo.private('chat.' + id)
                .listenForWhisper('typing', (e) => {
                    this.otherTyping = e.typing;
                })
                .listen('.message.sent', (e) => {
                    $wire.dispatch('refresh-chat');
                    this.playSound();
                });
        }
    });

    if (this.convId) {
        window.Echo.private('chat.' + this.convId)
            .listenForWhisper('typing', (e) => {
                this.otherTyping = e.typing;
            })
            .listen('.message.sent', (e) => {
                $wire.dispatch('refresh-chat');
                this.playSound();
            });
    }
">
    {{-- Floating Bubble --}}
    <flux:button 
        x-on:click="open = !open"
        variant="primary"
        class="!w-14 !h-14 !rounded-full shadow-lg shadow-emerald-500/30 flex items-center justify-center transition-all duration-300 hover:scale-110 group relative p-0"
    >
        <div x-show="!open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100">
            <x-lucide-message-circle class="w-7 h-7" />
        </div>
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="absolute">
            <x-lucide-x class="w-7 h-7" />
        </div>

        {{-- Unread Indicator --}}
        @php
            $unreadCount = $conversation ? $conversation->unreadMessages()->count() : 0;
        @endphp
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-zinc-900 group-hover:scale-110 transition-transform">
                {{ $unreadCount }}
            </span>
        @endif
    </flux:button>

    {{-- Chat Window --}}
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300 origin-bottom-right"
        x-transition:enter-start="opacity-0 scale-95 translate-y-10"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 origin-bottom-right"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        class="absolute bottom-20 right-0 w-[380px] h-[550px] bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
        @click.away="if (!previewImage) open = false"
    >
        {{-- Header --}}
        <div class="p-6 bg-emerald-500/5 dark:bg-emerald-500/10 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    @if($conversation && $conversation->author)
                        <img src="{{ $conversation->author->avatar ? asset('storage/' . $conversation->author->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($conversation->author->name) . '&color=FFFFFF&background=10b981' }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <x-lucide-headset class="w-6 h-6 text-white" />
                    @endif
                </div>
                @if(!$authorId)
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 border-2 border-white dark:border-zinc-900 rounded-full animate-pulse" :class="isAdminOnline ? 'bg-emerald-500' : 'bg-amber-500'"></div>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-zinc-900 dark:text-white leading-tight">
                    @if($authorId && $conversation && $conversation->author)
                        Chat with {{ $conversation->author->name }}
                    @else
                        Nexa Support
                    @endif
                </h3>
                <p class="text-xs font-medium" :class="isAdminOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                    @if($authorId)
                        @if($conversation && $conversation->product)
                            <span class="text-zinc-500 font-normal">Regarding {{ $conversation->product->name }}</span>
                        @else
                            <span class="text-zinc-500 font-normal">General Inquiry</span>
                        @endif
                    @else
                        <template x-if="isAdminOnline"><span>Online & Ready to Help</span></template>
                        <template x-if="!isAdminOnline"><span>Away - Will reply soon</span></template>
                    @endif
                </p>
            </div>
            @if($authorId)
                <flux:button wire:click="openAdminSupport" variant="subtle" size="sm" class="ml-auto !h-8 !px-3 rounded-xl text-[10px] font-bold uppercase tracking-wider">
                    To Admin
                </flux:button>
            @endif
        </div>

        {{-- Messages --}}
        <div id="chat-messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 scroll-smooth custom-scrollbar">
            @auth
                <div class="text-center mb-6">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded-full">
                        Conversation Started
                    </span>
                </div>

                @forelse($messages as $msg)
                    <div class="flex {{ $msg->is_admin ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[80%]">
                            <div class="
                                p-3 rounded-2xl text-sm leading-relaxed
                                {{ $msg->is_admin 
                                    ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 rounded-tl-none' 
                                    : 'bg-emerald-500 text-white rounded-tr-none shadow-md shadow-emerald-500/20' 
                                }}
                            ">
                                <div class="text-sm prose dark:prose-invert prose-sm max-w-none">
                                    @if($msg->type === 'quote')
                                        <div class="bg-emerald-600/10 dark:bg-emerald-500/10 rounded-xl p-3 border border-emerald-500/20 mb-2">
                                            <div class="text-[8px] uppercase font-black tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">Custom Offer</div>
                                            <div class="text-lg font-black tabular-nums text-zinc-900 dark:text-white">Rp {{ number_format($msg->metadata['amount'], 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-zinc-600 dark:text-zinc-400 mt-1 italic leading-tight">{{ $msg->message }}</div>
                                            <div class="mt-3 pt-2 border-t border-emerald-500/10">
                                                <flux:button variant="primary" size="sm" class="w-full !h-8 !text-[10px] uppercase font-bold tracking-wider" icon="credit-card">Accept & Pay</flux:button>
                                            </div>
                                        </div>
                                    @else
                                        {!! App\Helpers\MarkdownHelper::render($msg->message) !!}
                                    @endif
                                </div>
                                @if($msg->image_path)
                                    @php
                                        $extension = pathinfo($msg->image_path, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                    @endphp

                                    <div class="mt-2">
                                        @if($isImage)
                                            <img src="{{ Storage::url($msg->image_path) }}" class="rounded-xl max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity" x-on:click="previewImage = '{{ Storage::url($msg->image_path) }}'; document.getElementById('buyer-preview-img').src = previewImage; Flux.modal('modal-image-preview-widget').show()">
                                        @else
                                            <a href="{{ Storage::url($msg->image_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 transition-all no-underline">
                                                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                                    @if(in_array(strtolower($extension), ['zip', 'rar', '7z']))
                                                        <x-lucide-file-archive class="w-6 h-6 text-emerald-600" />
                                                    @elseif(strtolower($extension) === 'pdf')
                                                        <x-lucide-file-text class="w-6 h-6 text-emerald-600" />
                                                    @else
                                                        <x-lucide-file class="w-6 h-6 text-emerald-600" />
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-[10px] font-bold text-zinc-900 dark:text-white truncate">{{ basename($msg->image_path) }}</p>
                                                    <p class="text-[8px] text-zinc-500 uppercase tracking-widest">{{ strtoupper($extension) }} File • Download</p>
                                                </div>
                                                <x-lucide-download class="w-4 h-4 text-zinc-400" />
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-1 {{ $msg->is_admin ? 'text-left' : 'text-right' }}">
                                {{ $msg->created_at->format('H:i') }} 
                                @if(!$msg->is_admin)
                                    <span class="ml-1 opacity-50">{{ $msg->is_read ? 'Read' : 'Sent' }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center space-y-4 pb-12">
                        <div class="w-16 h-16 rounded-full bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center">
                            <x-lucide-sparkles class="w-8 h-8 text-zinc-300 dark:text-zinc-600" />
                        </div>
                        <div>
                            @if($authorId)
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Ada yang mau ditanyain ke Author?</p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Tanya detail produk atau minta bantuan instalasi di sini.</p>
                            @else
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Halo! Ada yang bisa kami bantu hari ini?</p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Kirim pesan dan admin kami akan membalas secepatnya.</p>
                            @endif
                        </div>
                    </div>
                @endforelse

                {{-- Typing Indicator --}}
                <div x-show="otherTyping" x-transition class="flex justify-start">
                    <div class="bg-zinc-100 dark:bg-zinc-800 p-3 rounded-2xl rounded-tl-none flex gap-1 items-center">
                        <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-center space-y-6 px-4">
                    <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 flex items-center justify-center relative">
                        <x-lucide-lock class="w-10 h-10 text-emerald-600" />
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center border-2 border-white dark:border-zinc-900">
                            <x-lucide-user-2 class="w-3 h-3 text-white" />
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-zinc-900 dark:text-white">Silakan Login</h4>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">
                            Ups! Kamu harus login dulu ya sebelum bisa ngobrol santai sama tim Nexa Support.
                        </p>
                    </div>
                    <flux:button href="{{ route('login') }}" variant="primary" class="w-full rounded-2xl shadow-lg shadow-emerald-500/20">
                        Login Sekarang
                    </flux:button>
                    <p class="text-xs text-zinc-400">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-600 font-semibold hover:underline">Daftar dulu yuk</a>
                    </p>
                </div>
            @endauth
        </div>

        {{-- Input --}}
        @if($isOpen && auth()->check())
            <div class="p-4 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
                @if($attachment)
                    <div class="mb-4 relative inline-block group">
                        <img src="{{ $attachment->temporaryUrl() }}" class="w-24 h-24 object-cover rounded-2xl border-2 border-emerald-500/20">
                        <flux:button wire:click="$set('attachment', null)" variant="danger" size="sm" icon="x-mark" class="absolute -top-2 -right-2 !rounded-full !p-1 shadow-lg" />
                    </div>
                @endif

                @if($protectionWarning)
                    <div class="mx-6 mb-4 px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center gap-2 text-amber-700 dark:text-amber-400 text-[10px] animate-pulse">
                        <flux:icon.exclamation-triangle variant="mini" class="w-4 h-4 flex-shrink-0" />
                        <p class="font-medium italic leading-tight">{{ $protectionWarning }}</p>
                    </div>
                @endif

                <form wire:submit="sendMessage">
                    <flux:composer 
                        wire:key="chat-composer-widget"
                        name="message"
                        wire:model="message" 
                        @input="setTyping()"
                        placeholder="Type your message..." 
                        rows="2" 
                        max-rows="6"
                        label="Message"
                        label:sr-only
                    >
                        <x-slot name="actionsLeading">
                            <label class="cursor-pointer">
                                <flux:button as="div" size="sm" variant="subtle" icon="paper-clip" />
                                <input type="file" wire:model="attachment" class="hidden" accept="image/*">
                            </label>
                        </x-slot>

                        <x-slot name="actionsTrailing">
                            <flux:button type="submit" size="sm" variant="primary" icon="paper-airplane" wire:loading.attr="disabled" />
                        </x-slot>
                    </flux:composer>
                </form>
                <p class="text-[10px] text-zinc-400 text-center mt-3">
                    Powered by NEXACODE Real-time
                </p>
            </div>
        @elseif(!auth()->check())
            <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800 text-center">
                <p class="text-[10px] text-zinc-400 uppercase tracking-widest font-bold">
                    Authentication Required
                </p>
            </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.4);
        }
    </style>

    {{-- Image Preview Modal --}}
    <flux:modal name="modal-image-preview-widget" class="max-w-4xl" x-on:close="previewImage = null">
        <div class="space-y-6">
            <div class="flex flex-col items-center">
                <img id="buyer-preview-img" src="" class="max-w-full max-h-[70vh] rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800">
            </div>

            <div class="flex">
                <flux:button class="flex-1" x-on:click="Flux.modal('modal-image-preview-widget').close()">Tutup Pratinjau</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
