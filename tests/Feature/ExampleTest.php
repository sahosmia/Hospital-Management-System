<?php

test('the application returns public landing home', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('the application returns login view', function () {
    $response = $this->get('/auth/login');
    $response->assertStatus(200);
});
