@extends('public.layout')

@section('title', 'Specialist Departments')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-8">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Specialist Departments</h2>
            <p class="text-sm text-gray-500">Explore our dedicated diagnostic and clinical wards staffed with top healthcare experts.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($departments as $dept)
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-gray-900">{{ $dept->name }}</h3>
                            <Badge type="info">{{ $dept->doctors()->count() }} Physicians</Badge>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $dept->description }}</p>

                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Our Doctors in Department</h4>
                            <div class="space-y-2">
                                @forelse($dept->doctors as $doc)
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="font-bold text-gray-800">{{ $doc->user?->name }}</span>
                                        <span class="text-gray-400 text-[10px]">{{ $doc->specialization }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-gray-400 italic">No specialist assigned.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
