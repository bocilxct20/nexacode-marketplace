<?php

namespace App\Livewire\Global;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class NotificationCenter extends Component
{
    use WithPagination;

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        $this->dispatch('notification-read');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('notification-read');
    }

    public function clearAll()
    {
        Auth::user()->notifications()->delete();
        $this->dispatch('notification-read');
    }

    public function render()
    {
        $user = Auth::user();
        
        $notifications = $user 
            ? $user->notifications()->paginate(15)
            : collect();

        return view('livewire.global.notification-center', [
            'notifications' => $notifications,
        ])->layout('layouts.app');
    }
}
