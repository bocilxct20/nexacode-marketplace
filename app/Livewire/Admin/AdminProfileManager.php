<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Flux;

class AdminProfileManager extends Component
{
    use WithFileUploads;

    public $tab = 'profile';
    public $name;
    public $email;
    public $bio;
    public $avatar;
    public $current_password;
    public $password;
    public $password_confirmation;
    public $notifications = [];
    public $readyToLoad = false;

    public function mount()
    {
        $user = auth()->user();
        $this->tab = request()->get('tab', 'profile');
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio;
        $this->notifications = $user->notification_preferences ? json_decode($user->notification_preferences, true) : [];
    }

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
        ];

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        $user->update($data);

        Flux::toast(variant: 'success', heading: 'Profile Updated', text: 'Your profile has been updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        Flux::toast(variant: 'success', heading: 'Password Updated', text: 'Your password has been changed successfully.');
    }

    public function updateNotifications()
    {
        auth()->user()->update([
            'notification_preferences' => json_encode($this->notifications),
        ]);

        Flux::toast(variant: 'success', heading: 'Settings Saved', text: 'Notification preferences have been saved.');
    }

    public function render()
    {
        return view('livewire.admin.admin-profile-manager', [
            'user' => auth()->user(),
        ]);
    }
}
