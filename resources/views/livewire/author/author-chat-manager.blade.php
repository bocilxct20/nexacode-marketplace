<div class="h-[calc(100vh-12rem)] flex gap-4 pointer-events-auto relative z-[100]" x-data="{ 
    isTyping: false,
    typingTimeout: null,
    otherTyping: false,
    uploading: false,
    previewImage: null,
    timer: '0:00',
    playSound() {
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
        audio.play().catch(e => console.log('Audio play failed:', e));
    },
    setTyping() {
        const convId = @js($selectedConversationId);
        if (!convId || !window.Echo) return;
        window.Echo.private('chat.' + convId)
            .whisper('typing', { typing: true });
        
        if (this.typingTimeout) clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            if (window.Echo) {
                window.Echo.private('chat.' + convId)
                    .whisper('typing', { typing: false });
            }
        }, 3000);
    },
    scrollToBottom() {
        $nextTick(() => {
            const container = document.getElementById('author-chat-container');
            if (container) container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        });
    },
    recording: false,
    mediaRecorder: null,
    audioChunks: [],
    duration: 0,
    interval: null,
    async toggleRecording() {
        if (!this.recording) {
            await this.startRecording();
        } else {
            this.stopRecording();
        }
    },
    async startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.mediaRecorder = new MediaRecorder(stream);
            this.audioChunks = [];
            this.mediaRecorder.ondataavailable = (e) => this.audioChunks.push(e.data);
            this.mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                @this.upload('voiceNote', audioBlob, (uploadedName) => {
                    $wire.sendVoiceNote(uploadedName, this.duration);
                });
            };
            this.mediaRecorder.start();
            this.recording = true;
            this.duration = 0;
            this.interval = setInterval(() => this.duration++, 1000);
        } catch (e) {
            console.error('Mic access denied:', e);
            Flux.toast({ variant: 'danger', heading: 'Error', text: 'Microphone access denied.' });
        }
    },
    stopRecording() {
        if (this.mediaRecorder) this.mediaRecorder.stop();
        this.recording = false;
        clearInterval(this.interval);
    },
    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    },
    initChat() {
        console.log('AUTHOR CHAT: ALPINE INITIALIZING...');
        
        // Use a persistent reference to handle potential binding issues
        const self = this;
        
        window.addEventListener('scroll-to-bottom-author', () => {
            self.scrollToBottom();
            if (typeof self.playSound === 'function') self.playSound();
        });
        
        self.scrollToBottom();

        self.$watch('otherTyping', value => { 
            if (value) self.scrollToBottom();
        });

        Livewire.on('conversation-selected', (data) => {
            const id = data.id || data;
            console.log('AUTHOR Chat: Conversation selected:', id);
            
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private('chat.' + id)
                    .listenForWhisper('typing', (e) => {
                        self.otherTyping = e.typing;
                    })
                    .listen('.message.sent', (e) => {
                        Livewire.dispatch('refresh-chat');
                    });
            }
        });
        
        console.log('AUTHOR CHAT: ALPINE LOADED SUCCESSFULLY');
    }
}" x-init="initChat()">
    {{-- Sidebar: Conversation List --}}
    <div class="w-80 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden shadow-sm relative z-10">
        <div class="px-6 pb-6 pt-6 border-b border-zinc-200 dark:border-zinc-800 space-y-4 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md relative z-20">
            <div class="flex justify-between items-center">
                <flux:heading size="lg" class="font-bold">Obrolan</flux:heading>
                <div class="flex gap-1 items-center">
                    <button wire:click="$toggle('showArchived')" class="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-colors" title="{{ $showArchived ? 'Sembunyikan Arsip' : 'Tampilkan Arsip' }}">
                        @if($showArchived)
                            <flux:icon.archive-box-x-mark size="sm" class="text-emerald-500" />
                        @else
                            <flux:icon.archive-box size="sm" class="text-zinc-400" />
                        @endif
                    </button>
                    <flux:button variant="subtle" size="xs" icon="cog-8-tooth" x-on:click="Flux.modal('auto-responder-settings').show()" square title="Pengaturan Auto-Responder" />
                    <flux:button variant="subtle" icon="megaphone" x-on:click="Flux.modal('broadcast-modal').show()" size="xs">Siaran</flux:button>
                </div>
            </div>

            <div class="space-y-3">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama pembeli..."
                    icon="magnifying-glass"
                    size="sm"
                    variant="filled"
                    class="!bg-zinc-50 dark:!bg-zinc-800/50"
                />

                <div class="flex gap-2">
                    <flux:button
                        wire:click="$set('filter', 'all')" 
                        size="xs" 
                        :variant="$filter === 'all' ? 'primary' : 'subtle'"
                        class="flex-1"
                    >
                        Semua
                    </flux:button>
                    <flux:button 
                        wire:click="$set('filter', 'unread')" 
                        size="xs" 
                        :variant="$filter === 'unread' ? 'primary' : 'subtle'"
                        class="flex-1"
                    >
                        Belum Terbaca
                    </flux:button>
                </div>
            </div>
        </div>
        
        {{-- Sidebar Conversation List --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
            {{-- Skeleton State for List --}}
            <div wire:loading.delay.longer wire:target="search, filter">
                @for($i = 0; $i < 5; $i++)
                    <div class="w-full p-4 flex items-center gap-4 animate-pulse">
                        <div class="w-10 h-10 bg-zinc-100 dark:bg-zinc-800 rounded-full flex-shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="flex justify-between">
                                <div class="h-4 w-24 bg-zinc-100 dark:bg-zinc-800 rounded"></div>
                                <div class="h-3 w-8 bg-zinc-100 dark:bg-zinc-800 rounded"></div>
                            </div>
                            <div class="h-3 w-40 bg-zinc-100 dark:bg-zinc-800 rounded"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <div wire:loading.remove wire:target="search, filter">
                @forelse($conversations as $conv)
                <button 
                    wire:click="selectConversation({{ $conv->id }})"
                    wire:key="conv-{{ $conv->id }}"
                    class="w-full p-4 flex items-center gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-all border-b border-zinc-50 dark:border-zinc-800/50 relative group cursor-pointer {{ $selectedConversationId == $conv->id ? 'bg-emerald-50/50 dark:bg-emerald-500/10' : '' }}"
                >
                    @if($selectedConversationId == $conv->id)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r-full"></div>
                    @endif
                    <div class="relative flex-shrink-0">
                        <flux:avatar :name="$conv->user?->name ?? 'Buyer'" :initials="$conv->user?->initials ?? 'B'" size="sm" />
                        @if($conv->user?->isOnline())
                            <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white dark:border-zinc-900" title="Online"></div>
                        @endif
                    </div>
                    <div class="flex-1 text-left min-w-0">
                        <div class="flex justify-between items-start">
                            <flux:heading size="sm" class="truncate">{{ $conv->user?->name ?? 'Pembeli' }}</flux:heading>
                            <span class="text-[10px] text-zinc-400 whitespace-nowrap">{{ $conv->last_message_at?->diffForHumans(short: true) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <flux:subheading size="xs" class="truncate flex-1">
                                {{ $conv->latestMessage?->message ?? 'No messages yet' }}
                            </flux:subheading>
                            @php $unreadCount = $conv->unreadMessages()->where('is_admin', false)->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="bg-emerald-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>

                        {{-- Tags and archived status --}}
                        <div class="flex flex-wrap gap-1 mt-1">
                            @if($conv->archived_at)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200 dark:border-zinc-700">Archived</span>
                            @endif
                            @if($conv->private_notes)
                                <flux:icon.document-text class="w-3 h-3 text-amber-500" title="Has Notes" />
                            @endif
                            @if($conv->tags)
                                @foreach($conv->tags as $tag)
                                    @php $tagData = $availableTags[$tag] ?? ['label' => $tag, 'color' => 'zinc']; @endphp
                                    <span class="px-1.5 py-0.5 text-[8px] font-bold uppercase rounded bg-{{ $tagData['color'] }}-100 dark:bg-{{ $tagData['color'] }}-900/40 text-{{ $tagData['color'] }}-600 dark:text-{{ $tagData['color'] }}-400 border border-{{ $tagData['color'] }}-200 dark:border-{{ $tagData['color'] }}-800">
                                        {{ $tagData['label'] }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-12 text-center">
                    <p class="text-sm text-zinc-400">Belum ada pesan pembeli</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Window --}}
    <div class="flex-1 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden shadow-sm relative z-10">
        @if($activeConversation)
            {{-- Header --}}
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md relative z-20">
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center gap-4">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <flux:avatar :name="$activeConversation->user?->name ?? 'Buyer'" :initials="$activeConversation->user?->initials ?? 'B'" />
                            <div class="min-w-0 flex-1">
                                <flux:heading size="lg" class="truncate">
                                    {{ $activeConversation->user?->name ?? 'Buyer' }}
                                </flux:heading>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($activeConversation->user?->isOnline())
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Online</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider">Offline</span>
                                    @endif

                                    @if($activeConversation->author->isElite())
                                        <flux:separator vertical class="h-2 mx-1 opacity-50" />
                                        <div class="flex items-center gap-1">
                                            <flux:icon.shield-check variant="mini" class="w-3 h-3 text-amber-500" />
                                            <span class="text-[9px] text-amber-600 font-black uppercase tracking-widest">Elite Chat Active</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            {{-- Search Toggle --}}
                            <flux:button wire:click="$toggle('searchActive')" variant="subtle" size="sm" :icon="$searchActive ? 'chevron-up' : 'magnifying-glass'" square title="Cari di percakapan" />
                            
                            {{-- Gallery Toggle --}}
                            <flux:button wire:click="$toggle('showGallery')" variant="subtle" size="sm" icon="photo" square title="Galeri File" :class="$showGallery ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : ''" />

                            {{-- Notification Settings --}}
                            <flux:dropdown>
                                <flux:button variant="subtle" size="sm" :icon="$activeConversation->notifications_enabled ? 'bell' : 'bell-slash'" square />
                                <flux:menu>
                                    <flux:menu.item wire:click="toggleNotifications">
                                        {{ $activeConversation->notifications_enabled ? 'Muted Notifikasi' : 'Aktifkan Notifikasi' }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.radio.group wire:model.live="notificationPriority" wire:change="setNotificationPriority($event.target.value)">
                                        <flux:menu.radio value="normal">Normal</flux:menu.radio>
                                        <flux:menu.radio value="high">Prioritas Tinggi</flux:menu.radio>
                                        <flux:menu.radio value="muted">Senyap</flux:menu.radio>
                                    </flux:menu.radio.group>
                                </flux:menu>
                            </flux:dropdown>

                            {{-- Action Buttons --}}
                            <flux:button variant="subtle" size="sm" icon="document-text" square title="Catatan Pribadi" wire:click="loadNotes" x-on:click="Flux.modal('private-notes-modal').show()" />
                            
                            <flux:dropdown>
                                <flux:button variant="subtle" size="sm" icon="ellipsis-vertical" square />
                                <flux:menu>
                                    <flux:menu.item wire:click="toggleArchive" icon="archive-box">
                                        {{ $activeConversation->archived_at ? 'Buka dari Arsip' : 'Arsipkan Percakapan' }}
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="$set('showReportModal', true)" x-on:click="Flux.modal('report-buyer').show()" icon="flag" class="text-red-500 hover:text-red-600">Laporkan Pembeli</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>

                    {{-- Contextual Product Card --}}
                    @if($activeConversation->product)
                        <div class="flex items-center gap-4 p-3 bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm group">
                            <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                @if($activeConversation->product->image_path)
                                    <img src="{{ Storage::url($activeConversation->product->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <flux:icon.cube class="w-6 h-6 text-zinc-400" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <flux:heading size="sm" class="truncate group-hover:text-emerald-500 transition-colors">
                                        {{ $activeConversation->product->name }}
                                    </flux:heading>
                                    <div class="text-xs font-black tabular-nums text-zinc-900 dark:text-white">
                                        Rp {{ number_format($activeConversation->product->price, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    @php
                                        $isBuyerOwned = \App\Models\Order::where('buyer_id', $activeConversation->user_id)
                                            ->where('status', 'completed')
                                            ->whereHas('items', function($q) use ($activeConversation) {
                                                $q->where('product_id', $activeConversation->product_id);
                                            })
                                            ->exists();
                                    @endphp
                                    @if($isBuyerOwned)
                                        <flux:badge color="lime" size="sm" class="text-[9px] font-black uppercase">Sudah Dibeli ✅</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm" class="text-[9px] font-black uppercase tracking-tighter">Pertanyaan Produk</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Inner Message Search Input --}}
                    @if($searchActive)
                        <div class="flex gap-2 animate-in slide-in-from-top duration-300">
                            <flux:input 
                                wire:model.live.debounce.300ms="msgSearch" 
                                placeholder="Cari pesan di percakapan ini..." 
                                icon="magnifying-glass"
                                class="flex-1"
                                clearable
                            />
                        </div>
                    @endif
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 flex min-h-0 relative">
                {{-- Skeleton State for Chat Area --}}
                <div wire:loading.delay.longer wire:target="selectConversation" class="absolute inset-0 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm z-30 flex flex-col p-8 space-y-6">
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex {{ $i % 2 == 0 ? 'justify-start' : 'justify-end' }}">
                            <div class="h-16 w-64 bg-zinc-100 dark:bg-zinc-800 animate-pulse rounded-2xl"></div>
                        </div>
                    @endfor
                </div>

                {{-- Messages Area --}}
                <div class="flex-1 flex flex-col min-w-0">
                    {{-- Pinned Messages (Top Bar) --}}
                    @php 
                        $pinnedMessages = $activeConversation->messages->where('is_pinned', true);
                    @endphp
                    @if($pinnedMessages->count() > 0)
                        <div x-data="{ open: false }" class="bg-amber-50 dark:bg-amber-900/10 border-b border-amber-200/50 dark:border-amber-800/30">
                            <button @click="open = !open" class="w-full px-6 py-2 flex items-center justify-between hover:bg-amber-100/50 transition-colors">
                                <div class="flex items-center gap-2">
                                    <flux:icon.map-pin variant="mini" class="w-4 h-4 text-amber-600" />
                                    <span class="text-xs font-bold text-amber-800 dark:text-amber-400">{{ $pinnedMessages->count() }} Pesan Disematkan</span>
                                </div>
                                <flux:icon.chevron-down variant="mini" class="w-4 h-4 text-amber-600 transition-transform" ::class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" x-collapse class="px-6 pb-4 space-y-3">
                                @foreach($pinnedMessages as $pinned)
                                    <div class="flex items-center gap-3 p-2 bg-white/50 dark:bg-white/5 rounded-xl border border-amber-200/50">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-1">{{ $pinned->message }}</p>
                                        </div>
                                        <flux:button wire:click="togglePin({{ $pinned->id }})" variant="ghost" size="xs" icon="map-pin" class="text-amber-600" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Messages List --}}
                    <div id="author-chat-container" class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-4 chat-bg-pattern custom-scrollbar">
                        @foreach($messages as $msg)
                            <div class="flex {{ $msg->is_admin ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $msg->id }}">
                            <div class="max-w-[85%] sm:max-w-[70%]">
                                <div class="
                                    {{ $msg->type === 'quote' ? '' : 'p-4 pb-2 rounded-2xl shadow-sm' }}
                                    text-sm leading-relaxed relative group
                                    {{ $msg->type === 'quote' 
                                        ? '' 
                                        : ($msg->is_admin 
                                            ? ($activeConversation->author->isElite() ? 'bg-amber-50 dark:bg-amber-400/10 text-amber-900 dark:text-amber-300 rounded-tr-none border border-amber-200 dark:border-amber-400/20' : 'bg-emerald-500 text-white rounded-tr-none shadow-emerald-500/10') 
                                            : 'bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-tl-none') 
                                    }}
                                    {{ $msg->is_pinned ? 'ring-2 ring-amber-400/50' : '' }}
                                ">
                                        {{-- Actions (Pin & React) --}}
                                        <div class="absolute -top-4 {{ $msg->is_admin ? 'right-0' : 'left-0' }} hidden group-hover:flex bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-full py-1 px-2 shadow-xl ring-1 ring-black/5 z-10 gap-1 items-center">
                                            <button wire:click="togglePin({{ $msg->id }})" class="p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ $msg->is_pinned ? 'text-amber-500' : 'text-zinc-400' }}" title="Sematkan pesan">
                                                <flux:icon.map-pin variant="mini" class="w-3.5 h-3.5" />
                                            </button>
                                            <div class="w-px h-3 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>
                                            @foreach(['👍', '❤️', '🔥', '👏', '😮', '🙏'] as $emoji)
                                                <button wire:click="toggleReaction({{ $msg->id }}, '{{ $emoji }}')" class="hover:scale-125 transition-transform p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-base leading-none">
                                                    {{ $emoji }}
                                                </button>
                                            @endforeach
                                        </div>

                                        {{-- Pinned Indicator --}}
                                        @if($msg->is_pinned)
                                            <div class="flex items-center gap-1 mb-1 text-[9px] font-bold uppercase {{ $msg->is_admin ? 'text-emerald-100' : 'text-amber-600' }}">
                                                <flux:icon.map-pin variant="mini" class="w-3 h-3" />
                                                <span>Disematkan</span>
                                            </div>
                                        @endif

                                        <div class="prose dark:prose-invert prose-sm max-w-none {{ $msg->is_admin ? ($activeConversation->author->isElite() ? 'text-[#451a03] font-medium' : 'text-white') : '' }}">
                                            @if($msg->voice_path)
                                                <div class="flex items-center gap-3 py-2 min-w-[200px]" x-data="{ 
                                                    audio: null, 
                                                    playing: false, 
                                                    progress: 0,
                                                    initAudio() {
                                                        this.audio = new Audio('{{ Storage::url($msg->voice_path) }}');
                                                        this.audio.onended = () => { this.playing = false; this.progress = 0; };
                                                        this.audio.ontimeupdate = () => { this.progress = (this.audio.currentTime / this.audio.duration) * 100; };
                                                    },
                                                    togglePlay() {
                                                        if (!this.audio) this.initAudio();
                                                        if (this.playing) { this.audio.pause(); } else { this.audio.play(); }
                                                        this.playing = !this.playing;
                                                    }
                                                }">
                                                    <button @click="togglePlay" class="w-10 h-10 rounded-full flex items-center justify-center transition-all bg-white/20 hover:bg-white/30 {{ !$msg->is_admin ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200' : '' }}">
                                                        <template x-if="!playing"><flux:icon.play variant="mini" class="w-5 h-5" /></template>
                                                        <template x-if="playing"><flux:icon.pause variant="mini" class="w-5 h-5" /></template>
                                                    </button>
                                                    <div class="flex-1 space-y-1">
                                                        <div class="h-1 bg-white/20 rounded-full overflow-hidden {{ !$msg->is_admin ? 'bg-zinc-200 dark:bg-zinc-700' : '' }}">
                                                            <div class="h-full bg-current transition-all" :style="'width: ' + progress + '%'"></div>
                                                        </div>
                                                        <div class="flex justify-between text-[10px] opacity-70">
                                                            <span>{{ $msg->voice_duration }}s</span>
                                                            <span>Voice Note</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($msg->type === 'quote')
                                                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden relative group/quote min-w-[320px]">
                                                    <div class="p-5">
                                                        <div class="relative z-10">
                                                            <div class="flex items-center justify-between mb-4">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                                                                        <flux:icon.credit-card class="w-4 h-4 text-emerald-600" />
                                                                    </div>
                                                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Penawaran Khusus</span>
                                                                </div>
                                                                <flux:badge color="emerald" size="sm" class="text-[9px] font-black">#OFFER-{{ substr($msg->id, 0, 5) }}</flux:badge>
                                                            </div>
                                                            <div class="text-3xl font-black text-zinc-900 dark:text-white tabular-nums mb-1 tracking-tighter">
                                                                Rp {{ number_format($msg->metadata['amount'], 0, ',', '.') }}
                                                            </div>
                                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6 leading-relaxed italic">
                                                                "{{ $msg->message }}"
                                                            </p>
                                                            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                                                                <div class="flex items-center gap-1.5">
                                                                    @php
                                                                        $status = strtolower($msg->metadata['status'] ?? 'pending');
                                                                        $statusColor = match($status) {
                                                                            'accepted' => 'bg-emerald-400',
                                                                            'rejected' => 'bg-red-400',
                                                                            default => 'bg-amber-400'
                                                                        };
                                                                        $statusLabel = match($status) {
                                                                            'accepted' => 'Diterima',
                                                                            'rejected' => 'Ditolak',
                                                                            default => 'Menunggu'
                                                                        };
                                                                        $footerLabel = match($status) {
                                                                            'accepted' => 'Penawaran Selesai',
                                                                            'rejected' => 'Penawaran Ditolak',
                                                                            default => 'Menunggu Pembeli'
                                                                        };
                                                                    @endphp
                                                                    <div class="w-2.5 h-2.5 rounded-full {{ $statusColor }} {{ $status === 'pending' ? 'animate-pulse' : '' }}"></div>
                                                                    <span class="text-[10px] font-bold uppercase text-zinc-500">{{ $statusLabel }}</span>
                                                                </div>
                                                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">{{ $footerLabel }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Integrated Timestamp for Quote --}}
                                                    <div class="bg-zinc-50 dark:bg-black/20 px-5 py-2 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800/50">
                                                        <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">Transaction Block</span>
                                                        <div class="flex items-center gap-1.5 opacity-60 text-[9px] font-medium text-zinc-500">
                                                            <span>{{ $msg->created_at->format('H:i') }}</span>
                                                            @if($msg->is_admin)
                                                                <span class="inline-flex items-center" title="{{ $msg->is_read ? 'Seen' : 'Delivered' }}">
                                                                    @if($msg->is_read)
                                                                        <flux:icon.check-badge size="xs" class="w-3 h-3 text-emerald-500" />
                                                                    @else
                                                                        <flux:icon.check size="xs" class="w-3 h-3 opacity-50" />
                                                                    @endif
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {!! App\Helpers\MarkdownHelper::render($msg->message) !!}
                                            @endif
                                        </div>
                                        
                                        {{-- Reactions Display --}}
                                        @if($msg->reactions)
                                            <div class="flex flex-wrap gap-1 mt-3">
                                                @foreach($msg->reactions as $emoji => $users)
                                                    <button 
                                                        wire:click="toggleReaction({{ $msg->id }}, '{{ $emoji }}')"
                                                        class="flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold border hover:scale-105 transition-transform
                                                            {{ in_array(Auth::id(), $users) 
                                                                ? 'bg-emerald-50 dark:bg-emerald-500/20 border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400' 
                                                                : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-500' }}"
                                                        title="{{ count($users) }} reactions"
                                                    >
                                                        <span>{{ $emoji }}</span>
                                                        <span class="opacity-70">{{ count($users) }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($msg->image_path)
                                            @php
                                                $extension = pathinfo($msg->image_path, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                            @endphp

                                            <div class="mt-2">
                                                @if($isImage)
                                                    <img src="{{ Storage::url($msg->image_path) }}" class="rounded-xl max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity whitespace-pre-wrap" x-on:click="previewImage = '{{ Storage::url($msg->image_path) }}'; document.getElementById('author-preview-img').src = previewImage; Flux.modal('modal-image-preview-author-chat').show()">
                                                @else
                                                    <a href="{{ Storage::url($msg->image_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 border border-white/20 hover:bg-white/20 transition-all no-underline {{ !$msg->is_admin ? 'dark:bg-zinc-900/50 dark:border-zinc-700' : '' }}">
                                                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                                            @if(in_array(strtolower($extension), ['zip', 'rar', '7z']))
                                                                <x-lucide-file-archive class="w-6 h-6 text-white" />
                                                            @elseif(strtolower($extension) === 'pdf')
                                                                <x-lucide-file-text class="w-6 h-6 text-white" />
                                                            @else
                                                                <x-lucide-file class="w-6 h-6 text-white" />
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-[10px] font-bold text-white truncate">{{ basename($msg->image_path) }}</p>
                                                            <p class="text-[8px] text-white/70 uppercase tracking-widest">{{ strtoupper($extension) }} File • Download</p>
                                                        </div>
                                                        <x-lucide-download class="w-4 h-4 text-white/50" />
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Integrated Timestamp --}}
                                        @if($msg->type !== 'quote')
                                            <div class="mt-1 flex items-center justify-end gap-1.5 opacity-60 text-[9px] font-medium {{ $msg->is_admin ? 'text-emerald-50' : 'text-zinc-500' }}">
                                                <span>{{ $msg->created_at->format('H:i') }}</span>
                                                @if($msg->is_admin)
                                                    <span class="inline-flex items-center" title="{{ $msg->is_read ? 'Seen' : 'Delivered' }}">
                                                        @if($msg->is_read)
                                                            <flux:icon.check-badge size="xs" class="w-3 h-3 text-white" />
                                                        @else
                                                            <flux:icon.check size="xs" class="w-3 h-3 opacity-50" />
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    {{-- Typing Indicator --}}
                    <div x-show="otherTyping" x-transition class="flex justify-start">
                        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 p-3 rounded-2xl rounded-tl-none flex gap-1 items-center shadow-sm">
                            <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                            <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                        </div>
                    </div>
                </div>
            </div>

                {{-- File Gallery Sidebar --}}
                @if($showGallery)
                    <div class="w-72 border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-y-auto animate-in slide-in-from-right duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <flux:heading size="sm">Galeri File</flux:heading>
                                <flux:button wire:click="$set('showGallery', false)" variant="ghost" size="xs" icon="x-mark" square />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                @forelse($this->galleryFiles as $file)
                                    @php
                                        $ext = pathinfo($file->image_path, PATHINFO_EXTENSION);
                                        $isImg = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    <div class="group relative aspect-square rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
                                        @if($isImg)
                                            <img src="{{ Storage::url($file->image_path) }}" class="w-full h-full object-cover">
                                            <button 
                                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
                                                x-on:click="previewImage = '{{ Storage::url($file->image_path) }}'; document.getElementById('author-preview-img').src = previewImage; Flux.modal('modal-image-preview-author-chat').show()"
                                            >
                                                <flux:icon.magnifying-glass-plus variant="mini" class="w-6 h-6 text-white" />
                                            </button>
                                        @else
                                            <a href="{{ Storage::url($file->image_path) }}" download class="absolute inset-0 flex flex-col items-center justify-center p-2 text-center">
                                                <flux:icon.document variant="mini" class="w-8 h-8 text-zinc-400 mb-1" />
                                                <span class="text-[8px] font-bold truncate w-full text-zinc-500 uppercase">{{ $ext }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <div class="col-span-2 py-12 text-center opacity-40">
                                        <flux:icon.photo class="w-8 h-8 mx-auto mb-2" />
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Belum ada file</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Input --}}
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                @if($protectionWarning)
                    <div class="mb-4 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl flex items-center gap-3 text-amber-700 dark:text-amber-400 text-sm animate-pulse">
                        <flux:icon.exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                        <p class="font-medium italic leading-tight">{{ $protectionWarning }}</p>
                    </div>
                @endif

                @if($attachment)
                    <div class="mb-4 relative inline-block group">
                        <img src="{{ $attachment->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-2xl border-2 border-emerald-500/20">
                        <flux:button wire:click="$set('attachment', null)" variant="danger" size="sm" icon="x-mark" class="absolute -top-2 -right-2 !rounded-full !p-1 shadow-lg" />
                    </div>
                @endif

                <form wire:submit="sendMessage">
                    <flux:composer 
                        name="replyMessage"
                        wire:model="replyMessage" 
                        @input="setTyping()"
                        placeholder="Bantu buyer kamu di sini..." 
                        label="Balas Pesan" 
                        label:sr-only
                        rows="2"
                        max-rows="6"
                    >
                        <x-slot name="actionsLeading">
                            <div class="flex items-center gap-1">
                                <label class="cursor-pointer">
                                    <flux:button as="div" size="sm" variant="subtle" icon="paper-clip" />
                                    <input type="file" wire:model="attachment" class="hidden">
                                </label>

                                <div class="relative">
                                    <flux:button 
                                        size="sm" 
                                        variant="subtle" 
                                        icon="microphone" 
                                        ::class="recording ? 'text-red-500 animate-pulse' : ''"
                                        @mousedown="startRecording(); interval = setInterval(() => { timer = formatTime(duration) }, 1000)" 
                                        @mouseup="stopRecording(); clearInterval(interval); timer = '0:00'"
                                        title="Hold to Record Voice Note"
                                    />
                                    <div x-show="recording" class="absolute -top-10 left-0 bg-red-500 text-white text-[10px] px-2 py-1 rounded-lg whitespace-nowrap badge animate-bounce">
                                        Recording: <span x-text="timer"></span>
                                    </div>
                                </div>

                                <flux:dropdown>
                                    <flux:button size="sm" variant="subtle" icon="chat-bubble-bottom-center-text" class="text-zinc-400 hover:text-emerald-500" />
                                    <flux:menu>
                                        <flux:menu.heading>Respon Cepat</flux:menu.heading>
                                        @foreach($cannedResponses as $canned)
                                            <flux:menu.item wire:click="$set('replyMessage', '{{ $canned->content }}')" icon="chat-bubble-left">{{ $canned->title }}</flux:menu.item>
                                        @endforeach
                                        <flux:menu.separator />
                                        <flux:menu.item x-on:click="Flux.modal('manage-canned').show()" icon="plus-circle">Kelola Template</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>

                                <flux:button x-on:click="Flux.modal('send-quote').show()" variant="subtle" size="sm" icon="currency-dollar" square title="Kirim Penawaran" />
                                <flux:button x-on:click="Flux.modal('schedule-message-modal').show()" variant="subtle" size="sm" icon="calendar" square title="Jadwalkan Pesan" />
                            </div>
                        </x-slot>

                        <x-slot name="actionsTrailing">
                            <flux:button type="submit" size="sm" variant="primary" icon="paper-airplane" wire:loading.attr="disabled" />
                        </x-slot>
                    </flux:composer>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-center p-12">
                <flux:icon.chat-bubble-left-right variant="outline" class="mb-6 w-16 h-16 text-zinc-300 dark:text-zinc-600" />
                <flux:heading size="lg" class="mb-2">Dashboard Dukungan Penulis</flux:heading>
                <flux:subheading class="max-w-sm">
                    Pilih percakapan dari sidebar untuk mulai membimbing dan membantu pembeli kamu secara real-time.
                </flux:subheading>
            </div>
        @endif
    </div>

    {{-- Right Sidebar: Buyer Profile (Context) --}}
    @if($activeConversation)
        <div class="w-80 space-y-6 flex flex-col h-full relative z-10">
            {{-- Profile Card --}}
            <flux:card class="shadow-sm border-zinc-100 dark:border-zinc-800 overflow-hidden relative group/profile">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent dark:from-emerald-500/5 opacity-0 group-hover/profile:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative">
                            <flux:avatar size="xl" :name="$activeConversation->user?->name" :initials="$activeConversation->user?->initials" class="!w-20 !h-20 shadow-xl border-4 border-white dark:border-zinc-800" />
                            <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-zinc-900 shadow-sm"></div>
                        </div>
                        <flux:heading size="lg" class="mt-4 font-black tracking-tight">{{ $activeConversation->user?->name }}</flux:heading>
                        <flux:subheading class="text-[9px] uppercase font-bold tracking-widest opacity-60">Member sejak {{ $buyerStats['joined_at'] }}</flux:subheading>
                    </div>

                    <flux:separator class="my-6 opacity-50" />

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <flux:subheading class="text-[9px] uppercase font-black tracking-tighter opacity-50">Total Belanja</flux:subheading>
                            <div class="text-xs font-black text-emerald-600 dark:text-emerald-400 tabular-nums">Rp {{ number_format($buyerStats['total_spent'], 0, ',', '.') }}</div>
                        </div>
                        <div class="space-y-1 text-right">
                            <flux:subheading class="text-[9px] uppercase font-black tracking-tighter opacity-50">Refund</flux:subheading>
                            <div class="text-xs font-black {{ $buyerStats['refund_count'] > 0 ? 'text-rose-500' : 'text-zinc-400' }}">{{ $buyerStats['refund_count'] }}</div>
                        </div>
                    </div>
                </div>
            </flux:card>

            {{-- Products Owned --}}
            <flux:card class="flex-1 min-h-0 flex flex-col overflow-hidden shadow-sm border-zinc-100 dark:border-zinc-800">
                <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-white/5">
                    <flux:heading size="xs" class="uppercase tracking-widest font-black opacity-60">Produk Terbeli</flux:heading>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    @forelse($buyerStats['products_owned'] as $product)
                        <div class="group flex items-center gap-3 p-3 rounded-2xl border border-zinc-100 dark:border-zinc-800 hover:border-emerald-500/30 hover:bg-emerald-50/10 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <flux:icon.cube class="w-4 h-4 text-emerald-600" />
                            </div>
                            <div class="min-w-0">
                                <flux:subheading class="text-[10px] truncate">{{ $product->name }}</flux:subheading>
                                <flux:badge color="lime" size="sm" class="text-[8px] mt-0.5">Purchased ✅</flux:badge>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 opacity-40">
                            <flux:icon.shopping-cart class="w-8 h-8 mb-2" />
                            <flux:subheading class="text-[10px] uppercase">Belum ada data</flux:subheading>
                        </div>
                    @endforelse
                </div>
            </flux:card>

            {{-- Merchant Tools --}}
            <flux:card class="shadow-xl">
                <flux:heading size="sm" class="mb-4">Alat Merchant Cepat</flux:heading>
                <div class="grid grid-cols-1 gap-2">
                    <flux:button variant="primary" size="sm" class="w-full" @click="$wire.showQuoteModal = true">Kirim Penawaran</flux:button>
                    <flux:button variant="ghost" size="sm" class="w-full" @click="$wire.showReportModal = true">Laporkan Pembeli</flux:button>
                </div>
            </flux:card>
        </div>
    @else
        <div class="w-80 flex flex-col items-center justify-center p-8 text-center">
            <flux:icon.user-circle variant="outline" class="w-16 h-16 mb-4 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="sm" class="text-zinc-400 uppercase">Pilih Chat</flux:heading>
            <flux:subheading class="text-[10px] mt-2 italic">
                Detail akun buyer akan muncul di sini untuk membantu kamu memberikan support terbaik.
            </flux:subheading>
        </div>
    @endif

    {{-- Bulk Broadcast Modal --}}
    <flux:modal name="broadcast-modal" wire:model="showBroadcastModal" class="max-w-md">
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <x-lucide-megaphone class="w-6 h-6 text-white" />
                </div>
                <div>
                    <flux:heading size="lg">Pesan Siaran</flux:heading>
                    <flux:subheading>Kirim pesan ke seluruh pembeli produk tertentu.</flux:subheading>
                </div>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Pilih Produk Target</flux:label>
                    <flux:select wire:model="broadcastProductId" placeholder="-- Pilih Produk --">
                        @foreach($authorProducts as $prod)
                            <flux:select.option value="{{ $prod->id }}">{{ $prod->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Hanya pembeli dengan status order 'Completed' yang akan menerima pesan ini.</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Isi Pesan Broadcast</flux:label>
                    <flux:textarea 
                        wire:model="broadcastMessage" 
                        rows="5" 
                        placeholder="Halo pembeli seklian! Ada kabar gembira..." 
                    />
                </flux:field>

                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-2xl">
                    <div class="flex gap-3">
                        <flux:icon.information-circle class="w-5 h-5 text-amber-600 flex-shrink-0" />
                        <p class="text-[10px] text-amber-700 dark:text-amber-400 leading-relaxed font-medium">
                            Gunakan fitur ini secara bijak. Spamming dapat menurunkan reputasi toko kamu dan dilaporkan oleh buyer.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <flux:button class="flex-1" x-on:click="Flux.modal('broadcast-modal').close()">Batal</flux:button>
                    <flux:button variant="primary" class="flex-1 !bg-emerald-500" wire:click="sendBroadcast" wire:loading.attr="disabled">Kirim Sekarang</flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    {{-- Auto-Responder Management Modal --}}
    <flux:modal name="auto-responder-settings" wire:model="showAutoReplyModal" class="max-w-md">
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center">
                    <x-lucide-bot class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <flux:heading size="lg">Auto-Responder</flux:heading>
                    <flux:subheading italic>Balas buyer secara otomatis saat chat masuk.</flux:subheading>
                </div>
            </div>

            <div class="space-y-6">
                <flux:field variant="inline">
                    <flux:label class="font-bold">Aktifkan Auto-Reply</flux:label>
                    <flux:switch wire:model="autoReplyEnabled" />
                </flux:field>

                <flux:field>
                    <flux:label>Pesan Otomatis</flux:label>
                    <flux:textarea 
                        wire:model="autoReplyMessage" 
                        rows="4" 
                        placeholder="Halo! Saya akan segera membalas..." 
                        :disabled="!$autoReplyEnabled"
                    />
                    <flux:description>Pesan ini akan terkirim otomatis saat buyer memulai percakapan baru dengan kamu.</flux:description>
                </flux:field>

                <div class="flex gap-3 pt-2">
                    <flux:button class="flex-1" x-on:click="Flux.modal('auto-responder-settings').close()">Batal</flux:button>
                    <flux:button variant="primary" class="flex-1" wire:click="saveAutoReply">Simpan Perubahan</flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    {{-- Canned Responses Management Modal --}}
    <flux:modal name="manage-canned" wire:model="showCannedModal" class="max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Manage Message Templates</flux:heading>
                <flux:subheading>Save common answers to reply faster to your buyers.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Template Title</flux:label>
                    <flux:input wire:model="newCannedTitle" placeholder="e.g., Installation Guide" />
                </flux:field>
                <flux:field>
                    <flux:label>Message Content</flux:label>
                    <flux:textarea wire:model="newCannedContent" rows="4" placeholder="Type your common response here..." />
                </flux:field>
                <flux:button variant="primary" class="w-full" wire:click="saveCannedResponse">Save Template</flux:button>
            </div>

            <flux:separator />

            <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                @foreach($cannedResponses as $canned)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 flex justify-between items-center group">
                        <div class="min-w-0">
                            <h4 class="font-bold text-sm">{{ $canned->title }}</h4>
                            <p class="text-xs text-zinc-500 truncate">{{ $canned->content }}</p>
                        </div>
                        <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500 opacity-0 group-hover:opacity-100" wire:click="deleteCannedResponse({{ $canned->id }})" />
                    </div>
                @endforeach
            </div>

            <div class="flex">
                <flux:button class="flex-1" x-on:click="Flux.modal('manage-canned').close()">Close</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Send Quote Modal --}}
    <flux:modal name="send-quote" wire:model="showQuoteModal" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Send Custom Offer</flux:heading>
                <flux:subheading>Propose a specialized price for custom requests or bundles.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Offer Amount (IDR)</flux:label>
                    <flux:input wire:model="quoteAmount" type="number" placeholder="e.g., 150000" />
                </flux:field>
                <flux:field>
                    <flux:label>Offer Description</flux:label>
                    <flux:textarea wire:model="quoteDescription" rows="3" placeholder="Explain what's included in this offer..." />
                </flux:field>
                <flux:button variant="primary" class="w-full" wire:click="sendQuote">Send Custom Offer</flux:button>
            </div>

            <div class="flex">
                <flux:button class="flex-1" x-on:click="Flux.modal('send-quote').close()">Cancel</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Report Buyer Modal --}}
    <flux:modal name="report-buyer" wire:model="showReportModal" class="max-w-md">
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-500 flex items-center justify-center shadow-lg shadow-rose-500/20">
                    <flux:icon.shield-exclamation class="w-6 h-6 text-white" />
                </div>
                <div>
                    <flux:heading size="lg">Laporkan Buyer</flux:heading>
                    <flux:subheading>Laporkan perilaku yang tidak sesuai kepada admin</flux:subheading>
                </div>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Kategori Laporan</flux:label>
                    <flux:select wire:model="reportCategory" placeholder="Pilih kategori...">
                        <option value="abusive_language">Bahasa Kasar / Tidak Sopan</option>
                        <option value="spam">Spam / Pesan Berulang</option>
                        <option value="refund_abuse">Penyalahgunaan Refund</option>
                        <option value="payment_issues">Masalah Pembayaran</option>
                        <option value="other">Lainnya</option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Alasan Laporan</flux:label>
                    <flux:description>Jelaskan detail masalah yang kamu alami (minimal 10 karakter)</flux:description>
                    <flux:textarea wire:model="reportReason" rows="4" placeholder="Jelaskan detail masalah yang kamu alami dengan buyer ini..." />
                </flux:field>
            </div>

            <div class="flex gap-2">
                <flux:button variant="ghost" class="flex-1" x-on:click="Flux.modal('report-buyer').close()">Batal</flux:button>
                <flux:button variant="danger" class="flex-1" wire:click="submitReport">Kirim Laporan</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Image Preview Modal --}}
    <flux:modal name="modal-image-preview-author-chat" class="max-w-5xl" variant="subtle" x-on:close="previewImage = null">
        <div class="space-y-6">
            <div class="flex flex-col items-center">
                <img id="author-preview-img" src="" class="max-w-full max-h-[80vh] rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800">
            </div>

            <div class="flex">
                <flux:button class="flex-1" x-on:click="Flux.modal('modal-image-preview-author-chat').close()">Tutup Pratinjau</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Private Notes --}}
    <flux:modal name="private-notes-modal" wire:model="showNotesModal" variant="thin" class="w-full max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Private Notes</flux:heading>
                <flux:subheading>This note is only visible to you and other authors. The buyer cannot see this.</flux:subheading>
            </div>

            <flux:textarea wire:model="privateNotes" label="Your Notes" rows="8" placeholder="e.g. Buyer needs help with installation, prefers email follow-up..." />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" x-on:click="Flux.modal('private-notes-modal').close()">Cancel</flux:button>
                <flux:button variant="primary" wire:click="saveNotes">Save Notes</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Schedule Message --}}
    <flux:modal name="schedule-message-modal" wire:model="showScheduleModal" variant="thin" class="w-full max-w-lg">
        <form wire:submit="scheduleMessage" class="space-y-6">
            <div>
                <flux:heading size="lg">Schedule Message</flux:heading>
                <flux:subheading>This message will be sent automatically at the specified time.</flux:subheading>
            </div>

            <flux:textarea wire:model="scheduleMessage" label="Message Content" rows="4" placeholder="Type your message here..." />

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="date" wire:model="scheduleDate" label="Date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                <flux:input type="time" wire:model="scheduleTime" label="Time" />
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" x-on:click="Flux.modal('schedule-message-modal').close()">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Schedule Now</flux:button>
            </div>
        </form>
    </flux:modal>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #52525b; }
        .chat-bg-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#f1f1f1 0.5px, transparent 0.5px), radial-gradient(#f1f1f1 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }
        .dark .chat-bg-pattern {
            background-color: #09090b;
            background-image: radial-gradient(#18181b 0.5px, transparent 0.5px), radial-gradient(#18181b 0.5px, #09090b 0.5px);
        }
    </style>
</div>
