<?php

namespace App\Livewire\Author;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use App\Models\CannedResponse;
use App\Services\ChatProtectionService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Flux;

class AuthorChatManager extends Component
{
    use WithFileUploads;

    public $selectedConversationId;
    public $replyMessage = '';
    public $attachment;

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

    // Custom Quotation
    public $showQuoteModal = false;
    public $quoteAmount = '';
    public $quoteDescription = '';

    // Buyer Context
    public $buyerStats = [
        'total_spent' => 0,
        'products_owned' => [],
        'refund_count' => 0,
        'joined_at' => '',
    ];

    // Auto-Responder
    public $showAutoReplyModal = false;
    public $autoReplyEnabled = false;
    public $autoReplyMessage = '';

    // Bulk Broadcast
    public $showBroadcastModal = false;
    public $broadcastProductId = '';
    public $broadcastMessage = '';
    public $authorProducts = [];

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

    // Archive
    public $showArchived = false;
    public $showGallery = false;

    public function updatedShowArchived()
    {
        $this->selectedConversationId = null;
    }

    // Private Notes
    public $showNotesModal = false;
    public $privateNotes = '';

    // Notifications
    public $notificationPriority = 'normal';

    public function mount()
    {
        $user = Auth::user();
        $this->autoReplyEnabled = (bool) $user->auto_reply_enabled;
        $this->autoReplyMessage = $user->auto_reply_message ?? 'Halo! Terima kasih sudah menghubungi saya. Pesan kamu sudah saya terima dan akan segera saya balas ya! 😊';

        // Load author products for broadcast
        $this->authorProducts = \App\Models\Product::where('author_id', Auth::id())->get();
    }

    public function updatedReplyMessage($value)
    {
        $detected = ChatProtectionService::scan($value);
        $this->protectionWarning = ChatProtectionService::getWarningMessage($detected);
    }

    public function selectConversation($id)
    {
        $this->selectedConversationId = $id;
        $this->markAsRead();
        $this->attachment = null;
        $this->protectionWarning = null;
        
        // Fetch Buyer Context
        $conversation = Conversation::where('author_id', Auth::id())->with('user')->find($id);
        if ($conversation && $conversation->user) {
            $buyer = $conversation->user;
            
            // Calculate total spent with THIS author
            $totalSpent = \App\Models\Order::where('buyer_id', $buyer->id)
                ->where('status', \App\Enums\OrderStatus::COMPLETED)
                ->whereHas('items.product', function($q) {
                    $q->where('author_id', Auth::id());
                })
                ->sum('total_amount');

            // Get products owned by THIS buyer from THIS author
            $productsOwned = \App\Models\Product::where('author_id', Auth::id())
                ->whereHas('orderItems.order', function($q) use ($buyer) {
                    $q->where('buyer_id', $buyer->id)->where('status', \App\Enums\OrderStatus::COMPLETED);
                })
                ->get();

            // Refund count from THIS author
            $refundCount = \App\Models\RefundRequest::where('user_id', $buyer->id)
                ->whereHas('order.items.product', function($q) {
                    $q->where('author_id', Auth::id());
                })
                ->count();

            $this->buyerStats = [
                'total_spent' => $totalSpent,
                'products_owned' => $productsOwned,
                'refund_count' => $refundCount,
                'joined_at' => $buyer->created_at->format('M Y'),
            ];
        }

        $this->dispatch('scroll-to-bottom-author');
        $this->dispatch('conversation-selected', id: $id);

        if ($conversation) {
            $this->notificationPriority = $conversation->notification_priority ?? 'normal';
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'replyMessage' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,zip,doc,docx', // Hardened
        ]);

        if (!$this->selectedConversationId) return;

        $imagePath = null;
        if ($this->attachment) {
            $isImage = str_starts_with($this->attachment->getMimeType(), 'image/');
            if ($isImage) {
                $imagePath = $this->compressImage($this->attachment);
            } else {
                $imagePath = $this->attachment->store('chat-attachments', 'public');
            }
        }

        $chatMessage = ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => $this->replyMessage ?? '',
            'image_path' => $imagePath,
            'is_admin' => true,
        ]);

        Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId)->update(['last_message_at' => now()]);

        broadcast(new MessageSent($chatMessage))->toOthers();

        $this->replyMessage = '';
        $this->attachment = null;
        $this->protectionWarning = null;
        $this->dispatch('scroll-to-bottom-author');
    }

    // Canned Response Management
    public function saveCannedResponse()
    {
        $this->validate([
            'newCannedTitle' => 'required|string|max:100',
            'newCannedContent' => 'required|string|max:1000',
        ]);

        CannedResponse::create([
            'user_id' => Auth::id(),
            'title' => $this->newCannedTitle,
            'content' => $this->newCannedContent,
        ]);

        $this->newCannedTitle = '';
        Flux::toast(variant: 'success', text: 'Canned response saved.');
        $this->showCannedModal = false;
    }

    public function useCannedResponse($id)
    {
        $canned = CannedResponse::where('user_id', Auth::id())->find($id);
        if ($canned) {
            $this->replyMessage = $canned->content;
            $this->updatedReplyMessage($canned->content);
        }
    }

    public function deleteCannedResponse($id)
    {
        CannedResponse::where('user_id', Auth::id())->findOrFail($id)->delete();
        Flux::toast(variant: 'success', heading: 'Dihapus', text: 'Template pesan telah dihapus.');
    }

    // Auto-Responder Management
    public function saveAutoReply()
    {
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

    // Bulk Broadcast Logic
    public function sendBroadcast()
    {
        $this->validate([
            'broadcastProductId' => 'required|exists:products,id',
            'broadcastMessage' => 'required|string|max:5000',
        ]);

        $product = \App\Models\Product::findOrFail($this->broadcastProductId);
        
        // Get all unique buyers who have completed orders for this product
        $buyerIds = \App\Models\Order::where('status', \App\Enums\OrderStatus::COMPLETED)
            ->whereHas('items', function ($q) {
                $q->where('product_id', $this->broadcastProductId);
            })
            ->pluck('buyer_id')
            ->unique();

        $count = 0;
        foreach ($buyerIds as $buyerId) {
            // Find or create conversation
            $conversation = Conversation::firstOrCreate([
                'user_id' => $buyerId,
                'author_id' => Auth::id(),
                'product_id' => $this->broadcastProductId,
            ], [
                'status' => 'open',
                'last_message_at' => now(),
            ]);

            // Send message
            $msg = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message' => "📢 **BROADCAST UPDATE: " . $product->name . "**\n\n" . $this->broadcastMessage,
                'is_admin' => true,
            ]);

            $conversation->update(['last_message_at' => now()]);
            broadcast(new \App\Events\MessageSent($msg))->toOthers();
            $count++;
        }

        $this->showBroadcastModal = false;
        $this->broadcastMessage = '';
        Flux::toast(variant: 'success', heading: 'Berhasil', text: "Broadcast berhasil dikirim ke {$count} buyer.");
    }

    // Buyer Report Logic
    public function submitReport()
    {
        if (!$this->selectedConversationId) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Tidak ada percakapan yang dipilih.');
            return;
        }

        $this->validate([
            'reportCategory' => 'required|in:abusive_language,spam,refund_abuse,payment_issues,other',
            'reportReason' => 'required|string|min:10|max:1000',
        ]);

        $conversation = Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId);

        \App\Models\BuyerReport::create([
            'author_id' => Auth::id(),
            'buyer_id' => $conversation->user_id,
            'conversation_id' => $conversation->id,
            'category' => $this->reportCategory,
            'reason' => $this->reportReason,
            'status' => 'pending',
        ]);

        $this->showReportModal = false;
        $this->reportCategory = '';
        $this->reportReason = '';

        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Laporan buyer berhasil dikirim ke admin untuk ditinjau.');
    }

    // Custom Quotation Logic
    public function sendQuote()
    {
        $this->validate([
            'quoteAmount' => 'required|numeric|min:1000',
            'quoteDescription' => 'required|string|max:500',
        ]);

        if (!$this->selectedConversationId) return;

        $chatMessage = ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => $this->quoteDescription,
            'is_admin' => true,
            'type' => 'quote',
            'metadata' => [
                'amount' => $this->quoteAmount,
                'status' => 'pending', // pending, accepted, rejected
            ],
        ]);

        Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId)->update(['last_message_at' => now()]);

        broadcast(new MessageSent($chatMessage))->toOthers();

        $this->quoteAmount = '';
        $this->quoteDescription = '';
        $this->showQuoteModal = false;
        $this->dispatch('scroll-to-bottom-author');
        Flux::toast(variant: 'success', heading: 'Terkirim', text: 'Penawaran khusus telah dikirim ke buyer.');
    }

    #[On('refresh-chat')]
    public function onMessageReceived($event = null)
    {
        $this->markAsRead();
        $this->dispatch('scroll-to-bottom-author');
    }

    public function markAsRead()
    {
        if ($this->selectedConversationId) {
            ChatMessage::where('conversation_id', $this->selectedConversationId)
                ->where('is_admin', false)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    private function compressImage($file)
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();

        if ($mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($path);
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

    // ===== ADVANCED FEATURES METHODS =====

    // Message Reactions
    public function toggleReaction($messageId, $emoji)
    {
        $message = ChatMessage::whereHas('conversation', function($q) {
            $q->where('author_id', Auth::id());
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
        Flux::toast(variant: 'success', text: 'Reaction updated.');
    }

    // Conversation Tags
    public function toggleTag($tag)
    {
        $conversation = Conversation::where('author_id', Auth::id())->find($this->selectedConversationId);
        if (!$conversation) return;
        
        $tags = $conversation->tags ?? [];
        
        if (in_array($tag, $tags)) {
            $conversation->removeTag($tag);
        } else {
            $conversation->addTag($tag);
        }
        
        Flux::toast(variant: 'success', heading: 'Updated', text: 'Tag updated successfully.');
    }

    // Scheduled Messages
    public function scheduleMessage()
    {
        $this->validate([
            'scheduleMessage' => 'required|string|max:1000',
            'scheduleDate' => 'required|date|after:today',
            'scheduleTime' => 'required',
        ]);
        
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
        $id = $conversationId ?? $this->selectedConversationId;
        $conversation = Conversation::where('author_id', Auth::id())->findOrFail($id);
        
        if ($conversation->archived_at) {
            $conversation->update(['archived_at' => null, 'archived_by' => null]);
            Flux::toast(variant: 'success', heading: 'Unarchived', text: 'Conversation unarchived.');
        } else {
            $conversation->update(['archived_at' => now(), 'archived_by' => Auth::id()]);
            Flux::toast(variant: 'success', heading: 'Archived', text: 'Conversation archived.');
            $this->selectedConversationId = null;
        }
    }

    // Private Notes
    public function saveNotes()
    {
        $conversation = Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId);
        $conversation->update(['private_notes' => $this->privateNotes]);
        
        $this->showNotesModal = false;
        Flux::toast(variant: 'success', heading: 'Saved', text: 'Notes saved successfully.');
    }

    public function loadNotes()
    {
        if ($this->selectedConversationId) {
            $conv = Conversation::where('author_id', Auth::id())->find($this->selectedConversationId);
            $this->privateNotes = $conv->private_notes ?? '';
        }
    }

    // Smart Notifications
    public function toggleNotifications()
    {
        $conversation = Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId);
        $conversation->update([
            'notifications_enabled' => !$conversation->notifications_enabled
        ]);
        
        $status = $conversation->notifications_enabled ? 'enabled' : 'muted';
        Flux::toast(variant: 'success', heading: 'Updated', text: "Notifications {$status}.");
    }

    public function setNotificationPriority($priority)
    {
        $conversation = Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId);
        $conversation->update(['notification_priority' => $priority]);
        
        Flux::toast(variant: 'success', heading: 'Updated', text: 'Notification priority updated.');
    }

    public function togglePin($messageId)
    {
        $message = ChatMessage::whereHas('conversation', function($q) {
            $q->where('author_id', Auth::id());
        })->findOrFail($messageId);
        $message->update(['is_pinned' => !$message->is_pinned]);
        
        $status = $message->is_pinned ? 'Pinned' : 'Unpinned';
        Flux::toast(variant: 'success', heading: $status, text: "Message has been {$status->toLowerCase()}.");
    }

    public function sendVoiceNote($voiceNotePath, $duration)
    {
        if (!$this->selectedConversationId) return;

        ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => '[Pesan Suara]',
            'voice_path' => $voiceNotePath,
            'voice_duration' => $duration,
            'is_admin' => true,
        ]);

        Conversation::where('author_id', Auth::id())->findOrFail($this->selectedConversationId)->update(['last_message_at' => now()]);
        
        $this->dispatch('scroll-to-bottom-author');
        Flux::toast(variant: 'success', heading: 'Sent', text: 'Voice note sent.');
    }

    public function getGalleryFilesProperty()
    {
        if (!$this->selectedConversationId) return collect();
        
        return ChatMessage::where('conversation_id', $this->selectedConversationId)
            ->whereNotNull('image_path')
            ->orderByDesc('created_at')
            ->get();
    }

    // ===== END ADVANCED FEATURES =====

    public function render()
    {
        $query = Conversation::with(['user', 'latestMessage', 'product'])
            ->where('author_id', Auth::id());

        // Apply archive filter
        if ($this->showArchived) {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'unread') {
            $query->whereHas('messages', function($q) {
                $q->where('is_read', false)->where('is_admin', false);
            });
        }

        $conversations = $query->get();

        $activeConversation = $this->selectedConversationId 
            ? Conversation::with(['messages.sender', 'product'])->find($this->selectedConversationId) 
            : null;

        // Filter messages if search is active
        $filteredMessages = $activeConversation ? $activeConversation->messages : collect();
        if ($this->msgSearch && $activeConversation) {
            $filteredMessages = $activeConversation->messages->filter(function($m) {
                return str_contains(strtolower($m->message), strtolower($this->msgSearch));
            });
        }

        $cannedResponses = CannedResponse::where('user_id', Auth::id())->latest()->get();

        return view('livewire.author.author-chat-manager', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $filteredMessages,
            'cannedResponses' => $cannedResponses,
        ])->layout('layouts.author');
    }
}
