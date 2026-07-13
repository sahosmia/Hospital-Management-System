<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAdministration extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_order_id',
        'admission_id',
        'administered_by',
        'administered_date',
        'administered_time',
        'scheduled_time',
        'dosage_given',
        'status',
        'reason_for_missed',
        'patient_response',
        'side_effects',
        'vitals_before',
        'vitals_after',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'administered_date' => 'date',
            'vitals_before' => 'json',
            'vitals_after' => 'json',
        ];
    }

    public function medicationOrder()
    {
        return $this->belongsTo(MedicationOrder::class, 'medication_order_id');
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function administrator()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
