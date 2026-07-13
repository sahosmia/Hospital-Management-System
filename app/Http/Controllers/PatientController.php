<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function __construct(protected PatientService $patientService) {}

    /**
     * List patients.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $patients = User::where('role', 'patient')
                ->with('profile')
                ->latest()
                ->paginate(15);

            return $this->jsonSuccess('Patients retrieved successfully.', $patients);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch patients: '.$e->getMessage(), 500);
        }
    }

    /**
     * Store new patient (offline).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email|max:100',
            'phone' => 'nullable|string|unique:users,phone|max:15',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'medical_history' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $patient = $this->patientService->register($request->all());
            $this->logAudit('CREATE_PATIENT', 'users', $patient->id, null, $patient->toArray());

            return $this->jsonSuccess('Patient created successfully.', $patient, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to create patient: '.$e->getMessage(), 500);
        }
    }

    /**
     * Show a patient.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $patient = User::where('role', 'patient')->with('profile')->findOrFail($id);

            return $this->jsonSuccess('Patient retrieved successfully.', $patient);
        } catch (\Exception $e) {
            return $this->jsonError('Patient not found.', 404);
        }
    }

    /**
     * Update a patient.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $patient = User::where('role', 'patient')->findOrFail($id);
        } catch (\Exception $e) {
            return $this->jsonError('Patient not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:users,email,'.$patient->id.'|max:100',
            'phone' => 'nullable|string|unique:users,phone,'.$patient->id.'|max:15',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'medical_history' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $oldValue = $patient->toArray();
            $patient->update($request->only(['name', 'email', 'phone']));

            $profileData = $request->only([
                'date_of_birth', 'gender', 'blood_group', 'address',
                'emergency_contact_name', 'emergency_contact_phone',
            ]);

            if ($request->has('medical_history')) {
                $profileData['medical_history'] = $request->medical_history;
            }

            if ($patient->profile) {
                $patient->profile->update($profileData);
            } else {
                UserProfile::create(array_merge(['user_id' => $patient->id], $profileData));
            }

            $patient->refresh();

            $this->logAudit('UPDATE_PATIENT', 'users', $patient->id, $oldValue, $patient->toArray());

            return $this->jsonSuccess('Patient updated successfully.', $patient->load('profile'));
        } catch (\Exception $e) {
            return $this->jsonError('Failed to update patient: '.$e->getMessage(), 500);
        }
    }

    /**
     * Search patients.
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $results = $this->patientService->search($request->input('query'));

            return $this->jsonSuccess('Search results retrieved successfully.', $results);
        } catch (\Exception $e) {
            return $this->jsonError('Search failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Patient history.
     */
    public function history(int $id): JsonResponse
    {
        try {
            $history = $this->patientService->getHistory($id);

            return $this->jsonSuccess('Patient history retrieved successfully.', $history);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch patient history: '.$e->getMessage(), 404);
        }
    }
}
