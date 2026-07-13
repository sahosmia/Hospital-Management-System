<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    // Table doesn't have standard updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'transaction_number',
        'supply_id',
        'surgery_id',
        'admission_id',
        'transaction_type',
        'quantity',
        'unit_price',
        'total_price',
        'description',
        'batch_number',
        'approved_by',
        'approved_at',
        'performed_by',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'approved_at' => 'datetime',
            'performed_at' => 'datetime',
        ];
    }

    public function supply()
    {
        return $this->belongsTo(SurgicalSupply::class, 'supply_id');
    }

    public function surgery()
    {
        return $this->belongsTo(Surgery::class, 'surgery_id');
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
