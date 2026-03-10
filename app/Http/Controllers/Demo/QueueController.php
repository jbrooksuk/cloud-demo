<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Jobs\Demo\ProcessDemoJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{
    public function index(): Response
    {
        $recentJobIds = Cache::get('demo-job-ids', []);

        $jobs = collect($recentJobIds)->map(fn (string $id) => [
            'id' => $id,
            ...Cache::get("demo-job:{$id}", ['status' => 'pending']),
        ]);

        return Inertia::render('demo/Queue', [
            'jobs' => $jobs,
            'queueDriver' => config('queue.default'),
        ]);
    }

    public function dispatch(): RedirectResponse
    {
        $jobId = Str::uuid()->toString();

        $recentJobIds = Cache::get('demo-job-ids', []);
        $recentJobIds[] = $jobId;
        $recentJobIds = array_slice($recentJobIds, -10);
        Cache::put('demo-job-ids', $recentJobIds, 300);

        Cache::put("demo-job:{$jobId}", ['status' => 'pending'], 300);

        ProcessDemoJob::dispatch($jobId);

        return back();
    }
}
