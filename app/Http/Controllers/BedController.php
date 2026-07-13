<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BedController extends Controller
{
    /**
     * List all beds.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Bed::with('currentPatient');

            if ($request->has('bed_type')) {
                $query->where('bed_type', $request->input('bed_type'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $beds = $query->latest()->get();

            return $this->jsonSuccess('Beds retrieved successfully.', $beds);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch beds: '.$e->getMessage(), 500);
        }
    }

    /**
     * Create a new bed.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bed_number' => 'required|string|unique:beds,bed_number|max:20',
            'ward_name' => 'required|string|max:50',
            'bed_type' => 'required|in:General,Private,Deluxe,ICU,HDU',
            'daily_charge' => 'required|numeric|min:0',
            'features' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();

            $bed = Bed::create($data);
            $this->logAudit('CREATE_BED', 'beds', $bed->id, null, $bed->toArray());

            return $this->jsonSuccess('Bed created successfully.', $bed, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to create bed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Update details or status of a bed.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $bed = Bed::findOrFail($id);
        } catch (\Exception $e) {
            return $this->jsonError('Bed not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'bed_number' => 'nullable|string|max:20|unique:beds,bed_number,'.$id,
            'ward_name' => 'nullable|string|max:50',
            'bed_type' => 'nullable|in:General,Private,Deluxe,ICU,HDU',
            'status' => 'nullable|in:available,occupied,reserved,maintenance',
            'daily_charge' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'last_cleaned_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $oldValue = $bed->toArray();

            $data = $request->all();

            $bed->update($data);
            $this->logAudit('UPDATE_BED', 'beds', $bed->id, $oldValue, $bed->toArray());

            return $this->jsonSuccess('Bed updated successfully.', $bed);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to update bed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get all available beds.
     */
    public function available(): JsonResponse
    {
        try {
            $beds = Bed::where('status', 'available')->latest()->get();

            return $this->jsonSuccess('Available beds retrieved successfully.', $beds);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch available beds: '.$e->getMessage(), 500);
        }
    }

    /**
     * Occupancy stats report.
     */
    public function occupancy(): JsonResponse
    {
        try {
            $totalBeds = Bed::count();
            $occupiedBeds = Bed::where('status', 'occupied')->count();
            $availableBeds = Bed::where('status', 'available')->count();
            $reservedBeds = Bed::where('status', 'reserved')->count();
            $maintenanceBeds = Bed::where('status', 'maintenance')->count();

            $stats = [
                'total' => $totalBeds,
                'occupied' => $occupiedBeds,
                'available' => $availableBeds,
                'reserved' => $reservedBeds,
                'maintenance' => $maintenanceBeds,
                'occupancy_rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0,
                'by_type' => Bed::selectRaw('bed_type, count(*) as count, sum(case when status = "occupied" then 1 else 0 end) as occupied_count')
                    ->groupBy('bed_type')
                    ->get(),
            ];

            return $this->jsonSuccess('Occupancy statistics retrieved successfully.', $stats);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch occupancy statistics: '.$e->getMessage(), 500);
        }
    }
}
