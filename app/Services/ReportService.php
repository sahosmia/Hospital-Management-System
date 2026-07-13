<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\FinancialTransaction;
use App\Models\Surgery;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate daily operational and financial metrics.
     */
    public function generateDailyReport(string $date): array
    {
        $appointmentsCount = Appointment::where('appointment_date', $date)->count();
        $admissionsCount = Admission::where('admit_date', $date)->count();
        $surgeriesCount = Surgery::where('scheduled_date', $date)->count();

        $revenue = FinancialTransaction::where('posting_date', $date)
            ->where('transaction_type', 'credit')
            ->where('category', 'payment')
            ->where('status', 'approved')
            ->sum('net_amount');

        $activeAdmissions = Admission::where('status', 'active')->count();
        $totalBeds = Bed::count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0;

        return [
            'date' => $date,
            'appointments_booked' => $appointmentsCount,
            'patients_admitted' => $admissionsCount,
            'surgeries_conducted' => $surgeriesCount,
            'daily_revenue' => (float) $revenue,
            'active_admissions' => $activeAdmissions,
            'bed_occupancy' => [
                'total' => $totalBeds,
                'occupied' => $occupiedBeds,
                'rate' => $occupancyRate,
            ],
        ];
    }

    /**
     * Generate monthly aggregated analytics.
     */
    public function generateMonthlyReport(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $appointmentsCount = Appointment::whereBetween('appointment_date', [$startDate, $endDate])->count();
        $admissionsCount = Admission::whereBetween('admit_date', [$startDate, $endDate])->count();
        $surgeriesCount = Surgery::whereBetween('scheduled_date', [$startDate, $endDate])->count();

        $revenue = FinancialTransaction::whereBetween('posting_date', [$startDate, $endDate])
            ->where('transaction_type', 'credit')
            ->where('category', 'payment')
            ->where('status', 'approved')
            ->sum('net_amount');

        return [
            'year' => $year,
            'month' => $month,
            'appointments_booked' => $appointmentsCount,
            'patients_admitted' => $admissionsCount,
            'surgeries_conducted' => $surgeriesCount,
            'monthly_revenue' => (float) $revenue,
        ];
    }
}
