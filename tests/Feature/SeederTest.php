<?php

use App\Models\Bed;
use App\Models\Doctor;
use App\Models\OperationTheater;
use App\Models\SurgicalSupply;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('verifies that the database has been successfully seeded with core data', function () {
    // Assert user counts
    expect(User::count())->toBeGreaterThanOrEqual(10);
    expect(UserProfile::count())->toBeGreaterThanOrEqual(10);
    expect(Doctor::count())->toBe(3);

    // Assert beds and theaters
    expect(Bed::count())->toBe(8);
    expect(OperationTheater::count())->toBe(3);
    expect(SurgicalSupply::count())->toBe(8);
    expect(SystemSetting::count())->toBe(5);

    // Verify relations
    $doctor = Doctor::first();
    expect($doctor->user)->toBeInstanceOf(User::class);
    expect($doctor->user->role)->toBe('doctor');

    $patient = User::where('role', 'patient')->first();
    expect($patient->profile)->toBeInstanceOf(UserProfile::class);
});
