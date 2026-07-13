<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurgicalSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_code',
        'supply_name',
        'category',
        'unit',
        'quantity_per_unit',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_level',
        'purchase_price',
        'selling_price',
        'last_purchase_price',
        'supplier_name',
        'supplier_contact',
        'expiry_date',
        'batch_number',
        'lot_number',
        'storage_location',
        'shelf_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quantity_per_unit' => 'integer',
            'current_stock' => 'integer',
            'minimum_stock' => 'integer',
            'maximum_stock' => 'integer',
            'reorder_level' => 'integer',
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'last_purchase_price' => 'decimal:2',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
