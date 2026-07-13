<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $fillable = ['day', 'hours', 'is_closed'];

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
        ];
    }
}
