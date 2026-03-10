<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('object storage page can be rendered', function () {
    Storage::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('demo.object-storage'));

    $response->assertSuccessful();
});

test('file can be uploaded', function () {
    Storage::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('demo.object-storage.upload'), [
        'file' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertRedirect();
    expect(Storage::files('demo'))->toHaveCount(1);
});

test('file can be deleted', function () {
    Storage::fake();
    Storage::put('demo/old-file.txt', 'content');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('demo.object-storage.destroy'), [
        'path' => 'demo/old-file.txt',
    ]);

    $response->assertRedirect();
    Storage::assertMissing('demo/old-file.txt');
});

test('object storage page requires authentication', function () {
    $response = $this->get(route('demo.object-storage'));

    $response->assertRedirect(route('login'));
});
