<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LongRunningProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Set time limit yang lebih tinggi untuk queue job
        set_time_limit(300); // 5 menit
        
        try {

            Log::info('Starting long running process', $this->data);

            sleep(10);
            
            Log::info('Long running process completed');
            
        } catch (\Exception $e) {
            Log::error('Long running process failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get job ID for tracking
     */
    public function getJobId(): string
    {
        return $this->job->getJobId();
    }
}
