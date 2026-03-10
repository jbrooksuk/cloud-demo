<?php

use App\Jobs\Demo\ProcessDemoJob;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('queue page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('demo.queue'));

    $response->assertSuccessful();
});

test('job can be dispatched', function () {
    Queue::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('demo.queue.dispatch'));

    $response->assertOk();
    Queue::assertPushed(ProcessDemoJob::class);
});

test('queue page requires authentication', function () {
    $response = $this->get(route('demo.queue'));

    $response->assertRedirect(route('login'));
});
