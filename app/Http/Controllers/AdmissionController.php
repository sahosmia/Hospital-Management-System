<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Services\AdmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdmissionController extends Controller
{
    public function __construct(protected AdmissionService $admissionService) {}

    /**
     * List admissions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Admission::with(['patient', 'doctor', 'bed']);

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $admissions = $query->latest()->paginate(15);

            return $this->jsonSuccess('Admissions retrieved successfully.', $admissions);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch admissions: '.$e->getMessage(), 500);
        }
    }

    /**
     * Admit a new patient.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'bed_id' => 'required|exists:beds,id',
            'admit_date' => 'required|date',
            'admit_time' => 'required',
            'admit_type' => 'nullable|in:emergency,planned,referred',
            'patient_condition' => 'nullable|in:critical,serious,stable,good',
            'primary_diagnosis' => 'nullable|string',
            'payment_type' => 'nullable|in:cash,insurance,corporate,government,free',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['created_by'] = Auth::id();

            $admission = $this->admissionService->admit($data);
            $this->logAudit('PATIENT_ADMISSION', 'admissions', $admission->id, null, $admission->toArray());

            return $this->jsonSuccess('Patient admitted successfully.', $admission, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to admit patient: '.$e->getMessage(), 500);
        }
    }

    /**
     * Discharge a patient.
     */
    public function discharge(Request $request, int $id): JsonResponse
    {
        try {
            $admission = Admission::findOrFail($id);
        } catch (\Exception $e) {
            return $this->jsonError('Admission not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'discharge_date' => 'required|date',
            'discharge_time' => 'required',
            'discharge_summary' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $oldValue = $admission->toArray();

            $data = $request->all();
            $data['updated_by'] = Auth::id();

            $dischargedAdmission = $this->admissionService->discharge($id, $data);
            $this->logAudit('PATIENT_DISCHARGE', 'admissions', $id, $oldValue, $dischargedAdmission->toArray());

            return $this->jsonSuccess('Patient discharged and bed released successfully.', $dischargedAdmission);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to discharge patient: '.$e->getMessage(), 500);
        }
    }

    /**
     * Transfer patient to another bed.
     */
    public function transfer(Request $request, int $id): JsonResponse
    {
        try {
            $admission = Admission::findOrFail($id);
        } catch (\Exception $e) {
            return $this->jsonError('Admission not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'bed_id' => 'required|exists:beds,id',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $oldValue = $admission->toArray();

            $transferredAdmission = $this->admissionService->transfer(
                $id,
                $request->input('bed_id'),
                Auth::id()
            );

            $this->logAudit('BED_TRANSFER', 'admissions', $id, $oldValue, $transferredAdmission->toArray());

            return $this->jsonSuccess('Patient transferred to the new bed successfully.', $transferredAdmission);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to transfer patient: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get active admissions.
     */
    public function active(): JsonResponse
    {
        try {
            $admissions = Admission::where('status', 'active')
                ->with(['patient', 'doctor', 'bed'])
                ->latest()
                ->get();

            return $this->jsonSuccess('Active admissions retrieved successfully.', $admissions);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch active admissions: '.$e->getMessage(), 500);
        }
    }

    /**
     * Post bed charge for active admissions (automatic posting).
     */
    public function postBedCharges(): JsonResponse
    {
        try {
            $count = $this->admissionService->postDailyBedCharges();
            $this->logAudit('POST_DAILY_BED_CHARGES', 'financial_transactions');

            return $this->jsonSuccess("Successfully posted bed charges for {$count} active admissions.");
        } catch (\Exception $e) {
            return $this->jsonError('Failed to post bed charges: '.$e->getMessage(), 500);
        }
    }
}
