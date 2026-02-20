<?php

namespace App\Livewire\Account;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Mail\EmailChangeVerification;
use App\Models\PendingEmailChange;
use App\Models\SecurityLog;

class ProfileManager extends Component
{
    use WithFileUploads;

    public $tab = 'profile';

    // Profile Fields
    public $name;
    public $email;
    public $bio;
    public $avatar;
    public $currentAvatar;

    // Elite Branding Fields
    public $cover_image;
    public $currentCoverImage;
    public $storefront_message;
    public $brand_color;

    // Password Fields
    public $current_password;
    public $password;
    public $password_confirmation;

    // Notification Prefs
    // Email Preferences
    public $pref_order_confirmations;
    public $pref_sale_notifications;
    public $pref_product_updates;
    public $pref_review_notifications;
    public $pref_withdrawal_notifications;
    public $pref_marketing_emails;
    public $pref_newsletter;

    // Bank Details
    public $bank_name;
    public $bank_account_number;
    public $bank_account_name;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio;
        $this->currentAvatar = $user->avatar;
        
        // Elite Branding
        $this->storefront_message = $user->storefront_message;
        $this->brand_color = $user->brand_color ?? '#10b981';
        $this->currentCoverImage = $user->cover_image;

        // Payout Settings
        $this->bank_name = $user->bank_name;
        $this->bank_account_number = $user->bank_account_number;
        $this->bank_account_name = $user->bank_account_name;

        // Email Preferences
        $prefs = \App\Models\EmailPreference::forUser($user->id);
        $this->pref_order_confirmations = $prefs->order_confirmations;
        $this->pref_sale_notifications = $prefs->sale_notifications;
        $this->pref_product_updates = $prefs->product_updates;
        $this->pref_review_notifications = $prefs->review_notifications;
        $this->pref_withdrawal_notifications = $prefs->withdrawal_notifications;
        $this->pref_marketing_emails = $prefs->marketing_emails;
        $this->pref_newsletter = $prefs->newsletter;

        // Sync from URL if tab is present
        $this->tab = request()->query('tab', 'profile');
    }

    public function setTab($name)
    {
        $this->tab = $name;
        $this->resetErrorBag();
        $this->dispatch('tab-changed', $name);
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($user->isElite()) {
            $eliteValidated = $this->validate([
                'storefront_message' => 'nullable|string|max:255',
                'brand_color' => 'nullable|string|max:7',
                'cover_image' => 'nullable|image|max:5120',
            ]);
            $validated = array_merge($validated, $eliteValidated);
        }

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $this->avatar->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        if ($this->cover_image) {
            if ($user->cover_image) {
                Storage::disk('public')->delete($user->cover_image);
            }
            $validated['cover_image'] = $this->cover_image->store('branding', 'public');
        } else {
            unset($validated['cover_image']);
        }

        // ── Feature #5: Email Change dengan Re-Verification ───────────────
        // Jika email berubah, jangan langsung update — kirim verifikasi dulu
        $emailChanged = $user->email !== $this->email;
        if ($emailChanged) {
            $newEmail = $this->email;

            // Simpan kembali email lama di form (jangan ubah dulu)
            $this->email      = $user->email;
            unset($validated['email']);

            // Buat pending change dan kirim email verifikasi
            $pending = PendingEmailChange::createFor($user, $newEmail);
            $verificationUrl = route('email.change.confirm', ['token' => $pending->token]);

            Mail::to($newEmail)->queue(new EmailChangeVerification(
                user           : $user,
                newEmail       : $newEmail,
                verificationUrl: $verificationUrl,
            ));

            SecurityLog::log('email_change_requested', $user->id, ['new_email' => $newEmail]);

            // Update fields lain (kecuali email)
            if (!empty($validated)) {
                $user->update($validated);
            }

            $this->dispatch('profile-updated');
            $this->dispatch('toast',
                variant: 'info',
                heading: 'Verifikasi Diperlukan',
                text   : "Link konfirmasi dikirim ke {$newEmail}. Email kamu belum berubah.",
            );
            return;
        }

        $user->update($validated);

        $this->dispatch('profile-updated');
        $this->dispatch('toast', variant: 'success', heading: 'Profile Updated', text: 'Your profile information has been saved.');
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password'            => Hash::make($this->password),
            'password_changed_at' => now(),
        ]);

        // Log & notify
        SecurityLog::log('password_changed', $user->id);

        // Logout semua device lain
        Auth::logoutOtherDevices($this->current_password);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        
        $this->dispatch('toast', variant: 'success', heading: 'Password Updated', text: 'Your password has been changed successfully.');
    }

    public function updateNotifications()
    {
        $user = Auth::user();
        $prefs = \App\Models\EmailPreference::forUser($user->id);
        
        $prefs->update([
            'order_confirmations' => $this->pref_order_confirmations,
            'sale_notifications' => $this->pref_sale_notifications,
            'product_updates' => $this->pref_product_updates,
            'review_notifications' => $this->pref_review_notifications,
            'withdrawal_notifications' => $this->pref_withdrawal_notifications,
            'marketing_emails' => $this->pref_marketing_emails,
            'newsletter' => $this->pref_newsletter,
        ]);

        $this->dispatch('toast', variant: 'success', heading: 'Preferences Saved', text: 'Email notification preferences updated.');
    }

    public function updatePayoutSettings()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
        ]);

        $user->update($validated);

        $this->dispatch('toast', variant: 'success', heading: 'Payout Settings Saved', text: 'Your bank details have been updated successfully.');
    }

    public function exportSecurityLog()
    {
        $user = Auth::user();
        $logs = \App\Models\SecurityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($logs->isEmpty()) {
            $this->dispatch('toast', variant: 'error', heading: 'Export Failed', text: 'No security logs found to export.');
            return;
        }

        $csvHeader = "Date,Action,IP Address,Device/User-Agent,Status\n";
        $csvData = $logs->map(function ($log) {
            return sprintf(
                "%s,%s,%s,%s,%s",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->action,
                $log->ip_address,
                str_replace(',', ';', $log->user_agent ?? ''),
                $log->response_status ?? 'success'
            );
        })->join("\n");

        $filename = 'security_log_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($csvHeader, $csvData) {
            echo $csvHeader . $csvData;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.account.profile-manager', [
            'user' => Auth::user(),
        ]);
    }
}
