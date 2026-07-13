<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_number',
        'admission_id',
        'patient_id',
        'surgeon_id',
        'assistant_surgeon_id',
        'anesthesiologist_id',
        'scrub_nurse_id',
        'ot_id',
        'scheduled_date',
        'scheduled_time',
        'actual_start_time',
        'actual_end_time',
        'duration_minutes',
        'surgery_type',
        'surgery_name',
        'urgency',
        'priority',
        'patient_condition_before',
        'patient_condition_after',
        'outcome',
        'anesthesia_type',
        'pre_op_notes',
        'post_op_notes',
        'surgical_notes',
        'complications',
        'status',
        'cancellation_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'actual_start_time' => 'datetime',
            'actual_end_time' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function surgeon()
    {
        return $this->belongsTo(User::class, 'surgeon_id');
    }

    public function assistantSurgeon()
    {
        return $this->belongsTo(User::class, 'assistant_surgeon_id');
    }

    public function anesthesiologist()
    {
        return $this->belongsTo(User::class, 'anesthesiologist_id');
    }

    public function scrubNurse()
    {
        return $this->belongsTo(User::class, 'scrub_nurse_id');
    }

    public function ot()
    {
        return $this->belongsTo(OperationTheater::class, 'ot_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
