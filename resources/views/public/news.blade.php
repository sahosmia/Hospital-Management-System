@extends('public.layout')

@section('title', 'Hospital News & Blogs')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-8">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Clinical Blogs & Notices</h2>
            <p class="text-sm text-gray-500">Read daily updates from our hospital specialists, health tips, and wellness bulletins.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- News list -->
            <div class="lg:col-span-3 space-y-6">
                @if(count($newsList) === 0)
                    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center text-gray-400 text-sm">
                        No articles published yet. Check back shortly.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($newsList as $news)
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow">
                                @if($news->image_url)
                                    <div class="h-48 bg-slate-100 flex items-center justify-center p-6 border-b border-gray-100">
                                        <img src="{{ $news->image_url }}" alt="Blog Banner" class="max-h-36">
                                    </div>
                                @endif
                                <div class="p-6 space-y-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 uppercase">
                                        {{ $news->category?->name }}
                                    </span>
                                    <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $news->title }}</h4>
                                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">
                                        {{ $news->content }}
                                    </p>
                                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center text-[10px] text-gray-400">
                                        <span>Posted on {{ $news->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4">
                        {{ $newsList->links() }}
                    </div>
                @endif
            </div>

            <!-- Categories Side panel -->
            <div class="space-y-6">
                <Card class="p-6 space-y-4">
                    <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider">News Categories</h4>
                    <div class="space-y-2">
                        @foreach($categories as $cat)
                            <div class="flex justify-between items-center text-xs text-gray-600">
                                <span class="font-semibold">{{ $cat->name }}</span>
                                <span class="bg-gray-100 px-2 py-0.5 rounded-full text-[10px] font-bold text-gray-500">{{ $cat->news_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </Card>
            </div>
        </div>
    </section>
@endsection
