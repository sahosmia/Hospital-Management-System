<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorHoliday;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Book a new appointment.
     */
    public function book(array $data): Appointment
    {
        $serial = 'APT-'.Carbon::parse($data['appointment_date'])->format('Ymd').'-'.rand(1000, 9999);

        return Appointment::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'serial_number' => $serial,
            'status' => 'scheduled',
            'type' => $data['type'] ?? 'physical',
            'booking_channel' => $data['booking_channel'] ?? 'online',
            'booked_by' => $data['booked_by'] ?? null,
            'symptoms' => $data['symptoms'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(int $id): bool
    {
        $appointment = Appointment::findOrFail($id);

        return $appointment->update(['status' => 'cancelled']);
    }

    /**
     * Get available slots for a doctor on a specific date.
     */
    public function getAvailableSlots(int $doctorId, string $date): array
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = strtolower($carbonDate->format('l'));

        // Check if doctor is on holiday
        $onHoliday = DoctorHoliday::where('doctor_id', $doctorId)
            ->where('holiday_date', $date)
            ->exists();

        if ($onHoliday) {
            return [];
        }

        // Get doctor's schedule for this day
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (! $schedule) {
            return [];
        }

        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $duration = $schedule->slot_duration;

        // Fetch already booked times
        $bookedTimes = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'completed'])
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        while ($startTime->lessThan($endTime)) {
            $slotTime = $startTime->format('H:i');
            $slots[] = [
                'time' => $slotTime,
                'is_available' => ! in_array($slotTime, $bookedTimes),
            ];
            $startTime->addMinutes($duration);
        }

        return $slots;
    }
}
