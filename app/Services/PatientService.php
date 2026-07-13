<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;

class PatientService
{
    /**
     * Register a new patient (online or offline).
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => 'patient',
            'patient_id' => $data['patient_id'] ?? 'PAT-'.rand(10000, 99999),
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'address' => $data['address'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'medical_history' => $data['medical_history'] ?? null,
        ]);

        return $user->load('profile');
    }

    /**
     * Search patients by name, phone, or patient_id.
     */
    public function search(string $query)
    {
        return User::where('role', 'patient')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('patient_id', 'like', "%{$query}%");
            })
            ->with('profile')
            ->get();
    }

    /**
     * Get complete medical and visit history of a patient.
     */
    public function getHistory(int $id): array
    {
        $patient = User::where('role', 'patient')->findOrFail($id);

        return [
            'patient' => $patient->load('profile'),
            'appointments' => $patient->appointmentsAsPatient()->with('doctor')->get(),
            'admissions' => $patient->admissionsAsPatient()->with(['bed', 'doctor'])->get(),
        ];
    }
}
