<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CacheController extends Controller
{
    public function index(): Response
    {
        $demoKeys = ['demo:counter', 'demo:message', 'demo:timestamp'];

        $entries = collect($demoKeys)->map(fn (string $key) => [
            'key' => $key,
            'value' => Cache::get($key),
            'exists' => Cache::has($key),
        ]);

        return Inertia::render('demo/Cache', [
            'entries' => $entries,
            'cacheDriver' => config('cache.default'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'key' => ['required', 'string', 'max:100'],
            'value' => ['required', 'string', 'max:1000'],
            'ttl' => ['required', 'integer', 'min:1', 'max:3600'],
        ]);

        Cache::put($request->input('key'), $request->input('value'), $request->integer('ttl'));

        return back();
    }

    public function flush(): RedirectResponse
    {
        Cache::forget('demo:counter');
        Cache::forget('demo:message');
        Cache::forget('demo:timestamp');

        return back();
    }

    public function increment(): RedirectResponse
    {
        Cache::increment('demo:counter');

        return back();
    }
}
