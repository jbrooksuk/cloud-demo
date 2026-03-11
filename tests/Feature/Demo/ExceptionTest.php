<?php

use App\Models\User;

test('exception page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('demo.exception'));

    $response->assertSuccessful();
});

test('exception page requires authentication', function () {
    $response = $this->get(route('demo.exception'));

    $response->assertRedirect(route('login'));
});
