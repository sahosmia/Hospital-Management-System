@extends('public.layout')

@section('title', 'Our Clinical Services')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-8">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Clinical Services</h2>
            <p class="text-sm text-gray-500">Learn about our primary healthcare, preventative checkups, and surgical services.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($services as $srv)
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center justify-center h-10 w-10 bg-brand-50 text-brand-600 rounded-lg">
                            🏥
                        </span>
                        <div>
                            <h4 class="font-bold text-gray-900 text-base">{{ $srv->name }}</h4>
                            <p class="text-xs text-brand-600 font-bold capitalize mt-1">{{ $srv->department?->name }} Wing</p>
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">{{ $srv->description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $services->links() }}
        </div>
    </section>
@endsection
