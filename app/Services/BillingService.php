<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\FinancialTransaction;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;

class BillingService
{
    /**
     * Generate an aggregated bill statement for an admission.
     */
    public function generateBill(int $admissionId): array
    {
        $admission = Admission::with(['patient', 'doctor', 'bed'])->findOrFail($admissionId);

        $transactions = FinancialTransaction::where('admission_id', $admissionId)
            ->whereIn('status', ['posted', 'approved'])
            ->get();

        $totalDebits = $transactions->where('transaction_type', 'debit')->sum('net_amount');
        $totalCredits = $transactions->where('transaction_type', 'credit')->sum('net_amount');

        $discountSetting = $transactions->sum('discount');

        $taxRate = (float) (SystemSetting::where('key', 'tax_rate')->first()?->value ?? 5.0);
        $subtotal = $totalDebits - $totalCredits;
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $totalDue = max(0, $subtotal + $taxAmount);

        return [
            'admission' => $admission,
            'transactions' => $transactions,
            'summary' => [
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'discount' => $discountSetting,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_due' => $totalDue,
            ],
        ];
    }

    /**
     * Process a payment towards an admission bill.
     */
    public function processPayment(array $data): FinancialTransaction
    {
        $txNumber = 'TXN-'.Carbon::today()->format('Ymd').'-'.rand(10000, 99999);
        $postedBy = $data['posted_by'] ?? User::where('role', 'cashier')->first()?->id ?? 1;

        return FinancialTransaction::create([
            'admission_id' => $data['admission_id'],
            'transaction_number' => $txNumber,
            'transaction_type' => 'credit',
            'category' => 'payment',
            'sub_category' => $data['payment_method'] ?? 'cash', // cash, card, mobile banking, insurance
            'amount' => $data['amount'],
            'quantity' => 1,
            'unit_price' => $data['amount'],
            'discount' => $data['discount'] ?? 0,
            'net_amount' => $data['amount'] - ($data['discount'] ?? 0),
            'description' => $data['description'] ?? 'Bill payment processing',
            'posting_date' => Carbon::today()->toDateString(),
            'posting_time' => Carbon::now()->toTimeString(),
            'posted_by' => $postedBy,
            'status' => 'approved', // Payments are immediately approved
            'approved_by' => $postedBy,
            'approved_at' => Carbon::now(),
        ]);
    }
}
