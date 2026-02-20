<?php

namespace App\Livewire\Admin;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Flux;

class AdminChatManager extends Component
{
    use WithFileUploads;

    public $selectedConversationId;
    public $replyMessage = '';
    public $attachment;
    public $voiceNote;

    // Search & Filtering
    public $search = '';
    public $filter = 'all'; // all, unread
    public $msgSearch = '';
    public $searchActive = false;

    // Protection & Canned Responses
    public $protectionWarning = null;
    public $showCannedModal = false;
    public $newCannedTitle = '';
    public $newCannedContent = '';

    // Buyer Context
    public $buyerStats = [
        'total_spent' => 0,
        'products_owned' => [],
        'refund_count' => 0,
        'joined_at' => '',
        'recent_orders' => [],
        'past_conversations' => [],
        'reputation' => 'normal', // normal, vip, high_risk
    ];

    // Auto-Responder
    public $showAutoReplyModal = false;
    public $autoReplyEnabled = false;
    public $autoReplyMessage = '';

    // Buyer Report
    public $showReportModal = false;
    public $reportCategory = '';
    public $reportReason = '';

    // Advanced Features
    public $availableTags = [
        'urgent' => ['label' => 'Urgent', 'color' => 'red'],
        'follow-up' => ['label' => 'Follow Up', 'color' => 'yellow'],
        'resolved' => ['label' => 'Resolved', 'color' => 'green'],
        'waiting-payment' => ['label' => 'Waiting Payment', 'color' => 'blue'],
        'vip' => ['label' => 'VIP', 'color' => 'purple'],
    ];

    // Scheduled Messages
    public $showScheduleModal = false;
    public $scheduleMessage = '';
    public $scheduleDate = '';
    public $scheduleTime = '';

    // Archive & UI
    public $showArchived = false;
    public $showGallery = false;

    // Private Notes
    public $showNotesModal = false;
    public $privateNotes = '';

    // Notifications
    public $notificationPriority = 'normal';

    public function mount()
    {
        $user = Auth::user();
        $this->autoReplyEnabled = (bool) ($user->auto_reply_enabled ?? false);
        $this->autoReplyMessage = $user->auto_reply_message ?? 'Halo! Terima kasih sudah menghubungi tim Support. Kami sudah menerima pesan kamu dan akan segera membalasnya! 😊';

        if ($this->selectedConversationId) {
            $this->selectConversation($this->selectedConversationId);
        }
    }

    public function selectConversation($id)
    {
        // Security: Ensure conversation exists and is a support ticket (author_id is null)
        $conversation = Conversation::where('id', $id)->whereNull('author_id')->first();
        
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Conversation not found or access denied.');
            return;
        }

        $this->selectedConversationId = $id;
        $this->markAsRead();
        $this->attachment = null;
        $this->protectionWarning = null;
        $this->loadBuyerStats();
        $this->loadNotes();
        $this->dispatch('scroll-to-bottom-admin');
        $this->dispatch('conversation-selected', id: $id);

        // The $conversation variable is already loaded and checked above
        $this->notificationPriority = $conversation->notification_priority ?? 'normal';
    }

    private function loadBuyerStats()
    {
        if (!$this->selectedConversationId) return;

        $conversation = Conversation::with('user')->find($this->selectedConversationId);
        if (!$conversation || !$conversation->user) return;

        $user = $conversation->user;
        
        // Products owned by this buyer (Global)
        $productsOwned = \App\Models\Product::whereHas('orderItems', function($q) use ($user) {
            $q->whereHas('order', function($qq) use ($user) {
                $qq->where('buyer_id', $user->id)->where('status', \App\Enums\OrderStatus::COMPLETED);
            });
        })->get();

        $totalSpent = \App\Models\Order::where('buyer_id', $user->id)->where('status', \App\Enums\OrderStatus::COMPLETED)->sum('total_amount');
        $refundCount = \App\Models\Order::where('buyer_id', $user->id)->where('status', \App\Enums\OrderStatus::REFUNDED)->count();
        
        // Reputation Logic
        $reputation = 'normal';
        if ($totalSpent > 1000000) $reputation = 'vip';
        if ($refundCount >= 2) $reputation = 'high_risk';

        $this->buyerStats = [
            'total_spent' => $totalSpent,
            'products_owned' => $productsOwned,
            'refund_count' => $refundCount,
            'joined_at' => $user->created_at->format('M Y'),
            'recent_orders' => \App\Models\Order::where('buyer_id', $user->id)->latest()->take(5)->get(),
            'past_conversations' => Conversation::where('user_id', $user->id)
                ->where('id', '!=', $this->selectedConversationId)
                ->where('status', \App\Enums\SupportStatus::RESOLVED)
                ->latest()
                ->take(3)
                ->get(),
            'reputation' => $reputation,
        ];
    }

    public function updatedReplyMessage($value)
    {
        $detected = \App\Services\ChatProtectionService::scan($value);
        $this->protectionWarning = \App\Services\ChatProtectionService::getWarningMessage($detected);
    }

    public function sendMessage()
    {
        if (!auth()->user()?->isAdmin()) return;

        $this->validate([
            'replyMessage' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,zip,doc,docx', // Hardened
        ]);

        if (!$this->selectedConversationId) return;

        // Ensure the selected conversation is a support ticket
        $conversation = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Cannot send message to this conversation.');
            return;
        }

        $filePath = null;
        if ($this->attachment) {
            $filePath = $this->handleAttachment($this->attachment);
        }

        $chatMessage = ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => $this->replyMessage ?? '',
            'image_path' => $filePath,
            'is_admin' => true,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_buyer_message_at' => null // Admin replied, stop the SLA clock
        ]);

        broadcast(new MessageSent($chatMessage))->toOthers();

        $this->replyMessage = '';
        $this->attachment = null;
        $this->protectionWarning = null;
        $this->dispatch('scroll-to-bottom-admin');
    }

    // Message Reactions
    public function toggleReaction($messageId, $emoji)
    {
        if (!auth()->user()?->isAdmin()) return;

        $message = ChatMessage::whereHas('conversation', function($q) {
            $q->whereNull('author_id'); // Admin support conversations are author_id NULL
        })->findOrFail($messageId);
        $userId = Auth::id();
        
        $reactions = $message->reactions ?? [];
        $hasReacted = isset($reactions[$emoji]) && in_array($userId, $reactions[$emoji]);
        
        if ($hasReacted) {
            $message->removeReaction($emoji, $userId);
        } else {
            $message->addReaction($emoji, $userId);
        }
        
        $this->dispatch('reaction-updated');
    }

    // Conversation Tags
    public function toggleTag($tag)
    {
        if (!auth()->user()?->isAdmin()) return;

        $conversation = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Conversation not found or access denied.');
            return;
        }
        
        $tags = $conversation->tags ?? [];
        
        if (in_array($tag, $tags)) {
            $conversation->removeTag($tag);
        } else {
            $conversation->addTag($tag);
        }
        
        Flux::toast(variant: 'success', heading: 'Updated', text: 'Tag updated successfully.');
    }

    // Canned Response Management
    public function saveCannedResponse()
    {
        if (!auth()->user()?->isAdmin()) return;

        $this->validate([
            'newCannedTitle' => 'required|string|max:100',
            'newCannedContent' => 'required|string|max:1000',
        ]);

        \App\Models\CannedResponse::create([
            'user_id' => Auth::id(),
            'title' => $this->newCannedTitle,
            'content' => $this->newCannedContent,
        ]);

        $this->newCannedTitle = '';
        $this->newCannedContent = '';
        $this->showCannedModal = false;
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Template pesan berhasil disimpan.');
    }

    public function useCannedResponse($id)
    {
        if (!auth()->user()?->isAdmin()) return;

        $canned = \App\Models\CannedResponse::where('user_id', Auth::id())->find($id);
        if ($canned) {
            $this->replyMessage = $canned->content;
            $this->updatedReplyMessage($canned->content);
        }
    }

    public function deleteCannedResponse($id)
    {
        if (!auth()->user()?->isAdmin()) return;

        \App\Models\CannedResponse::where('user_id', Auth::id())->findOrFail($id)->delete();
        Flux::toast(variant: 'success', heading: 'Dihapus', text: 'Template pesan telah dihapus.');
    }

    // Auto-Responder Management
    public function saveAutoReply()
    {
        if (!auth()->user()?->isAdmin()) return;

        $this->validate([
            'autoReplyMessage' => 'required_if:autoReplyEnabled,true|string|max:1000',
        ]);

        Auth::user()->update([
            'auto_reply_enabled' => $this->autoReplyEnabled,
            'auto_reply_message' => $this->autoReplyMessage,
        ]);

        $this->showAutoReplyModal = false;
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Pengaturan pesan otomatis berhasil disimpan.');
    }


    // Scheduled Messages
    public function scheduleMessage()
    {
        if (!auth()->user()?->isAdmin()) return;

        $this->validate([
            'scheduleMessage' => 'required|string|max:1000',
            'scheduleDate' => 'required|date|after:today',
            'scheduleTime' => 'required',
        ]);
        
        // Ensure the selected conversation is a support ticket
        $conversation = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Cannot schedule message for this conversation.');
            return;
        }

        $scheduledAt = \Carbon\Carbon::parse($this->scheduleDate . ' ' . $this->scheduleTime);
        
        \App\Models\ScheduledMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => $this->scheduleMessage,
            'scheduled_at' => $scheduledAt,
        ]);
        
        $this->reset(['scheduleMessage', 'scheduleDate', 'scheduleTime', 'showScheduleModal']);
        Flux::toast(variant: 'success', heading: 'Scheduled', text: 'Message scheduled successfully.');
    }

    // Archive
    public function toggleArchive($conversationId = null)
    {
        if (!auth()->user()?->isAdmin()) return;

        $id = $conversationId ?? $this->selectedConversationId;
        $conversation = Conversation::where('id', $id)->whereNull('author_id')->first();
        
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Conversation not found or access denied.');
            return;
        }
        
        if ($conversation->archived_at) {
            $conversation->update(['archived_at' => null]);
            Flux::toast(variant: 'success', heading: 'Unarchived', text: 'Conversation unarchived.');
        } else {
            $conversation->update(['archived_at' => now()]);
            Flux::toast(variant: 'success', heading: 'Archived', text: 'Conversation archived.');
            $this->selectedConversationId = null;
        }
    }

    public function resolveConversation()
    {
        if (!auth()->user()?->isAdmin()) return;

        if (!$this->selectedConversationId) return;

        $conversation = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Conversation not found or access denied.');
            return;
        }

        $conversation->update(['status' => \App\Enums\SupportStatus::CLOSED]);

        Flux::toast(variant: 'success', heading: 'Resolved', text: 'Conversation marked as resolved.');
        $this->selectedConversationId = null;
        $this->dispatch('refresh-chat');
    }

    // Private Notes
    public function saveNotes()
    {
        if (!auth()->user()?->isAdmin()) return;

        $conversation = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Conversation not found or access denied.');
            return;
        }

        $conversation->update(['private_notes' => $this->privateNotes]);
        
        $this->showNotesModal = false;
        Flux::toast(variant: 'success', heading: 'Saved', text: 'Notes saved successfully.');
    }

    public function loadNotes()
    {
        if (!$this->selectedConversationId) return;

        $conv = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if ($conv) {
            $this->privateNotes = $conv->private_notes ?? '';
        } else {
            $this->privateNotes = ''; // Clear notes if conversation not found or not a support ticket
        }
    }

    public function sendVoiceNote($voiceNotePath, $duration)
    {
        if (!auth()->user()?->isAdmin()) return;

        if (!$this->selectedConversationId) return;

        // Ensure the selected conversation is a support ticket
        $conversation = Conversation::where('id', $this->selectedConversationId)->whereNull('author_id')->first();
        if (!$conversation) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Cannot send voice note to this conversation.');
            return;
        }

        ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => '[Pesan Suara]',
            'voice_path' => $voiceNotePath,
            'voice_duration' => $duration,
            'is_admin' => true,
        ]);

        $conversation->update(['last_message_at' => now()]);
        
        $this->dispatch('scroll-to-bottom-admin');
        Flux::toast(variant: 'success', heading: 'Sent', text: 'Voice note sent.');
    }

    #[On('echo-private:admin.chat,.message.sent')]
    public function onMessageReceived($event)
    {
        if (isset($event['message']['conversation_id']) && $event['message']['conversation_id'] == $this->selectedConversationId) {
            $this->markAsRead();
            $this->dispatch('scroll-to-bottom-admin');
        } else {
            $this->dispatch('new-message');
        }

        $this->dispatch('$refresh');
    }

    public function markAsRead()
    {
        if (!auth()->user()?->isAdmin()) return;
        if (!$this->selectedConversationId) return;

        ChatMessage::where('conversation_id', $this->selectedConversationId)
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->dispatch('message-read');
    }

    private function handleAttachment($file)
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');

        if (!$isImage) {
            return $file->store('chat-attachments', 'public');
        }

        if ($mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($mime == 'image/png') {
            $image = @imagecreatefrompng($path);
            if (!$image) {
                return $file->store('chat-attachments', 'public');
            }
            $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $bg;
        } elseif ($mime == 'image/webp') {
            $image = imagecreatefromwebp($path);
        } else {
            return $file->store('chat-attachments', 'public');
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $maxSize = 1200;

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $newWidth = $maxSize;
                $newHeight = $maxSize / $ratio;
            } else {
                $newHeight = $maxSize;
                $newWidth = $maxSize * $ratio;
            }
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'chat_img');
        imagejpeg($image, $tempPath, 75);
        imagedestroy($image);

        $filename = 'chat-attachments/' . $file->hashName();
        Storage::disk('public')->put($filename, file_get_contents($tempPath));
        unlink($tempPath);

        return $filename;
    }

    public function getGalleryFilesProperty()
    {
        if (!$this->selectedConversationId) return collect();
        
        return ChatMessage::where('conversation_id', $this->selectedConversationId)
            ->whereNotNull('image_path')
            ->orderByDesc('created_at')
            ->get();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = Conversation::with(['user', 'latestMessage'])
            ->whereNull('author_id')
            ->orderByDesc('last_message_at');

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'unread') {
            $query->whereHas('unreadMessages', function($q) {
                $q->where('is_admin', false);
            });
        }

        // Apply archive filter
        if ($this->showArchived) {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        $conversations = $query->get();

        $activeConversation = $this->selectedConversationId 
            ? Conversation::with(['messages.sender', 'user'])->find($this->selectedConversationId) 
            : null;

        // Filter messages if search is active
        $messages = $activeConversation ? $activeConversation->messages : collect();
        if ($this->msgSearch && $activeConversation) {
            $messages = $activeConversation->messages->filter(function($m) {
                return str_contains(strtolower($m->message ?? ''), strtolower($this->msgSearch));
            });
        }

        return view('livewire.admin.admin-chat-manager', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
        ]);
    }
}
