@extends('public.layout')

@section('title', 'Terms & Conditions')

@section('content')
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-6">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Terms of Service</h2>
        <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-xs leading-relaxed text-sm text-gray-600">
            <p>
                {{ $terms }}
            </p>
        </div>
    </section>
@endsection
