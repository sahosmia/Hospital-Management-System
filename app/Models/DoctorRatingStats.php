<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorRatingStats extends Model
{
    use HasFactory;

    // Table doesn't have standard timestamps
    public $timestamps = false;

    protected $fillable = [
        'doctor_id',
        'total_reviews',
        'average_rating',
        'rating_5_count',
        'rating_4_count',
        'rating_3_count',
        'rating_2_count',
        'rating_1_count',
        'avg_communication',
        'avg_expertise',
        'avg_behavior',
        'avg_cleanliness',
        'avg_waiting_time',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'total_reviews' => 'integer',
            'average_rating' => 'decimal:2',
            'rating_5_count' => 'integer',
            'rating_4_count' => 'integer',
            'rating_3_count' => 'integer',
            'rating_2_count' => 'integer',
            'rating_1_count' => 'integer',
            'avg_communication' => 'decimal:2',
            'avg_expertise' => 'decimal:2',
            'avg_behavior' => 'decimal:2',
            'avg_cleanliness' => 'decimal:2',
            'avg_waiting_time' => 'decimal:2',
            'last_updated' => 'datetime',
        ];
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
