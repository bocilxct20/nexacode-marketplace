<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReleasePendingEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'earnings:release-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release pending earnings that have passed the hold period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $released = \App\Models\Earning::where('status', \App\Models\Earning::STATUS_PENDING)
            ->where('available_at', '<=', now())
            ->get();

        foreach ($released as $earning) {
            $earning->markAsAvailable();
        }

        $this->info("Released {$released->count()} pending earnings.");
        
        return 0;
    }
}
