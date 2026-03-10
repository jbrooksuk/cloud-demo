<?php

namespace App\Http\Controllers\Demo;

use App\Events\DemoMessageSent;
use App\Http\Controllers\Controller;
use App\Models\DemoMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebsocketsController extends Controller
{
    public function index(): Response
    {
        $messages = DemoMessage::query()
            ->latest()
            ->limit(50)
            ->get(['username', 'message', 'created_at'])
            ->reverse()
            ->values()
            ->map(fn (DemoMessage $message) => [
                'username' => $message->username,
                'message' => $message->message,
                'timestamp' => $message->created_at->toISOString(),
            ]);

        return Inertia::render('demo/Websockets', [
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $demoMessage = DemoMessage::query()->create([
            'user_id' => $request->user()->id,
            'username' => $request->user()->name,
            'message' => $request->input('message'),
        ]);

        try {
            DemoMessageSent::dispatch(
                username: $demoMessage->username,
                message: $demoMessage->message,
                timestamp: $demoMessage->created_at->toISOString(),
            );
        } catch (\Throwable) {
            // Broadcasting may not be available; the message was still saved.
        }

        return response()->json([
            'status' => 'ok',
            'username' => $demoMessage->username,
            'message' => $demoMessage->message,
            'timestamp' => $demoMessage->created_at->toISOString(),
        ]);
    }
}
