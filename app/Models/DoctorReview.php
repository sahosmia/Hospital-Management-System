<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_id',
        'rating',
        'communication_rating',
        'expertise_rating',
        'behavior_rating',
        'cleanliness_rating',
        'waiting_time_rating',
        'review',
        'positive_points',
        'negative_points',
        'tags',
        'media',
        'is_anonymous',
        'doctor_reply',
        'doctor_replied_at',
        'status',
        'flagged_reason',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'communication_rating' => 'integer',
            'expertise_rating' => 'integer',
            'behavior_rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'waiting_time_rating' => 'integer',
            'tags' => 'json',
            'media' => 'json',
            'is_anonymous' => 'boolean',
            'doctor_replied_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
