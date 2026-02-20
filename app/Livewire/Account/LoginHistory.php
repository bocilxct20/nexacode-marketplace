<?php

namespace App\Livewire\Account;

use App\Models\SecurityLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class LoginHistory extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all | success | failed

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function getActionLabel(string $action): string
    {
        return match($action) {
            'login_success'        => 'Login Berhasil',
            'login_failed'         => 'Login Gagal',
            'social_login_success' => 'Login via Social',
            '2fa_verified'         => 'Verifikasi 2FA',
            '2fa_failed'           => '2FA Gagal',
            '2fa_backup_code_used' => 'Pakai Backup Code',
            'email_changed'        => 'Email Diubah',
            'password_changed'     => 'Password Diubah',
            '2fa_enabled'          => '2FA Diaktifkan',
            '2fa_disabled'         => '2FA Dinonaktifkan',
            default                => ucwords(str_replace('_', ' ', $action)),
        };
    }

    public function getActionColor(string $action): string
    {
        return match(true) {
            str_contains($action, 'success') || str_contains($action, 'verified') => 'text-emerald-600 dark:text-emerald-400',
            str_contains($action, 'failed')  => 'text-red-600 dark:text-red-400',
            str_contains($action, 'enabled') => 'text-blue-600 dark:text-blue-400',
            str_contains($action, 'changed') => 'text-amber-600 dark:text-amber-400',
            default                          => 'text-zinc-600 dark:text-zinc-400',
        };
    }

    public function getActionBadge(string $action): string
    {
        return match(true) {
            str_contains($action, 'success') || str_contains($action, 'verified') => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
            str_contains($action, 'failed')  => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
            str_contains($action, 'changed') => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
            str_contains($action, 'enabled') => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            default                          => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300',
        };
    }

    public function parseDevice(string $userAgent): array
    {
        $browser = 'Unknown';
        $os = 'Unknown';

        // Browser detection
        if (str_contains($userAgent, 'Chrome'))       $browser = 'Chrome';
        elseif (str_contains($userAgent, 'Firefox'))  $browser = 'Firefox';
        elseif (str_contains($userAgent, 'Safari'))   $browser = 'Safari';
        elseif (str_contains($userAgent, 'Edge'))     $browser = 'Edge';
        elseif (str_contains($userAgent, 'Opera'))    $browser = 'Opera';

        // OS detection
        if (str_contains($userAgent, 'Windows'))      $os = 'Windows';
        elseif (str_contains($userAgent, 'Macintosh'))$os = 'Mac';
        elseif (str_contains($userAgent, 'iPhone'))   $os = 'iPhone';
        elseif (str_contains($userAgent, 'Android'))  $os = 'Android';
        elseif (str_contains($userAgent, 'Linux'))    $os = 'Linux';

        return compact('browser', 'os');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $user = Auth::user();

        $query = SecurityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($this->filter === 'success') {
            $query->where(fn ($q) =>
                $q->where('action', 'like', '%success%')
                  ->orWhere('action', 'like', '%verified%')
            );
        } elseif ($this->filter === 'failed') {
            $query->where('action', 'like', '%failed%');
        }

        return view('livewire.account.login-history', [
            'logs' => $query->paginate(15),
        ]);
    }
}
