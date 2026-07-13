<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use App\Services\SurgeryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SurgeryController extends Controller
{
    public function __construct(protected SurgeryService $surgeryService) {}

    /**
     * List all scheduled / current surgeries.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Surgery::with(['patient', 'surgeon', 'ot', 'admission']);

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('date')) {
                $query->where('scheduled_date', $request->input('date'));
            }

            $surgeries = $query->latest()->get();

            return $this->jsonSuccess('Surgeries retrieved successfully.', $surgeries);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch surgeries: '.$e->getMessage(), 500);
        }
    }

    /**
     * Schedule a surgery.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admission_id' => 'required|exists:admissions,id',
            'patient_id' => 'required|exists:users,id',
            'surgeon_id' => 'required|exists:users,id',
            'assistant_surgeon_id' => 'nullable|exists:users,id',
            'anesthesiologist_id' => 'nullable|exists:users,id',
            'scrub_nurse_id' => 'nullable|exists:users,id',
            'ot_id' => 'required|exists:operation_theaters,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'surgery_type' => 'required|string|max:100',
            'surgery_name' => 'required|string|max:255',
            'urgency' => 'nullable|in:elective,urgent,emergency,semi_emergency',
            'priority' => 'nullable|in:routine,high,critical',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['created_by'] = Auth::id();

            $surgery = $this->surgeryService->schedule($data);
            $this->logAudit('SCHEDULE_SURGERY', 'surgeries', $surgery->id, null, $surgery->toArray());

            return $this->jsonSuccess('Surgery scheduled successfully.', $surgery, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to schedule surgery: '.$e->getMessage(), 500);
        }
    }

    /**
     * Start a surgery.
     */
    public function start(int $id): JsonResponse
    {
        try {
            $surgery = Surgery::findOrFail($id);
            $oldValue = $surgery->toArray();

            $startedSurgery = $this->surgeryService->start($id);
            $this->logAudit('START_SURGERY', 'surgeries', $id, $oldValue, $startedSurgery->toArray());

            return $this->jsonSuccess('Surgery started and OT marked as occupied.', $startedSurgery);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to start surgery: '.$e->getMessage(), 500);
        }
    }

    /**
     * Complete a surgery.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $surgery = Surgery::findOrFail($id);
            $oldValue = $surgery->toArray();
        } catch (\Exception $e) {
            return $this->jsonError('Surgery not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'patient_condition_after' => 'nullable|string',
            'outcome' => 'required|in:successful,partial,unsuccessful,complicated',
            'anesthesia_type' => 'nullable|in:general,local,regional,spinal,epidural,sedation',
            'surgical_notes' => 'nullable|string',
            'post_op_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $completedSurgery = $this->surgeryService->complete($id, $request->all());
            $this->logAudit('COMPLETE_SURGERY', 'surgeries', $id, $oldValue, $completedSurgery->toArray());

            return $this->jsonSuccess('Surgery marked completed. Base charges posted successfully.', $completedSurgery);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to complete surgery: '.$e->getMessage(), 500);
        }
    }

    /**
     * Cancel a surgery.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $surgery = Surgery::findOrFail($id);
            $oldValue = $surgery->toArray();
        } catch (\Exception $e) {
            return $this->jsonError('Surgery not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $cancelledSurgery = $this->surgeryService->cancel($id, $request->reason);
            $this->logAudit('CANCEL_SURGERY', 'surgeries', $id, $oldValue, $cancelledSurgery->toArray());

            return $this->jsonSuccess('Surgery cancelled successfully.', $cancelledSurgery);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to cancel surgery: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get active surgeries scheduled for today.
     */
    public function today(): JsonResponse
    {
        try {
            $todayStr = date('Y-m-d');
            $surgeries = Surgery::where('scheduled_date', $todayStr)
                ->with(['patient', 'surgeon', 'ot'])
                ->get();

            return $this->jsonSuccess('Today\'s surgeries retrieved successfully.', $surgeries);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch today\'s surgeries: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get surgery cost breakdown.
     */
    public function costReport(): JsonResponse
    {
        try {
            $completedSurgeries = Surgery::where('status', 'completed')
                ->with(['patient', 'surgeon', 'ot', 'admission'])
                ->get()
                ->map(function ($surgery) {
                    $surgeonFee = $surgery->surgeon?->doctor?->surgery_fee ?? 1000.00;
                    $duration = $surgery->duration_minutes ?? 60;
                    $otBase = $surgery->ot?->base_charge ?? 200.00;
                    $otHourly = $surgery->ot?->per_hour_charge ?? 100.00;
                    $otCost = $otBase + ($otHourly * ceil($duration / 60));

                    return [
                        'surgery_number' => $surgery->surgery_number,
                        'surgery_name' => $surgery->surgery_name,
                        'patient_name' => $surgery->patient?->name,
                        'duration_minutes' => $duration,
                        'surgeon_fee' => $surgeonFee,
                        'ot_charge' => $otCost,
                        'total_cost' => $surgeonFee + $otCost,
                    ];
                });

            return $this->jsonSuccess('Surgery cost reports retrieved successfully.', $completedSurgeries);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to load surgery cost reports: '.$e->getMessage(), 500);
        }
    }
}
