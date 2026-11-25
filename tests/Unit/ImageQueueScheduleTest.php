<?php

namespace Tests\Unit;

use App\Console\Kernel;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\EventMutex;
use Illuminate\Console\Scheduling\Schedule;
use PHPUnit\Framework\TestCase;

class ImageQueueScheduleTest extends TestCase
{
    public function test_image_worker_finishes_before_queue_retry_and_scheduler_lock_expiry(): void
    {
        $queue = require __DIR__.'/../../config/queue.php';
        foreach (['database', 'redis', 'beanstalkd'] as $connection) {
            $this->assertGreaterThan(300, $queue['connections'][$connection]['retry_after']);
        }

        $command = 'queue:work --stop-when-empty --tries=3 --timeout=300 --max-time=50';
        $event = new Event($this->createMock(EventMutex::class), $command);
        $prune = new Event($this->createMock(EventMutex::class), 'images:prune-cache');
        $schedule = $this->createMock(Schedule::class);
        $schedule->method('command')->willReturnCallback(fn ($name) => $name === $command ? $event : $prune);

        $reflection = new \ReflectionClass(Kernel::class);
        $kernel = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('schedule');
        $method->setAccessible(true);
        $method->invoke($kernel, $schedule);

        $this->assertSame('* * * * *', $event->expression);
        $this->assertTrue($event->runInBackground);
        $this->assertTrue($event->withoutOverlapping);
        $this->assertGreaterThan(350, $event->expiresAt * 60);
    }
}
