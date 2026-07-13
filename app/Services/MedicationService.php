<?php

namespace App\Services;

use App\Models\MedicationAdministration;
use App\Models\MedicationOrder;
use Carbon\Carbon;

class MedicationService
{
    /**
     * Prescribe / Create a new medication order.
     */
    public function order(array $data): MedicationOrder
    {
        return MedicationOrder::create([
            'admission_id' => $data['admission_id'],
            'doctor_id' => $data['doctor_id'],
            'treatment_plan_id' => $data['treatment_plan_id'] ?? null,
            'medicine_name' => $data['medicine_name'],
            'generic_name' => $data['generic_name'] ?? null,
            'medicine_category' => $data['medicine_category'] ?? null,
            'medicine_form' => $data['medicine_form'] ?? 'tablet',
            'dosage' => $data['dosage'],
            'frequency' => $data['frequency'],
            'route' => $data['route'] ?? 'oral',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'duration_days' => $data['duration_days'] ?? null,
            'scheduled_times' => $data['scheduled_times'] ?? null,
            'status' => 'active',
            'is_emergency' => $data['is_emergency'] ?? false,
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    /**
     * Record a medication administration.
     */
    public function administer(array $data): MedicationAdministration
    {
        return MedicationAdministration::create([
            'medication_order_id' => $data['medication_order_id'],
            'admission_id' => $data['admission_id'],
            'administered_by' => $data['administered_by'],
            'administered_date' => $data['administered_date'] ?? Carbon::today()->toDateString(),
            'administered_time' => $data['administered_time'] ?? Carbon::now()->toTimeString(),
            'scheduled_time' => $data['scheduled_time'],
            'dosage_given' => $data['dosage_given'],
            'status' => $data['status'] ?? 'given',
            'reason_for_missed' => $data['reason_for_missed'] ?? null,
            'patient_response' => $data['patient_response'] ?? null,
            'side_effects' => $data['side_effects'] ?? null,
            'vitals_before' => $data['vitals_before'] ?? null,
            'vitals_after' => $data['vitals_after'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Get active medications scheduled for today.
     */
    public function getTodayMedications(): array
    {
        $todayStr = Carbon::today()->toDateString();

        $orders = MedicationOrder::where('status', 'active')
            ->where('start_date', '<=', $todayStr)
            ->where(function ($query) use ($todayStr) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $todayStr);
            })
            ->with(['admission.patient', 'doctor'])
            ->get();

        return $orders->toArray();
    }
}
