<?php

namespace Modules\AuthUserManagement\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Authenticate user with email and password.
     */
    public function loginWithEmail(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password_hash)) {
            return null;
        }

        if (! $user->is_active) {
            return null;
        }

        return $this->generateUserToken($user);
    }

    /**
     * Authenticate user via phone (OTP success).
     */
    public function loginWithPhone(string $phone): ?array
    {
        $user = User::where('phone', $phone)->first();

        if (! $user) {
            // Auto register patient if they don't exist
            $user = User::create([
                'name' => 'Patient '.substr($phone, -4),
                'phone' => $phone,
                'role' => 'patient',
                'patient_id' => 'PAT-'.rand(1000, 9999),
                'is_active' => true,
                'phone_verified_at' => now(),
            ]);

            UserProfile::create([
                'user_id' => $user->id,
            ]);
        }

        if (! $user->is_active) {
            return null;
        }

        return $this->generateUserToken($user);
    }

    /**
     * Register a new patient.
     */
    public function registerPatient(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password_hash' => isset($data['password']) ? Hash::make($data['password']) : null,
            'role' => 'patient',
            'patient_id' => 'PAT-'.rand(10000, 99999),
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
        ]);

        return $this->generateUserToken($user);
    }

    /**
     * Helper to generate token and update login metadata.
     */
    protected function generateUserToken(User $user): array
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        $token = $user->createToken('auth_token', ['*'], now()->addDays(7))->plainTextToken;

        return [
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }
}
