<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\OperationTheater;
use App\Models\Surgery;
use App\Models\User;
use Carbon\Carbon;

class SurgeryService
{
    /**
     * Schedule a surgery and reserve the OT.
     */
    public function schedule(array $data): Surgery
    {
        $ot = OperationTheater::findOrFail($data['ot_id']);
        $ot->update(['status' => 'reserved']);

        $surgeryNum = 'SRG-'.Carbon::parse($data['scheduled_date'])->format('Ymd').'-'.rand(1000, 9999);

        return Surgery::create([
            'surgery_number' => $surgeryNum,
            'admission_id' => $data['admission_id'],
            'patient_id' => $data['patient_id'],
            'surgeon_id' => $data['surgeon_id'],
            'assistant_surgeon_id' => $data['assistant_surgeon_id'] ?? null,
            'anesthesiologist_id' => $data['anesthesiologist_id'] ?? null,
            'scrub_nurse_id' => $data['scrub_nurse_id'] ?? null,
            'ot_id' => $data['ot_id'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'surgery_type' => $data['surgery_type'],
            'surgery_name' => $data['surgery_name'],
            'urgency' => $data['urgency'] ?? 'elective',
            'priority' => $data['priority'] ?? 'routine',
            'status' => 'scheduled',
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    /**
     * Start a scheduled surgery.
     */
    public function start(int $id): Surgery
    {
        $surgery = Surgery::findOrFail($id);
        $surgery->update([
            'status' => 'in_progress',
            'actual_start_time' => Carbon::now(),
        ]);

        $ot = OperationTheater::findOrFail($surgery->ot_id);
        $ot->update([
            'status' => 'occupied',
            'current_surgery_id' => $id,
        ]);

        return $surgery;
    }

    /**
     * Complete a surgery and post costs.
     */
    public function complete(int $id, array $data): Surgery
    {
        $surgery = Surgery::findOrFail($id);

        $actualStartTime = $surgery->actual_start_time ? Carbon::parse($surgery->actual_start_time) : Carbon::now()->subHours(1);
        $actualEndTime = Carbon::now();
        $duration = max(15, $actualStartTime->diffInMinutes($actualEndTime));

        $surgery->update([
            'status' => 'completed',
            'actual_end_time' => $actualEndTime,
            'duration_minutes' => $duration,
            'patient_condition_after' => $data['patient_condition_after'] ?? null,
            'outcome' => $data['outcome'] ?? 'successful',
            'anesthesia_type' => $data['anesthesia_type'] ?? null,
            'surgical_notes' => $data['surgical_notes'] ?? null,
            'post_op_notes' => $data['post_op_notes'] ?? null,
        ]);

        $ot = OperationTheater::findOrFail($surgery->ot_id);
        $ot->update([
            'status' => 'cleaning',
            'current_surgery_id' => null,
        ]);

        // Post financial transactions: base surgery fee + OT usage charges
        $this->calculateAndPostCosts($surgery, $duration);

        return $surgery;
    }

    /**
     * Cancel a scheduled surgery.
     */
    public function cancel(int $id, string $reason): Surgery
    {
        $surgery = Surgery::findOrFail($id);
        $surgery->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);

        $ot = OperationTheater::findOrFail($surgery->ot_id);
        $ot->update(['status' => 'available']);

        return $surgery;
    }

    /**
     * Calculate surgery fees and post them.
     */
    protected function calculateAndPostCosts(Surgery $surgery, int $duration): void
    {
        $postedBy = $surgery->created_by ?? User::where('role', 'super_admin')->first()?->id ?? 1;

        // 1. Surgeon fee
        $surgeonUser = User::find($surgery->surgeon_id);
        $doctorProfile = $surgeonUser?->doctor;
        $surgeonFee = $doctorProfile?->surgery_fee ?? 1000.00;

        $txNumber1 = 'TXN-'.Carbon::today()->format('Ymd').'-'.rand(10000, 99999);
        FinancialTransaction::create([
            'admission_id' => $surgery->admission_id,
            'transaction_number' => $txNumber1,
            'transaction_type' => 'debit',
            'category' => 'surgery',
            'sub_category' => 'Surgeon Fee - '.$surgery->surgery_name,
            'amount' => $surgeonFee,
            'unit_price' => $surgeonFee,
            'net_amount' => $surgeonFee,
            'description' => 'Professional surgeon fee for Dr. '.($surgeonUser?->name ?? 'Surgeon'),
            'posting_date' => Carbon::today()->toDateString(),
            'posting_time' => Carbon::now()->toTimeString(),
            'posted_by' => $postedBy,
            'status' => 'posted',
        ]);

        // 2. OT Usage charge: base charge + hourly rate
        $ot = $surgery->ot;
        $hours = ceil($duration / 60.0);
        $otCharge = $ot->base_charge + ($ot->per_hour_charge * $hours);

        $txNumber2 = 'TXN-'.Carbon::today()->format('Ymd').'-'.rand(10000, 99999);
        FinancialTransaction::create([
            'admission_id' => $surgery->admission_id,
            'transaction_number' => $txNumber2,
            'transaction_type' => 'debit',
            'category' => 'procedure',
            'sub_category' => 'OT Usage Charge',
            'amount' => $otCharge,
            'quantity' => $hours,
            'unit_price' => $ot->per_hour_charge,
            'net_amount' => $otCharge,
            'description' => "Operation theater usage fee for OT: {$ot->ot_number} ({$duration} minutes)",
            'posting_date' => Carbon::today()->toDateString(),
            'posting_time' => Carbon::now()->toTimeString(),
            'posted_by' => $postedBy,
            'status' => 'posted',
        ]);
    }
}
