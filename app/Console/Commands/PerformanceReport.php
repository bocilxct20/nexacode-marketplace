<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate performance report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Performance Report ===');
        $this->newLine();

        // Cache Statistics
        $this->info('📊 Cache Statistics:');
        $this->line('  Driver: ' . config('cache.default'));
        $this->line('  Enabled: ' . (config('performance.cache.enabled') ? 'Yes' : 'No'));
        $this->line('  Default TTL: ' . config('performance.cache.default_ttl') . 's');
        $this->newLine();

        // Database Statistics
        $this->info('🗄️  Database Statistics:');
        try {
            $productCount = DB::table('products')->count();
            $orderCount = DB::table('orders')->count();
            $userCount = DB::table('users')->count();
            
            $this->line('  Products: ' . number_format($productCount));
            $this->line('  Orders: ' . number_format($orderCount));
            $this->line('  Users: ' . number_format($userCount));
        } catch (\Exception $e) {
            $this->error('  Could not fetch database stats');
        }
        $this->newLine();

        // Compression Statistics
        $this->info('🗜️  Compression:');
        $this->line('  Enabled: ' . (config('performance.compression.enabled') ? 'Yes' : 'No'));
        $this->line('  Level: ' . config('performance.compression.level'));
        $this->line('  Min Size: ' . config('performance.compression.min_size') . ' bytes');
        $this->newLine();

        // Image Optimization
        $this->info('🖼️  Image Optimization:');
        $this->line('  Lazy Load: ' . (config('performance.images.lazy_load') ? 'Yes' : 'No'));
        $this->line('  WebP Conversion: ' . (config('performance.images.webp_conversion') ? 'Yes' : 'No'));
        $this->line('  Quality: ' . config('performance.images.quality') . '%');
        $this->newLine();

        $this->info('Report generated successfully!');
        
        return Command::SUCCESS;
    }
}
