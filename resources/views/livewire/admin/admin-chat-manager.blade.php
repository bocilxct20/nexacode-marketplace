<div class="h-[calc(100vh-12rem)] flex gap-4 pointer-events-auto relative z-[100]" x-data="{ 
    replyMessage: @entangle('replyMessage'),
    isTyping: false,
    typingTimeout: null,
    otherTyping: false,
    uploading: false,
    previewImage: null,
    previewIndex: 0,
    galleryImages: [],
    showSlashMenu: false,
    slashQuery: '',
    slashResults: [],
    slashIndex: 0,
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
            const container = document.getElementById('admin-chat-container');
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
        console.log('ADMIN CHAT: ALPINE INITIALIZING...');
        const self = this;
        
        window.addEventListener('scroll-to-bottom-admin', () => {
            self.scrollToBottom();
            self.playSound();
        });
        
        self.scrollToBottom();

        self.$watch('otherTyping', value => { 
            if (value) self.scrollToBottom();
        });

        Livewire.on('conversation-selected', (data) => {
            const id = data.id || data;
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
        
        console.log('ADMIN CHAT: ALPINE LOADED SUCCESSFULLY');
    },
    handleSlash(e) {
        const val = e.target.value;
        const cursor = e.target.selectionStart;
        const beforeCursor = val.substring(0, cursor);
        const slashIndex = beforeCursor.lastIndexOf('/');

        if (slashIndex !== -1) {
            this.showSlashMenu = true;
            this.slashQuery = beforeCursor.substring(slashIndex + 1).toLowerCase();
            this.slashIndex = 0;
            // The actual filtering happens in Livewire or we can do it here if we pass canned responses to Alpine
        } else {
            this.showSlashMenu = false;
        }
    },
    openImage(src, index, gallery) {
        this.previewImage = src;
        this.previewIndex = index;
        this.galleryImages = gallery;
        document.getElementById('admin-preview-img').src = src;
        Flux.modal('modal-image-preview-admin-chat').show();
    },
    nextImage() {
        if (this.previewIndex < this.galleryImages.length - 1) {
            this.previewIndex++;
            this.previewImage = this.galleryImages[this.previewIndex];
            document.getElementById('admin-preview-img').src = this.previewImage;
        }
    },
    prevImage() {
        if (this.previewIndex > 0) {
            this.previewIndex--;
            this.previewImage = this.galleryImages[this.previewIndex];
            document.getElementById('admin-preview-img').src = this.previewImage;
        }
    }
}" x-init="initChat()">
    {{-- Sidebar: Conversation List --}}
    <div class="w-80 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden shadow-sm relative z-10">
        <div class="px-6 pb-6 pt-6 border-b border-zinc-200 dark:border-zinc-800 space-y-4 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md relative z-20">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">Support Inbox</flux:heading>
                <div class="flex gap-1 items-center">
                    <button wire:click="$toggle('showArchived')" class="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-colors" title="{{ $showArchived ? 'Hide Archived' : 'Show Archived' }}">
                        @if($showArchived)
                            <flux:icon.archive-box-x-mark size="sm" class="text-emerald-500" />
                        @else
                            <flux:icon.archive-box size="sm" class="text-zinc-400" />
                        @endif
                    </button>
                    <flux:button variant="subtle" size="xs" icon="cog-8-tooth" x-on:click="Flux.modal('auto-responder-settings').show()" square title="Auto-Responder Settings" />
                </div>
            </div>
            
            <div class="space-y-3">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search name or email..." 
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
                        All
                    </flux:button>
                    <flux:button 
                        wire:click="$set('filter', 'unread')" 
                        size="xs" 
                        :variant="$filter === 'unread' ? 'primary' : 'subtle'"
                        class="flex-1"
                    >
                        Unread
                    </flux:button>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
            {{-- Conversation List Skeletons --}}
            <div wire:loading.flex wire:target="search, filter, showArchived" class="flex-col gap-1 p-1">
                @foreach(range(1, 10) as $i)
                    <div class="p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800/50 space-y-3 opacity-50 bg-white/50 dark:bg-zinc-800/30">
                        <div class="flex items-center gap-3">
                            <flux:skeleton class="size-10 rounded-full" />
                            <div class="flex-1 space-y-2">
                                <div class="flex justify-between items-center">
                                    <flux:skeleton class="w-1/2 h-3" />
                                    <flux:skeleton class="w-8 h-2" />
                                </div>
                                <flux:skeleton class="w-3/4 h-2" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div wire:loading.remove wire:target="search, filter, showArchived">
                @forelse($conversations as $conv)
                <button 
                    wire:click="selectConversation({{ $conv->id }})"
                    wire:key="conv-{{ $conv->id }}"
                    class="w-full p-4 flex items-center gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-all border-b border-zinc-100 dark:border-zinc-800/50 relative group cursor-pointer {{ $selectedConversationId == $conv->id ? 'bg-emerald-50/50 dark:bg-emerald-500/10' : '' }}"
                >
                    @if($selectedConversationId == $conv->id)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r-full"></div>
                    @endif
                    <div class="relative flex-shrink-0">
                        <flux:avatar :name="$conv->user?->name ?? 'Guest'" :initials="$conv->user?->initials ?? 'G'" size="sm" />
                        @if($conv->user?->isOnline())
                            <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white dark:border-zinc-900" title="Online"></div>
                        @endif
                    </div>
                    <div class="flex-1 text-left min-w-0">
                        <div class="flex justify-between items-start">
                            <flux:heading size="sm" class="truncate">{{ $conv->user?->name ?? 'Guest' }}</flux:heading>
                            <span class="text-[10px] text-zinc-400 whitespace-nowrap">{{ $conv->last_message_at?->diffForHumans(short: true) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <flux:subheading size="xs" class="truncate">
                                    {{ $conv->latestMessage?->message ?? 'No messages yet' }}
                                </flux:subheading>
                                @if($conv->last_buyer_message_at)
                                    <div class="mt-1 flex items-center gap-1.5" x-data="{ 
                                        time: '',
                                        start: @js($conv->last_buyer_message_at->timestamp),
                                        now: Math.floor(Date.now() / 1000),
                                        update() {
                                            const diff = Math.floor(Date.now() / 1000) - this.start;
                                            const mins = Math.floor(diff / 60);
                                            const secs = diff % 60;
                                            this.time = mins + 'm ' + secs + 's';
                                            this.$el.className = 'mt-1 flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider ' + 
                                                (mins >= 15 ? 'text-rose-500 animate-pulse' : (mins >= 5 ? 'text-amber-500' : 'text-emerald-500'));
                                        }
                                    }" x-init="update(); setInterval(() => update(), 1000)">
                                        <div class="w-1 h-1 rounded-full bg-current"></div>
                                        <span x-text="'Waiting ' + time"></span>
                                    </div>
                                @endif
                            </div>
                            @php $unreadCount = $conv->unreadMessages()->where('is_admin', false)->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="bg-emerald-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                </button>
                @empty
                    <div wire:loading.remove wire:target="search, filter, showArchived" class="p-12 text-center">
                        <p class="text-sm text-zinc-400">No active chats</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Chat Window & Context --}}
    <div class="flex-1 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 flex overflow-hidden shadow-sm relative">
        @if($activeConversation)
            {{-- Main Column --}}
            <div class="flex-1 flex flex-col min-w-0 bg-zinc-50/50 dark:bg-zinc-950/20 relative">
                {{-- Message Area Loading Overlay (Refined) --}}
                <div wire:loading.flex wire:target="selectConversation, sendMessage, sendVoiceNote" class="absolute inset-0 z-50 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md items-center justify-center animate-in fade-in duration-300">
                    <div class="w-full max-w-md p-8 space-y-10">
                        @foreach(range(1, 4) as $i)
                            <div class="flex {{ $i % 2 == 0 ? 'justify-end' : 'justify-start' }} opacity-50">
                                <div class="w-2/3 space-y-3">
                                    <div class="flex items-end gap-3 {{ $i % 2 == 0 ? 'flex-row-reverse' : '' }}">
                                        <flux:skeleton class="size-8 rounded-full shrink-0" />
                                        <flux:skeleton class="h-14 w-full rounded-2xl {{ $i % 2 == 0 ? 'rounded-tr-none' : 'rounded-tl-none' }}" />
                                    </div>
                                    <div class="flex {{ $i % 2 == 0 ? 'justify-start' : 'justify-end' }}">
                                        <flux:skeleton class="w-12 h-2" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- Header --}}
                <div class="p-4 sm:p-6 border-b border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl relative z-30">
                    <div class="flex justify-between items-center gap-4">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <flux:avatar :name="$activeConversation->user?->name ?? 'Guest'" :initials="$activeConversation->user?->initials ?? 'G'" />
                            <div class="min-w-0 flex-1">
                                <flux:heading size="lg" class="truncate !text-base sm:!text-lg">
                                    {{ $activeConversation->user?->name ?? 'Guest User' }}
                                </flux:heading>
                                <div class="flex items-center gap-2 mt-0.5 min-w-0">
                                    <span class="text-[10px] text-zinc-400 uppercase font-black tracking-widest opacity-70 truncate">
                                        {{ $activeConversation->user?->email ?? 'Session: ' . $activeConversation->session_id }}
                                    </span>
                                    
                                    @if($activeConversation->current_context)
                                        <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 animate-pulse">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></div>
                                            <span class="text-[9px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tabular-nums">
                                                Viewing: {{ $activeConversation->current_context['title'] ?? 'Marketplace' }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if($activeConversation->last_buyer_message_at)
                                        <div x-data="{ 
                                            time: '',
                                            start: @js($activeConversation->last_buyer_message_at->timestamp),
                                            update() {
                                                const diff = Math.floor(Date.now() / 1000) - this.start;
                                                const mins = Math.floor(diff / 60);
                                                this.time = mins + 'm';
                                                this.$el.className = 'flex items-center gap-1 px-2 py-0.5 rounded-md border font-black text-[9px] uppercase ' + 
                                                    (mins >= 15 ? 'bg-rose-500/10 border-rose-500/30 text-rose-500' : (mins >= 5 ? 'bg-amber-500/10 border-amber-500/30 text-amber-500' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500'));
                                            }
                                        }" x-init="update(); setInterval(() => update(), 10000)">
                                            <span x-text="'Wait: ' + time"></span>
                                        </div>
                                    @endif

                                    @if($activeConversation->tags)
                                        <div class="flex gap-1 flex-wrap">
                                            @foreach($activeConversation->tags as $tag)
                                                <flux:badge size="sm" color="{{ $availableTags[$tag]['color'] ?? 'zinc' }}" variant="subtle" class="!text-[8px] !px-1 !py-0 uppercase font-black tracking-tighter">
                                                    {{ $availableTags[$tag]['label'] ?? $tag }}
                                                </flux:badge>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            <flux:dropdown>
                                <flux:button variant="subtle" size="sm" icon="tag" square title="Add Tag" />
                                <flux:menu>
                                    @foreach($availableTags as $key => $tag)
                                        <flux:menu.item wire:click="toggleTag('{{ $key }}')" :icon="in_array($key, $activeConversation->tags ?? []) ? 'check' : 'tag'" :class="in_array($key, $activeConversation->tags ?? []) ? 'text-' . $tag['color'] . '-500 font-bold' : ''">
                                            {{ $tag['label'] }}
                                        </flux:menu.item>
                                    @endforeach
                                </flux:menu>
                            </flux:dropdown>
                            <flux:button wire:click="$toggle('searchActive')" variant="subtle" size="sm" icon="magnifying-glass" square title="Cari di Percakapan" :class="$searchActive ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'opacity-60'" />
                            <flux:button wire:click="$toggle('showGallery')" variant="subtle" size="sm" icon="photo" square title="Galeri File" :class="$showGallery ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'opacity-60'" />
                            <flux:button wire:click="resolveConversation" variant="subtle" size="sm" icon="check-circle" square class="text-emerald-600 font-bold" title="Resolve Conversation" />
                            <flux:button wire:click="toggleArchive" variant="subtle" size="sm" icon="archive-box" square class="text-zinc-500 font-bold" title="Archive Conversation" />
                        </div>
                    </div>
                    @if($searchActive)
                        <div class="px-6 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/10 dark:bg-black/10 animate-in slide-in-from-top duration-300">
                            <flux:input 
                                wire:model.live.debounce.300ms="msgSearch" 
                                placeholder="Cari pesan di percakapan ini..." 
                                icon="magnifying-glass"
                                class="w-full"
                                clearable
                                x-init="$nextTick(() => $el.focus())"
                            />
                        </div>
                    @endif
                </div>

                {{-- Messages Area --}}
                <div id="admin-chat-container" class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-6 chat-bg-pattern custom-scrollbar relative z-10 scroll-smooth">
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->is_admin ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $msg->id }}">
                            <div class="max-w-[85%] sm:max-w-[70%] {{ !$msg->is_admin ? 'min-w-[140px]' : 'min-w-[120px]' }}">
                                <div class="
                                    {{ $msg->type === 'quote' ? '' : 'p-4 sm:p-5 pb-2 rounded-2xl shadow-md border-b-[3px] border-black/5' }}
                                    text-sm leading-relaxed relative group
                                    {{ $msg->type === 'quote' 
                                        ? '' 
                                        : ($msg->is_admin 
                                            ? 'bg-emerald-500 text-white rounded-tr-none shadow-emerald-500/10' 
                                            : 'bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-tl-none') 
                                    }}
                                ">
                                    <div class="prose dark:prose-invert prose-sm max-w-none {{ $msg->is_admin ? 'text-white' : '' }}">
                                        @if($msg->voice_path)
                                            {{-- Voice Rendering --}}
                                            <div class="flex items-center gap-3 py-2 min-w-[200px]" x-data="{ 
                                                audio: null, playing: false, progress: 0,
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
                                                <button @click="togglePlay" class="w-10 h-10 rounded-full flex items-center justify-center transition-all bg-white/20 hover:bg-white/30">
                                                    <template x-if="!playing"><flux:icon.play variant="mini" class="w-5 h-5" /></template>
                                                    <template x-if="playing"><flux:icon.pause variant="mini" class="w-5 h-5" /></template>
                                                </button>
                                                <div class="flex-1 space-y-1">
                                                    <div class="h-1 bg-white/20 rounded-full overflow-hidden">
                                                        <div class="h-full bg-current transition-all" :style="'width: ' + progress + '%'"></div>
                                                    </div>
                                                    <div class="text-[10px] opacity-70">Voice Note ({{ $msg->voice_duration }}s)</div>
                                                </div>
                                            </div>
                                        @elseif(in_array(pathinfo($msg->image_path, PATHINFO_EXTENSION), ['zip', 'rar', 'pdf', 'doc', 'docx']))
                                            {{-- File Rendering --}}
                                            <a href="{{ Storage::url($msg->image_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-black/5 dark:bg-white/5 hover:bg-black/10 transition-colors no-underline border border-white/10">
                                                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                                    @if(pathinfo($msg->image_path, PATHINFO_EXTENSION) === 'pdf')
                                                        <flux:icon.document-text class="w-6 h-6 text-emerald-500" />
                                                    @else
                                                        <flux:icon.archive-box class="w-6 h-6 text-emerald-500" />
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-[11px] font-bold truncate {{ $msg->is_admin ? 'text-white' : 'text-zinc-900 dark:text-white' }}">
                                                        {{ basename($msg->image_path) }}
                                                    </div>
                                                    <div class="text-[9px] opacity-50 uppercase font-black tracking-widest">
                                                        {{ pathinfo($msg->image_path, PATHINFO_EXTENSION) }}
                                                        @if(Storage::disk('public')->exists($msg->image_path))
                                                            • {{ round(Storage::disk('public')->size($msg->image_path) / 1024, 1) }} KB
                                                        @endif
                                                    </div>
                                                </div>
                                                <flux:icon.arrow-down-tray size="sm" class="opacity-50" />
                                            </a>
                                        @else
                                            {!! App\Helpers\MarkdownHelper::render($msg->message) !!}
                                        @endif
                                    </div>

                                    @if($msg->image_path && !in_array(pathinfo($msg->image_path, PATHINFO_EXTENSION), ['zip', 'rar', 'pdf', 'doc', 'docx']) && !$msg->voice_path)
                                        <div class="mt-2">
                                            @php
                                                $gallery = $messages->filter(fn($m) => $m->image_path && !in_array(pathinfo($m->image_path, PATHINFO_EXTENSION), ['zip', 'rar', 'pdf', 'doc', 'docx']) && !$m->voice_path)->map(fn($m) => Storage::url($m->image_path))->values()->toArray();
                                                $currentIndex = array_search(Storage::url($msg->image_path), $gallery);
                                            @endphp
                                            <img 
                                                src="{{ Storage::url($msg->image_path) }}" 
                                                class="rounded-xl max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity" 
                                                x-on:click="openImage('{{ Storage::url($msg->image_path) }}', {{ $currentIndex }}, @js($gallery))"
                                            >
                                        </div>
                                    @endif

                                    @if($msg->type !== 'quote')
                                        <div class="mt-1 flex items-center justify-end gap-1.5 opacity-60 text-[9px] font-medium {{ $msg->is_admin ? 'text-emerald-50' : 'text-zinc-500' }}">
                                            <span>{{ $msg->created_at->format('H:i') }}</span>
                                            @if($msg->is_admin)
                                                <span class="inline-flex items-center">
                                                    @if($msg->is_read)
                                                        <flux:icon.check-badge size="xs" class="w-3 h-3 text-white" />
                                                    @else
                                                        <flux:icon.check size="xs" class="w-3 h-3 opacity-50" />
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Reactions Display --}}
                                    @if($msg->reactions)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($msg->reactions as $emoji => $users)
                                                <button 
                                                    wire:click="toggleReaction('{{ $msg->id }}', '{{ $emoji }}')"
                                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] hover:scale-110 transition-transform {{ in_array(Auth::id(), $users) ? ' ring-2 ring-emerald-500' : '' }}"
                                                >
                                                    <span>{{ $emoji }}</span>
                                                    <span class="font-bold text-zinc-500">{{ count($users) }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Reaction Trigger (Hover) --}}
                                    <div class="absolute -top-4 {{ $msg->is_admin ? 'right-0' : 'left-0' }} hidden group-hover:flex items-center gap-1 p-1 bg-white dark:bg-zinc-800 rounded-full shadow-xl border border-zinc-100 dark:border-zinc-700 z-50 animate-in fade-in zoom-in duration-200">
                                        @foreach(['👍', '❤️', '🔥', '👏', '😂', '😮'] as $emoji)
                                            <button 
                                                wire:click="toggleReaction('{{ $msg->id }}', '{{ $emoji }}')"
                                                class="w-6 h-6 flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-full transition-colors"
                                            >
                                                {{ $emoji }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Typing Indicator --}}
                    <template x-if="otherTyping">
                        <div class="flex justify-start animate-in slide-in-from-left duration-300">
                            <div class="bg-white dark:bg-zinc-800 px-4 py-2.5 rounded-2xl rounded-tl-none border border-zinc-100 dark:border-zinc-700 shadow-sm flex items-center gap-2">
                                <div class="flex gap-1">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></span>
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                                </div>
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider">Customer is typing...</span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Input Area --}}
                <div class="p-4 sm:p-6 border-t border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl relative z-30">
                    @if($protectionWarning)
                        <div class="mb-4 p-3 rounded-xl bg-orange-500/10 border border-orange-500/30 text-orange-600 dark:text-orange-400 text-[10px] font-bold flex items-center gap-2 animate-in slide-in-from-bottom duration-300">
                            <flux:icon.exclamation-triangle size="sm" />
                            {{ $protectionWarning }}
                        </div>
                    @endif

                    @if($attachment)
                        <div class="mb-4 relative inline-block group animate-in slide-in-from-bottom duration-300">
                            <img src="{{ $attachment->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-2xl border-2 border-emerald-500/50 shadow-lg">
                            <flux:button wire:click="$set('attachment', null)" variant="danger" size="sm" icon="x-mark" class="absolute -top-2 -right-2 !rounded-full !p-1 shadow-xl hover:rotate-90 transition-transform" />
                        </div>
                    @endif

                    <form wire:submit="sendMessage" class="relative">
                        {{-- Slash Command Menu --}}
                        <div 
                            x-show="showSlashMenu" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute bottom-full left-0 w-64 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl drop-shadow-2xl border border-zinc-200 dark:border-zinc-800 mb-6 overflow-hidden z-[9999]"
                            @click.away="showSlashMenu = false"
                        >
                            <div class="p-3 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50">
                                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Pilih Template (/)</span>
                            </div>
                            <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                @foreach(\App\Models\CannedResponse::where('user_id', Auth::id())->get() as $index => $canned)
                                    <button 
                                        type="button"
                                        x-show="'{{ strtolower($canned->title) }}'.includes(slashQuery)"
                                        @click="
                                            const val = replyMessage;
                                            const slashIdx = val.lastIndexOf('/');
                                            replyMessage = val.substring(0, slashIdx) + @js($canned->content);
                                            showSlashMenu = false;
                                            $refs.composer.focus();
                                        "
                                        class="w-full p-3 text-left hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors border-b border-zinc-50 dark:border-zinc-800/50 group"
                                    >
                                        <div class="text-[11px] font-bold text-zinc-900 dark:text-white group-hover:text-emerald-500 transition-colors">{{ $canned->title }}</div>
                                        <div class="text-[9px] text-zinc-500 truncate">{{ $canned->content }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <flux:composer 
                            x-ref="composer"
                            name="replyMessage"
                            wire:model="replyMessage" 
                            @input="setTyping(); handleSlash($event)"
                            @keydown.escape="showSlashMenu = false"
                            placeholder="Type your reply here..." 
                            label="Reply" 
                            label:sr-only
                            rows="2"
                            max-rows="6"
                            class="!bg-zinc-50 dark:!bg-zinc-800/50 !border-none !shadow-none !rounded-2xl"
                        >
                            <x-slot name="actionsLeading">
                                <div class="flex gap-1">
                                    <label class="cursor-pointer group">
                                        <flux:button as="div" size="sm" variant="subtle" icon="paper-clip" class="group-hover:bg-zinc-100 dark:group-hover:bg-zinc-700 transition-colors" square />
                                        <input type="file" wire:model="attachment" class="hidden" accept="image/*,.pdf,.zip,.rar">
                                    </label>
                                    <flux:button variant="subtle" size="sm" icon="chat-bubble-bottom-center-text" x-on:click="Flux.modal('canned-responses-modal').show()" square title="Message Templates" />
                                    <flux:button variant="subtle" size="sm" @click="toggleRecording" square title="Voice Note" x-bind:class="recording ? 'text-red-500 animate-pulse focus:ring-red-500' : ''">
                                        <template x-if="!recording"><flux:icon.microphone variant="mini" /></template>
                                        <template x-if="recording"><flux:icon.stop variant="mini" class="text-red-500" /></template>
                                    </flux:button>
                                    <flux:button variant="subtle" size="sm" icon="clock" x-on:click="Flux.modal('schedule-modal').show()" square title="Schedule Message" />
                                </div>
                            </x-slot>

                            <x-slot name="actionsTrailing">
                                <div class="flex items-center gap-3">
                                    <template x-if="recording">
                                        <span class="text-[10px] font-black font-mono text-red-500 animate-pulse" x-text="formatTime(duration)"></span>
                                    </template>
                                    <flux:button type="submit" size="sm" variant="primary" icon="paper-airplane" wire:loading.attr="disabled" class="!bg-emerald-500 hover:!bg-emerald-600 shadow-lg shadow-emerald-500/20" />
                                </div>
                            </x-slot>
                        </flux:composer>
                    </form>
                </div>
            </div>

            {{-- Right Sidebar (Customer Profile) --}}
            @if(!$showGallery)
                <div 
                    class="w-72 border-l border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md flex flex-col flex-shrink-0 animate-in slide-in-from-right duration-500 overflow-y-auto custom-scrollbar"
                >
                    {{-- User Stats --}}
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                        <div class="flex items-center gap-3 mb-4">
                            <flux:avatar :name="$activeConversation->user?->name" size="sm" />
                            <div>
                                <div class="flex items-center gap-2">
                                    <flux:heading size="sm">Customer Profile</flux:heading>
                                    @if($buyerStats['reputation'] === 'vip')
                                        <flux:badge color="purple" size="sm" class="!text-[8px] !px-1.5 !py-0 uppercase font-black tracking-tighter animate-pulse">VIP Buyer</flux:badge>
                                    @elseif($buyerStats['reputation'] === 'high_risk')
                                        <flux:badge color="red" size="sm" class="!text-[8px] !px-1.5 !py-0 uppercase font-black tracking-tighter">High Risk</flux:badge>
                                    @endif
                                </div>
                                <flux:subheading size="xs" class="!text-[10px]">Joined {{ $buyerStats['joined_at'] }}</flux:subheading>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <flux:card class="p-3 transition-transform hover:scale-[1.02] !rounded-2xl shadow-sm border-zinc-100 dark:border-zinc-800">
                                <flux:subheading size="xs" class="font-bold uppercase !text-[9px] mb-1">Spent</flux:subheading>
                                <flux:heading size="sm" class="!text-xs !font-black text-emerald-600">Rp {{ number_format($buyerStats['total_spent'], 0, ',', '.') }}</flux:heading>
                            </flux:card>
                            <flux:card class="p-3 transition-transform hover:scale-[1.02] !rounded-2xl shadow-sm border-zinc-100 dark:border-zinc-800">
                                <flux:subheading size="xs" class="font-bold uppercase !text-[9px] mb-1">Refunds</flux:subheading>
                                <flux:heading size="sm" class="!text-xs !font-black text-red-500">{{ $buyerStats['refund_count'] }}</flux:heading>
                            </flux:card>
                        </div>
                    </div>

                    {{-- Private Notes --}}
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex justify-between items-center mb-3">
                            <flux:heading size="sm" class="uppercase tracking-wider !text-[10px]">Private Notes</flux:heading>
                            <flux:button variant="subtle" size="xs" icon="pencil-square" wire:click="loadNotes" x-on:click="Flux.modal('notes-modal').show()" square />
                        </div>
                        <flux:card class="bg-yellow-50/50 dark:bg-yellow-500/5 border-yellow-200/50 dark:border-yellow-500/20 shadow-none overflow-hidden">
                            <div class="p-3 max-h-32 overflow-y-auto custom-scrollbar text-[11px] text-zinc-600 dark:text-zinc-400 italic">
                                {{ $activeConversation->private_notes ?? 'No notes for this customer yet.' }}
                            </div>
                        </flux:card>
                    </div>

                    {{-- Order Timeline --}}
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                        <flux:heading size="sm" class="uppercase tracking-wider !text-[10px] mb-4">Recent Transactions</flux:heading>
                        <div class="space-y-3">
                            @forelse($buyerStats['recent_orders'] as $order)
                                <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 shadow-sm transition-transform hover:scale-[1.02]">
                                    <div class="min-w-0">
                                        <div class="text-[10px] font-bold truncate text-zinc-900 dark:text-white">#{{ $order->transaction_id }}</div>
                                        <div class="text-[9px] text-zinc-500">{{ $order->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] font-black text-zinc-900 dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                        @php
                                            $color = match($order->status) {
                                                'completed' => 'emerald',
                                                'pending' => 'yellow',
                                                'refunded' => 'red',
                                                default => 'zinc'
                                            };
                                        @endphp
                                        <flux:badge :color="$color" size="sm" class="!text-[7px] !px-1 !py-0 uppercase font-black">{{ $order->status }}</flux:badge>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-[10px] text-zinc-500 italic">No recent transactions found.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Support History --}}
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <flux:heading size="sm" class="uppercase tracking-wider !text-[10px]">Support History</flux:heading>
                            <span class="text-[9px] font-bold text-zinc-400">{{ count($buyerStats['past_conversations']) }} Logs</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($buyerStats['past_conversations'] as $past)
                                <div 
                                    class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors cursor-pointer group"
                                    wire:click="selectConversation({{ $past->id }})"
                                >
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="text-[10px] font-bold text-zinc-900 dark:text-white truncate">Ticket #{{ $past->id }}</div>
                                        <flux:icon.arrow-right size="xs" class="opacity-0 group-hover:opacity-100 transition-opacity text-emerald-500" />
                                    </div>
                                    <div class="text-[9px] text-zinc-500 flex items-center gap-1.5">
                                        <flux:icon.clock size="xs" class="w-3 h-3" />
                                        Resolved {{ $past->updated_at->diffForHumans() }}
                                    </div>
                                    @if($past->tags)
                                        <div class="flex gap-1 mt-2 flex-wrap">
                                            @foreach(array_slice($past->tags, 0, 2) as $tag)
                                                <span class="text-[7px] px-1 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-700 text-zinc-500 font-bold uppercase">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4 text-[10px] text-zinc-500 italic">First-time support request.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            @endif

            {{-- Gallery Sidebar --}}
            @if($showGallery)
                <div class="w-72 border-l border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md flex flex-col flex-shrink-0 animate-in slide-in-from-right duration-500 overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <flux:heading size="sm">File Gallery</flux:heading>
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
                                            x-on:click="previewImage = '{{ Storage::url($file->image_path) }}'; document.getElementById('admin-preview-img').src = previewImage; Flux.modal('modal-image-preview-admin-chat').show()"
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
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">No files found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-center p-12 bg-white dark:bg-zinc-900">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-emerald-500/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-24 h-24 rounded-full bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center mb-6 relative">
                        <x-lucide-messages-square class="w-12 h-12 text-zinc-300 dark:text-zinc-700" />
                    </div>
                </div>
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2 tracking-tight">Support Management</h3>
                <p class="text-zinc-500 dark:text-zinc-400 max-w-[280px] leading-relaxed italic text-sm">
                    Select a conversation from the sidebar to provide world-class assistance.
                </p>
            </div>
        @endif
    </div>

    {{-- Modals Area --}}
    <flux:modal name="canned-responses-modal" class="min-w-[500px]">
        <div class="space-y-6">
            <flux:heading size="lg">Template Pesan</flux:heading>
            <div class="space-y-4">
                <div class="max-h-64 overflow-y-auto space-y-2 custom-scrollbar pr-2">
                    @forelse(\App\Models\CannedResponse::where('user_id', Auth::id())->latest()->get() as $canned)
                        <div class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 flex justify-between items-center group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="cursor-pointer flex-1" wire:click="useCannedResponse({{ $canned->id }})" x-on:click="Flux.modal('canned-responses-modal').hide()">
                                <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $canned->title }}</div>
                                <div class="text-xs text-zinc-500 truncate">{{ $canned->content }}</div>
                            </div>
                            <flux:button variant="subtle" size="xs" icon="trash" wire:click="deleteCannedResponse({{ $canned->id }})" square class="opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>
                    @empty
                        <div class="text-center py-8 text-zinc-400 text-xs">Belum ada template tersimpan.</div>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-4">
                    <flux:input wire:model="newCannedTitle" label="Judul Template" placeholder="Contoh: Greetings" />
                    <flux:textarea wire:model="newCannedContent" label="Isi Pesan" rows="3" />
                    <flux:button wire:click="saveCannedResponse" variant="primary" class="w-full">Simpan Template Baru</flux:button>
                </div>
            </div>
        </div>
    </flux:modal>


    <flux:modal name="auto-responder-settings" class="min-w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Auto-Responder Settings</flux:heading>
                <flux:subheading>Atur pesan otomatis saat anda tidak sedang online.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:switch wire:model="autoReplyEnabled" label="Aktifkan Balasan Otomatis" />
                <flux:textarea wire:model="autoReplyMessage" label="Pesan Balasan" rows="4" />
                <flux:button wire:click="saveAutoReply" variant="primary" class="w-full">Simpan Pengaturan</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="notes-modal" class="min-w-[500px]">
        <div class="space-y-6">
            <flux:heading size="lg">Customer Private Notes</flux:heading>
            <flux:textarea wire:model="privateNotes" label="Catatan Internal (Hanya dilihat Admin)" rows="6" placeholder="Tambahkan catatan khusus tentang customer ini..." />
            <flux:button wire:click="saveNotes" variant="primary" class="w-full">Simpan Catatan</flux:button>
        </div>
    </flux:modal>

    <flux:modal name="schedule-modal" class="min-w-[400px]">
        <div class="space-y-6">
            <flux:heading size="lg">Schedule Message</flux:heading>
            <flux:textarea wire:model="scheduleMessage" label="Pesan" rows="4" />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="date" wire:model="scheduleDate" label="Tanggal" />
                <flux:input type="time" wire:model="scheduleTime" label="Waktu" />
            </div>
            <flux:button wire:click="scheduleMessage" variant="primary" class="w-full">Jadwalkan Pesan</flux:button>
        </div>
    </flux:modal>

    <flux:modal name="modal-image-preview-admin-chat" class="max-w-5xl" variant="subtle" x-on:close="previewImage = null">
        <div class="space-y-6 relative group/modal">
            <div class="flex items-center justify-center p-4 min-h-[50vh]">
                <img id="admin-preview-img" :src="previewImage" class="rounded-2xl max-h-[70vh] w-auto shadow-2xl border border-white/10 transition-all duration-300">
                
                {{-- Navigation Overlays --}}
                <template x-if="galleryImages.length > 1">
                    <div class="absolute inset-0 flex items-center justify-between p-4 pointer-events-none">
                        <button @click="prevImage" class="w-12 h-12 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-all pointer-events-auto shadow-xl" x-show="previewIndex > 0">
                            <flux:icon.chevron-left class="w-6 h-6" />
                        </button>
                        <button @click="nextImage" class="w-12 h-12 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-all pointer-events-auto shadow-xl" x-show="previewIndex < galleryImages.length - 1">
                            <flux:icon.chevron-right class="w-6 h-6" />
                        </button>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-between gap-3 px-4">
                <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Image <span x-text="previewIndex + 1"></span> of <span x-text="galleryImages.length"></span>
                </div>
                <flux:button size="sm" x-on:click="Flux.modal('modal-image-preview-admin-chat').close()">Close Preview</flux:button>
            </div>
        </div>
    </flux:modal>

    <style>
        .chat-bg-pattern {
            background-color: transparent;
            background-image: radial-gradient(rgba(0,0,0,0.05) 1px, transparent 0);
            background-size: 24px 24px;
            background-position: -12px -12px;
        }
        .dark .chat-bg-pattern {
            background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 0);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.05);
        }
    </style>
</div>
