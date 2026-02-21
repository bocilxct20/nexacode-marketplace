<?php

namespace App\Livewire\Customer;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Livewire\Component;
use App\Services\ChatProtectionService;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Flux;

class BuyerChatManager extends Component
{
    use WithFileUploads;

    public $selectedConversationId;
    public $replyMessage = '';

    protected $queryString = [
        'selectedConversationId' => ['as' => 'id', 'except' => ''],
    ];

    public $attachment;
    public $protectionWarning = null;
    public $readyToLoad = false;

    public function mount()
    {
        if ($this->selectedConversationId) {
            $this->selectConversation($this->selectedConversationId);
        }
    }

    public function load()
    {
        $this->readyToLoad = true;
    }
    
    // Search & Filtering
    public $search = '';
    public $filter = 'all'; // all, unread
    public $msgSearch = '';
    public $searchActive = false;

    // Advanced Features
    public $showTemplateModal = false;
    public $newTemplateTitle = '';
    public $newTemplateContent = '';
    
    // Voice Notes
    public $voiceNote;
    
    // Archive
    public $showArchived = false;
    public $showGallery = false;

    public function updatedShowArchived()
    {
        $this->selectedConversationId = null;
    }

    // Notifications
    public $notificationPriority = 'normal';

    // Report Author
    public $showReportModal = false;
    public $reportCategory = '';
    public $reportReason = '';

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
        $this->dispatch('scroll-to-bottom-buyer');
        $this->dispatch('conversation-selected', id: $id);

        $conversation = Conversation::where('user_id', Auth::id())->find($id);
        if ($conversation) {
            $this->notificationPriority = $conversation->notification_priority ?? 'normal';
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'replyMessage' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,zip,doc,docx', // Hardened validation
        ]);

        if (!$this->selectedConversationId) return;

        $imagePath = null;
        if ($this->attachment) {
            $isImage = str_starts_with($this->attachment->getMimeType(), 'image/');
            if ($isImage) {
                $imagePath = $this->compressImage($this->attachment);
            } else {
                // Store non-image files directly
                $imagePath = $this->attachment->store('chat-attachments', 'public');
            }
        }

        $chatMessage = ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => $this->replyMessage ?? '',
            'image_path' => $imagePath,
            'is_admin' => false,
        ]);

        Conversation::where('user_id', Auth::id())->findOrFail($this->selectedConversationId)->update([
            'last_message_at' => now(),
            'last_buyer_message_at' => now(), // Start/Update SLA clock
            'status' => 'open' // Auto re-open if resolved
        ]);

        broadcast(new MessageSent($chatMessage))->toOthers();

        $this->replyMessage = '';
        $this->attachment = null;
        $this->protectionWarning = null;
        $this->dispatch('scroll-to-bottom-buyer');
        
        Flux::toast(variant: 'success', heading: 'Sent', text: 'Message sent successfully.');
    }

    #[On('refresh-chat')]
    public function onMessageReceived($event = null)
    {
        $this->markAsRead();
        $this->dispatch('scroll-to-bottom-buyer');
    }

    public function markAsRead()
    {
        if ($this->selectedConversationId) {
            ChatMessage::where('conversation_id', $this->selectedConversationId)
                ->where('is_admin', true) // Mark messages from author/admin as read
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

    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.customer.buyer-chat-manager', [
                'conversations' => collect(),
                'activeConversation' => null,
                'latestOrder' => null,
                'messages' => collect(),
                'templates' => collect(),
            ])->layout('layouts.app');
        }

        $query = Conversation::with(['author', 'latestMessage', 'product'])
            ->where('user_id', Auth::id());

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('author', function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('product', function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Apply filter
        if ($this->filter === 'unread') {
            $query->whereHas('messages', function($q) {
                $q->where('is_admin', true)->where('is_read', false);
            });
        }

        // Apply archive filter
        if ($this->showArchived) {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        $conversations = $query->orderByDesc('last_message_at')->get();

        $activeConversation = $this->selectedConversationId 
            ? Conversation::where('user_id', Auth::id())->with(['messages.sender', 'product', 'author'])->find($this->selectedConversationId) 
            : null;

        // Contextual Product Data
        $latestOrder = null;
        if ($activeConversation && $activeConversation->product_id) {
            $latestOrder = \App\Models\Order::where('buyer_id', Auth::id())
                ->where('status', 'completed')
                ->whereHas('items', function($q) use ($activeConversation) {
                    $q->where('product_id', $activeConversation->product_id);
                })
                ->latest()
                ->first();
        }

        // Filter messages if search is active
        $filteredMessages = $activeConversation ? $activeConversation->messages : collect();
        if ($this->msgSearch && $activeConversation) {
            $filteredMessages = $activeConversation->messages->filter(function($m) {
                return str_contains(strtolower($m->message), strtolower($this->msgSearch));
            });
        }

        $templates = \App\Models\CannedResponse::where('user_id', Auth::id())->latest()->get();

        return view('livewire.customer.buyer-chat-manager', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'latestOrder' => $latestOrder,
            'messages' => $filteredMessages,
            'templates' => $templates,
        ])->layout('layouts.app');
    }

    // ===== ADVANCED FEATURES METHODS =====

    // Message Reactions & Pinned
    public function toggleReaction($messageId, $emoji)
    {
        $message = ChatMessage::whereHas('conversation', function($q) {
            $q->where('user_id', Auth::id());
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

    public function togglePin($messageId)
    {
        $message = ChatMessage::whereHas('conversation', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($messageId);

        $message->update(['is_pinned' => !$message->is_pinned]);
        
        $status = $message->is_pinned ? 'Pinned' : 'Unpinned';
        Flux::toast(variant: 'success', heading: $status, text: "Message has been {$status->toLowerCase()}.");
    }

    public function getGalleryFilesProperty()
    {
        if (!$this->selectedConversationId) return collect();
        
        return ChatMessage::where('conversation_id', $this->selectedConversationId)
            ->whereNotNull('image_path')
            ->orderByDesc('created_at')
            ->get();
    }

    // Buyer Message Templates
    public function saveTemplate()
    {
        $this->validate([
            'newTemplateTitle' => 'required|string|max:100',
            'newTemplateContent' => 'required|string|max:1000',
        ]);
        
        \App\Models\CannedResponse::create([
            'user_id' => Auth::id(),
            'title' => $this->newTemplateTitle,
            'content' => $this->newTemplateContent,
        ]);
        
        $this->reset(['newTemplateTitle', 'newTemplateContent', 'showTemplateModal']);
        Flux::toast(variant: 'success', heading: 'Saved', text: 'Template saved successfully.');
    }

    public function useTemplate($id)
    {
        $template = \App\Models\CannedResponse::where('user_id', Auth::id())->findOrFail($id);
        $this->replyMessage = $template->content;
        $this->showTemplateModal = false;
    }

    public function deleteTemplate($id)
    {
        \App\Models\CannedResponse::where('user_id', Auth::id())->findOrFail($id)->delete();
        Flux::toast(variant: 'success', heading: 'Deleted', text: 'Template deleted.');
    }

    // Voice Notes
    public function sendVoiceNote($voiceNotePath, $duration)
    {
        if (!$this->selectedConversationId) return;

        ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => '[Pesan Suara]',
            'voice_path' => $voiceNotePath,
            'voice_duration' => $duration,
            'is_admin' => false,
        ]);

        Conversation::where('user_id', Auth::id())->findOrFail($this->selectedConversationId)->update(['last_message_at' => now()]);
        
        $this->dispatch('scroll-to-bottom-buyer');
        Flux::toast(variant: 'success', heading: 'Sent', text: 'Voice note sent.');
    }

    // Archive
    public function toggleArchive($conversationId = null)
    {
        $id = $conversationId ?? $this->selectedConversationId;
        if (!$id) return;
        
        $conversation = Conversation::where('user_id', Auth::id())->findOrFail($id);
        
        if ($conversation->archived_at) {
            $conversation->update(['archived_at' => null, 'archived_by' => null]);
            Flux::toast(variant: 'success', heading: 'Unarchived', text: 'Conversation unarchived.');
        } else {
            $conversation->update(['archived_at' => now(), 'archived_by' => Auth::id()]);
            Flux::toast(variant: 'success', heading: 'Archived', text: 'Conversation archived.');
            $this->selectedConversationId = null;
        }
    }

    // Smart Notifications
    public function toggleNotifications()
    {
        if (!$this->selectedConversationId) return;
        
        $conversation = Conversation::where('user_id', Auth::id())->findOrFail($this->selectedConversationId);
        $conversation->update([
            'notifications_enabled' => !$conversation->notifications_enabled
        ]);
        
        $status = $conversation->notifications_enabled ? 'enabled' : 'muted';
        Flux::toast(variant: 'success', heading: 'Updated', text: "Notifications {$status}.");
    }

    public function setNotificationPriority($priority)
    {
        if (!$this->selectedConversationId) return;
        
        $conversation = Conversation::where('user_id', Auth::id())->findOrFail($this->selectedConversationId);
        $conversation->update(['notification_priority' => $priority]);
        
        Flux::toast(variant: 'success', heading: 'Updated', text: 'Notification priority updated.');
    }

    // New Functional Methods
    public function redirectToRefund()
    {
        $conv = Conversation::where('user_id', Auth::id())->with('product')->find($this->selectedConversationId);
        if (!$conv || !$conv->product_id) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Product not found.');
            return;
        }

        // Find the latest completed order for this product by this user
        $order = \App\Models\Order::where('buyer_id', Auth::id())
            ->where('status', 'completed')
            ->whereHas('items', function($q) use ($conv) {
                $q->where('product_id', $conv->product_id);
            })
            ->latest()
            ->first();

        if (!$order) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'You need a completed order for this product to request a refund.');
            return;
        }

        return redirect()->route('refunds.create', $order->id);
    }

    public function redirectToOrders()
    {
        return redirect()->route('orders.index');
    }

    public function redirectToRate()
    {
        $conv = Conversation::where('user_id', Auth::id())->with('product')->find($this->selectedConversationId);
        if (!$conv || !$conv->product) {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'Product not found.');
            return;
        }

        return redirect()->route('products.show', $conv->product->slug) . '#reviews';
    }

    public function submitAuthorReport()
    {
        if (!$this->selectedConversationId) return;

        $this->validate([
            'reportCategory' => 'required|in:abusive_language,spam,scam,poor_service,other',
            'reportReason' => 'required|string|min:10|max:1000',
        ]);

        $conversation = Conversation::where('user_id', Auth::id())->findOrFail($this->selectedConversationId);

        \App\Models\AuthorReport::create([
            'buyer_id' => Auth::id(),
            'author_id' => $conversation->author_id,
            'conversation_id' => $conversation->id,
            'category' => $this->reportCategory,
            'reason' => $this->reportReason,
            'status' => 'pending',
        ]);

        $this->showReportModal = false;
        $this->reset(['reportCategory', 'reportReason']);

        Flux::toast(variant: 'success', heading: 'Report Sent', text: 'Your report has been submitted to admin for review.');
    }

    public function acceptQuote($messageId)
    {
        $message = ChatMessage::whereHas('conversation', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($messageId);
        
        if ($message->type !== 'quote' || !$message->metadata || $message->metadata['status'] !== 'pending') {
            Flux::toast(variant: 'danger', heading: 'Error', text: 'This quote cannot be accepted.');
            return;
        }

        // Update status to accepted
        $metadata = $message->metadata;
        $metadata['status'] = 'accepted';
        $message->update(['metadata' => $metadata]);

        // In a real app, this would redirect to a specific payment flow or create an order
        // For now, we'll simulate a redirect to checkout with the quoted price
        Flux::toast(variant: 'success', heading: 'Quote Accepted', text: 'Redirecting to checkout...');
        
        // Example: redirect to a generic checkout with the amount
        // return redirect()->route('checkout.index', ['amount' => $metadata['amount'], 'quote_id' => $message->id]);
    }

    // Context Tracking
    #[On('update-context')]
    public function updateContext($url, $title = null)
    {
        if (!$this->selectedConversationId) return;
        
        Conversation::where('user_id', Auth::id())->findOrFail($this->selectedConversationId)->update([
            'current_context' => [
                'url' => $url,
                'title' => $title,
                'updated_at' => now()->toDateTimeString()
            ]
        ]);
    }

    // ===== END ADVANCED FEATURES =====
}
