@extends('frontsite.layouts.master')
@section('title', $notice->title_gu)
@section('meta_description', Str::limit($notice->content_gu ?? $notice->title_gu, 160))
@section('content')
<section class="py-12 md:py-16 text-white" style="background: linear-gradient(135deg, var(--theme-accent), #ea580c);">
    <div class="max-w-7xl mx-auto px-4">
        <a href="{{ route('frontsite.notices.index') }}" class="inline-flex items-center gap-1 text-white-80 hover:text-white transition text-sm mb-3">
            <i class="lni lni-arrow-left text-xs"></i> બધી સૂચનાઓ પર પાછા જાઓ
        </a>
        <h1 class="text-2xl md:text-4xl font-bold">{{ $notice->title_gu }}</h1>
        <p class="text-white-70 text-sm mt-1">
            <i class="lni lni-calendar"></i> {{ $notice->date->locale('gu')->isoFormat('D MMMM, YYYY') }}
        </p>
    </div>
</section>

<section class="py-12 md:py-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
            @if(!empty(trim($notice->content_gu ?? '')))
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                {{ $notice->content_gu }}
            </div>
            @else
            <p class="text-gray-400">વિગતો ઉપલબ્ધ નથી.</p>
            @endif

            @if($notice->file_path)
            <div class="mt-8 pt-6 border-t border-gray-100">
                <a href="{{ asset('storage/' . $notice->file_path) }}" target="_blank"
                   class="bg-accent-light text-accent inline-flex items-center gap-2 font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="lni lni-download-1"></i> જોડાયેલ ફાઇલ ડાઉનલોડ કરો
                </a>
            </div>
            @endif
        </div>

        @if($recent->isNotEmpty())
        <div class="mt-12">
            <h3 class="text-lg font-bold text-gray-900 mb-4">તાજી સૂચનાઓ</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($recent as $r)
                <a href="{{ route('frontsite.notices.show', $r->id) }}"
                   class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-4 flex items-start gap-3 group">
                    <div class="w-12 h-12 bg-accent-light rounded-xl flex flex-col items-center justify-center flex-shrink-0 border-accent">
                        <span class="text-base font-bold text-accent leading-none">{{ $r->date->format('d') }}</span>
                        <span class="text-[9px] font-medium leading-tight" style="color: var(--theme-accent); opacity: 0.7;">{{ $r->date->locale('gu')->isoFormat('MMM') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 transition text-sm group-hover:text-[var(--theme-accent)]">{{ $r->title_gu }}</h4>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
