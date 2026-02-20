<?php

namespace App\Livewire\Home;

use App\Models\NewsletterSubscriber;
use Livewire\Component;

class NewsletterSubscription extends Component
{
    public $email;

    protected $rules = [
        'email' => 'required|email|unique:newsletter_subscribers,email',
    ];

    public function subscribe()
    {
        $this->validate();

        NewsletterSubscriber::create([
            'email' => $this->email,
            'status' => 'active',
        ]);

        $this->reset('email');

        $this->dispatch('toast', [
            'variant' => 'success',
            'heading' => 'Subscribed!',
            'text' => 'You will now receive the weekly NexaCode curation.',
        ]);
    }

    public function render()
    {
        return view('livewire.home.newsletter-subscription', [
            'subscriberCount' => NewsletterSubscriber::count() + 5000, // Basing it on real count + initial community
        ]);
    }
}
