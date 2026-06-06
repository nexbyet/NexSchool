@php
    use App\Models\Notice;
    $notices = Notice::where('status', true)->where('date', '<=', today())->orderBy('date', 'desc')->take(10)->get();
@endphp
@if($notices->isNotEmpty())
<div class="fixed top-0 left-0 right-0 z-50 bg-amber-500 text-white text-xs py-1.5 no-print shadow-sm">
    <div class="max-w-7xl mx-auto px-4 flex items-center gap-3">
        <span class="font-bold bg-white text-amber-600 px-2.5 py-0.5 rounded whitespace-nowrap text-[10px] flex-shrink-0">સૂચના</span>
        <div class="overflow-hidden flex-1 relative">
            <div class="whitespace-nowrap inline-flex gap-8 animate-scroll">
                @foreach ($notices as $n)
                <span class="inline-flex items-center gap-1.5 flex-shrink-0"><i class="lni lni-star-fat text-[9px] text-amber-200"></i> {{ $n->title_gu }}</span>
                @endforeach
                <span class="inline-flex items-center gap-1.5 flex-shrink-0"><i class="lni lni-star-fat text-[9px] text-amber-200"></i> {{ $notices->first()->title_gu }}</span>
            </div>
        </div>
    </div>
</div>
@endif