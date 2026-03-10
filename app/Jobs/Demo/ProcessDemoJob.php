<?php

namespace App\Jobs\Demo;

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

        sleep($this->sleepSeconds);

        $demoJob->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
