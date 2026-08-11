<?php

it('dumps the resolved host and request headers', function () {
    $this->get('/host')
        ->assertOk()
        ->assertJsonStructure(['host', 'headers'])
        ->assertJsonPath('host', parse_url(config('app.url'), PHP_URL_HOST));
});
