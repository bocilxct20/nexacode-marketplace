<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WarmCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm critical application caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Warming caches...');

        $cacheService = app(\App\Services\CacheService::class);

        try {
            $cacheService->warmCache();
            
            $this->info('✓ Products cache warmed');
            $this->info('✓ Categories cache warmed');
            $this->info('✓ Trending products cache warmed');
            
            $this->newLine();
            $this->info('Cache warming completed successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Cache warming failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
