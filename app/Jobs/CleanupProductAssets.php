<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupProductAssets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum attempts to cleanup files.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Maximum job runtime before timing out (seconds).
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param array<int, string|null> $paths
     * @param string $disk
     */
    public function __construct(
        public array $paths,
        public string $disk = 'r2',
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->paths as $path) {
            if (empty($path)) {
                continue;
            }

            try {
                $storage = Storage::disk($this->disk);
                if (preg_match('#^(.*)/(thumb|medium|large)/([^/]+)$#', $path, $m)) {
                    $base = $m[1];
                    $file = $m[3];
                    foreach (['thumb', 'medium', 'large'] as $size) {
                        $candidate = "{$base}/{$size}/{$file}";
                        if ($storage->exists($candidate)) {
                            $storage->delete($candidate);
                        }
                    }
                } elseif ($storage->exists($path)) {
                    $storage->delete($path);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to delete product asset from storage', [
                    'path' => $path,
                    'disk' => $this->disk,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
