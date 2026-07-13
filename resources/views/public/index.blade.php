@extends('public.layout')

@section('title', 'Welcome to St. Jude')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-brand-900 to-slate-900 text-white py-20 lg:py-28 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="max-w-xl space-y-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-600/30 text-blue-300 uppercase tracking-widest">
                    Emergency Care 24/7
                </span>
                <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight">
                    {{ $settings['hero_title'] }}
                </h1>
                <p class="text-slate-300 text-base leading-relaxed">
                    {{ $settings['hero_subtitle'] }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="/doctors" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm px-6 py-3.5 rounded-md shadow-md text-center transition-colors">
                        Book Doctor Appointment
                    </a>
                    <a href="/contact" class="border border-slate-700 hover:bg-slate-800 text-slate-100 font-semibold text-sm px-6 py-3.5 rounded-md text-center transition-colors">
                        Inquire Online
                    </a>
                </div>
            </div>

            <!-- Hero Image / Brand graphic -->
            <div class="hidden lg:block w-1/2 max-w-md relative">
                <div class="aspect-square bg-slate-800 rounded-2xl flex items-center justify-center p-8 border border-slate-700 shadow-xl">
                    <img src="{{ $settings['hero_image'] }}" alt="Hospital" class="max-h-72">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Counters row -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 grid grid-cols-2 lg:grid-cols-4 gap-8 p-6 text-center">
            <div class="p-4 border-r border-gray-100 last:border-0">
                <span class="block text-3xl font-extrabold text-brand-900">{{ $counts['doctors'] }}</span>
                <span class="block text-xs font-bold text-gray-400 uppercase mt-1">Specialists</span>
            </div>
            <div class="p-4 border-r border-gray-100 last:border-0">
                <span class="block text-3xl font-extrabold text-brand-900">{{ $counts['departments'] }}</span>
                <span class="block text-xs font-bold text-gray-400 uppercase mt-1">Departments</span>
            </div>
            <div class="p-4 border-r border-gray-100 last:border-0">
                <span class="block text-3xl font-extrabold text-brand-900">{{ $counts['patients'] }}</span>
                <span class="block text-xs font-bold text-gray-400 uppercase mt-1">Patients Served</span>
            </div>
            <div class="p-4">
                <span class="block text-3xl font-extrabold text-brand-900">{{ $counts['appointments'] }}</span>
                <span class="block text-xs font-bold text-gray-400 uppercase mt-1">Appointments Completed</span>
            </div>
        </div>
    </section>

    <!-- Emergency Hotline Callout -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <div class="bg-rose-50 border border-rose-100 rounded-lg p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xs">
            <div class="space-y-1">
                <h4 className="text-rose-900 font-bold text-lg flex items-center">
                    🚨 Emergency Trauma Support
                </h4>
                <p class="text-xs text-rose-700">
                    If you require immediate medical admission, call the hotline. Available {{ $settings['emergency_hours'] }}.
                </p>
            </div>
            <a href="tel:{{ $settings['emergency_phone'] }}" class="text-2xl font-extrabold text-rose-600 bg-white hover:bg-rose-100 px-6 py-3 rounded-lg border border-rose-200 transition-colors shadow-sm">
                {{ $settings['emergency_phone'] }}
            </a>
        </div>
    </section>

    <!-- Notices board -->
    @if(count($notices) > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 space-y-6">
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Active Notices</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($notices as $notice)
                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 uppercase mb-3">Notice</span>
                        <h4 class="font-bold text-gray-900 mb-2">{{ $notice->title }}</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $notice->content }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Services Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 space-y-8">
        <div class="flex justify-between items-end">
            <div class="space-y-1">
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Our Core Services</h3>
                <p class="text-xs text-gray-400">Discover what we provide across our departments.</p>
            </div>
            <a href="/services" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View All Services &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($services as $srv)
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs space-y-4 hover:shadow-md transition-shadow">
                    <span class="inline-flex items-center justify-center h-10 w-10 bg-brand-50 text-brand-600 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $srv->name }}</h4>
                        <p class="text-xs text-gray-500 mt-2 capitalize font-semibold">Dept: {{ $srv->department?->name }}</p>
                        <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $srv->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Doctors -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 space-y-8">
        <div class="flex justify-between items-end">
            <div class="space-y-1">
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Featured Specialists</h3>
                <p class="text-xs text-gray-400">Consult with certified professional medical practitioners.</p>
            </div>
            <a href="/doctors" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View Active Rosters &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($featuredDoctors as $doc)
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-lg uppercase">
                                {{ substr($doc->user?->name ?? 'D', 0, 2) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">{{ $doc->user?->name }}</h4>
                                <p class="text-xs text-gray-400 font-semibold">{{ $doc->specialization }} &bull; {{ $doc->department?->name }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 italic">"Dedicated to patient-centered clinical care and innovative therapeutics."</p>
                    </div>
                    <div class="border-t border-gray-100 mt-4 pt-3 flex justify-between items-center text-xs">
                        <span class="font-bold text-brand-600">${{ number_format($doc->consultation_fee, 2) }}</span>
                        <a href="/doctor/{{ $doc->id }}" class="text-xs font-bold text-brand-700 hover:text-brand-600">Schedules &rarr;</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Patient Testimonials -->
    @if(count($testimonials) > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 space-y-6">
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight text-center">Loved by Patients</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                @foreach($testimonials as $test)
                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 text-sm">{{ $test->patient_name }}</span>
                            <span class="text-xs font-bold text-amber-500 uppercase">{{ $test->patient_title }}</span>
                        </div>
                        <p class="text-xs text-gray-500 italic leading-relaxed">
                            "{{ $test->comment }}"
                        </p>
                        <div class="flex text-amber-400 text-xs">
                            @for($i=1; $i<=$test->rating; $i++)
                                ★
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
