<?php

namespace App\Jobs\Demo;

use App\Events\DemoJobUpdated;
use App\Models\DemoJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessDemoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $demoJobId,
        public int $sleepSeconds = 3,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $demoJob = DemoJob::query()->find($this->demoJobId);

        if (! $demoJob) {
            return;
        }

        $demoJob->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        DemoJobUpdated::dispatch(
            id: $demoJob->id,
            status: 'processing',
            startedAt: $demoJob->started_at->toISOString(),
        );

        sleep($this->sleepSeconds);

        $demoJob->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        DemoJobUpdated::dispatch(
            id: $demoJob->id,
            status: 'completed',
            startedAt: $demoJob->started_at->toISOString(),
            completedAt: $demoJob->completed_at->toISOString(),
        );
    }
}
