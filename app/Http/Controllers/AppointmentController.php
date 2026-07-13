<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentService $appointmentService) {}

    /**
     * List appointments for the current user (patient / doctor) or paginated list for admins.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Appointment::with(['patient', 'doctor']);

            if ($user->role === 'patient') {
                $query->where('patient_id', $user->id);
            } elseif ($user->role === 'doctor') {
                $query->where('doctor_id', $user->id);
            }

            if ($request->has('date')) {
                $query->where('appointment_date', $request->input('date'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $appointments = $query->latest()->paginate(15);

            return $this->jsonSuccess('Appointments retrieved successfully.', $appointments);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch appointments: '.$e->getMessage(), 500);
        }
    }

    /**
     * Book a new appointment.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'type' => 'nullable|in:physical,video,telephone',
            'booking_channel' => 'nullable|in:online,offline',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['booked_by'] = Auth::id();

            // Check if slot is available
            $slots = $this->appointmentService->getAvailableSlots($data['doctor_id'], $data['appointment_date']);
            $timeFormatted = date('H:i', strtotime($data['appointment_time']));
            $slotFoundAndAvailable = false;

            foreach ($slots as $slot) {
                if ($slot['time'] === $timeFormatted && $slot['is_available']) {
                    $slotFoundAndAvailable = true;
                    break;
                }
            }

            if (! $slotFoundAndAvailable) {
                return $this->jsonError('The selected appointment slot is not available.', 400);
            }

            $appointment = $this->appointmentService->book($data);
            $this->logAudit('BOOK_APPOINTMENT', 'appointments', $appointment->id, null, $appointment->toArray());

            return $this->jsonSuccess('Appointment booked successfully.', $appointment, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to book appointment: '.$e->getMessage(), 500);
        }
    }

    /**
     * Reschedule / Update an appointment.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
        } catch (\Exception $e) {
            return $this->jsonError('Appointment not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'status' => 'nullable|in:scheduled,completed,cancelled,no-show',
            'type' => 'nullable|in:physical,video,telephone',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $oldValue = $appointment->toArray();

            $appointment->update($request->only([
                'appointment_date', 'appointment_time', 'status', 'type', 'notes',
            ]));

            $this->logAudit('UPDATE_APPOINTMENT', 'appointments', $appointment->id, $oldValue, $appointment->toArray());

            return $this->jsonSuccess('Appointment updated successfully.', $appointment);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to update appointment: '.$e->getMessage(), 500);
        }
    }

    /**
     * Cancel an appointment.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $oldValue = $appointment->toArray();

            $this->appointmentService->cancel($id);
            $appointment->refresh();

            $this->logAudit('CANCEL_APPOINTMENT', 'appointments', $id, $oldValue, $appointment->toArray());

            return $this->jsonSuccess('Appointment cancelled successfully.', $appointment);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to cancel appointment: '.$e->getMessage(), 500);
        }
    }

    /**
     * Fetch available slots for any doctor.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $slots = $this->appointmentService->getAvailableSlots(
                $request->input('doctor_id'),
                $request->input('date')
            );

            return $this->jsonSuccess('Available slots retrieved successfully.', $slots);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to retrieve slots: '.$e->getMessage(), 500);
        }
    }
}
