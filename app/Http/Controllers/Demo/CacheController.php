<?php

namespace App\Http\Controllers\Demo;

use App\Events\DemoCacheUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CacheController extends Controller
{
    private const DEMO_KEYS = ['demo:counter', 'demo:message', 'demo:timestamp'];

    public function index(): Response
    {
        return Inertia::render('demo/Cache', [
            'entries' => $this->getEntries(),
            'cacheDriver' => config('cache.default'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'key' => ['required', 'string', 'max:100'],
            'value' => ['required', 'string', 'max:1000'],
            'ttl' => ['required', 'integer', 'min:1', 'max:3600'],
        ]);

        Cache::put($request->input('key'), $request->input('value'), $request->integer('ttl'));

        $entries = $this->getEntries();
        DemoCacheUpdated::dispatch(entries: $entries);

        return response()->json(['entries' => $entries]);
    }

    public function flush(): JsonResponse
    {
        Cache::forget('demo:counter');
        Cache::forget('demo:message');
        Cache::forget('demo:timestamp');

        $entries = $this->getEntries();
        DemoCacheUpdated::dispatch(entries: $entries);

        return response()->json(['entries' => $entries]);
    }

    public function increment(): JsonResponse
    {
        Cache::increment('demo:counter');

        $entries = $this->getEntries();
        DemoCacheUpdated::dispatch(entries: $entries);

        return response()->json(['entries' => $entries]);
    }

    /**
     * @return array<int, array{key: string, value: string|null, exists: bool}>
     */
    private function getEntries(): array
    {
        return collect(self::DEMO_KEYS)->map(fn (string $key) => [
            'key' => $key,
            'value' => Cache::get($key),
            'exists' => Cache::has($key),
        ])->all();
    }
}
