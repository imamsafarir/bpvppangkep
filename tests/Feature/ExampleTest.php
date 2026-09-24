<?php

test('the application returns a successful response', function () {
    $this->artisan('migrate');
    $response = $this->get('/');

    $response->assertStatus(200);
});
