<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorReview;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function __construct(protected AppointmentService $appointmentService) {}

    /**
     * List doctors with filters.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::where('role', 'doctor')
                ->with(['doctor', 'ratingStats']);

            if ($request->has('specialty')) {
                $specialty = $request->input('specialty');
                $query->whereHas('doctor', function ($q) use ($specialty) {
                    $q->where('specialization', 'like', "%{$specialty}%");
                });
            }

            if ($request->has('rating')) {
                $rating = (float) $request->input('rating');
                $query->whereHas('ratingStats', function ($q) use ($rating) {
                    $q->where('average_rating', '>=', $rating);
                });
            }

            $doctors = $query->latest()->get();

            return $this->jsonSuccess('Doctors retrieved successfully.', $doctors);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch doctors: '.$e->getMessage(), 500);
        }
    }

    /**
     * Show a doctor's profile.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $doctor = User::where('role', 'doctor')
                ->with(['doctor', 'ratingStats', 'doctorSchedules'])
                ->findOrFail($id);

            return $this->jsonSuccess('Doctor profile retrieved successfully.', $doctor);
        } catch (\Exception $e) {
            return $this->jsonError('Doctor not found.', 404);
        }
    }

    /**
     * Get doctor schedules.
     */
    public function schedule(int $id): JsonResponse
    {
        try {
            $doctor = User::where('role', 'doctor')->findOrFail($id);
            $schedule = $doctor->doctorSchedules;

            return $this->jsonSuccess('Doctor schedule retrieved successfully.', $schedule);
        } catch (\Exception $e) {
            return $this->jsonError('Doctor schedule not found.', 404);
        }
    }

    /**
     * Get doctor slots for a given date.
     */
    public function slots(Request $request, int $id): JsonResponse
    {
        $date = $request->input('date', date('Y-m-d'));

        try {
            $slots = $this->appointmentService->getAvailableSlots($id, $date);

            return $this->jsonSuccess('Available slots retrieved successfully.', $slots);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to retrieve slots: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get doctor reviews.
     */
    public function reviews(int $id): JsonResponse
    {
        try {
            $reviews = DoctorReview::where('doctor_id', $id)
                ->where('status', 'approved')
                ->with('patient')
                ->latest()
                ->get();

            return $this->jsonSuccess('Doctor reviews retrieved successfully.', $reviews);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch reviews: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get doctor rating stats.
     */
    public function rating(int $id): JsonResponse
    {
        try {
            $doctor = User::where('role', 'doctor')->findOrFail($id);
            $stats = $doctor->ratingStats;

            return $this->jsonSuccess('Doctor rating statistics retrieved successfully.', $stats);
        } catch (\Exception $e) {
            return $this->jsonError('Rating stats not found.', 404);
        }
    }
}
