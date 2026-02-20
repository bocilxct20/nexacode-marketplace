<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:check-expiring')->daily();

// Prune temporary Livewire uploads daily
Schedule::command('livewire:prune-temporary-uploads --older-than=24')->daily();

// Cleanup orphan chat attachments weekly
Schedule::command('chat:cleanup-storage')->weekly();
// Send scheduled chat messages every minute
Schedule::command('chat:send-scheduled')->everyMinute();

// Growth: Remind abandoned checkouts every 2 hours
Schedule::command('app:remind-abandoned-checkouts')->everyTwoHours();

// Growth: Send review reminders daily
Schedule::command('app:send-review-reminders')->dailyAt('10:00');

// Growth: Check anniversaries and milestones daily
Schedule::command('app:check-growth-milestones')->dailyAt('09:00');

// Growth: Weekly marketplace curation (Every Monday)
Schedule::command('app:curate-weekly-digest')->weeklyOn(1, '08:00');

// Earnings: Release pending earnings hourly
Schedule::command('earnings:release-pending')->hourly();
