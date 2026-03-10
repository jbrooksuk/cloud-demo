<?php

namespace App\Http\Controllers\Demo;

use App\Events\DemoJobDispatched;
use App\Http\Controllers\Controller;
use App\Jobs\Demo\ProcessDemoJob;
use App\Models\DemoJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{
    public function index(Request $request): Response
    {
        $jobs = DemoJob::query()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (DemoJob $job) => [
                'id' => $job->id,
                'status' => $job->status,
                'started_at' => $job->started_at?->toISOString(),
                'completed_at' => $job->completed_at?->toISOString(),
            ]);

        return Inertia::render('demo/Queue', [
            'jobs' => $jobs,
            'queueDriver' => config('queue.default'),
        ]);
    }

    public function dispatch(Request $request): JsonResponse
    {
        $demoJob = DemoJob::query()->create([
            'user_id' => $request->user()->id,
        ]);

        ProcessDemoJob::dispatch($demoJob->id);

        DemoJobDispatched::dispatch(
            id: $demoJob->id,
            status: 'pending',
        );

        return response()->json(['status' => 'ok', 'id' => $demoJob->id]);
    }
}
