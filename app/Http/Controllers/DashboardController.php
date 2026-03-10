<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'databaseDriver' => config('database.default'),
            'userCount' => User::query()->count(),
            'cacheDriver' => config('cache.default'),
            'queueDriver' => config('queue.default'),
            'storageDriver' => config('filesystems.default'),
            'broadcastDriver' => config('broadcasting.default'),
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
        ]);
    }
}
