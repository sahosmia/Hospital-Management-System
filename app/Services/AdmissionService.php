<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\FinancialTransaction;
use App\Models\User;
use Carbon\Carbon;

class AdmissionService
{
    /**
     * Admit a patient and allocate a bed.
     */
    public function admit(array $data): Admission
    {
        $bed = Bed::findOrFail($data['bed_id']);
        $bed->update([
            'status' => 'occupied',
            'current_patient_id' => $data['patient_id'],
        ]);

        $admissionNum = 'ADM-'.Carbon::parse($data['admit_date'])->format('Ymd').'-'.rand(1000, 9999);

        return Admission::create([
            'admission_number' => $admissionNum,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'bed_id' => $data['bed_id'],
            'admit_date' => $data['admit_date'],
            'admit_time' => $data['admit_time'],
            'admit_type' => $data['admit_type'] ?? 'planned',
            'patient_condition' => $data['patient_condition'] ?? 'stable',
            'primary_diagnosis' => $data['primary_diagnosis'] ?? null,
            'payment_type' => $data['payment_type'] ?? 'cash',
            'status' => 'active',
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    /**
     * Discharge a patient and release their bed.
     */
    public function discharge(int $id, array $data): Admission
    {
        $admission = Admission::findOrFail($id);
        $admission->update([
            'status' => 'discharged',
            'discharge_date' => $data['discharge_date'],
            'discharge_time' => $data['discharge_time'],
            'discharge_summary' => $data['discharge_summary'] ?? null,
            'updated_by' => $data['updated_by'] ?? null,
        ]);

        $bed = Bed::findOrFail($admission->bed_id);
        $bed->update([
            'status' => 'available',
            'current_patient_id' => null,
        ]);

        // Post final bed charge
        $this->postBedChargeForAdmission($admission);

        return $admission;
    }

    /**
     * Transfer patient to another bed.
     */
    public function transfer(int $id, int $newBedId, int $userId): Admission
    {
        $admission = Admission::findOrFail($id);
        $oldBed = Bed::findOrFail($admission->bed_id);
        $newBed = Bed::findOrFail($newBedId);

        // Release old bed
        $oldBed->update([
            'status' => 'available',
            'current_patient_id' => null,
        ]);

        // Post bed charge for the old bed up to now
        $this->postBedChargeForAdmission($admission);

        // Occupy new bed
        $newBed->update([
            'status' => 'occupied',
            'current_patient_id' => $admission->patient_id,
        ]);

        // Update admission record
        $admission->update([
            'bed_id' => $newBedId,
            'updated_by' => $userId,
        ]);

        return $admission;
    }

    /**
     * Calculate and post bed charges for a specific admission.
     */
    public function postBedChargeForAdmission(Admission $admission): void
    {
        $bed = $admission->bed;
        $admitDate = Carbon::parse($admission->admit_date);
        $endDate = $admission->discharge_date ? Carbon::parse($admission->discharge_date) : Carbon::today();

        $days = max(1, $admitDate->diffInDays($endDate));
        $amount = $bed->daily_charge * $days;

        $txNumber = 'TXN-'.Carbon::today()->format('Ymd').'-'.rand(10000, 99999);

        // Find a fallback poster (admin)
        $postedBy = $admission->created_by ?? User::where('role', 'super_admin')->first()?->id ?? 1;

        FinancialTransaction::create([
            'admission_id' => $admission->id,
            'transaction_number' => $txNumber,
            'transaction_type' => 'debit',
            'category' => 'bed_charge',
            'sub_category' => $bed->bed_type.' Bed Charge',
            'amount' => $amount,
            'quantity' => $days,
            'unit_price' => $bed->daily_charge,
            'discount' => 0,
            'net_amount' => $amount,
            'description' => "Bed charge for bed {$bed->bed_number} for {$days} days",
            'posting_date' => Carbon::today()->toDateString(),
            'posting_time' => Carbon::now()->toTimeString(),
            'posted_by' => $postedBy,
            'status' => 'posted',
        ]);
    }

    /**
     * Post daily bed charges for all active admissions.
     */
    public function postDailyBedCharges(): int
    {
        $activeAdmissions = Admission::where('status', 'active')->get();
        $count = 0;

        foreach ($activeAdmissions as $admission) {
            $this->postBedChargeForAdmission($admission);
            $count++;
        }

        return $count;
    }
}
