@extends('layouts.app')
@section('title', $teacher->name . ' - પ્રોફાઇલ')
@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 sm:p-8 mb-6 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-end gap-5">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg border-2 border-white/30 shrink-0">
                <span class="text-3xl sm:text-4xl font-bold text-white">{{ substr($teacher->name, 0, 1) }}</span>
            </div>
            <div class="text-center sm:text-left flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-white truncate">{{ $teacher->name }}</h1>
                <p class="text-sm text-emerald-100 mt-0.5">{{ $teacher->teacher_id }}</p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                        <i class="lni lni-user-4 text-[10px]"></i> શિક્ષક
                    </span>
                    @if($teacher->status === 'active')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/30 text-white backdrop-blur-sm">સક્રિય</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-400/30 text-white backdrop-blur-sm">નિષ્ક્રિય</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('teachers.index') }}" class="shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-medium transition backdrop-blur-sm flex items-center gap-1.5">
                <i class="lni lni-arrow-left text-xs"></i> પાછા
            </a>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Personal Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="lni lni-user-4 text-emerald-500"></i>
                <h2 class="text-base font-semibold text-gray-900">વ્યક્તિગત માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">પૂરું નામ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">ઈમેલ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->email ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">ફોન</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->phone ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">વોટ્સએપ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->whatsapp_number ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">જન્મ તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->date_of_birth ? \Carbon\Carbon::parse($teacher->date_of_birth)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">લિંગ</label>
                    <p class="text-sm font-medium text-gray-800 capitalize">{{ $teacher->gender ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">લોહીનો પ્રકાર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->blood_group ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">સરનામું</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->address ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Professional Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="lni lni-book-1 text-indigo-500"></i>
                <h2 class="text-base font-semibold text-gray-900">વ્યાવસાયિક માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">શિક્ષક ક્રમાંક</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->teacher_id ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">જોડાવાની તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->joining_date ? \Carbon\Carbon::parse($teacher->joining_date)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">જોડાવાનો ક્રમ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->joining_number ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">અનુભવ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->experience_in_years ? $teacher->experience_in_years . ' વર્ષ' : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">લાયકાત</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->qualification ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">વિશેષતા</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->specialization ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">મૂળ પગાર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->basic_pay ? '₹' . number_format($teacher->basic_pay, 2) : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">મૂળ પગાર (માસિક)</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->basic_salary ? '₹' . number_format($teacher->basic_salary, 2) : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">અન્ય પગાર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->other_salary ? '₹' . number_format($teacher->other_salary, 2) : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">મહત્તમ LWP</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->max_lwp ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">મહત્તમ CL</label>
                    <p class="text-sm font-medium text-gray-800">{{ $teacher->max_cl ?? '—' }}</p>
                </div>
            </div>

            @if($teacher->subjects->count())
            <div class="mt-5 pt-5 border-t border-gray-100">
                <label class="block text-xs font-medium text-gray-400 mb-2.5">આ શિક્ષકના વિષયો</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($teacher->subjects as $subj)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $subj->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($classes->count())
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-400 mb-2.5">વર્ગશિક્ષક</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($classes as $cls)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                            <i class="lni lni-buildings-1 text-[10px]"></i>
                            {{ $cls->standard?->name ?? '' }} - {{ $cls->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @if($teacher->status === 'inactive' && ($teacher->reason_inactive || $teacher->date_inactive))
        <div class="bg-rose-50 rounded-2xl border border-rose-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <i class="lni lni-ban-2 text-rose-500"></i>
                <h2 class="text-base font-semibold text-rose-900">નિષ્ક્રિયતા માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($teacher->date_inactive)
                <div>
                    <label class="block text-xs font-medium text-rose-400 mb-1">તારીખ</label>
                    <p class="text-sm font-medium text-rose-800">{{ \Carbon\Carbon::parse($teacher->date_inactive)->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($teacher->reason_inactive)
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-rose-400 mb-1">કારણ</label>
                    <p class="text-sm font-medium text-rose-800">{{ $teacher->reason_inactive }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
