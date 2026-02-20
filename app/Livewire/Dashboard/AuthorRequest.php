<?php

namespace App\Livewire\Dashboard;

use App\Models\AuthorRequest as AuthorRequestModel;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthorRequest extends Component
{
    public string $portfolio_url = '';
    public string $message = '';
    public bool $submitted = false;

    public function mount()
    {
        if (Auth::user()->isAuthor()) {
            return redirect()->route('author.dashboard');
        }

        $existingRequest = AuthorRequestModel::where('user_id', Auth::id())->where('status', 'pending')->first();
        if ($existingRequest) {
            $this->submitted = true;
        }
    }

    public function submit()
    {
        $this->validate([
            'portfolio_url' => 'nullable|url',
            'message' => 'required|min:20',
        ]);

        $autoApprove = (bool) \App\Models\PlatformSetting::get('auto_approve_authors', false);
        $status = $autoApprove ? 'approved' : 'pending';

        $request = AuthorRequestModel::create([
            'user_id' => Auth::id(),
            'portfolio_url' => $this->portfolio_url,
            'message' => $this->message,
            'status' => $status,
        ]);

        // Notify Admin
        \Illuminate\Support\Facades\Mail::to(config('mail.aliases.admin'))->queue(new \App\Mail\AdminNewAuthorApplication($request));

        // Database Notification for Admins
        $admins = \App\Models\User::admins();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification([
                'title' => 'New Author Request ✍️',
                'message' => 'User ' . Auth::user()->name . ' has submitted an author application.',
                'type' => 'warning',
                'action_text' => 'Review Application',
                'action_url' => route('admin.author-requests'),
            ]));
        }

        if ($autoApprove) {
            $user = Auth::user();
            $user->assignRole('author');
            
            // Assign default subscription plan if not already set
            if (!$user->subscription_plan_id) {
                $defaultPlan = \App\Models\SubscriptionPlan::getDefaultPlan();
                if ($defaultPlan) {
                    $user->update(['subscription_plan_id' => $defaultPlan->id]);
                }
            }
            
            // Notify Author
            $user->notify(new \App\Notifications\SystemNotification([
                'title' => 'Selamat Datang di Keluarga Author! 🚀',
                'message' => 'Pengajuan kamu telah disetujui secara otomatis. Sekarang kamu bisa mulai mengunggah produk-produk kreatif kamu.',
                'type' => 'success',
                'action_text' => 'Buka Dashboard Author',
                'action_url' => route('author.dashboard'),
            ]));
        }

        $this->submitted = true;
        \Flux::toast(
            variant: 'success',
            heading: $autoApprove ? 'Welcome on Board!' : 'Application Sent',
            text: $autoApprove ? 'Your application was auto-approved. Welcome!' : 'Your request has been submitted and is awaiting approval.',
        );
    }

    public function render()
    {
        return view('livewire.dashboard.author-request', [
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('price', 'asc')->get(),
        ]);
    }
}
