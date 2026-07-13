@extends('public.layout')

@section('title', 'Meet Our Specialists')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-8">
        <div class="space-y-2">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Meet Our Specialists</h2>
            <p class="text-sm text-gray-500">Book direct consultations with our board-certified clinical experts.</p>
        </div>

        <!-- Search Filter Block -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs flex flex-col md:flex-row gap-6 items-end justify-between">
            <form action="/doctors" method="GET" class="w-full grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Search Doctors</label>
                    <input type="text" name="search" placeholder="e.g. Elizabeth" value="{{ request('search') }}" class="w-full text-sm border border-gray-300 rounded px-3 py-2 focus:ring-brand-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Filter Specialty</label>
                    <select name="specialty" class="w-full text-sm border border-gray-300 rounded px-3 py-2 focus:ring-brand-600">
                        <option value="">All Specialties</option>
                        @foreach($specialties as $spec)
                            <option value="{{ $spec->id }}" {{ request('specialty') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-2 rounded shadow-xs transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Doctors directory list -->
        <div class="space-y-6">
            @if(count($doctors) === 0)
                <div class="bg-white rounded-lg border border-gray-200 p-12 text-center text-gray-500">
                    No matching specialists found. Please clear or refine your filters.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($doctors as $doc)
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <div class="h-16 w-14 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-2xl uppercase">
                                        {{ substr($doc->user?->name ?? 'D', 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-gray-900 text-base">{{ $doc->user?->name }}</h4>
                                        <p class="text-xs text-brand-600 font-bold capitalize">{{ $doc->specialization }} SPECIALIST &bull; {{ $doc->department?->name }}</p>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Offers comprehensive, patient-centered diagnostics and therapy. Consulting chamber located at {{ $doc->chamber_location || 'Main Wing OPD' }}.
                                </p>

                                <div class="pt-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                                    <span>Experience: <strong class="text-gray-800">{{ $doc->experience_years }} Years</strong></span>
                                    <span>Consultation Fee: <strong class="text-brand-600">${{ number_format($doc->consultation_fee, 2) }}</strong></span>
                                </div>

                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span>Rating: 
                                        <strong class="text-amber-500">★ {{ number_format($doc->reviews()->avg('rating') ?? 4.8, 1) }}</strong> 
                                        ({{ $doc->reviews()->count() }} reviews)
                                    </span>
                                    
                                    @php
                                        // Check availability on today's day
                                        $isAvailableToday = \App\Models\DoctorSchedule::where('doctor_id', $doc->user_id)
                                            ->where('day_of_week', $todayDay)
                                            ->where('is_available', true)
                                            ->exists();
                                    @endphp
                                    <span>Today's Status: 
                                        @if($isAvailableToday)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800">Available</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">No Chambers</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <a href="/doctor/{{ $doc->id }}" class="block mt-6">
                                <button class="w-full bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold py-2.5 rounded transition-colors text-center">
                                    View Schedules & Patient Feedback &rarr;
                                </button>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination links -->
                <div class="pt-4">
                    {{ $doctors->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
