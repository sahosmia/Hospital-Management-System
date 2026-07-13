<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_number',
        'ward_name',
        'bed_type',
        'status',
        'current_patient_id',
        'daily_charge',
        'features',
        'last_cleaned_at',
    ];

    protected function casts(): array
    {
        return [
            'daily_charge' => 'decimal:2',
            'features' => 'json',
            'last_cleaned_at' => 'datetime',
        ];
    }

    public function currentPatient()
    {
        return $this->belongsTo(User::class, 'current_patient_id');
    }
}
