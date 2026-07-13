<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'transaction_number',
        'transaction_type',
        'category',
        'sub_category',
        'amount',
        'quantity',
        'unit_price',
        'discount',
        'net_amount',
        'description',
        'reference_id',
        'reference_type',
        'posting_date',
        'posting_time',
        'posted_by',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'posting_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
