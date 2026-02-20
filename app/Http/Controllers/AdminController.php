<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Earning;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin overview.
     */
    public function index()
    {
        return view('admin.index');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
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

        return redirect()->route('admin.profile')->with('success', 'Notification preferences updated successfully.');
    }
}
