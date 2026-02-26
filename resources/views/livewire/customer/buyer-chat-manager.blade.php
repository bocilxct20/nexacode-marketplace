<div wire:init="load" class="h-[calc(100vh-12rem)] flex gap-6" x-data="{ 
    isTyping: false,
    typingTimeout: null,
    otherTyping: false,
    uploading: false,
    previewImage: null,
    playSound() {
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
        audio.play().catch(e => console.log('Audio play failed:', e));
    },
    setTyping() {
        const convId = @js($selectedConversationId);
        if (!convId) return;
        window.Echo.private('chat.' + convId)
            .whisper('typing', { typing: true });
        
        if (this.typingTimeout) clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            window.Echo.private('chat.' + convId)
                .whisper('typing', { typing: false });
        }, 3000);
    },
    scrollToBottom() {
        $nextTick(() => {
            const container = document.getElementById('buyer-chat-container');
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
    }
}" x-init="
    window.addEventListener('scroll-to-bottom-buyer', () => {
        scrollToBottom();
        playSound();
    });
    scrollToBottom();

    $watch('otherTyping', value => { if (value) scrollToBottom() });

    Livewire.on('conversation-selected', (data) => {
        const id = data.id || data;
        
        // Listen for typing whispers
        window.Echo.private('chat.' + id)
            .listenForWhisper('typing', (e) => {
                this.otherTyping = e.typing;
            })
            .listen('.message.sent', (e) => {
                Livewire.dispatch('refresh-chat');
            });
    });
">
    {{-- Sidebar: Conversation List --}}
    <div class="w-80 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">My Messages</flux:heading>
                <button wire:click="$toggle('showArchived')" class="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-colors" title="{{ $showArchived ? 'Hide Archived' : 'Show Archived' }}">
                    @if($showArchived)
                        <flux:icon.archive-box-x-mark size="sm" class="text-emerald-500" />
                    @else
                        <flux:icon.archive-box size="sm" class="text-zinc-400" />
                    @endif
                </button>
            </div>
            
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search conversations..." 
                icon="magnifying-glass"
                size="sm"
            />
            
            <div class="flex gap-2">
                <flux:button 
                    size="xs" 
                    :variant="$filter === 'all' ? 'primary' : 'ghost'"
                    wire:click="$set('filter', 'all')"
                >
                    All
                </flux:button>
                <flux:button 
                    size="xs" 
                    :variant="$filter === 'unread' ? 'primary' : 'ghost'"
                    wire:click="$set('filter', 'unread')"
                >
                    Unread
                </flux:button>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @if(!$readyToLoad)
                @for($i=0; $i<6; $i++)
                    <div class="p-4 flex items-center gap-4 border-b border-zinc-100 dark:border-zinc-800/50 animate-pulse">
                        <flux:skeleton class="w-10 h-10 rounded-full" />
                        <div class="flex-1 space-y-2">
                            <div class="flex justify-between">
                                <flux:skeleton class="h-4 w-24" />
                                <flux:skeleton class="h-3 w-8" />
                            </div>
                            <flux:skeleton class="h-3 w-32" />
                            <flux:skeleton class="h-3 w-20" />
                        </div>
                    </div>
                @endfor
            @else
                @forelse($conversations as $conv)
                    <button 
                        wire:click="selectConversation({{ $conv->id }})"
                        class="w-full p-4 flex items-center gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-all border-b border-zinc-100 dark:border-zinc-800/50 relative {{ $selectedConversationId == $conv->id ? 'bg-emerald-50/50 dark:bg-emerald-500/10' : '' }}"
                    >
                        @if($selectedConversationId == $conv->id)
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r-full"></div>
                        @endif
                        <div class="relative">
                            <flux:avatar 
                                :name="$conv->author?->name ?? 'Nexa Support'" 
                                :initials="$conv->author?->initials ?? 'S'" 
                                class="bg-emerald-500 text-white {{ $conv->author?->isElite() ? 'ring-2 ring-amber-400' : '' }}" 
                            />
                            @if($conv->author?->isOnline())
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-zinc-900 shadow-sm" title="Online"></div>
                            @endif
                        </div>
                        <div class="flex-1 text-left min-w-0">
                            <div class="flex justify-between items-start">
                                <flux:heading size="sm" class="truncate">{{ $conv->author?->name ?? 'Nexa Support' }}</flux:heading>
                                <span class="text-[10px] text-zinc-400 whitespace-nowrap">{{ $conv->last_message_at?->diffForHumans(short: true) }}</span>
                            </div>
                            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-tighter truncate mt-0.5">
                                {{ $conv->product?->name ?? 'General Inquiry' }}
                            </p>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <flux:subheading size="xs" class="truncate flex-1">
                                    {{ $conv->latestMessage?->message ?? 'No messages yet' }}
                                </flux:subheading>
                                @php $unreadCount = $conv->unreadMessages()->where('is_admin', true)->count(); @endphp
                                @if($unreadCount > 0)
                                    <span class="bg-emerald-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </div>

                            {{-- Archived status --}}
                            @if($conv->archived_at)
                                <div class="mt-1">
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200 dark:border-zinc-700">Archived</span>
                                </div>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="p-12 text-center">
                        <p class="text-sm text-zinc-400">No messages yet</p>
                    </div>
                @endforelse
            @endif
        </div>
    </div>

    {{-- Chat Window --}}
    <div class="flex-1 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden shadow-sm">
        @if($activeConversation)
            {{-- Header --}}
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 bg-emerald-500/5">
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center gap-4">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <flux:avatar 
                                :name="$activeConversation->author?->name ?? 'Nexa Support'" 
                                :initials="$activeConversation->author?->initials ?? 'S'" 
                                class="bg-emerald-500 text-white {{ $activeConversation->author?->isElite() ? 'ring-2 ring-amber-400 elite-glow-gold' : '' }}" 
                            />
                            <div class="min-w-0 flex-1">
                                <flux:heading size="lg" class="truncate">
                                    {{ $activeConversation->author?->name ?? 'Nexa Support' }}
                                </flux:heading>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($activeConversation->author?->isOnline())
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Online</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider">Offline</span>
                                    @endif

                                    @if($activeConversation->author?->isElite())
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
                            {{-- Pro Feature 2: Search Toggle --}}
                            <flux:button wire:click="$toggle('searchActive')" variant="subtle" size="sm" :icon="$searchActive ? 'chevron-up' : 'magnifying-glass'" square title="Search in messages" />
                            
                            {{-- Pro Feature 6: Gallery Toggle --}}
                            <flux:button wire:click="$toggle('showGallery')" variant="subtle" size="sm" icon="photo" square title="File Gallery" :class="$showGallery ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : ''" />

                            {{-- Notification Settings --}}
                            <flux:dropdown>
                                <flux:button variant="subtle" size="sm" :icon="$activeConversation->notifications_enabled ? 'bell' : 'bell-slash'" square />
                                <flux:menu>
                                    <flux:menu.item wire:click="toggleNotifications">
                                        {{ $activeConversation->notifications_enabled ? 'Mute Notifications' : 'Unmute Notifications' }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.radio.group wire:model.live="notificationPriority" wire:change="setNotificationPriority($event.target.value)">
                                        <flux:menu.radio value="normal">Normal</flux:menu.radio>
                                        <flux:menu.radio value="high">High Priority</flux:menu.radio>
                                        <flux:menu.radio value="muted">Muted</flux:menu.radio>
                                    </flux:menu.radio.group>
                                </flux:menu>
                            </flux:dropdown>

                            {{-- More Actions --}}
                            <flux:dropdown>
                                <flux:button variant="subtle" size="sm" icon="ellipsis-vertical" square />
                                <flux:menu>
                                    <flux:menu.item wire:click="toggleArchive" icon="archive-box">
                                        {{ $activeConversation->archived_at ? 'Unarchive Conversation' : 'Archive Conversation' }}
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="$set('showReportModal', true)" icon="flag" class="text-red-500 hover:text-red-600">Report Author</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>

                    {{-- Pro Feature 1: Contextual Product Card --}}
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
                                    @if($latestOrder)
                                        <flux:badge color="lime" size="sm" class="text-[9px] font-black uppercase">Purchased ✅</flux:badge>
                                        <span class="text-[10px] text-zinc-400">Bought on {{ $latestOrder->completed_at?->format('d M Y') ?? $latestOrder->created_at->format('d M Y') }}</span>
                                    @else
                                        <flux:badge color="zinc" size="sm" class="text-[9px] font-black uppercase tracking-tighter">Inquiry</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <flux:button wire:click="redirectToRefund" variant="ghost" size="xs" icon="receipt-refund" class="text-amber-600" title="Request Refund" />
                                <flux:button wire:click="redirectToRate" variant="ghost" size="xs" icon="star" class="text-emerald-600" title="Rate Product" />
                            </div>
                        </div>
                    @endif

                    {{-- Pro Feature 2: Inner Message Search Input --}}
                    @if($searchActive)
                        <div class="flex gap-2 animate-in slide-in-from-top duration-300">
                            <flux:input 
                                wire:model.live.debounce.300ms="msgSearch" 
                                placeholder="Search messages in this conversation..." 
                                icon="magnifying-glass"
                                class="flex-1"
                                clearable
                            />
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex-1 flex min-h-0 relative">
                {{-- Messages Area --}}
                <div class="flex-1 flex flex-col min-w-0">
                    {{-- Pro Feature 3: Pinned Messages (Top Bar) --}}
                    @php 
                        $pinnedMessages = $activeConversation->messages->where('is_pinned', true);
                    @endphp
                    @if($pinnedMessages->count() > 0)
                        <div x-data="{ open: false }" class="bg-amber-50 dark:bg-amber-900/10 border-b border-amber-200/50 dark:border-amber-800/30">
                            <button @click="open = !open" class="w-full px-6 py-2 flex items-center justify-between hover:bg-amber-100/50 transition-colors">
                                <div class="flex items-center gap-2">
                                    <flux:icon.map-pin variant="mini" class="w-4 h-4 text-amber-600" />
                                    <span class="text-xs font-bold text-amber-800 dark:text-amber-400">{{ $pinnedMessages->count() }} Pinned Messages</span>
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
                    <div id="buyer-chat-container" class="flex-1 overflow-y-auto p-8 space-y-6 bg-zinc-50/50 dark:bg-zinc-950/20">
                        @foreach($messages as $msg)
                            @if($msg->type === 'system')
                                {{-- Pro Feature 4: System Event Message --}}
                                <div class="flex justify-center my-4">
                                    <div class="px-4 py-2 rounded-full bg-zinc-200/50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 flex items-center gap-2 shadow-sm">
                                        @if(str_contains(strtolower($msg->message), 'paid') || str_contains(strtolower($msg->message), 'pembayaran'))
                                            <flux:icon.check-circle variant="mini" class="w-4 h-4 text-emerald-500" />
                                        @elseif(str_contains(strtolower($msg->message), 'refund'))
                                            <flux:icon.receipt-refund variant="mini" class="w-4 h-4 text-amber-500" />
                                        @else
                                            <flux:icon.information-circle variant="mini" class="w-4 h-4 text-zinc-400" />
                                        @endif
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">{{ $msg->message }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex {{ !$msg->is_admin ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[70%]">
                                        <div class="
                                            p-4 rounded-2xl text-sm leading-relaxed relative group
                                            {{ !$msg->is_admin 
                                                ? 'bg-emerald-500 text-white rounded-tr-none shadow-md shadow-emerald-500/20' 
                                                : ($activeConversation->author?->isElite() ? 'bg-amber-50 dark:bg-amber-400/10 text-amber-900 dark:text-amber-300 font-medium rounded-tl-none border border-amber-200 dark:border-amber-400/20' : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-tl-none shadow-sm') 
                                            }}
                                            {{ $msg->is_pinned ? 'ring-2 ring-amber-400/50' : '' }}
                                        ">
                                            {{-- Pro Feature 3: Actions (Pin & React) --}}
                                            <div class="absolute -top-4 {{ !$msg->is_admin ? 'right-0' : 'left-0' }} hidden group-hover:flex bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-full py-1 px-2 shadow-xl ring-1 ring-black/5 z-10 gap-1 items-center">
                                                <button wire:click="togglePin({{ $msg->id }})" class="p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ $msg->is_pinned ? 'text-amber-500' : 'text-zinc-400' }}" title="Pin message">
                                                    <flux:icon.map-pin variant="mini" class="w-3.5 h-3.5" />
                                                </button>
                                                <div class="w-px h-3 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>
                                                @foreach(['👍', '❤️', '🔥', '👏', '😮'] as $emoji)
                                                    <button wire:click="toggleReaction({{ $msg->id }}, '{{ $emoji }}')" class="hover:scale-125 transition-transform p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-base leading-none">
                                                        {{ $emoji }}
                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- Pinned Indicator --}}
                                            @if($msg->is_pinned)
                                                <div class="flex items-center gap-1 mb-1 text-[9px] font-bold uppercase {{ !$msg->is_admin ? 'text-emerald-100' : ($activeConversation->author?->isElite() ? 'text-[#451a03]' : 'text-amber-600') }}">
                                                    <flux:icon.map-pin variant="mini" class="w-3 h-3" />
                                                    <span>Pinned</span>
                                                </div>
                                            @endif

                                            <div class="prose dark:prose-invert prose-sm max-w-none {{ !$msg->is_admin ? 'text-white' : ($activeConversation->author?->isElite() ? 'text-amber-900 dark:text-amber-300' : '') }}">
                                                @if($msg->voice_path)
                                                    {{-- Voice Note Player --}}
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
                                                        <button @click="togglePlay" class="w-10 h-10 rounded-full flex items-center justify-center transition-all bg-white/20 hover:bg-white/30 {{ $msg->is_admin ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200' : '' }}">
                                                            <template x-if="!playing"><flux:icon.play variant="mini" class="w-5 h-5" /></template>
                                                            <template x-if="playing"><flux:icon.pause variant="mini" class="w-5 h-5" /></template>
                                                        </button>
                                                        <div class="flex-1 space-y-1">
                                                            <div class="h-1 bg-white/20 rounded-full overflow-hidden {{ $msg->is_admin ? 'bg-zinc-200 dark:bg-zinc-700' : '' }}">
                                                                <div class="h-full bg-current transition-all" :style="'width: ' + progress + '%'"></div>
                                                            </div>
                                                            <div class="flex justify-between text-[10px] opacity-70">
                                                                <span>{{ $msg->voice_duration }}s</span>
                                                                <span>Voice Note</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($msg->type === 'quote')
                                                    {{-- Quote Card --}}
                                                    <div class="bg-emerald-600/10 dark:bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20 mb-3">
                                                        <div class="text-[10px] uppercase font-black tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">Custom Offer</div>
                                                        <div class="text-2xl font-black tabular-nums text-zinc-900 dark:text-white">Rp {{ number_format($msg->metadata['amount'], 0, ',', '.') }}</div>
                                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">{{ $msg->message }}</div>
                                                        <div class="mt-4 pt-3 border-t border-emerald-500/10 flex flex-col gap-2">
                                                            <flux:button wire:click="acceptQuote({{ $msg->id }})" variant="primary" size="sm" class="w-full" icon="credit-card" wire:loading.attr="disabled">
                                                                {{ $msg->metadata['status'] === 'accepted' ? 'Accepted' : 'Accept & Pay' }}
                                                            </flux:button>
                                                            <div class="flex justify-between items-center px-1">
                                                                <span class="text-[10px] font-bold uppercase {{ $msg->metadata['status'] === 'accepted' ? 'text-emerald-500' : 'text-zinc-400' }}">
                                                                    {{ $msg->metadata['status'] ?? 'Pending' }}
                                                                </span>
                                                                <flux:icon.banknotes variant="mini" class="w-4 h-4 text-emerald-500" />
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
                                                        >
                                                            <span>{{ $emoji }}</span>
                                                            <span class="opacity-70">{{ count($users) }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Image/File Attachment --}}
                                            @if($msg->image_path)
                                                <div class="mt-2">
                                                    @php
                                                        $extension = pathinfo($msg->image_path, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    @endphp
                                                    
                                                    @if($isImage)
                                                        <img src="{{ Storage::url($msg->image_path) }}" class="rounded-xl max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity" x-on:click="previewImage = '{{ Storage::url($msg->image_path) }}'; document.getElementById('buyer-preview-img').src = previewImage; Flux.modal('modal-image-preview-buyer-chat').show()">
                                                    @else
                                                        <a href="{{ Storage::url($msg->image_path) }}" download class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                                            @if(str_ends_with($msg->image_path, '.zip'))
                                                                <flux:icon.archive-box class="w-8 h-8 text-blue-500" />
                                                            @elseif(str_ends_with($msg->image_path, '.pdf'))
                                                                <flux:icon.document-text class="w-8 h-8 text-red-500" />
                                                            @else
                                                                <flux:icon.document class="w-8 h-8 text-zinc-500" />
                                                            @endif
                                                            <div class="flex-1 min-w-0">
                                                                <p class="font-medium text-sm truncate {{ !$msg->is_admin ? 'text-zinc-800' : '' }}">{{ basename($msg->image_path) }}</p>
                                                                <p class="text-[10px] text-zinc-500">Click to download</p>
                                                            </div>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-zinc-500 mt-2 {{ !$msg->is_admin ? 'text-right' : 'text-left' }}">
                                            {{ $msg->created_at->format('M d, H:i') }}
                                            @if(!$msg->is_admin)
                                                <span class="ml-1 opacity-50">{{ $msg->is_read ? 'Seen' : 'Delivered' }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
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

                {{-- Pro Feature 6: File Gallery Sidebar --}}
                @if($showGallery)
                    <div class="w-72 border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-y-auto animate-in slide-in-from-right duration-300">
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
                                                x-on:click="previewImage = '{{ Storage::url($file->image_path) }}'; document.getElementById('buyer-preview-img').src = previewImage; Flux.modal('modal-image-preview-buyer-chat').show()"
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
                                        <p class="text-[10px] uppercase font-bold tracking-widest">No files found</p>
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
                        @php
                            $isImagePreview = str_starts_with($attachment->getMimeType(), 'image/');
                        @endphp
                        
                        @if($isImagePreview)
                            <img src="{{ $attachment->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-2xl border-2 border-emerald-500/20">
                        @else
                            <div class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-2xl border-2 border-emerald-500/20">
                                @if(str_ends_with($attachment->getClientOriginalName(), '.zip'))
                                    <flux:icon.archive-box class="w-8 h-8 text-blue-500" />
                                @elseif(str_ends_with($attachment->getClientOriginalName(), '.pdf'))
                                    <flux:icon.document-text class="w-8 h-8 text-red-500" />
                                @elseif(str_ends_with($attachment->getClientOriginalName(), '.rar'))
                                    <flux:icon.archive-box class="w-8 h-8 text-purple-500" />
                                @else
                                    <flux:icon.document class="w-8 h-8 text-zinc-500" />
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate">{{ $attachment->getClientOriginalName() }}</p>
                                    <p class="text-xs text-zinc-500">{{ number_format($attachment->getSize() / 1024, 2) }} KB</p>
                                </div>
                            </div>
                        @endif
                        <flux:button wire:click="$set('attachment', null)" variant="danger" size="sm" icon="x-mark" class="absolute -top-2 -right-2 !rounded-full !p-1 shadow-lg" />
                    </div>
                @endif

                <form wire:submit="sendMessage">
                    <flux:composer 
                        name="replyMessage"
                        wire:model="replyMessage" 
                        @input="setTyping()"
                        placeholder="Type your message here..." 
                        label="Message" 
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

                                    {{-- Voice Recording --}}
                                    <div x-data="{ timer: '0:00' }" @mousedown="startRecording(); interval = setInterval(() => { timer = formatTime(duration) }, 1000)" @mouseup="stopRecording(); clearInterval(interval); timer = '0:00'" class="relative">
                                        <flux:button 
                                            size="sm" 
                                            variant="subtle" 
                                            icon="microphone" 
                                            ::class="recording ? 'text-red-500 animate-pulse' : ''"
                                            title="Hold to Record Voice Note"
                                        />
                                        <div x-show="recording" class="absolute -top-10 left-0 bg-red-500 text-white text-[10px] px-2 py-1 rounded-lg whitespace-nowrap badge animate-bounce">
                                            Recording: <span x-text="timer"></span>
                                        </div>
                                    </div>

                                    {{-- Message Templates --}}
                                    <flux:dropdown>
                                        <flux:button size="sm" variant="subtle" icon="chat-bubble-bottom-center-text" />
                                        <flux:menu>
                                            <flux:menu.heading>My Templates</flux:menu.heading>
                                            @foreach($templates ?? [] as $tmpl)
                                                <flux:menu.item wire:click="useTemplate({{ $tmpl->id }})">{{ $tmpl->title }}</flux:menu.item>
                                            @endforeach
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="$set('showTemplateModal', true)" icon="plus">Create Template</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
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
                <flux:heading size="lg" class="mb-2">Message Center</flux:heading>
                <flux:subheading class="max-w-sm">
                    Select a conversation from the sidebar to chat with Authors or Nexa Support.
                </flux:subheading>
            </div>
        @endif
    </div>

    {{-- Image Preview Modal --}}
    <flux:modal name="modal-image-preview-buyer-chat" class="max-w-5xl" variant="subtle" x-on:close="previewImage = null">
        <div class="space-y-6">
            <div class="flex flex-col items-center">
                <img id="buyer-preview-img" src="" class="max-w-full max-h-[80vh] rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800">
            </div>

            <div class="flex">
                <flux:button class="flex-1" x-on:click="Flux.modal('modal-image-preview-buyer-chat').close()">Close Preview</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Message Template --}}
    <flux:modal wire:model="showTemplateModal" variant="thin" class="w-full max-w-lg">
        <form wire:submit="saveTemplate" class="space-y-6">
            <div>
                <flux:heading size="lg">Save as Template</flux:heading>
                <flux:subheading>Save common messages as templates to reuse them later.</flux:subheading>
            </div>

            <flux:input wire:model="newTemplateTitle" label="Template Title" placeholder="e.g. Asking for progress..." />
            <flux:textarea wire:model="newTemplateContent" label="Content" rows="4" placeholder="Type your template message here..." />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" @click="$wire.showTemplateModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Save Template</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal: Report Author --}}
    <flux:modal wire:model="showReportModal" variant="thin" class="w-full max-w-lg">
        <form wire:submit="submitAuthorReport" class="space-y-6">
            <div>
                <flux:heading size="lg">Report Author</flux:heading>
                <flux:subheading>Report this author for violating platform rules.</flux:subheading>
            </div>

            <flux:select wire:model="reportCategory" label="Category" placeholder="Select a reason...">
                <flux:select.option value="abusive_language">Abusive Language</flux:select.option>
                <flux:select.option value="spam">Spam / Advertising</flux:select.option>
                <flux:select.option value="scam">Scam / Fraud</flux:select.option>
                <flux:select.option value="poor_service">Poor Service / No Response</flux:select.option>
                <flux:select.option value="other">Other</flux:select.option>
            </flux:select>

            <flux:textarea wire:model="reportReason" label="Reason" rows="4" placeholder="Describe the issue in detail..." />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" @click="$wire.showReportModal = false">Cancel</flux:button>
                <flux:button type="submit" variant="danger">Submit Report</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
