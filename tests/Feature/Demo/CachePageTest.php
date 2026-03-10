<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('cache page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('demo.cache'));

    $response->assertSuccessful();
});

test('cache value can be stored', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('demo.cache.store'), [
        'key' => 'demo:message',
        'value' => 'Hello, Cloud!',
        'ttl' => 60,
    ]);

    $response->assertRedirect();
    expect(Cache::get('demo:message'))->toBe('Hello, Cloud!');
});

test('cache counter can be incremented', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('demo.cache.increment'));
    $this->actingAs($user)->post(route('demo.cache.increment'));

    expect(Cache::get('demo:counter'))->toBe(2);
});

test('cache can be flushed', function () {
    $user = User::factory()->create();
    Cache::put('demo:counter', 5);
    Cache::put('demo:message', 'test');

    $this->actingAs($user)->delete(route('demo.cache.flush'));

    expect(Cache::has('demo:counter'))->toBeFalse();
    expect(Cache::has('demo:message'))->toBeFalse();
});

test('cache page requires authentication', function () {
    $response = $this->get(route('demo.cache'));

    $response->assertRedirect(route('login'));
});
