<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneImageCache extends Command
{
    protected $signature = 'images:prune-cache';

    protected $description = 'Hapus file cache gambar yang tidak diperbarui selama tujuh hari';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $removed = 0;
        foreach ($disk->directories('image-cache') as $directory) {
            if (! preg_match('#^image-cache/[a-f0-9]{64}$#', $directory)) {
                continue;
            }
            $lock = fopen($disk->path($directory.'/write.lock'), 'c');
            if (! $lock) {
                continue;
            }
            if (! flock($lock, LOCK_EX | LOCK_NB)) {
                fclose($lock);

                continue;
            }
            try {
                foreach ($disk->files($directory) as $file) {
                    if (basename($file) !== 'write.lock' && $disk->lastModified($file) < time() - 7 * 86400) {
                        $disk->delete($file);
                        $removed++;
                    }
                }
            } finally {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
        $this->info("{$removed} file cache gambar dihapus.");

        return self::SUCCESS;
    }
}
