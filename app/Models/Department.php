<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'department_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'department_id');
    }
}
