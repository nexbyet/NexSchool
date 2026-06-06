@extends('frontsite.layouts.master')
@section('title', 'સૂચનાઓ')
@section('meta_description', 'શાળાની તમામ સૂચનાઓ')
@section('content')
<section class="py-12 md:py-16 text-white" style="background: linear-gradient(135deg, var(--theme-accent), #ea580c);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-4xl font-bold">સૂચનાઓ</h1>
        <p class="text-white-80 text-sm mt-1">શાળાની તમામ સૂચનાઓ અને સમાચાર</p>
    </div>
</section>

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        @if($notices->isEmpty())
        <div class="text-center py-16">
            <i class="lni lni-file-question text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-400">હાલમાં કોઈ સૂચના નથી</p>
        </div>
        @else
        <div class="grid md:grid-cols-2 gap-5">
            @foreach ($notices as $n)
            <a href="{{ route('frontsite.notices.show', $n->id) }}" class="bg-white rounded-xl border-accent shadow-sm hover:shadow-md transition p-5 flex items-start gap-4 group">
                <div class="w-16 h-16 bg-accent-light rounded-xl flex flex-col items-center justify-center flex-shrink-0 border-accent">
                    <span class="text-xl font-bold text-accent leading-none">{{ $n->date->format('d') }}</span>
                    <span class="text-[10px] font-medium leading-tight" style="color: var(--theme-accent); opacity: 0.7;">{{ $n->date->locale('gu')->isoFormat('MMM') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-semibold text-gray-900 group-hover:text-[var(--theme-accent)] transition">{{ $n->title_gu }}</h2>
                    @if($n->content_gu)<p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($n->content_gu, 120) }}</p>@endif
                    <span class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-accent transition">વધુ વાંચો <i class="lni lni-arrow-right text-[10px]"></i></span>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $notices->links() }}
        </div>
        @endif
    </div>
</section>
@endsection
