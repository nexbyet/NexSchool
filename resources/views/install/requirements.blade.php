@extends('install.master')
@section('content')
<div class="cd p-6">
    <h2 class="text-base font-semibold text-white">System Requirements</h2>
    <p class="text-gray-400 text-xs mt-1 mb-5">Ensure your server meets all requirements below.</p>
    <div class="space-y-2 mb-6">
        @foreach($checks as $key => $check)
        <div class="flex items-center justify-between p-3 rounded-lg {{ $check['pass'] ? 'bg-emerald-500/5 border border-emerald-500/10' : 'bg-red-500/5 border border-red-500/10' }}">
            <div class="flex items-center gap-2.5">
                @if($check['pass'])
                <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center"><i class="lni lni-checkmark text-emerald-400 text-[10px]"></i></div>
                @else
                <div class="w-5 h-5 rounded-full bg-red-500/20 flex items-center justify-center"><i class="lni lni-close text-red-400 text-[10px]"></i></div>
                @endif
                <span class="text-sm font-medium {{ $check['pass'] ? 'text-gray-200' : 'text-red-300' }}">{{ $check['label'] }}</span>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $check['pass'] ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ $check['pass'] ? 'OK' : 'FAIL' }}</span>
        </div>
        @endforeach
    </div>
    @if($allPass)
    <form method="POST" action="{{ route('install.requirements.next') }}">@csrf<button type="submit" class="btn btn-p w-full">Continue <i class="lni lni-arrow-right"></i></button></form>
    @else
    <div class="alert-e flex items-start gap-2 mb-4 text-sm"><i class="lni lni-warning mt-0.5"></i> Fix failing requirements (enable extensions in <code class="px-1 py-0.5 bg-black/30 rounded text-xs">php.ini</code>).</div>
    <a href="{{ route('install.requirements') }}" class="btn btn-g w-full">Retry <i class="lni lni-loop"></i></a>
    @endif
</div>
@endsection
