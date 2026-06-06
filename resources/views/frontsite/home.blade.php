@extends('frontsite.layouts.master')
@section('title', $school->school_name_gu ?? 'હોમ')
@push('styles')
<style>
.slide { transition: opacity 0.6s ease-in-out; }
.counter { transition: all 0.5s ease; }
</style>
@endpush
@section('content')

@foreach ($sections as $section)
@if ($section->type === 'slider')
@php $slides = App\Models\SliderItem::where('status', true)->orderBy('sort_order')->get(); @endphp
@if($slides->isNotEmpty())
<section class="relative overflow-hidden bg-gray-900" style="height:65vh;min-height:400px">
    @foreach ($slides as $i => $slide)
    <div class="slide absolute inset-0 {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-index="{{ $i }}">
        @if($slide->image)
        <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title_gu }}" class="w-full h-full object-cover">
        @else
        <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, var(--theme-header-from), #064e3b);">
            <div class="text-center text-white px-6">
                <h2 class="text-3xl md:text-5xl font-bold mb-3">{{ $slide->title_gu }}</h2>
                @if($slide->subtitle_gu)<p class="text-lg md:text-xl text-white-80">{{ $slide->subtitle_gu }}</p>@endif
            </div>
        </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent">
            <div class="absolute bottom-12 left-0 right-0 text-center text-white px-6">
                <h2 class="text-2xl md:text-4xl font-bold mb-2">{{ $slide->title_gu }}</h2>
                @if($slide->subtitle_gu)<p class="text-base md:text-lg text-white/80">{{ $slide->subtitle_gu }}</p>@endif
                @if($slide->link_url)<a href="{{ $slide->link_url }}" class="btn-primary inline-block mt-4 text-sm">વધુ જાણો</a>@endif
            </div>
        </div>
    </div>
    @endforeach
    <button onclick="prevSlide()" class="slider-btn left-3"><i class="lni lni-angle-double-left text-gray-700"></i></button>
    <button onclick="nextSlide()" class="slider-btn right-3"><i class="lni lni-angle-double-right text-gray-700"></i></button>
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        @foreach ($slides as $i => $slide)
        <button onclick="goSlide({{ $i }})" class="w-2.5 h-2.5 rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white/50' }} hover:bg-white transition" data-dot="{{ $i }}"></button>
        @endforeach
    </div>
</section>
@endif

@php
    $allNotices = App\Models\Notice::where('status', true)->where('date', '<=', now())->orderBy('date', 'desc')->take(4)->get();
@endphp
@if($allNotices->isNotEmpty())
<section class="py-12 md:py-16" style="background: linear-gradient(to bottom, rgba(var(--theme-accent-rgb), 0.06), white);">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-accent-light rounded-xl flex items-center justify-center"><i class="lni lni-bell-1 text-accent"></i></div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">તાજી ખબર</h2>
                <p class="text-xs text-gray-400">શાળાના સમાચાર અને સૂચનાઓ</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            @foreach ($allNotices as $n)
            <a href="{{ route('frontsite.notices.show', $n->id) }}" class="bg-white rounded-xl border-accent shadow-sm hover:shadow-md transition p-4 flex items-start gap-4 group">
                <div class="w-14 h-14 bg-accent-light rounded-xl flex flex-col items-center justify-center flex-shrink-0 border-accent">
                    <span class="text-lg font-bold text-accent leading-none">{{ $n->date->format('d') }}</span>
                    <span class="text-[10px] font-medium leading-tight" style="color: var(--theme-accent); opacity: 0.7;">{{ $n->date->locale('gu')->isoFormat('MMM') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-accent transition">{{ $n->title_gu }}</h3>
                    @if($n->content_gu)<p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $n->content_gu }}</p>@endif
                    @if($n->file_path)
                    <span class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-accent">
                        <i class="lni lni-download-1 text-xs"></i> ડાઉનલોડ કરો
                    </span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('frontsite.notices.index') }}" class="bg-accent inline-flex items-center gap-2 text-white font-medium px-6 py-2.5 rounded-lg transition shadow-sm">
                બધી સૂચનાઓ જુઓ <i class="lni lni-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>
@endif

@elseif ($section->type === 'about')
@php $c = $section->content; @endphp
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">{{ $c['title_gu'] ?? 'અમારા વિશે' }}</h2>
                <div class="w-16 h-1 rounded-full mb-6" style="background-color: var(--theme-primary);"></div>
                <p class="text-gray-600 leading-relaxed">{{ $c['description_gu'] ?? '' }}</p>
            </div>
            <div class="relative">
                @if(!empty($c['image']))
                <img src="{{ asset('storage/' . $c['image']) }}" alt="About" class="rounded-2xl shadow-lg w-full object-cover" style="height:350px">
                @else
                <div class="rounded-2xl w-full flex items-center justify-center text-6xl bg-primary-light text-primary" style="height:350px;"><i class="lni lni-school-bench-1"></i></div>
                @endif
            </div>
        </div>
    </div>
</section>

@elseif ($section->type === 'features')
@php $c = $section->content; $items = $c['items'] ?? []; @endphp
@if(!empty($items))
<section class="py-16 md:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="section-title">{{ $c['title_gu'] ?? 'અમારી વિશેષતાઓ' }}</h2>
        <p class="section-subtitle">{{ $c['subtitle_gu'] ?? '' }}</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($items as $item)
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-gray-100 transition text-center">
                <div class="w-14 h-14 bg-primary-light rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="{{ $item['icon'] ?? 'lni lni-star-fat' }} text-2xl text-primary"></i></div>
                <h3 class="font-bold text-gray-900 mb-1">{{ $item['title_gu'] ?? '' }}</h3>
                <p class="text-sm text-gray-500">{{ $item['description_gu'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@elseif ($section->type === 'stats')
@php $c = $section->content; $stats = $c['stats'] ?? []; @endphp
@if(!empty($stats))
<section class="py-16 md:py-20 text-white" style="background: linear-gradient(135deg, var(--theme-header-from), var(--theme-header-to));">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-10">{{ $c['title_gu'] ?? 'અમારી સિદ્ધિઓ' }}</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ($stats as $st)
            <div class="text-center counter">
                <div class="stat-number stat-number-light">{{ $st['number'] ?? '0' }}</div>
                <p class="stat-label-light text-sm mt-1">{{ $st['label_gu'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@elseif ($section->type === 'gallery')
@php
    $galleries = App\Models\Gallery::where('status', true)->with('images')->get();
    $allImages = $galleries->flatMap->images->take(12);
@endphp
@if($allImages->isNotEmpty())
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="section-title">ફોટો ગેલેરી</h2>
        <p class="section-subtitle">શાળાની યાદગાર ક્ષણો</p>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($allImages as $img)
            <a href="{{ asset('storage/' . $img->image) }}" target="_blank" class="group block rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $img->caption_gu ?? '' }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@elseif ($section->type === 'contact')
@php $c = $section->content; @endphp
<section class="py-16 md:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="section-title">{{ $c['title_gu'] ?? 'સંપર્ક' }}</h2>
        <p class="section-subtitle">{{ $c['subtitle_gu'] ?? 'અમારો સંપર્ક કરવા માટે નીચેની વિગતોનો ઉપયોગ કરો' }}</p>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                @if($school && $school->address)
                <div class="flex items-start gap-3"><div class="w-10 h-10 bg-primary-light rounded-lg flex items-center justify-center flex-shrink-0"><i class="lni lni-map-marker-1 text-primary"></i></div><div><p class="font-medium text-gray-900">સરનામું</p><p class="text-sm text-gray-500">{{ $school->address }}</p></div></div>
                @endif
                @if($school && $school->mobile)
                <div class="flex items-start gap-3"><div class="w-10 h-10 bg-primary-light rounded-lg flex items-center justify-center flex-shrink-0"><i class="lni lni-phone text-primary"></i></div><div><p class="font-medium text-gray-900">ફોન</p><p class="text-sm text-gray-500">{{ $school->mobile }}</p></div></div>
                @endif
                @if($school && $school->email)
                <div class="flex items-start gap-3"><div class="w-10 h-10 bg-primary-light rounded-lg flex items-center justify-center flex-shrink-0"><i class="lni lni-envelope-1 text-primary"></i></div><div><p class="font-medium text-gray-900">ઇમેઇલ</p><p class="text-sm text-gray-500">{{ $school->email }}</p></div></div>
                @endif
            </div>
            <div>
                @if(!empty($c['map_embed']))
                <div class="rounded-xl overflow-hidden shadow-sm border border-gray-100">{!! $c['map_embed'] !!}</div>
                @else
                <div class="rounded-xl w-full flex items-center justify-center h-64 bg-primary-light text-primary"><p class="text-sm">નકશો અહીં દેખાશે</p></div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
@endforeach

<script>
var currentSlide = 0;
var slides = document.querySelectorAll('.slide');
var dots = document.querySelectorAll('[data-dot]');
var totalSlides = slides.length;
function showSlide(idx) {
    if (totalSlides === 0) return;
    slides.forEach(function(s, i) {
        s.classList.toggle('opacity-100', i === idx);
        s.classList.toggle('z-10', i === idx);
        s.classList.toggle('opacity-0', i !== idx);
        s.classList.toggle('z-0', i !== idx);
    });
    dots.forEach(function(d, i) {
        d.classList.toggle('bg-white', i === idx);
        d.classList.toggle('bg-white/50', i !== idx);
    });
    currentSlide = idx;
}
function nextSlide() { showSlide((currentSlide + 1) % totalSlides); }
function prevSlide() { showSlide((currentSlide - 1 + totalSlides) % totalSlides); }
function goSlide(idx) { showSlide(idx); }
setInterval(nextSlide, 5000);
</script>
@endsection

