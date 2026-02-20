<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\ChatProtectionService;

class ChatWidget extends Component
{
    use WithFileUploads;

    public $conversation;
    public $conversationId;
    public $authorId = null;
    public $productId = null;
    public $session_id;
    public $protectionWarning = null;
    public $isOpen = false;
    public $message = '';
    public $attachment;

    public function mount()
    {
        $this->session_id = Session::getId();
        $this->loadConversation();
    }

    public function loadConversation($authorId = null, $productId = null)
    {
        if (!Auth::check()) {
            return;
        }

        $this->authorId = $authorId;
        $this->productId = $productId;

        $this->conversation = Conversation::firstOrCreate(
            [
                'user_id' => Auth::id(), 
                'author_id' => $this->authorId,
                'product_id' => $this->productId,
                'status' => 'open'
            ],
            ['last_message_at' => now()]
        );
        $this->conversationId = $this->conversation->id;
    }

    #[On('open-author-chat')]
    public function openAuthorChat($authorId, $productId = null)
    {
        $this->loadConversation($authorId, $productId);
        $this->isOpen = true;
        $this->markAsRead();
    }

    #[On('open-admin-support')]
    public function openAdminSupport()
    {
        $this->loadConversation(null, null);
        $this->isOpen = true;
        $this->markAsRead();
    }

    public function updatedIsOpen($value)
    {
        if ($value) {
            $this->markAsRead();
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->markAsRead();
        }
    }

    public function sendMessage()
    {
        // Rate Limiting
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('chat-uploads', 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('chat-uploads');
            $this->dispatch('toast', variant: 'error', heading: 'Terlalu Cepat', text: "Tunggu {$seconds} detik lagi ya.");
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit('chat-uploads');

        $this->validate([
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,zip,doc,docx', // Hardened
        ]);

        if ($this->attachment) {
            $isImage = str_starts_with($this->attachment->getMimeType(), 'image/');
            if ($isImage && !$this->deepValidateFile($this->attachment)) {
                $this->attachment = null;
                $this->dispatch('toast', variant: 'error', heading: 'File Tidak Valid', text: 'File yang kamu upload sepertinya bukan gambar beneran.');
                return;
            }
        }

        if (!$this->conversation) {
            $this->loadConversation();
        }

        $isOffline = !$this->isWithinWorkingHours();

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
            'conversation_id' => $this->conversation->id,
            'sender_id' => Auth::id(),
            'message' => $this->message ?? '',
            'image_path' => $imagePath,
            'is_admin' => false,
        ]);

        Conversation::find($this->conversation->id)->update([
            'last_message_at' => now(),
            'last_buyer_message_at' => now(), // Start SLA timer
            'status' => 'open' // Auto re-open
        ]);

        // If it's a chat with author, we might want a different auto-reply or just wait
        if ($this->authorId) {
            $author = \App\Models\User::find($this->authorId);
            if ($author && $author->auto_reply_enabled && $author->auto_reply_message) {
                // Check if auto-reply was already sent in the last 24 hours for this conversation
                $lastAutoReply = ChatMessage::where('conversation_id', $this->conversation->id)
                    ->where('sender_id', $author->id)
                    ->where('message', $author->auto_reply_message)
                    ->where('created_at', '>=', now()->subDay())
                    ->exists();

                if (!$lastAutoReply) {
                    $autoMsg = ChatMessage::create([
                        'conversation_id' => $this->conversation->id,
                        'sender_id' => $author->id,
                        'message' => $author->auto_reply_message,
                        'is_admin' => true, // Author is treated as admin in the widget's bubble logic
                    ]);
                    broadcast(new MessageSent($autoMsg))->toOthers();
                }
            }
        }

        // Only send admin offline auto-reply for admin support chats
        if ($isOffline && $this->authorId === null) {
            $this->sendAutoReply();
        }

        $this->message = '';
        $this->attachment = null;
        $this->protectionWarning = null;
        $this->dispatch('scroll-to-bottom');
    }

    public function updatedMessage($value)
    {
        $detected = ChatProtectionService::scan($value);
        $this->protectionWarning = ChatProtectionService::getWarningMessage($detected);
    }

    private function isWithinWorkingHours()
    {
        $now = now()->timezone('Asia/Jakarta');
        $hour = $now->hour;
        $day = $now->dayOfWeek; // 0 (Sun) - 6 (Sat)

        // Monday - Friday, 09:00 - 18:00
        return ($day >= 1 && $day <= 5) && ($hour >= 9 && $hour < 18);
    }

    private function sendAutoReply()
    {
        // Check if auto-reply was already sent in the last 24 hours for this conversation
        $lastAutoReply = ChatMessage::where('conversation_id', $this->conversation->id)
            ->where('is_admin', true)
            ->where('message', 'like', '%Tim kami sedang offline%')
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($lastAutoReply) {
            return;
        }

        // Get an admin user ID (first admin) using the correct relationship
        $adminId = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'admin');
        })->first()?->id;

        $autoMsg = ChatMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $adminId,
            'message' => 'Halo! Terima kasih sudah menghubungi Nexa Support. Tim kami sedang offline saat ini (Jam kerja: Senin-Jumat, 09:00-18:00 WIB). Pesan kamu sudah kami terima dan akan langsung kami balas saat kami kembali online ya! 😊',
            'is_admin' => true,
        ]);

        broadcast(new MessageSent($autoMsg))->toOthers();
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('$refresh');
    }

    #[On('refresh-chat')]
    public function onMessageReceived()
    {
        $this->conversation->load('messages');
        if ($this->isOpen) {
            $this->markAsRead();
        } else {
            $this->dispatch('new-message');
        }
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('$refresh');
    }

    public function markAsRead()
    {
        if ($this->conversation) {
            $this->conversation->messages()
                ->where('is_admin', true)
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

    private function deepValidateFile($file)
    {
        $path = $file->getRealPath();
        
        // Basic binary header check (Magic Numbers)
        // JPEG starts with FF D8 FF
        // PNG starts with 89 50 4E 47
        // WEBP starts with 52 49 46 46 (RIFF)
        $handle = fopen($path, 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        $hex = bin2hex($header);
        
        // JPEG check
        if (str_starts_with($hex, 'ffd8ff')) return true;
        // PNG check
        if ($hex === '89504e47') return true;
        // WEBP/RIFF check
        if ($hex === '52494646') return true;

        return false;
    }

    private function scanFile($path)
    {
        // Placeholder for ClamAV scan (Linux)
        // If on Linux/VPS, you can run: shell_exec("clamscan --no-summary " . escapeshellarg($path));
        // For Windows development, we skip this but keep the structure ready.
        return true;
    }

    public function render()
    {
        $messages = $this->conversation ? $this->conversation->messages()->oldest()->get() : collect();
        return view('livewire.chat-widget', [
            'messages' => $messages,
        ]);
    }

    // Context Tracking
    #[On('update-context')]
    public function updateContext($url, $title = null)
    {
        if (!$this->conversation) return;
        
        $this->conversation->update([
            'current_context' => [
                'url' => $url,
                'title' => $title,
                'updated_at' => now()->toDateTimeString()
            ]
        ]);
    }
}
