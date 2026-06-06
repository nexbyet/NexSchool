@extends('frontsite.layouts.master')
@section('title', $page->title_gu)
@section('meta_description', $page->meta_description)
@section('content')

<section class="py-12 md:py-16 text-white" style="background: linear-gradient(135deg, var(--theme-header-from), var(--theme-header-to));">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-4xl font-bold">{{ $page->title_gu }}</h1>
        @if($page->title_en)
        <p class="text-white/70 text-sm mt-1">{{ $page->title_en }}</p>
        @endif
    </div>
</section>

<section class="py-12 md:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-gray-700 leading-relaxed text-base md:text-lg">
            {!! nl2br(e($page->content_gu)) !!}
        </div>
    </div>
</section>

@endsection
