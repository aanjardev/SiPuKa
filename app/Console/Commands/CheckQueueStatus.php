<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class CheckQueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:check {--detailed : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Laravel queue status and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('  LARAVEL QUEUE STATUS CHECK');
        $this->info('========================================');
        $this->newLine();

        $this->checkQueueConfiguration();

        $this->checkDatabaseTables();

        $this->checkQueueJobs();

        $this->checkFailedJobs();

        $this->showRecommendations();

        return Command::SUCCESS;
    }

    private function checkQueueConfiguration()
    {
        $this->info('1. KONFIGURASI QUEUE');
        $this->line(str_repeat('-', 40));

        $connection = config('queue.default');
        $this->line("   Queue Connection: <fg=cyan>{$connection}</>");

        if ($connection === 'sync') {
            $this->error('   ❌ ERROR: Queue masih "sync" (synchronous)');
            $this->warn('   → Queue akan dijalankan langsung, tidak di background');
            $this->warn('   → Untuk background processing, ubah QUEUE_CONNECTION=database di .env');
            $this->warn('   → Kemudian jalankan: php artisan config:clear');
        } else {
            $this->info('   ✅ Queue connection: ' . $connection);
        }

        $this->newLine();
    }

    private function checkDatabaseTables()
    {
        $this->info('2. CEK TABEL DATABASE');
        $this->line(str_repeat('-', 40));

        try {

            $jobsExists = DB::getSchemaBuilder()->hasTable('jobs');
            if ($jobsExists) {
                $this->info('   ✅ Tabel "jobs": Ada');
            } else {
                $this->error('   ❌ Tabel "jobs": TIDAK ADA');
                $this->warn('   → Jalankan: php artisan queue:table');
                $this->warn('   → Kemudian: php artisan migrate');
            }

            $failedExists = DB::getSchemaBuilder()->hasTable('failed_jobs');
            if ($failedExists) {
                $this->info('   ✅ Tabel "failed_jobs": Ada');
            } else {
                $this->error('   ❌ Tabel "failed_jobs": TIDAK ADA');
                $this->warn('   → Jalankan: php artisan queue:failed-table');
                $this->warn('   → Kemudian: php artisan migrate');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            $this->warn('   → Pastikan database connection sudah benar');
        }

        $this->newLine();
    }

    private function checkQueueJobs()
    {
        $this->info('3. STATUS JOBS DI QUEUE');
        $this->line(str_repeat('-', 40));

        try {
            $pendingJobs = DB::table('jobs')->count();
            $reservedJobs = DB::table('jobs')->whereNotNull('reserved_at')->count();
            $availableJobs = $pendingJobs - $reservedJobs;

            $this->line("   Total Jobs Pending: <fg=cyan>{$pendingJobs}</>");
            $this->line("   Jobs Sedang Diproses: <fg=yellow>{$reservedJobs}</>");
            $this->line("   Jobs Tersedia: <fg=green>{$availableJobs}</>");

            if ($pendingJobs > 0) {
                if ($reservedJobs > 0) {
                    $this->info('   ✅ Ada job yang sedang diproses (queue worker berjalan)');
                } else {
                    $this->warn('   ⚠️  Ada job pending tapi tidak ada yang diproses');
                    $this->warn('   → Queue worker mungkin tidak berjalan');
                    $this->warn('   → Jalankan: php artisan queue:work');
                }

                if ($this->option('detailed') && $pendingJobs > 0) {
                    $this->newLine();
                    $this->line('   Detail Jobs (5 terakhir):');
                    $latestJobs = DB::table('jobs')
                        ->orderBy('id', 'desc')
                        ->limit(5)
                        ->get();

                    foreach ($latestJobs as $job) {
                        $payload = json_decode($job->payload, true);
                        $jobClass = $payload['displayName'] ?? 'Unknown';
                        $reserved = $job->reserved_at ? 'Sedang diproses' : 'Menunggu';
                        $this->line("   - ID: {$job->id} | Class: {$jobClass} | Status: {$reserved}");
                    }
                }
            } else {
                $this->info('   ✅ Tidak ada job pending (queue kosong)');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function checkFailedJobs()
    {
        $this->info('4. FAILED JOBS');
        $this->line(str_repeat('-', 40));

        try {
            $failedCount = DB::table('failed_jobs')->count();

            if ($failedCount > 0) {
                $this->error("   ❌ Ada <fg=red>{$failedCount}</> failed jobs");
                $this->warn('   → Cek detail: php artisan queue:failed');
                $this->warn('   → Retry failed jobs: php artisan queue:retry all');

                if ($this->option('detailed')) {
                    $this->newLine();
                    $this->line('   Failed Jobs (5 terakhir):');
                    $failedJobs = DB::table('failed_jobs')
                        ->orderBy('id', 'desc')
                        ->limit(5)
                        ->get();

                    foreach ($failedJobs as $job) {
                        $this->line("   - ID: {$job->id} | Failed at: {$job->failed_at}");
                    }
                }
            } else {
                $this->info('   ✅ Tidak ada failed jobs');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function showRecommendations()
    {
        $this->info('5. REKOMENDASI');
        $this->line(str_repeat('-', 40));

        $connection = config('queue.default');

        if ($connection === 'sync') {
            $this->warn('   📋 LANGKAH SETUP QUEUE:');
            $this->line('   1. Edit file .env');
            $this->line('   2. Ubah: QUEUE_CONNECTION=database');
            $this->line('   3. Jalankan: php artisan config:clear');
            $this->line('   4. Pastikan tabel jobs sudah ada (php artisan migrate)');
            $this->line('   5. Jalankan queue worker: php artisan queue:work');
        } else {
            $pendingJobs = DB::table('jobs')->count();
            $reservedJobs = DB::table('jobs')->whereNotNull('reserved_at')->count();

            if ($pendingJobs > 0 && $reservedJobs === 0) {
                $this->warn('   📋 QUEUE WORKER TIDAK BERJALAN:');
                $this->line('   → Jalankan: php artisan queue:work');
                $this->line('   → Atau untuk development: php artisan queue:listen');
                $this->line('   → Untuk production, setup supervisor atau systemd');
            } else {
                $this->info('   ✅ Queue system sudah dikonfigurasi dengan benar');
                $this->line('   → Untuk monitoring, jalankan: php artisan queue:check --detailed');
            }
        }

        $this->newLine();
        $this->info('========================================');
    }
}

