<?php

use App\Models\Appointment;
use App\Models\Bed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('tests patient registration and login with email', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John von Neumann',
        'email' => 'john.neumann@hms.com',
        'phone' => '1112223334',
        'password' => 'supersecret',
        'gender' => 'male',
        'blood_group' => 'B+',
    ]);

    $response->assertStatus(201);
    expect(User::where('email', 'john.neumann@hms.com')->exists())->toBeTrue();

    // Now login
    $loginRes = $this->postJson('/api/auth/login', [
        'email' => 'john.neumann@hms.com',
        'password' => 'supersecret',
    ]);

    $loginRes->assertStatus(200);
    $loginRes->assertJsonPath('success', true);
    expect($loginRes->json('data.token'))->not->toBeNull();
});

it('tests doctor available slots and appointments booking', function () {
    $doctor = User::where('role', 'doctor')->first();
    $patient = User::where('role', 'patient')->first();

    $this->actingAs($patient);

    // Get slots
    $slotsRes = $this->getJson("/api/doctors/{$doctor->id}/slots?date=".date('Y-m-d'));
    $slotsRes->assertStatus(200);
    expect(count($slotsRes->json('data')))->toBeGreaterThan(0);

    // Book appointment
    $bookRes = $this->postJson('/api/appointments', [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => date('Y-m-d', strtotime('+1 day')),
        'appointment_time' => '09:00',
        'type' => 'physical',
        'symptoms' => 'Regular checkup',
    ]);

    $bookRes->assertStatus(201);
    expect(Appointment::where('patient_id', $patient->id)->count())->toBe(1);
});

it('tests patient admission, bed allocation, and bed transfer', function () {
    $admin = User::where('role', 'super_admin')->first();
    $patient = User::where('role', 'patient')->first();
    $doctor = User::where('role', 'doctor')->first();
    $bed = Bed::where('status', 'available')->first();

    $this->actingAs($admin);

    // Admit patient
    $admitRes = $this->postJson('/api/admissions', [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'bed_id' => $bed->id,
        'admit_date' => date('Y-m-d'),
        'admit_time' => '10:00:00',
        'admit_type' => 'planned',
    ]);

    $admitRes->assertStatus(201);

    $bed->refresh();
    expect($bed->status)->toBe('occupied');
    expect($bed->current_patient_id)->toBe($patient->id);

    $admissionId = $admitRes->json('data.id');

    // Transfer patient to another available bed
    $newBed = Bed::where('status', 'available')->where('id', '!=', $bed->id)->first();
    $transferRes = $this->putJson("/api/admissions/{$admissionId}/transfer", [
        'bed_id' => $newBed->id,
    ]);

    $transferRes->assertStatus(200);

    $bed->refresh();
    $newBed->refresh();

    expect($bed->status)->toBe('available');
    expect($newBed->status)->toBe('occupied');
});

it('tests generating bills and processing payments', function () {
    $admin = User::where('role', 'super_admin')->first();
    $patient = User::where('role', 'patient')->first();
    $doctor = User::where('role', 'doctor')->first();
    $bed = Bed::where('status', 'available')->first();

    $this->actingAs($admin);

    // Admit
    $admitRes = $this->postJson('/api/admissions', [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'bed_id' => $bed->id,
        'admit_date' => date('Y-m-d'),
        'admit_time' => '10:00:00',
    ]);

    $admissionId = $admitRes->json('data.id');

    // Discharge
    $dischargeRes = $this->putJson("/api/admissions/{$admissionId}/discharge", [
        'discharge_date' => date('Y-m-d'),
        'discharge_time' => '11:00:00',
    ]);

    $dischargeRes->assertStatus(200);

    // Generate Bill
    $billRes = $this->postJson("/api/bills/generate/{$admissionId}");
    $billRes->assertStatus(200);
    $totalDue = $billRes->json('data.summary.total_due');
    expect($totalDue)->toBeGreaterThan(0);

    // Pay bill
    $payRes = $this->postJson('/api/bills/payment', [
        'admission_id' => $admissionId,
        'amount' => $totalDue,
        'payment_method' => 'card',
        'description' => 'Final checkout billing',
    ]);

    $payRes->assertStatus(201);
});
