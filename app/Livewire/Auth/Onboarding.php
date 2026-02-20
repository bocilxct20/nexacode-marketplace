<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Flux;

class Onboarding extends Component
{
    use WithFileUploads;

    public string $username = '';
    public string $bio = '';
    public $avatar;

    public function mount()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        
        // If already has username, they might have completed onboarding
        if ($user->username) {
            return redirect()->route('home');
        }
        
        $this->username = strtolower(str_replace(' ', '', $user->name));
    }

    public function updatedUsername()
    {
        $this->username = strtolower(str_replace(' ', '', $this->username));
        $this->validateOnly('username');
    }

    public function updatedAvatar()
    {
        $this->validateOnly('avatar');
    }

    protected function rules()
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username,' . Auth::id()],
            'bio' => ['nullable', 'string', 'max:160'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        
        $data = [
            'username' => $this->username,
            'bio' => $this->bio,
        ];

        if ($this->avatar) {
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        $user->update($data);

        Flux::toast(
            variant: 'success',
            heading: 'Profile Setup Complete',
            text: 'Welcome aboard! Your profile is ready.',
        );

        return redirect()->route('home');
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.onboarding');
    }
}
