<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\SurgicalSupply;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Add a new supply.
     */
    public function add(array $data): SurgicalSupply
    {
        return SurgicalSupply::create([
            'supply_code' => $data['supply_code'],
            'supply_name' => $data['supply_name'],
            'category' => $data['category'],
            'unit' => $data['unit'],
            'quantity_per_unit' => $data['quantity_per_unit'] ?? 1,
            'current_stock' => $data['current_stock'] ?? 0,
            'minimum_stock' => $data['minimum_stock'] ?? 10,
            'maximum_stock' => $data['maximum_stock'] ?? 100,
            'reorder_level' => $data['reorder_level'] ?? 20,
            'purchase_price' => $data['purchase_price'],
            'selling_price' => $data['selling_price'],
            'supplier_name' => $data['supplier_name'] ?? null,
            'supplier_contact' => $data['supplier_contact'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'batch_number' => $data['batch_number'] ?? null,
            'lot_number' => $data['lot_number'] ?? null,
            'storage_location' => $data['storage_location'] ?? null,
            'shelf_number' => $data['shelf_number'] ?? null,
        ]);
    }

    /**
     * Consume a supply during surgery or medication.
     */
    public function consume(int $supplyId, int $quantity, int $userId, ?int $surgeryId = null, ?int $admissionId = null): bool
    {
        $supply = SurgicalSupply::findOrFail($supplyId);
        if ($supply->current_stock < $quantity) {
            return false;
        }

        $supply->decrement('current_stock', $quantity);

        $txNumber = 'ITX-'.Carbon::today()->format('Ymd').'-'.rand(10000, 99999);
        InventoryTransaction::create([
            'transaction_number' => $txNumber,
            'supply_id' => $supplyId,
            'surgery_id' => $surgeryId,
            'admission_id' => $admissionId,
            'transaction_type' => 'consumption',
            'quantity' => $quantity,
            'unit_price' => $supply->selling_price,
            'total_price' => $supply->selling_price * $quantity,
            'performed_by' => $userId,
            'performed_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Request restock of a supply.
     */
    public function requestRestock(int $supplyId, int $quantity, int $userId): InventoryTransaction
    {
        $supply = SurgicalSupply::findOrFail($supplyId);
        $txNumber = 'ITX-'.Carbon::today()->format('Ymd').'-'.rand(10000, 99999);

        return InventoryTransaction::create([
            'transaction_number' => $txNumber,
            'supply_id' => $supplyId,
            'transaction_type' => 'purchase',
            'quantity' => $quantity,
            'unit_price' => $supply->purchase_price,
            'total_price' => $supply->purchase_price * $quantity,
            'performed_by' => $userId,
            'performed_at' => Carbon::now(),
        ]);
    }

    /**
     * Approve a pending restock request.
     */
    public function approveRestock(int $transactionId, int $approverId): InventoryTransaction
    {
        $transaction = InventoryTransaction::findOrFail($transactionId);
        $transaction->update([
            'approved_by' => $approverId,
            'approved_at' => Carbon::now(),
        ]);

        return $transaction;
    }

    /**
     * Fulfill a restock request.
     */
    public function fulfillRestock(int $transactionId): InventoryTransaction
    {
        $transaction = InventoryTransaction::findOrFail($transactionId);
        $supply = SurgicalSupply::findOrFail($transaction->supply_id);

        $supply->increment('current_stock', $transaction->quantity);

        return $transaction;
    }

    /**
     * Get low stock alerts.
     */
    public function getLowStockAlerts()
    {
        return SurgicalSupply::whereRaw('current_stock <= reorder_level')->get();
    }

    /**
     * Get expiring supplies (expiring within next 30 days).
     */
    public function getExpiringSupplies()
    {
        return SurgicalSupply::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::today()->addDays(30)->toDateString())
            ->get();
    }
}
