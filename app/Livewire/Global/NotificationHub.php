<?php

namespace App\Livewire\Global;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationHub extends Component
{
    public function getListeners()
    {
        $authId = Auth::id();
        return [
            "echo-private:App.Models.User.{$authId},notification" => '$refresh',
            'notification-read' => '$refresh',
        ];
    }

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
            ? $user->notifications()->take(10)->get()
            : collect();

        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;

        $unpaidOrders = $user
            ? \App\Models\Order::where('buyer_id', $user->id)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->latest()
                ->get()
            : collect();

        return view('livewire.global.notification-hub', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount + $unpaidOrders->count(),
            'unpaidOrders' => $unpaidOrders,
        ]);
    }
}
