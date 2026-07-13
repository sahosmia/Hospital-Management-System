<?php

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('tests that public homepage loads successfully and fetches system settings', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Providing World-Class Medical Care');
    $response->assertSee('St. Jude General Hospital');
});

it('tests that public doctors list loads successfully and allows searching', function () {
    $response = $this->get('/doctors');
    $response->assertStatus(200);
    $response->assertSee('Dr. Elizabeth Blackwell');

    // Search doctor
    $searchResponse = $this->get('/doctors?search=Elizabeth');
    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('Dr. Elizabeth Blackwell');
});

it('tests that public about page loads successfully', function () {
    $response = $this->get('/about');
    $response->assertStatus(200);
    $response->assertSee('About Our Hospital');
});

it('tests that public contact page loads successfully and receives contact messages', function () {
    $response = $this->get('/contact');
    $response->assertStatus(200);

    // Post contact message
    $postResponse = $this->post('/contact', [
        'name' => 'Albert Einstein',
        'email' => 'albert@example.com',
        'subject' => 'Research Query',
        'message' => 'I would like to inquire about your neurology EEG facilities.',
    ]);

    $postResponse->assertStatus(302); // Redirect back on success
    expect(\App\Models\ContactMessage::where('name', 'Albert Einstein')->exists())->toBeTrue();
});

it('tests that public services, departments, and faq pages load successfully', function () {
    $this->get('/services')->assertStatus(200)->assertSee('Coronary Bypass Surgery');
    $this->get('/departments')->assertStatus(200)->assertSee('Cardiology');
    $this->get('/faq')->assertStatus(200)->assertSee('How do I claim insurance for admissions?');
    $this->get('/news')->assertStatus(200)->assertSee('10 Tips to Keep Your Heart Healthy');
});
