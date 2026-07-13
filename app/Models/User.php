<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password_hash',
        'google_id',
        'avatar',
        'role',
        'patient_id',
        'is_active',
        'phone_verified_at',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash ?? '';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /* Relationships */

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }

    public function appointmentsAsPatient()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function appointmentsAsDoctor()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function admissionsAsPatient()
    {
        return $this->hasMany(Admission::class, 'patient_id');
    }

    public function admissionsAsDoctor()
    {
        return $this->hasMany(Admission::class, 'doctor_id');
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    public function doctorHolidays()
    {
        return $this->hasMany(DoctorHoliday::class, 'doctor_id');
    }

    public function reviewsAsPatient()
    {
        return $this->hasMany(DoctorReview::class, 'patient_id');
    }

    public function reviewsAsDoctor()
    {
        return $this->hasMany(DoctorReview::class, 'doctor_id');
    }

    public function ratingStats()
    {
        return $this->hasOne(DoctorRatingStats::class, 'doctor_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
