<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    if (!$conversation) return false;
    
    // Allow if user is the conversation owner (buyer) OR the author OR is an admin
    return (int) $user->id === (int) $conversation->user_id || 
           (int) $user->id === (int) $conversation->author_id || 
           $user->isAdmin();
});

Broadcast::channel('admin.chat', function ($user) {
    return $user->isAdmin();
});

Broadcast::channel('presence-support-team', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'is_admin' => $user->isAdmin(),
    ];
});
