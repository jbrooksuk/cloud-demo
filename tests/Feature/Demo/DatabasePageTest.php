<?php

use App\Models\User;

test('database page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('demo.database'));

    $response->assertSuccessful();
});

test('database page shows user count', function () {
    $user = User::factory()->create();
    User::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('demo.database'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('demo/Database')
        ->where('userCount', 4)
    );
});

test('database page requires authentication', function () {
    $response = $this->get(route('demo.database'));

    $response->assertRedirect(route('login'));
});
