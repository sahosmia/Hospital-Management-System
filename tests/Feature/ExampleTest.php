<?php

test('the application redirects from root to login', function () {
    $response = $this->get('/');
    $response->assertStatus(302);
});

test('the application returns login view', function () {
    $response = $this->get('/auth/login');
    $response->assertStatus(200);
});
