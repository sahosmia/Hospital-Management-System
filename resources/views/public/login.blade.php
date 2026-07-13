@extends('public.layout')

@section('title', 'Sign In')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-gray-900">
                    Sign in to Portal
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Choose your portal access below
                </p>
            </div>

            <div class="space-y-4 pt-6">
                <!-- Option 1: Patient Portal (OTP login) -->
                <div class="p-6 bg-brand-50 border border-brand-100 rounded-lg space-y-3">
                    <h4 class="font-bold text-brand-900 text-sm">Patients Secured Portal</h4>
                    <p class="text-xs text-brand-700 leading-relaxed">
                        Access your appointments, view clinic reports, pay bills, and rate specialists. Requires SMS OTP validation.
                    </p>
                    <a href="/auth/login" class="block w-full text-center bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2.5 rounded transition-colors shadow-sm">
                        Enter Patient Access &rarr;
                    </a>
                </div>

                <!-- Option 2: Staff / Admin login -->
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                    <h4 class="font-bold text-slate-900 text-sm">Staff & Admins Portal</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        For consulting physicians, ward nurses, receptionists, cashiers, and system administrators.
                    </p>
                    <a href="/auth/admin-login" class="block w-full text-center border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold py-2.5 rounded transition-colors">
                        Enter Staff Access &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
