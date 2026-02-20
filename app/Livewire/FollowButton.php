<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowButton extends Component
{
    public $authorId;
    public $isFollowing = false;

    public function mount($authorId)
    {
        $this->authorId = $authorId;

        if (Auth::check()) {
            $this->isFollowing = Auth::user()->following()->where('following_id', $this->authorId)->exists();
        }
    }

    public function toggleFollow()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $author = User::findOrFail($this->authorId);
        
        if ($this->isFollowing) {
            $user->following()->detach($this->authorId);
            $this->isFollowing = false;
            
            $this->dispatch('toast', 
                heading: 'Unfollowed',
                text: "You are no longer following {$author->name}",
                variant: 'success'
            );
        } else {
            $user->following()->attach($this->authorId);
            $this->isFollowing = true;
            
            $this->dispatch('toast', 
                heading: 'Following',
                text: "You are now following {$author->name}",
                variant: 'success'
            );
        }

        $this->dispatch('follow-updated', authorId: $this->authorId, isFollowing: $this->isFollowing);
    }

    public function render()
    {
        return view('livewire.follow-button');
    }
}
