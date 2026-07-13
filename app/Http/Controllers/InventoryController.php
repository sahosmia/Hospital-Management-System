<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\SurgicalSupply;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Get supplies list.
     */
    public function supplies(): JsonResponse
    {
        try {
            $supplies = SurgicalSupply::latest()->get();

            return $this->jsonSuccess('Surgical supplies retrieved successfully.', $supplies);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch supplies: '.$e->getMessage(), 500);
        }
    }

    /**
     * Add new surgical supply.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supply_code' => 'required|string|unique:surgical_supplies,supply_code|max:50',
            'supply_name' => 'required|string|max:255',
            'category' => 'required|in:suture,dressing,glove,mask,gown,drape,catheter,tube,drain,implant,other',
            'unit' => 'required|string|max:20',
            'quantity_per_unit' => 'nullable|integer',
            'current_stock' => 'nullable|integer',
            'minimum_stock' => 'nullable|integer',
            'maximum_stock' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:50',
            'lot_number' => 'nullable|string|max:50',
            'storage_location' => 'nullable|string|max:100',
            'shelf_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $supply = $this->inventoryService->add($request->all());
            $this->logAudit('CREATE_SUPPLY', 'surgical_supplies', $supply->id, null, $supply->toArray());

            return $this->jsonSuccess('Surgical supply added successfully.', $supply, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to add supply: '.$e->getMessage(), 500);
        }
    }

    /**
     * Update supply details.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $supply = SurgicalSupply::findOrFail($id);
            $oldValue = $supply->toArray();
        } catch (\Exception $e) {
            return $this->jsonError('Supply not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'supply_code' => 'nullable|string|max:50|unique:surgical_supplies,supply_code,'.$id,
            'supply_name' => 'nullable|string|max:255',
            'category' => 'nullable|in:suture,dressing,glove,mask,gown,drape,catheter,tube,drain,implant,other',
            'unit' => 'nullable|string|max:20',
            'current_stock' => 'nullable|integer',
            'minimum_stock' => 'nullable|integer',
            'maximum_stock' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $supply->update($request->all());
            $this->logAudit('UPDATE_SUPPLY', 'surgical_supplies', $id, $oldValue, $supply->toArray());

            return $this->jsonSuccess('Surgical supply updated successfully.', $supply);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to update supply: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get low stock alerts.
     */
    public function lowStock(): JsonResponse
    {
        try {
            $alerts = $this->inventoryService->getLowStockAlerts();

            return $this->jsonSuccess('Low stock supplies retrieved successfully.', $alerts);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch low stock list: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get expiring supplies.
     */
    public function expiring(): JsonResponse
    {
        try {
            $expiring = $this->inventoryService->getExpiringSupplies();

            return $this->jsonSuccess('Expiring supplies retrieved successfully.', $expiring);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch expiring list: '.$e->getMessage(), 500);
        }
    }

    /**
     * Create restock request.
     */
    public function request(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supply_id' => 'required|exists:surgical_supplies,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $tx = $this->inventoryService->requestRestock(
                $request->input('supply_id'),
                $request->input('quantity'),
                Auth::id()
            );

            $this->logAudit('REQUEST_RESTOCK', 'inventory_transactions', $tx->id, null, $tx->toArray());

            return $this->jsonSuccess('Restock request submitted successfully.', $tx, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to request restock: '.$e->getMessage(), 500);
        }
    }

    /**
     * Approve restock request.
     */
    public function approve(int $id): JsonResponse
    {
        try {
            $transaction = InventoryTransaction::findOrFail($id);
            $oldValue = $transaction->toArray();

            $approvedTx = $this->inventoryService->approveRestock($id, Auth::id());
            $this->logAudit('APPROVE_RESTOCK', 'inventory_transactions', $id, $oldValue, $approvedTx->toArray());

            return $this->jsonSuccess('Restock request approved successfully.', $approvedTx);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to approve restock: '.$e->getMessage(), 500);
        }
    }

    /**
     * Fulfill restock request.
     */
    public function fulfill(int $id): JsonResponse
    {
        try {
            $transaction = InventoryTransaction::findOrFail($id);
            $oldValue = $transaction->toArray();

            if (! $transaction->approved_by) {
                return $this->jsonError('Restock request must be approved before fulfillment.', 400);
            }

            $fulfilledTx = $this->inventoryService->fulfillRestock($id);
            $this->logAudit('FULFILL_RESTOCK', 'inventory_transactions', $id, $oldValue, $fulfilledTx->toArray());

            return $this->jsonSuccess('Restock transaction fulfilled and stock increased.', $fulfilledTx);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fulfill restock: '.$e->getMessage(), 500);
        }
    }

    /**
     * Fetch restock transactions.
     */
    public function requests(): JsonResponse
    {
        try {
            $txs = InventoryTransaction::where('transaction_type', 'purchase')
                ->with(['supply', 'performer', 'approver'])
                ->latest()
                ->get();

            return $this->jsonSuccess('Restock requests retrieved successfully.', $txs);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch restock requests: '.$e->getMessage(), 500);
        }
    }
}
