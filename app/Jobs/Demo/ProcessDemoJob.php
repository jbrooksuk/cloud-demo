<?php

namespace App\Jobs\Demo;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class ProcessDemoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $jobId,
        public int $sleepSeconds = 3,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put("demo-job:{$this->jobId}", [
            'status' => 'processing',
            'started_at' => now()->toISOString(),
        ], 300);

        sleep($this->sleepSeconds);

        Cache::put("demo-job:{$this->jobId}", [
            'status' => 'completed',
            'started_at' => now()->toISOString(),
            'completed_at' => now()->toISOString(),
        ], 300);
    }
}
