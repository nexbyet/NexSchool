@extends('install.master')
@section('content')
<div class="text-center">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/15 mb-5">
        <i class="lni lni-graduation text-2xl text-white"></i>
    </div>
    <h1 class="text-2xl font-bold text-white tracking-tight">NexSchool</h1>
    <p class="text-gray-500 text-sm mt-1 mb-6">School Management System — Installation</p>

    <div class="cd p-6 text-center">
        <i class="lni lni-flag text-3xl text-indigo-400 mb-3 inline-block"></i>
        <h2 class="text-base font-semibold text-white mb-1">Welcome! સ્વાગત છે</h2>
        <p class="text-gray-400 text-xs mb-5">Select your preferred language to begin.</p>
        <form method="POST" action="{{ route('install.language') }}">
            @csrf
            <div class="flex gap-3 justify-center mb-4">
                <button type="submit" name="lang" value="gu" class="px-6 py-4 rounded-xl border-2 border-white/5 bg-white/[.02] hover:border-indigo-500/50 hover:bg-white/[.04] text-white transition-all min-w-[130px]">
                    <span class="text-2xl block mb-1">🇮🇳</span>
                    <span class="font-medium text-sm">ગુજરાતી</span>
                </button>
                <button type="submit" name="lang" value="en" class="px-6 py-4 rounded-xl border-2 border-white/5 bg-white/[.02] hover:border-indigo-500/50 hover:bg-white/[.04] text-white transition-all min-w-[130px]">
                    <span class="text-2xl block mb-1">🇬🇧</span>
                    <span class="font-medium text-sm">English</span>
                </button>
            </div>
            <p class="text-[11px] text-gray-600">All forms support Gujarati input.</p>
        </form>
    </div>
</div>
@endsection
