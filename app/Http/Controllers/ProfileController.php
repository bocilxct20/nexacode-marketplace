<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SecurityService;

class ProfileController extends Controller
{
    /**
     * Display the profile settings page.
     */
    public function show()
    {
        $user = auth()->user();

        // Super smart redirection: guide users to their role-specific profile root
        if ($user->isAdmin()) {
            return redirect()->route('admin.profile');
        }

        if ($user->isAuthor()) {
            return redirect()->route('author.profile');
        }

        // Buyers stay in the marketplace/dashboard root context
        return view('profile');
    }

    /**
     * Update user profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        $oldEmail = $user->email;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        if ($oldEmail !== $user->email) {
            app(SecurityService::class)->logActivity('email_changed', $user, [
                'old_email' => $oldEmail,
                'new_email' => $user->email
            ]);
            
            // Send alert to OLD email
            \Illuminate\Support\Facades\Mail::to($oldEmail)->queue(new \App\Mail\SecurityAlert(
                $user,
                'Email Akun Kamu Diganti',
                'Email untuk akun NexaCode kamu baru saja diganti dari ' . $oldEmail . ' ke ' . $user->email . '. Jika ini bukan tindakan kamu, segera hubungi support.',
                route('profile.edit'),
                'Amankan Akun'
            ));
        }

        \Flux::toast(
            variant: 'success',
            heading: 'Profile Saved',
            text: 'Your account information has been updated.',
        );
        return redirect()->route('profile');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        app(SecurityService::class)->recordPasswordChange($user);

        \Flux::toast(
            variant: 'success',
            heading: 'Security Updated',
            text: 'Your password has been changed successfully.',
        );
        return redirect()->route('profile');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotificationPreferences(Request $request)
    {
        $preferences = $request->input('notifications', []);
        
        auth()->user()->update([
            'notification_preferences' => json_encode($preferences),
        ]);

        \Flux::toast(
            variant: 'success',
            heading: 'Preferences Saved',
            text: 'Your notification settings have been updated.',
        );
        return redirect()->route('profile');
    }
}
