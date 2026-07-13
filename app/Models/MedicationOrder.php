<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'doctor_id',
        'treatment_plan_id',
        'medicine_name',
        'generic_name',
        'medicine_category',
        'medicine_form',
        'dosage',
        'frequency',
        'route',
        'start_date',
        'end_date',
        'duration_days',
        'scheduled_times',
        'status',
        'is_emergency',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_days' => 'integer',
            'scheduled_times' => 'json',
            'is_emergency' => 'boolean',
        ];
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function administrations()
    {
        return $this->hasMany(MedicationAdministration::class, 'medication_order_id');
    }
}
