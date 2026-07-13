<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_number',
        'patient_id',
        'doctor_id',
        'bed_id',
        'admit_date',
        'admit_time',
        'admit_type',
        'patient_condition',
        'primary_diagnosis',
        'payment_type',
        'status',
        'discharge_date',
        'discharge_time',
        'discharge_summary',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'admit_date' => 'date',
            'discharge_date' => 'date',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'admission_id');
    }

    public function medicationOrders()
    {
        return $this->hasMany(MedicationOrder::class, 'admission_id');
    }

    public function surgeries()
    {
        return $this->hasMany(Surgery::class, 'admission_id');
    }
}
