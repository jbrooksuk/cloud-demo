<?php

namespace App\Http\Controllers\Demo;

use App\Events\DemoMessageSent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebsocketsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('demo/Websockets');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        DemoMessageSent::dispatch(
            username: $request->user()->name,
            message: $request->input('message'),
            timestamp: now()->toISOString(),
        );

        return back();
    }
}
