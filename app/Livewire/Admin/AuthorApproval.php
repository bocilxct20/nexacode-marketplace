<?php

namespace App\Livewire\Admin;

use App\Models\AuthorRequest;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Flux;

class AuthorApproval extends Component
{
    use WithPagination;

    public $selectedRequest = null;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function requests()
    {
        return AuthorRequest::with('user')
            ->where('status', \App\Enums\AuthorRequestStatus::PENDING)
            ->orderBy(
                in_array($this->sortBy, ['name', 'created_at']) ? $this->sortBy : 'created_at',
                in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : 'desc'
            )
            ->paginate(10);
    }

    public function viewRequest($requestId)
    {
        $this->selectedRequest = AuthorRequest::with(['user', 'user.roles'])->findOrFail($requestId);
        $this->dispatch('modal-opened', name: 'review-application');
    }

    public function approve($requestId)
    {
        $request = AuthorRequest::findOrFail($requestId);
        $user = $request->user;

        // Update request status
        $request->update(['status' => \App\Enums\AuthorRequestStatus::APPROVED]);

        // Attach author role if not already attached
        $authorRole = Role::where('slug', 'author')->first();
        if ($authorRole && !$user->isAuthor()) {
            $user->roles()->attach($authorRole);
        }

        // Assign default subscription plan if not already set
        if (!$user->subscription_plan_id) {
            $defaultPlan = \App\Models\SubscriptionPlan::getDefaultPlan();
            if ($defaultPlan) {
                $user->update(['subscription_plan_id' => $defaultPlan->id]);
            }
        }

        // Notify User
        \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\AuthorApplicationApproved($request));

        if ($this->selectedRequest && $this->selectedRequest->id == $requestId) {
            $this->selectedRequest->refresh();
        }
 
        Flux::toast(variant: 'success', heading: 'Approved', text: "Application for {$user->name} has been approved.");
    }

    public function reject($requestId)
    {
        $request = AuthorRequest::findOrFail($requestId);
        $request->update(['status' => \App\Enums\AuthorRequestStatus::REJECTED]);

        // Notify User
        \Illuminate\Support\Facades\Mail::to($request->user->email)->queue(new \App\Mail\AuthorApplicationRejected($request));

        if ($this->selectedRequest && $this->selectedRequest->id == $requestId) {
            $this->selectedRequest->refresh();
        }
 
        Flux::toast(variant: 'success', heading: 'Rejected', text: "Application for {$request->user->name} has been rejected.");
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.author-approval');
    }
}
