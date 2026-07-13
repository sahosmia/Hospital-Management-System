<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SurgeryController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC AUTH ROUTES ---
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('otp/request', [AuthController::class, 'otpRequest']);
    Route::post('otp/verify', [AuthController::class, 'otpVerify']);
    Route::get('google', [AuthController::class, 'googleLogin']);
    Route::get('google/callback', [AuthController::class, 'googleCallback']);
});

// --- PROTECTED ROUTES (Require Authentication) ---
Route::middleware('auth:sanctum')->group(function () {

    // Auth profile
    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // Patient Management
    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->middleware('role:super_admin,hospital_admin,doctor,nurse,receptionist');
        Route::get('search', [PatientController::class, 'search'])->middleware('role:super_admin,hospital_admin,doctor,nurse,receptionist');
        Route::get('{id}', [PatientController::class, 'show'])->middleware('role:super_admin,hospital_admin,doctor,nurse,receptionist,patient');
        Route::post('/', [PatientController::class, 'store'])->middleware('role:super_admin,hospital_admin,receptionist');
        Route::put('{id}', [PatientController::class, 'update'])->middleware('role:super_admin,hospital_admin,receptionist,patient');
        Route::get('{id}/history', [PatientController::class, 'history'])->middleware('role:super_admin,hospital_admin,doctor,nurse,patient');
    });

    // Doctor Details
    Route::prefix('doctors')->group(function () {
        Route::get('/', [DoctorController::class, 'index']);
        Route::get('{id}', [DoctorController::class, 'show']);
        Route::get('{id}/schedule', [DoctorController::class, 'schedule']);
        Route::get('{id}/slots', [DoctorController::class, 'slots']);
        Route::get('{id}/reviews', [DoctorController::class, 'reviews']);
        Route::get('{id}/rating', [DoctorController::class, 'rating']);
    });

    // Appointment Booking
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::put('{id}', [AppointmentController::class, 'update']);
        Route::delete('{id}', [AppointmentController::class, 'destroy']);
        Route::get('available-slots', [AppointmentController::class, 'availableSlots']);
    });

    // Admission & Bed Allocation
    Route::prefix('admissions')->group(function () {
        Route::get('/', [AdmissionController::class, 'index'])->middleware('role:super_admin,hospital_admin,doctor,nurse,receptionist');
        Route::get('active', [AdmissionController::class, 'active'])->middleware('role:super_admin,hospital_admin,doctor,nurse,receptionist');
        Route::post('/', [AdmissionController::class, 'store'])->middleware('role:super_admin,hospital_admin,receptionist');
        Route::put('{id}', [AdmissionController::class, 'update'])->middleware('role:super_admin,hospital_admin,receptionist');
        Route::put('{id}/discharge', [AdmissionController::class, 'discharge'])->middleware('role:super_admin,hospital_admin,receptionist');
        Route::put('{id}/transfer', [AdmissionController::class, 'transfer'])->middleware('role:super_admin,hospital_admin,nurse,receptionist');
    });

    // Bed Management
    Route::prefix('beds')->group(function () {
        Route::get('/', [BedController::class, 'index']);
        Route::get('available', [BedController::class, 'available']);
        Route::get('occupancy', [BedController::class, 'occupancy']);
        Route::post('/', [BedController::class, 'store'])->middleware('role:super_admin,hospital_admin');
        Route::put('{id}', [BedController::class, 'update'])->middleware('role:super_admin,hospital_admin,nurse');
    });

    // Medication & Prescriptions
    Route::prefix('medications')->group(function () {
        Route::get('today', [MedicationController::class, 'today'])->middleware('role:super_admin,hospital_admin,doctor,nurse');
        Route::post('order', [MedicationController::class, 'order'])->middleware('role:super_admin,hospital_admin,doctor');
        Route::post('administer', [MedicationController::class, 'administer'])->middleware('role:super_admin,hospital_admin,nurse');
        Route::get('{id}/history', [MedicationController::class, 'history'])->middleware('role:super_admin,hospital_admin,doctor,nurse,patient');
    });

    // Surgery & OT Operations
    Route::prefix('surgeries')->group(function () {
        Route::get('/', [SurgeryController::class, 'index'])->middleware('role:super_admin,hospital_admin,doctor,nurse');
        Route::get('today', [SurgeryController::class, 'today'])->middleware('role:super_admin,hospital_admin,doctor,nurse');
        Route::get('cost-report', [SurgeryController::class, 'costReport'])->middleware('role:super_admin,hospital_admin');
        Route::post('/', [SurgeryController::class, 'store'])->middleware('role:super_admin,hospital_admin,doctor');
        Route::put('{id}/start', [SurgeryController::class, 'start'])->middleware('role:super_admin,hospital_admin,doctor');
        Route::put('{id}/complete', [SurgeryController::class, 'complete'])->middleware('role:super_admin,hospital_admin,doctor');
    });

    // Inventory & Supplies
    Route::prefix('inventory')->group(function () {
        Route::get('supplies', [InventoryController::class, 'supplies']);
        Route::post('supplies', [InventoryController::class, 'store'])->middleware('role:super_admin,hospital_admin');
        Route::put('supplies/{id}', [InventoryController::class, 'update'])->middleware('role:super_admin,hospital_admin');
        Route::get('low-stock', [InventoryController::class, 'lowStock']);
        Route::get('expiring', [InventoryController::class, 'expiring']);
        Route::get('requests', [InventoryController::class, 'requests']);
        Route::post('requests', [InventoryController::class, 'request']);
        Route::put('requests/{id}/approve', [InventoryController::class, 'approve'])->middleware('role:super_admin,hospital_admin');
        Route::put('requests/{id}/fulfill', [InventoryController::class, 'fulfill'])->middleware('role:super_admin,hospital_admin');
    });

    // Billing & Finance
    Route::prefix('bills')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->middleware('role:super_admin,hospital_admin,cashier');
        Route::post('generate/{admissionId}', [BillingController::class, 'generate'])->middleware('role:super_admin,hospital_admin,cashier');
        Route::post('payment', [BillingController::class, 'payment'])->middleware('role:super_admin,hospital_admin,cashier');
        Route::get('patient/{patientId}', [BillingController::class, 'patientBills']);
        Route::get('download/{id}', [BillingController::class, 'downloadPDF']);
    });

    // Reviews moderation
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::get('pending', [ReviewController::class, 'pending'])->middleware('role:super_admin,hospital_admin');
        Route::post('/', [ReviewController::class, 'store'])->middleware('role:patient');
        Route::put('{id}', [ReviewController::class, 'approve'])->middleware('role:super_admin,hospital_admin');
        Route::post('{id}/flag', [ReviewController::class, 'flag']);
        Route::post('{id}/reply', [ReviewController::class, 'reply'])->middleware('role:doctor');
    });

    // Reports & Analytics
    Route::prefix('reports')->group(function () {
        Route::get('daily', [ReportController::class, 'daily'])->middleware('role:super_admin,hospital_admin');
        Route::get('monthly', [ReportController::class, 'monthly'])->middleware('role:super_admin,hospital_admin');
        Route::get('revenue', [ReportController::class, 'revenue'])->middleware('role:super_admin,hospital_admin');
        Route::get('download/{type}/{date}', [ReportController::class, 'download'])->middleware('role:super_admin,hospital_admin');
    });

    // Admin Operations
    Route::prefix('admin')->middleware('role:super_admin,hospital_admin')->group(function () {
        Route::get('users', [AdminController::class, 'users']);
        Route::post('users', [AdminController::class, 'storeUser']);
        Route::put('users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('users/{id}', [AdminController::class, 'destroyUser']);
        Route::get('audit-logs', [AdminController::class, 'auditLogs']);
        Route::get('system-settings', [AdminController::class, 'settings']);
        Route::put('system-settings', [AdminController::class, 'updateSettings']);
        Route::post('backup', [AdminController::class, 'backup']);
        Route::get('system-health', [AdminController::class, 'systemHealth']);
    });
});
