<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationTheater extends Model
{
    use HasFactory;

    protected $fillable = [
        'ot_number',
        'ot_name',
        'ot_type',
        'capacity',
        'is_active',
        'has_ventilator',
        'has_heart_lung_machine',
        'has_c_arm',
        'has_microscope',
        'has_laparoscopic_tower',
        'has_robotic_system',
        'floor',
        'room_number',
        'base_charge',
        'per_hour_charge',
        'status',
        'current_surgery_id',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
            'has_ventilator' => 'boolean',
            'has_heart_lung_machine' => 'boolean',
            'has_c_arm' => 'boolean',
            'has_microscope' => 'boolean',
            'has_laparoscopic_tower' => 'boolean',
            'has_robotic_system' => 'boolean',
            'base_charge' => 'decimal:2',
            'per_hour_charge' => 'decimal:2',
        ];
    }

    public function currentSurgery()
    {
        return $this->belongsTo(Surgery::class, 'current_surgery_id');
    }
}
