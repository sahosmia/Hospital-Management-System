@extends('public.layout')

@section('title', 'Patient Registration')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-gray-900">
                    Register Patient Profile
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Create a secure health profile and book appointments instantly
                </p>
            </div>

            <!-- Redirect to interactive register -->
            <div class="p-6 bg-brand-50 border border-brand-100 rounded-lg space-y-4">
                <h4 class="font-bold text-brand-900 text-sm">Interactive Patient Portal</h4>
                <p class="text-xs text-brand-700 leading-relaxed">
                    Create your health account inside our interactive portal. Supports secure medical records, emergency contacts, and direct doctor consultations.
                </p>
                <a href="/auth/register" class="block w-full text-center bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2.5 rounded transition-colors shadow-sm">
                    Open Register Panel &rarr;
                </a>
            </div>

            <!-- Policy Compliance notices -->
            <div class="space-y-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
                <div class="space-y-1">
                    <span class="font-bold text-gray-700">Terms of Services:</span>
                    <p class="leading-relaxed italic">"{{ $terms }}"</p>
                </div>
                <div class="space-y-1">
                    <span class="font-bold text-gray-700">Patient Confidentiality:</span>
                    <p class="leading-relaxed italic">"{{ $privacy }}"</p>
                </div>
            </div>
        </div>
    </div>
@endsection
