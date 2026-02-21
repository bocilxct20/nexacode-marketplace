<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'bio',
        'website_url',
        'twitter_url',
        'github_url',
        'avatar',
        'otp_code',
        'otp_expires_at',
        'github_id',
        'google_id',
        'two_factor_enabled',
        'two_factor_confirmed_at',
        'remember_2fa_token',
        'password_changed_at',
        'force_password_change',
        'two_factor_secret',
        'notification_preferences',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
        'bank_account_number',
        'bank_account_name',
        'auto_reply_enabled',
        'auto_reply_message',
        'trial_ends_at',
        'subscription_ends_at',
        'cover_image',
        'storefront_message',
        'brand_color',
        'affiliate_code',
        'referred_by_id',
        // Feature #6: Account lockout
        'failed_login_count',
        'account_locked_until',
        // Feature #5: Email change pending
        'pending_email',
        'xp',
        'level',
    ];

    /**
     * Check if the user has bank details configured.
     */
    public function hasBankDetails(): bool
    {
        return !empty($this->bank_name) && 
               !empty($this->bank_account_number) && 
               !empty($this->bank_account_name);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'last_seen_at'            => 'datetime',
            'last_login_at'           => 'datetime',
            'otp_expires_at'          => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password_changed_at'     => 'datetime',
            'trial_ends_at'           => 'datetime',
            'subscription_ends_at'    => 'datetime',
            'account_locked_until'    => 'datetime',
            'xp'                      => 'integer',
            'level'                   => 'integer',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'author_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'buyer_id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class, 'author_id');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'author_id');
    }

    public function authorRequests()
    {
        return $this->hasMany(AuthorRequest::class);
    }

    public function hasRole($role)
    {
        if ($role instanceof \App\Enums\UserRole) {
            return $this->roles->contains('slug', $role->value);
        }
        
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        
        return !! $role->intersect($this->roles)->count();
    }

    public function isAdmin()
    {
        return $this->hasRole(\App\Enums\UserRole::ADMIN);
    }

    public function isAuthor()
    {
        return $this->hasRole(\App\Enums\UserRole::AUTHOR);
    }

    public static function admins()
    {
        return static::whereHas('roles', function ($q) {
            $q->where('slug', \App\Enums\UserRole::ADMIN->value);
        })->get();
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the user's current subscription plan, or the default 'Basic' if not set.
     */
    public function currentPlan()
    {
        // If the subscription is NOT active (and not trialing), return default plan
        if (!$this->isSubscriptionActive() && !$this->isTrialing()) {
            return SubscriptionPlan::getDefaultPlan();
        }

        return $this->subscriptionPlan ?? SubscriptionPlan::getDefaultPlan();
    }

    /**
     * Check if the user is an Elite author.
     */
    public function isElite(): bool
    {
        $plan = $this->currentPlan();
        return $plan && $plan->is_elite;
    }

    /**
     * Get the user's tier badge info (label and color).
     */
    public function getTierBadgeAttribute(): object
    {
        $plan = $this->currentPlan();
        
        if ($plan && $plan->is_elite) {
            return (object) [
                'label' => 'Elite',
                'color' => 'amber',
                'icon' => 'sparkles',
            ];
        }

        if ($plan && ($plan->slug === 'pro' || $plan->allow_trial)) {
            return (object) [
                'label' => 'Pro',
                'color' => 'indigo',
                'icon' => 'bolt',
            ];
        }

        return (object) [
            'label' => 'Basic',
            'color' => 'zinc',
            'icon' => 'user',
        ];
    }

    /**
     * Check if the user is a Pro author.
     */
    public function isPro(): bool
    {
        // Pro features are usually available for both Pro and Elite tiers
        $plan = $this->currentPlan();
        if (!$plan) return false;
        
        return $plan->allow_trial || $plan->is_elite;
    }

    /**
     * Get the user's initials for Flux avatars.
     */
    public function getInitialsAttribute()
    {
        return collect(explode(' ', $this->name))
            ->map(fn($segment) => mb_substr($segment, 0, 1))
            ->take(2)
            ->join('');
    }

    /**
     * Calculate a smart ranking score for the author.
     */
    public function getRankingScoreAttribute(): float
    {
        // 1. Sales Impact (Quantity)
        $totalSales = $this->products()->sum('sales_count');
        $salesScore = $totalSales * 15;

        // 2. Revenue Impact (Economic Value)
        // Reward high-value sales: Rp 1.000.000 revenue = 100 points
        $totalRevenue = $this->earnings()->sum('amount');
        $revenueScore = ($totalRevenue / 10000); 

        // 3. Quality Impact (Weighted by product count)
        $avgRating = $this->products()->avg('avg_rating') ?: 0;
        $productCount = $this->products()->count();
        $qualityScore = ($avgRating * 250) + ($productCount * 100);

        // 4. XP Impact (Loyalty/Activity)
        $xpScore = $this->xp * 0.15;

        // 5. Tier Bonus
        $tierBonus = 0;
        if ($this->isElite()) $tierBonus = 10000;
        elseif ($this->isPro()) $tierBonus = 3000;

        return $salesScore + $revenueScore + $qualityScore + $xpScore + $tierBonus;
    }

    /**
     * Get the author's global rank position.
     */
    public function getGlobalRankPosition(): int
    {
        return \Illuminate\Support\Facades\Cache::remember("user_{$this->id}_rank", 3600, function () {
            $authors = User::whereHas('roles', fn($q) => $q->where('slug', 'author'))
                ->get()
                ->sortByDesc('ranking_score')
                ->values();
            
            $position = $authors->search(fn($user) => $user->id === $this->id);
            
            return $position !== false ? $position + 1 : 0;
        });
    }

    public function isTrialing(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasUsedTrial(): bool
    {
        return $this->has_used_trial;
    }

    /**
     * Check if the user has an active paid subscription.
     */
    public function isSubscriptionActive(): bool
    {
        // If no plan is assigned, it's not active
        if (!$this->subscription_plan_id) return false;
        
        // If it's the Default plan (via direct relationship), it's always active
        if ($this->subscriptionPlan && $this->subscriptionPlan->is_default) return true;

        // Otherwise check ends_at
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    /**
     * Check if the subscription expires in less than 5 days.
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->subscription_ends_at) return false;
        return $this->subscription_ends_at->isFuture() && $this->subscription_ends_at->diffInDays(now()) <= 5;
    }

    /**
     * Get the user's email preferences
     */
    public function emailPreferences()
    {
        return $this->hasOne(EmailPreference::class);
    }

    /**
     * Get the user's two-factor authentication settings
     */
    public function twoFactorAuth()
    {
        return $this->hasOne(TwoFactorAuth::class);
    }

    /**
     * Get the user's security logs
     */
    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }

    /**
     * Users who follow this user (as an author).
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    /**
     * Authors followed by this user.
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    /**
     * Get the OS info from the user agent.
     */
    public function getDeviceOsInfoAttribute()
    {
        $ua = $this->last_login_device ?? '';
        
        $osData = [
            'Windows' => ['regex' => '/Windows/i', 'icon' => 'lucide-monitor', 'color' => 'text-blue-500'],
            'Apple' => ['regex' => '/Macintosh|iPhone|iPad|Mac OS X|iOS/i', 'icon' => 'lucide-apple', 'color' => 'text-zinc-900 dark:text-white'],
            'Android' => ['regex' => '/Android/i', 'icon' => 'lucide-smartphone', 'color' => 'text-emerald-500'],
            'Linux' => ['regex' => '/Linux/i', 'icon' => 'lucide-terminal', 'color' => 'text-orange-500'],
        ];

        foreach ($osData as $os => $data) {
            if (preg_match($data['regex'], $ua)) {
                return (object) [
                    'name' => $os,
                    'icon' => $data['icon'],
                    'color' => $data['color']
                ];
            }
        }

        return (object) [
            'name' => 'Unknown Device',
            'icon' => 'lucide-help-circle',
            'color' => 'text-zinc-400'
        ];
    }

    /**
     * Check if user is online (active within last 5 minutes).
     */
    public function isOnline()
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /**
     * Collections owned by the user.
     */
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Affiliate relationships
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function affiliateEarnings()
    {
        return $this->hasMany(AffiliateEarning::class);
    }

    public function affiliateClicks()
    {
        return $this->hasMany(AffiliateClick::class, 'affiliate_id');
    }

    public function affiliatePayouts()
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            // Check if it's an external URL (from Socialite)
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }

            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }

        // Return a UI Avatar if no avatar is set
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=06b6d4&background=ecfeff';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    public function wantsEmail(string $type): bool
    {
        $prefs = $this->emailPreferences ?: EmailPreference::forUser($this->id);
        return $prefs->{$type} ?? true;
    }
}
