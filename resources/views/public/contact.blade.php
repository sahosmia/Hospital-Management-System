@extends('public.layout')

@section('title', 'Contact Us')

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <!-- Left Side: Contact details, hours, links -->
            <div class="space-y-8">
                <div class="space-y-2">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Get in Touch</h2>
                    <p class="text-sm text-gray-500">Reach out for any administrative inquiries, appointments questions, or operational support.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <span class="p-2.5 bg-brand-50 text-brand-600 rounded">📍</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Hospital Address</h4>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $settings['address'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <span class="p-2.5 bg-brand-50 text-brand-600 rounded">📞</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Direct Contact lines</h4>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $settings['phone'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <span class="p-2.5 bg-brand-50 text-brand-600 rounded">✉️</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Email Channels</h4>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $settings['email'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Working hours -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Opening & Consulting Hours</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($workingHours as $hr)
                            <div class="p-3 bg-white border border-gray-200 rounded flex justify-between items-center text-xs">
                                <span class="font-semibold text-gray-700">{{ $hr->day }}</span>
                                <span class="text-gray-500">{{ $hr->hours }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact form -->
            <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-xs h-fit space-y-6">
                <h3 class="text-lg font-bold text-gray-900">Submit Inpatient Enquiry</h3>

                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded text-emerald-800 text-xs font-semibold">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/contact" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label>
                        <input type="text" name="name" placeholder="e.g. Alan Turing" required class="w-full text-xs border border-gray-300 rounded px-3 py-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="e.g. alan@example.com" required class="w-full text-xs border border-gray-300 rounded px-3 py-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Subject</label>
                        <input type="text" name="subject" placeholder="Inquiry subject..." class="w-full text-xs border border-gray-300 rounded px-3 py-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Detailed Message</label>
                        <textarea name="message" rows="4" placeholder="Briefly specify your query..." required class="w-full text-xs border border-gray-300 rounded px-3 py-2.5"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-3 rounded transition-colors shadow-sm">
                        Submit Contact Inquiry
                    </button>
                </form>
            </div>

        </div>
    </section>
@endsection
