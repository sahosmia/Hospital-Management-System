@extends('public.layout')

@section('title', $doctor->user?->name)

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Side: Doctor Info card -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg border border-gray-200 p-6 text-center space-y-4 shadow-xs">
                    <div class="h-24 w-24 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-extrabold text-3xl mx-auto uppercase">
                        {{ substr($doctor->user?->name ?? 'D', 0, 2) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $doctor->user?->name }}</h3>
                        <p class="text-xs text-brand-600 font-bold capitalize mt-1">{{ $doctor->specialization }} Specialist</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $doctor->department?->name }} Department</p>
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center text-xs">
                        <span class="text-gray-400">Consultation Fee</span>
                        <span class="font-extrabold text-brand-600 text-sm">${{ number_format($doctor->consultation_fee, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400">Average Rating</span>
                        <span class="font-bold text-amber-500">★ {{ number_format($ratingsAvg, 1) }} / 5.0</span>
                    </div>

                    <a href="/auth/login" class="block pt-2">
                        <button class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-2.5 rounded-md transition-colors shadow-xs">
                            Book Live Consultation
                        </button>
                    </a>
                </div>

                <!-- Related Doctors -->
                @if(count($relatedDoctors) > 0)
                    <div class="space-y-4">
                        <h4 class="font-bold text-gray-900 text-sm">Other Specialists in {{ $doctor->department?->name }}</h4>
                        <div class="space-y-3">
                            @foreach($relatedDoctors as $rel)
                                <a href="/doctor/{{ $rel->id }}" class="block p-4 bg-white hover:bg-brand-50 rounded-lg border border-gray-200 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($rel->user?->name ?? 'D', 0, 2) }}
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-gray-900">{{ $rel->user?->name }}</h5>
                                            <p class="text-[10px] text-gray-400">{{ $rel->specialization }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Weekly Schedule & Patient Feedback reviews list -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Weekly schedule table -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        📅 Weekly Consulting Schedule
                    </h3>
                    @if(count($schedules) === 0)
                        <p class="text-xs text-gray-500">No work schedules defined for this doctor.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($schedules as $sch)
                                <div class="p-4 bg-gray-50 rounded-lg flex justify-between items-center text-xs">
                                    <span class="font-bold text-gray-800 capitalize">{{ $sch->day_of_week }}</span>
                                    <span class="text-gray-500">{{ date('H:i A', strtotime($sch->start_time)) }} - {{ date('H:i A', strtotime($sch->end_time)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Reviews lists -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Patient Feedbacks ({{ $ratingsCount }})</h3>
                    @if(count($reviews) === 0)
                        <div class="bg-white rounded-lg border border-gray-200 p-6 text-center text-gray-400 text-xs">
                            No approved patient feedbacks recorded yet for this specialist.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($reviews as $rev)
                                <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-3">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="font-bold text-gray-900">
                                            @if($rev->is_anonymous)
                                                Anonymous Patient
                                            @else
                                                {{ $rev->patient?->name }}
                                            @endif
                                        </span>
                                        <span class="text-amber-500 font-bold">★ {{ $rev->rating }} / 5</span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed italic">
                                        "{{ $rev->review }}"
                                    </p>

                                    <!-- Doctor reply if any -->
                                    @if($rev->doctor_reply)
                                        <div class="mt-3 p-3 bg-brand-50 border-l-4 border-brand-600 rounded text-[11px] text-brand-900 leading-relaxed">
                                            <span class="block font-bold">Response from {{ $doctor->user?->name }}:</span>
                                            <p class="mt-1">"{{ $rev->doctor_reply }}"</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
@endsection
