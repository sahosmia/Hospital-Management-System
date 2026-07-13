@extends('public.layout')

@section('title', 'About Us')

@section('content')
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-12">
        <div class="space-y-4 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">About Our Hospital</h2>
            <p class="text-sm text-gray-500">Founded in {{ $settings['hospital_established'] }} &bull; Committed to medical breakthroughs and patient-centered service.</p>
        </div>

        <!-- Description card -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-xs leading-relaxed space-y-4">
            <p class="text-sm text-gray-600">
                {{ $settings['hospital_description'] }}
            </p>
        </div>

        <!-- Mission / Vision grids -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-brand-50 border border-brand-100 rounded-lg space-y-2">
                <h4 class="font-bold text-brand-900 text-sm">🎯 Our Mission</h4>
                <p class="text-xs text-brand-800 leading-relaxed">{{ $settings['mission'] }}</p>
            </div>
            <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-lg space-y-2">
                <h4 class="font-bold text-indigo-900 text-sm">👁 Our Vision</h4>
                <p class="text-xs text-indigo-800 leading-relaxed">{{ $settings['vision'] }}</p>
            </div>
        </div>

        <!-- Core values banner -->
        <div class="p-6 bg-slate-900 text-slate-100 rounded-lg text-center space-y-2">
            <h4 class="text-sm font-bold uppercase tracking-wider text-brand-600">Our Core Values</h4>
            <p class="text-xs text-slate-300 leading-relaxed">{{ $settings['core_values'] }}</p>
        </div>

        <!-- Facilities -->
        <div class="space-y-4">
            <h3 class="text-xl font-bold text-gray-900">Modern Facilities & Clinical Infrastructure</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($facilities as $fac)
                    <div class="p-4 bg-white border border-gray-200 rounded-lg flex items-start space-x-3">
                        <span class="p-2.5 bg-brand-50 text-brand-600 rounded">
                            🏛
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">{{ $fac->name }}</h4>
                            <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $fac->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Awards -->
        <div class="space-y-4">
            <h3 class="text-xl font-bold text-gray-900">Recognition & Awards</h3>
            <div class="space-y-3">
                @foreach($awards as $awd)
                    <div class="p-4 bg-white border border-gray-200 rounded-lg flex justify-between items-center text-xs">
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $awd->title }}</h4>
                            <p class="text-gray-400 mt-1">Conferred by {{ $awd->organization }}</p>
                        </div>
                        <span class="font-bold text-brand-600">{{ $awd->year }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
