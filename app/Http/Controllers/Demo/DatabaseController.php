<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DatabaseController extends Controller
{
    public function index(): Response
    {
        $userCount = User::query()->count();
        $latestUsers = User::query()->latest()->limit(5)->get(['id', 'name', 'email', 'created_at']);
        $databaseDriver = config('database.default');
        $tables = Schema::getTableListing(schemaQualified: false);

        return Inertia::render('demo/Database', [
            'userCount' => $userCount,
            'latestUsers' => $latestUsers,
            'databaseDriver' => $databaseDriver,
            'tables' => $tables,
        ]);
    }
}
