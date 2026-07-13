@extends('public.layout')

@section('title', 'Frequently Asked Questions')

@section('content')
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-8">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Frequently Asked Questions</h2>
            <p class="text-sm text-gray-500">Find answers to common support queries, billing, and clinical admission workflows.</p>
        </div>

        <div class="space-y-8">
            @foreach($categories as $cat)
                @if(count($cat->faqs) > 0)
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-brand-900 border-b border-gray-200 pb-2 capitalize">{{ $cat->name }}</h3>
                        <div class="space-y-4">
                            @foreach($cat->faqs as $faq)
                                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs space-y-2">
                                    <h4 class="font-bold text-gray-900 text-sm">Q: {{ $faq->question }}</h4>
                                    <p class="text-xs text-gray-500 leading-relaxed pl-5">
                                        {{ $faq->answer }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
@endsection
