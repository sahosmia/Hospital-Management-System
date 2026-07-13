<?php

namespace App\Http\Controllers;

use App\Models\MedicationAdministration;
use App\Services\MedicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MedicationController extends Controller
{
    public function __construct(protected MedicationService $medicationService) {}

    /**
     * Prescribe medication.
     */
    public function order(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admission_id' => 'required|exists:admissions,id',
            'medicine_name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'medicine_category' => 'nullable|string|max:100',
            'medicine_form' => 'nullable|in:tablet,capsule,syrup,injection,drip,ointment,inhaler',
            'dosage' => 'required|string|max:50',
            'frequency' => 'required|string|max:100',
            'route' => 'nullable|in:oral,iv,im,sc,topical,inhalation,rectal',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_days' => 'nullable|integer',
            'scheduled_times' => 'nullable|array',
            'is_emergency' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['doctor_id'] = Auth::id();
            $data['created_by'] = Auth::id();

            $medicationOrder = $this->medicationService->order($data);
            $this->logAudit('CREATE_MEDICATION_ORDER', 'medication_orders', $medicationOrder->id, null, $medicationOrder->toArray());

            return $this->jsonSuccess('Medication order created successfully.', $medicationOrder, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to prescribe medication: '.$e->getMessage(), 500);
        }
    }

    /**
     * Administer a medication.
     */
    public function administer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medication_order_id' => 'required|exists:medication_orders,id',
            'admission_id' => 'required|exists:admissions,id',
            'scheduled_time' => 'required',
            'dosage_given' => 'required|string|max:50',
            'status' => 'nullable|in:given,missed,refused,held,delayed',
            'reason_for_missed' => 'nullable|string',
            'patient_response' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'vitals_before' => 'nullable|array',
            'vitals_after' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['administered_by'] = Auth::id();

            $adminRecord = $this->medicationService->administer($data);
            $this->logAudit('ADMINISTER_MEDICATION', 'medication_administrations', $adminRecord->id, null, $adminRecord->toArray());

            return $this->jsonSuccess('Medication administration recorded successfully.', $adminRecord, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to administer medication: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get active medications scheduled for today.
     */
    public function today(): JsonResponse
    {
        try {
            $list = $this->medicationService->getTodayMedications();

            return $this->jsonSuccess('Today\'s medication schedule retrieved successfully.', $list);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch today\'s schedule: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get medication history for an admission.
     */
    public function history(int $id): JsonResponse
    {
        try {
            $history = MedicationAdministration::where('admission_id', $id)
                ->with(['medicationOrder', 'administrator'])
                ->latest()
                ->get();

            return $this->jsonSuccess('Medication administration history retrieved successfully.', $history);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch medication history: '.$e->getMessage(), 500);
        }
    }
}
