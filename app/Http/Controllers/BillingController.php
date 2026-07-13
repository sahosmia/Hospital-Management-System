<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\FinancialTransaction;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BillingController extends Controller
{
    public function __construct(protected BillingService $billingService) {}

    /**
     * List financial transactions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = FinancialTransaction::with('admission.patient');

            if ($request->has('category')) {
                $query->where('category', $request->input('category'));
            }

            if ($request->has('type')) {
                $query->where('transaction_type', $request->input('type'));
            }

            $txs = $query->latest()->paginate(15);

            return $this->jsonSuccess('Financial transactions retrieved successfully.', $txs);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch transactions: '.$e->getMessage(), 500);
        }
    }

    /**
     * Generate comprehensive bill for an admission.
     */
    public function generate(int $admissionId): JsonResponse
    {
        try {
            $bill = $this->billingService->generateBill($admissionId);

            return $this->jsonSuccess('Bill invoice generated successfully.', $bill);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to generate bill: '.$e->getMessage(), 500);
        }
    }

    /**
     * Process a credit payment.
     */
    public function payment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admission_id' => 'required|exists:admissions,id',
            'amount' => 'required|numeric|min:0.01',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile banking,insurance',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['posted_by'] = Auth::id();

            $tx = $this->billingService->processPayment($data);
            $this->logAudit('BILL_PAYMENT', 'financial_transactions', $tx->id, null, $tx->toArray());

            return $this->jsonSuccess('Payment processed and posted successfully.', $tx, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to process payment: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get billing details for a patient.
     */
    public function patientBills(int $patientId): JsonResponse
    {
        try {
            $admissions = Admission::where('patient_id', $patientId)
                ->with(['bed', 'doctor'])
                ->get();

            $bills = $admissions->map(function ($admission) {
                return $this->billingService->generateBill($admission->id);
            });

            return $this->jsonSuccess('Patient bills retrieved successfully.', $bills);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch patient bills: '.$e->getMessage(), 500);
        }
    }

    /**
     * Download Invoice as PDF (Simulated).
     */
    public function downloadPDF(int $id): JsonResponse
    {
        try {
            $bill = $this->billingService->generateBill($id);
            $invoiceContent = [
                'hospital_name' => 'St. Jude General Hospital',
                'admission_number' => $bill['admission']->admission_number,
                'patient_name' => $bill['admission']->patient?->name,
                'invoice_date' => date('Y-m-d'),
                'total_due' => $bill['summary']['total_due'],
                'pdf_url' => url('/storage/invoices/invoice_'.$id.'.pdf'),
            ];

            return $this->jsonSuccess('Invoice PDF generated and ready for download (Simulated).', $invoiceContent);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to download PDF: '.$e->getMessage(), 500);
        }
    }
}
