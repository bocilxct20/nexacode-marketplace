<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Password strength rules (global defaults) ──────────────────────
        // Aturan: min 8 karakter, harus ada huruf besar+kecil, harus ada angka
        // Berlaku di Register dan ResetPassword (semua yang pakai Password::defaults())
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()   // minimal 1 huruf besar + 1 huruf kecil
                ->numbers();    // minimal 1 angka
        });

        // Fix Vite Preload Warnings & Improve Livewire Navigate performance
        \Illuminate\Support\Facades\Vite::useStyleTagAttributes([
            'data-navigate-track' => 'reload',
        ]);
        \Illuminate\Support\Facades\Vite::useScriptTagAttributes([
            'data-navigate-track' => 'reload',
        ]);

        \Illuminate\Support\Facades\RateLimiter::for('chat-uploads', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(auth()->id() ?: $request->ip());
        });

        // Share basic settings globally
        if (!app()->runningInConsole() && \Illuminate\Support\Facades\Schema::hasTable('platform_settings')) {
            $settings = \App\Models\PlatformSetting::all()->pluck('value', 'key')->all();
            view()->share('platformSettings', $settings);
            
            // Sync config if keys exist
            if (isset($settings['site_name'])) {
                config(['app.name' => $settings['site_name']]);
                config(['mail.from.name' => $settings['site_name']]);
            }
            if (isset($settings['support_email'])) {
                config(['mail.from.address' => $settings['support_email']]);
            }

            // Share categories globally for navigation
            if (\Illuminate\Support\Facades\Schema::hasTable('product_tags')) {
                view()->share('categories', \App\Models\ProductTag::all());
            }
        }
        // Register Email Logging
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Mail\Events\MessageSent::class,
            \App\Listeners\LogSentMessage::class
        );

        // Register Observers
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }
}
