<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    /**
     * Get daily operations report.
     */
    public function daily(Request $request): JsonResponse
    {
        $date = $request->input('date', date('Y-m-d'));

        try {
            $report = $this->reportService->generateDailyReport($date);

            return $this->jsonSuccess('Daily report generated successfully.', $report);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to generate daily report: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get monthly operations report.
     */
    public function monthly(Request $request): JsonResponse
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('m'));

        try {
            $report = $this->reportService->generateMonthlyReport($year, $month);

            return $this->jsonSuccess('Monthly report generated successfully.', $report);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to generate monthly report: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get revenue trends for dashboards.
     */
    public function revenue(): JsonResponse
    {
        try {
            // Aggregate payments by date for the last 30 days
            $startDate = now()->subDays(30)->toDateString();
            $endDate = now()->toDateString();

            $payments = FinancialTransaction::where('transaction_type', 'credit')
                ->where('category', 'payment')
                ->where('status', 'approved')
                ->whereBetween('posting_date', [$startDate, $endDate])
                ->selectRaw('posting_date as date, sum(net_amount) as total')
                ->groupBy('posting_date')
                ->orderBy('posting_date')
                ->get();

            return $this->jsonSuccess('Revenue trends retrieved successfully.', $payments);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch revenue trends: '.$e->getMessage(), 500);
        }
    }

    /**
     * Download reports (simulated CSV / Excel download).
     */
    public function download(string $type, string $date): JsonResponse
    {
        try {
            $report = $this->reportService->generateDailyReport($date);

            $fileContent = "HMS Report - Type: {$type}, Date: {$date}\n";
            $fileContent .= 'Appointments Booked: '.$report['appointments_booked']."\n";
            $fileContent .= 'Patients Admitted: '.$report['patients_admitted']."\n";
            $fileContent .= 'Surgeries Conducted: '.$report['surgeries_conducted']."\n";
            $fileContent .= 'Daily Revenue: $'.$report['daily_revenue']."\n";

            return $this->jsonSuccess('Report generated and ready for export (Simulated).', [
                'type' => $type,
                'date' => $date,
                'filename' => "hms_report_{$type}_{$date}.csv",
                'content' => $fileContent,
                'download_url' => url('/storage/reports/'."hms_report_{$type}_{$date}.csv"),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to export report: '.$e->getMessage(), 500);
        }
    }
}
