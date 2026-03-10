<?php

use App\Events\DemoMessageSent;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('websockets page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('demo.websockets'));

    $response->assertSuccessful();
});

test('message can be broadcast', function () {
    Event::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('demo.websockets.send'), [
        'message' => 'Hello from the demo!',
    ]);

    $response->assertRedirect();
    Event::assertDispatched(DemoMessageSent::class, fn ($event) => $event->message === 'Hello from the demo!'
        && $event->username === $user->name
    );
});

test('websockets page requires authentication', function () {
    $response = $this->get(route('demo.websockets'));

    $response->assertRedirect(route('login'));
});
