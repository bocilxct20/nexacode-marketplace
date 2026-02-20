<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\File;

class ChatStorageCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orphan chat attachments that are not referenced in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Chat Storage Cleanup...');

        $directory = 'chat-attachments';
        
        if (!Storage::disk('public')->exists($directory)) {
            $this->warn('Directory chat-attachments does not exist in public storage.');
            return;
        }

        $files = Storage::disk('public')->files($directory);
        $totalFiles = count($files);
        $deletedCount = 0;

        $this->info("Scanning {$totalFiles} files in storage...");

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $file) {
            // Check if this file path exists in ChatMessage table
            $exists = ChatMessage::where('image_path', $file)->exists();

            if (!$exists) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($deletedCount > 0) {
            $this->info("Cleanup complete! Deleted {$deletedCount} orphan files.");
        } else {
            $this->info("Cleanup complete! No orphan files found.");
        }
    }
}
