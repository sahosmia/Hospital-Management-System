<?php

namespace Modules\AuthUserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Modules\AuthUserManagement\Services\AuthService;
use Modules\AuthUserManagement\Services\OTPService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected OTPService $otpService
    ) {}

    /**
     * Register a new patient.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:100',
            'phone' => 'required|string|unique:users,phone|max:15',
            'password' => 'required|string|min:6',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $result = $this->authService->registerPatient($request->all());
            $this->logAudit('REGISTER', 'users', $result['user']->id, null, ['email' => $result['user']->email]);

            return $this->jsonSuccess('Patient registered successfully.', $result, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Registration failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Login via Email & Password.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $result = $this->authService->loginWithEmail($request->email, $request->password);
            if (! $result) {
                return $this->jsonError('Invalid email or password.', 401);
            }
            $this->logAudit('LOGIN_EMAIL', 'users', $result['user']->id);

            return $this->jsonSuccess('Logged in successfully.', $result);
        } catch (\Exception $e) {
            return $this->jsonError('Login failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Request OTP via SMS.
     */
    public function otpRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $otp = $this->otpService->generate($request->phone);

            return $this->jsonSuccess('OTP sent successfully (Simulated).', ['phone' => $request->phone, 'otp' => $otp]);
        } catch (\Exception $e) {
            return $this->jsonError('OTP request failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Verify OTP and Login.
     */
    public function otpVerify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $verified = $this->otpService->verify($request->phone, $request->otp);
            if (! $verified) {
                return $this->jsonError('Invalid or expired OTP.', 400);
            }

            $result = $this->authService->loginWithPhone($request->phone);
            if (! $result) {
                return $this->jsonError('User account is inactive.', 401);
            }

            $this->logAudit('LOGIN_OTP', 'users', $result['user']->id);

            return $this->jsonSuccess('OTP verified and logged in successfully.', $result);
        } catch (\Exception $e) {
            return $this->jsonError('OTP verification failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Mock Google Login URL.
     */
    public function googleLogin(): JsonResponse
    {
        return $this->jsonSuccess('Redirecting to Google OAuth (Simulated).', [
            'url' => 'https://accounts.google.com/o/oauth2/v2/auth?client_id=mock_id&redirect_uri='.url('/api/auth/google/callback').'&response_type=code&scope=email%20profile',
        ]);
    }

    /**
     * Mock Google OAuth callback.
     */
    public function googleCallback(Request $request): JsonResponse
    {
        try {
            // Simulated user retrieved from Google OAuth
            $googleId = 'google_user_'.rand(10000, 99999);
            $email = 'google_user_'.rand(1000, 9999).'@example.com';
            $name = 'Google User '.rand(1, 100);

            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'role' => 'patient',
                    'patient_id' => 'PAT-'.rand(10000, 99999),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                UserProfile::create([
                    'user_id' => $user->id,
                ]);
            }

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $this->logAudit('LOGIN_GOOGLE', 'users', $user->id);

            return $this->jsonSuccess('Logged in via Google successfully.', [
                'user' => $user->load('profile'),
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('Google callback failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get authenticated profile.
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return $this->jsonError('Unauthenticated.', 401);
        }

        return $this->jsonSuccess('Profile retrieved successfully.', $user->load('profile'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return $this->jsonError('Unauthenticated.', 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|unique:users,phone,'.$user->id.'|max:15',
            'password' => 'nullable|string|min:6',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $oldValue = $user->toArray();

            $userUpdateData = $request->only(['name', 'email', 'phone']);
            if ($request->has('password') && $request->password !== null) {
                $userUpdateData['password_hash'] = Hash::make($request->password);
            }
            $user->update($userUpdateData);

            $profileData = $request->only([
                'date_of_birth', 'gender', 'blood_group', 'address',
                'emergency_contact_name', 'emergency_contact_phone',
            ]);

            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                UserProfile::create(array_merge(['user_id' => $user->id], $profileData));
            }

            $user->refresh();

            $this->logAudit('UPDATE_PROFILE', 'users', $user->id, $oldValue, $user->toArray());

            return $this->jsonSuccess('Profile updated successfully.', $user->load('profile'));
        } catch (\Exception $e) {
            return $this->jsonError('Profile update failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Logout.
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->currentAccessToken()->delete();
                $this->logAudit('LOGOUT', 'users', $user->id);
            }

            return $this->jsonSuccess('Logged out successfully.');
        } catch (\Exception $e) {
            return $this->jsonError('Logout failed: '.$e->getMessage(), 500);
        }
    }
}
