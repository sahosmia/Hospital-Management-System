<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'consultation_fee',
        'surgery_fee',
        'experience_years',
        'qualifications',
        'chamber_location',
        'department_id',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',
            'surgery_fee' => 'decimal:2',
            'experience_years' => 'integer',
            'qualifications' => 'json',
            'is_featured' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function reviews()
    {
        return $this->hasMany(DoctorReview::class, 'doctor_id', 'user_id');
    }
}
