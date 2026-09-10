<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ResetR2Bucket extends Command
{
    protected $signature = 'r2:reset {folder=product-images}';
    protected $description = 'Delete all files inside R2 bucket folder';

    public function handle()
    {
        $folder = $this->argument('folder');

        $disk = Storage::disk('r2');

        $files = $disk->allFiles($folder);

        foreach ($files as $file) {
            $disk->delete($file);
        }

        $this->info("All files in folder '{$folder}' deleted!");
    }
}
