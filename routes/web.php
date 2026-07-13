<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- GUEST/PUBLIC SEO BLADE VIEWS (No login required) ---
Route::get('/', [PublicController::class, 'index']);
Route::get('/doctors', [PublicController::class, 'doctors']);
Route::get('/doctor/{id}', [PublicController::class, 'doctorProfile']);
Route::get('/about', [PublicController::class, 'about']);
Route::get('/contact', [PublicController::class, 'contact']);
Route::post('/contact', [PublicController::class, 'contactSubmit']);
Route::get('/services', [PublicController::class, 'services']);
Route::get('/services/{id}', [PublicController::class, 'serviceDetails']);
Route::get('/departments', [PublicController::class, 'departments']);
Route::get('/news', [PublicController::class, 'news']);
Route::get('/news/{id}', [PublicController::class, 'newsDetails']);
Route::get('/faq', [PublicController::class, 'faq']);
Route::get('/terms', [PublicController::class, 'terms']);
Route::get('/privacy', [PublicController::class, 'privacy']);
Route::get('/login', [PublicController::class, 'login']);
Route::get('/register', [PublicController::class, 'register']);


// --- SECURE REACT PORTALS (Inertia.js pages) ---

// Authentication Pages
Route::prefix('auth')->group(function () {
    Route::get('login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');

    Route::get('otp-verify', function () {
        return Inertia::render('Auth/OTPVerification');
    });

    Route::get('admin-login', function () {
        return Inertia::render('Auth/AdminLogin');
    });
    
    Route::get('register', function () {
        return Inertia::render('Auth/Login'); // Registered patients can log in via OTP
    });
});

// Patient Views
Route::prefix('patient')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Patient/Dashboard');
    });
    Route::get('appointments', function () {
        return Inertia::render('Patient/Appointments');
    });
    Route::get('book', function () {
        return Inertia::render('Patient/BookAppointment');
    });
    Route::get('history', function () {
        return Inertia::render('Patient/MedicalHistory');
    });
    Route::get('bills', function () {
        return Inertia::render('Patient/Bills');
    });
    Route::get('profile', function () {
        return Inertia::render('Patient/Profile');
    });
});

// Doctor Views
Route::prefix('doctor')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Patient/Dashboard');
    });
    Route::get('profile', function () {
        return Inertia::render('Doctor/Profile');
    });
    Route::get('reviews', function () {
        return Inertia::render('Doctor/ReviewForm');
    });
});

// Nurse Views
Route::prefix('nurse')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Nurse/Dashboard');
    });
});

// Admin Views
Route::prefix('admin')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    });
    Route::get('patients', function () {
        return Inertia::render('Admin/PatientManagement');
    });
    Route::get('beds', function () {
        return Inertia::render('Admin/BedManagement');
    });
    Route::get('surgeries', function () {
        return Inertia::render('Admin/SurgeryScheduler');
    });
    Route::get('inventory', function () {
        return Inertia::render('Admin/InventoryManagement');
    });
    Route::get('billing', function () {
        return Inertia::render('Admin/Billing');
    });
    Route::get('reports', function () {
        return Inertia::render('Admin/Reports');
    });
    Route::get('settings', function () {
        return Inertia::render('Admin/SystemSettings');
    });
    Route::get('users', function () {
        return Inertia::render('Admin/UserManagement');
    });
});
