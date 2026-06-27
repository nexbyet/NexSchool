@extends('layouts.app')
@section('title', 'શિક્ષક ડેશબોર્ડ')
@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Welcome heading --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">શિક્ષક ડેશબોર્ડ</h1>
                <p class="text-gray-500 mt-1">{{ $schoolSetting->school_name_gu ?? 'NexSchool' }} માં આપનું સ્વાગત છે, {{ $user->name }}</p>
            </div>
            @if($activeYear)
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl px-5 py-3 text-right shadow-sm">
                    <span class="text-xs font-medium text-emerald-200 uppercase tracking-wide">ચાલુ વર્ષ</span>
                    <p class="text-base font-bold text-white">{{ $activeYear->year }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="lni lni-user-multiple-4 text-xl text-white"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['students'] ?? 0 }}</p>
                <p class="text-sm text-emerald-100 mt-1">મારા વિદ્યાર્થીઓ</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-5 shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="lni lni-buildings-1 text-xl text-white"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['classes'] ?? 0 }}</p>
                <p class="text-sm text-blue-100 mt-1">મારા વર્ગો</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="lni lni-book-1 text-xl text-white"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white">{{ $mySubjects->count() ?? 0 }}</p>
                <p class="text-sm text-amber-100 mt-1">મારા વિષયો</p>
            </div>
        </div>
    </div>

    {{-- Today's Attendance --}}
    @if($todayAttendance)
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="lni lni-clipboard text-indigo-500"></i>
            <h2 class="text-base font-semibold text-gray-900">આજની હાજરી ({{ $todayAttendance['date'] }})</h2>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 rounded-xl">
                <span class="text-2xl font-bold text-emerald-600">{{ $todayAttendance['present'] }}</span>
                <span class="text-sm text-emerald-700">હાજર</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-3 bg-red-50 rounded-xl">
                <span class="text-2xl font-bold text-red-600">{{ $todayAttendance['total'] - $todayAttendance['present'] }}</span>
                <span class="text-sm text-red-700">ગેરહાજર</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 rounded-xl">
                <span class="text-2xl font-bold text-gray-600">{{ $todayAttendance['total'] }}</span>
                <span class="text-sm text-gray-700">કુલ</span>
            </div>
        </div>
    </div>
    @endif

    {{-- My Classes --}}
    @if($myClasses->count())
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="lni lni-buildings-1 text-purple-500"></i>
            <h2 class="text-base font-semibold text-gray-900">મારા વર્ગો</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($myClasses as $cls)
            <div class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl border border-purple-100">
                <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                    <i class="lni lni-buildings-1 text-purple-700"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $cls->standard?->name ?? '' }} - {{ $cls->name }}</p>
                    <p class="text-xs text-gray-500">વર્ગશિક્ષક</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Upcoming 10 Days Birthdays --}}
    @if($upcomingBirthdays->count())
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center shadow-sm">
                <i class="lni lni-cake-1 text-sm text-white"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">આગામી ૧૦ દિવસ — જન્મદિવસ</h2>
            <span class="text-xs bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full font-medium ml-auto">
                {{ now()->format('d/m/Y') }} થી {{ now()->addDays(10)->format('d/m/Y') }}
            </span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-blue-200 overflow-hidden shadow-sm">
                <div class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-50 to-blue-100/50 border-b border-blue-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center shadow-sm">
                        <i class="lni lni-crown-1 text-sm text-white"></i>
                    </div>
                    <span class="font-semibold text-blue-800">કુમાર</span>
                    <span class="text-xs bg-blue-200 text-blue-700 px-2 py-0.5 rounded-full ml-auto">{{ $birthdayBoys->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($birthdayBoys as $b)
                    <a href="{{ route('students.show', $b->id) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-blue-50/50 transition group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($b->photo)
                                <img src="{{ asset('storage/' . $b->photo) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-bold text-blue-600">{{ mb_substr($b->student_name_gu ?? 'S', 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-700 truncate">{{ $b->full_name_gu }}</p>
                            <p class="text-xs text-gray-400">{{ $b->currentStandard?->name ?? '' }}{{ $b->currentClass?->name ? ' - ' . $b->currentClass->name : '' }} | GR: {{ $b->gr_number }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[10px] text-gray-400">જન્મ</p>
                            <p class="text-sm font-bold text-blue-600">{{ \Carbon\Carbon::parse($b->date_of_birth)->format('d/m') }}</p>
                        </div>
                    </a>
                    @empty
                    <div class="px-5 py-6 text-center text-gray-400 text-sm">આગામી ૧૦ દિવસમાં કોઈ કુમારનો જન્મદિવસ નથી</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-xl border border-rose-200 overflow-hidden shadow-sm">
                <div class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-rose-50 to-pink-100/50 border-b border-rose-100">
                    <div class="w-8 h-8 rounded-lg bg-rose-500 flex items-center justify-center shadow-sm">
                        <i class="lni lni-star-1 text-sm text-white"></i>
                    </div>
                    <span class="font-semibold text-rose-800">કુમારી</span>
                    <span class="text-xs bg-rose-200 text-rose-700 px-2 py-0.5 rounded-full ml-auto">{{ $birthdayGirls->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($birthdayGirls as $b)
                    <a href="{{ route('students.show', $b->id) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-rose-50/50 transition group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rose-100 to-pink-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($b->photo)
                                <img src="{{ asset('storage/' . $b->photo) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-bold text-rose-600">{{ mb_substr($b->student_name_gu ?? 'S', 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-rose-700 truncate">{{ $b->full_name_gu }}</p>
                            <p class="text-xs text-gray-400">{{ $b->currentStandard?->name ?? '' }}{{ $b->currentClass?->name ? ' - ' . $b->currentClass->name : '' }} | GR: {{ $b->gr_number }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[10px] text-gray-400">જન્મ</p>
                            <p class="text-sm font-bold text-rose-600">{{ \Carbon\Carbon::parse($b->date_of_birth)->format('d/m') }}</p>
                        </div>
                    </a>
                    @empty
                    <div class="px-5 py-6 text-center text-gray-400 text-sm">આગામી ૧૦ દિવસમાં કોઈ કુમારીનો જન્મદિવસ નથી</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Standard × Class × Category Matrix --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm mb-8">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-500 flex items-center justify-center shadow-sm">
                    <i class="lni lni-box-archive-1 text-lg text-white"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">ધોરણ અને કેટેગરી મુજબ વિદ્યાર્થીઓ</h2>
                    <p class="text-xs text-gray-500 mt-0.5">સક્રિય વિદ્યાર્થીઓનું ધોરણ, વર્ગ અને કેટેગરી મુજબ વિગતવાર વિતરણ</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th rowspan="2" class="text-left py-3 px-3 font-semibold text-gray-700 uppercase tracking-wider min-w-[60px]">ધોરણ</th>
                        <th rowspan="2" class="text-left py-3 px-2 font-semibold text-gray-700 uppercase tracking-wider min-w-[50px]">વર્ગ</th>
                        <th colspan="3" class="text-center py-3 px-1 font-semibold text-gray-700 uppercase tracking-wider bg-blue-50/50 border-x border-gray-200">General</th>
                        <th colspan="3" class="text-center py-3 px-1 font-semibold text-gray-700 uppercase tracking-wider bg-green-50/50 border-x border-gray-200">SC</th>
                        <th colspan="3" class="text-center py-3 px-1 font-semibold text-gray-700 uppercase tracking-wider bg-amber-50/50 border-x border-gray-200">ST</th>
                        <th colspan="3" class="text-center py-3 px-1 font-semibold text-gray-700 uppercase tracking-wider bg-orange-50/50 border-x border-gray-200">OBC</th>
                        <th colspan="3" class="text-center py-3 px-1 font-semibold text-gray-800 uppercase tracking-wider bg-indigo-50/50 border-x border-gray-200">કુલ</th>
                        <th colspan="3" class="text-center py-3 px-1 font-semibold text-gray-700 uppercase tracking-wider bg-rose-50/50 border-x border-gray-200">OBC લઘુમતી</th>
                    </tr>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમાર</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમારી</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુલ</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમાર</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમારી</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુલ</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમાર</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમારી</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુલ</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમાર</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમારી</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુલ</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમાર</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમારી</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુલ</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમાર</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુમારી</th>
                        <th class="text-center py-2 px-1 font-medium text-gray-500 text-[10px] border-x border-gray-200">કુલ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($classStats as $row)
                        <tr class="hover:bg-purple-50/30 transition">
                            <td class="py-2.5 px-3 font-medium text-gray-800">{{ $row->standard_name }}</td>
                            <td class="py-2.5 px-2 text-gray-600">{{ $row->class_name }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-blue-600">{{ $row->general_boys }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-rose-600">{{ $row->general_girls }}</td>
                            <td class="text-center py-2.5 px-1 text-gray-700">{{ $row->general_total }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-blue-600">{{ $row->sc_boys }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-rose-600">{{ $row->sc_girls }}</td>
                            <td class="text-center py-2.5 px-1 text-gray-700">{{ $row->sc_total }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-blue-600">{{ $row->st_boys }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-rose-600">{{ $row->st_girls }}</td>
                            <td class="text-center py-2.5 px-1 text-gray-700">{{ $row->st_total }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-blue-600">{{ $row->obc_boys }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-rose-600">{{ $row->obc_girls }}</td>
                            <td class="text-center py-2.5 px-1 text-gray-700">{{ $row->obc_total }}</td>
                            <td class="text-center py-2.5 px-1 font-bold text-gray-900">{{ $row->total_boys }}</td>
                            <td class="text-center py-2.5 px-1 font-bold text-gray-900">{{ $row->total_girls }}</td>
                            <td class="text-center py-2.5 px-1 font-bold text-gray-900">{{ $row->total_students }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-blue-600">{{ $row->obc_min_boys }}</td>
                            <td class="text-center py-2.5 px-1 font-medium text-rose-600">{{ $row->obc_min_girls }}</td>
                            <td class="text-center py-2.5 px-1 text-gray-700">{{ $row->obc_min_total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="20" class="text-center py-10 text-gray-400">
                                <i class="lni lni-box-archive-1 text-2xl block mb-2 text-gray-300"></i>
                                કોઈ સક્રિય વિદ્યાર્થી ડેટા નથી
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($classStats->count())
                <tfoot>
                    <tr class="bg-gradient-to-r from-purple-50 to-indigo-50 border-t-2 border-purple-200 font-bold">
                        <td class="py-3 px-3 text-gray-900" colspan="2">કુલ સરવાળો</td>
                        <td class="text-center py-3 px-1 text-blue-700">{{ $summaryTotals->general_boys }}</td>
                        <td class="text-center py-3 px-1 text-rose-700">{{ $summaryTotals->general_girls }}</td>
                        <td class="text-center py-3 px-1 text-gray-900">{{ $summaryTotals->general_total }}</td>
                        <td class="text-center py-3 px-1 text-blue-700">{{ $summaryTotals->sc_boys }}</td>
                        <td class="text-center py-3 px-1 text-rose-700">{{ $summaryTotals->sc_girls }}</td>
                        <td class="text-center py-3 px-1 text-gray-900">{{ $summaryTotals->sc_total }}</td>
                        <td class="text-center py-3 px-1 text-blue-700">{{ $summaryTotals->st_boys }}</td>
                        <td class="text-center py-3 px-1 text-rose-700">{{ $summaryTotals->st_girls }}</td>
                        <td class="text-center py-3 px-1 text-gray-900">{{ $summaryTotals->st_total }}</td>
                        <td class="text-center py-3 px-1 text-blue-700">{{ $summaryTotals->obc_boys }}</td>
                        <td class="text-center py-3 px-1 text-rose-700">{{ $summaryTotals->obc_girls }}</td>
                        <td class="text-center py-3 px-1 text-gray-900">{{ $summaryTotals->obc_total }}</td>
                        <td class="text-center py-3 px-1 text-blue-700">{{ $summaryTotals->total_boys }}</td>
                        <td class="text-center py-3 px-1 text-rose-700">{{ $summaryTotals->total_girls }}</td>
                        <td class="text-center py-3 px-1 text-gray-900">{{ $summaryTotals->total_students }}</td>
                        <td class="text-center py-3 px-1 text-blue-700">{{ $summaryTotals->obc_min_boys }}</td>
                        <td class="text-center py-3 px-1 text-rose-700">{{ $summaryTotals->obc_min_girls }}</td>
                        <td class="text-center py-3 px-1 text-gray-900">{{ $summaryTotals->obc_min_total }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Upcoming Events: Activities & Holidays --}}
    @php
        $combined = collect();
        foreach ($upcomingPlans as $p) {
            $combined->push((object)[
                'date' => $p->date,
                'title' => $p->activity_name,
                'type' => 'activity',
                'order' => $p->sort_order,
                'remarks' => $p->remarks,
            ]);
        }
        foreach ($upcomingHolidays as $h) {
            $combined->push((object)[
                'date' => $h->date,
                'title' => $h->name,
                'type' => $h->type,
                'order' => 0,
                'remarks' => null,
            ]);
        }
        $combined = $combined->sortBy('date')->groupBy(fn($i) => \Carbon\Carbon::parse($i->date)->format('Y-m-d'));
    @endphp
    @if($combined->count())
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-4">
            <i class="lni lni-calendar-days text-indigo-500 text-lg"></i>
            <h2 class="text-lg font-semibold text-gray-900">આગામી ૧૦ દિવસ — કાર્યક્રમ અને રજાઓ</h2>
            <span class="text-xs bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full font-medium ml-auto">
                {{ now()->format('d/m/Y') }} થી {{ now()->addDays(10)->format('d/m/Y') }}
            </span>
        </div>
        <div class="space-y-3">
            @foreach($combined as $dateKey => $items)
                @php $dayName = str_replace('બુધ્વાર', 'બુધવાર', \Carbon\Carbon::parse($dateKey)->locale('gu')->dayName); @endphp
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-3 bg-gray-50 border-b border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <span class="font-bold text-indigo-700 text-sm">{{ \Carbon\Carbon::parse($dateKey)->format('d') }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $dayName }}, {{ \Carbon\Carbon::parse($dateKey)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($items as $ev)
                            <div class="flex items-center gap-3 px-5 py-3">
                                @if($ev->type === 'activity')
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <i class="lni lni-book-1 text-emerald-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800">{{ $ev->title }}</p>
                                        @if($ev->remarks)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $ev->remarks }}</p>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-medium flex-shrink-0">પ્રવૃત્તિ</span>
                                @else
                                    <div class="w-7 h-7 rounded-lg {{ $ev->type === 'jaher' ? 'bg-red-100' : 'bg-amber-100' }} flex items-center justify-center flex-shrink-0">
                                        <i class="lni lni-calendar-days text-xs {{ $ev->type === 'jaher' ? 'text-red-600' : 'text-amber-600' }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800">{{ $ev->title }} — <span class="text-xs text-gray-500">{{ $ev->type === 'jaher' ? 'જાહેર રજા' : 'સ્થાનિક રજા' }}</span></p>
                                    </div>
                                    <span class="text-[10px] {{ $ev->type === 'jaher' ? 'text-red-600 bg-red-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded-full font-medium flex-shrink-0">રજા</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="mb-8 bg-white rounded-xl border border-gray-200 p-6 shadow-sm text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="lni lni-calendar-days text-xl text-gray-400"></i>
        </div>
        <p class="text-gray-500 font-medium">આગામી ૧૦ દિવસમાં કોઈ કાર્યક્રમ કે રજા નથી</p>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <i class="lni lni-bulb-2 text-amber-500 text-lg"></i>
            <h2 class="text-lg font-semibold text-gray-900">ઝડપી ક્રિયાઓ</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('students.index') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition group border border-blue-100">
                <div class="w-11 h-11 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-user-multiple-4 text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-700">વિદ્યાર્થીઓ</p>
                    <p class="text-xs text-gray-500">જુઓ અને સંચાલન કરો</p>
                </div>
            </a>
            <a href="{{ route('attendance.index') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl hover:from-emerald-100 hover:to-green-100 transition group border border-emerald-100">
                <div class="w-11 h-11 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-clipboard text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-emerald-700">હાજરી</p>
                    <p class="text-xs text-gray-500">આજની હાજરી નોંધો</p>
                </div>
            </a>
            <a href="{{ route('profile.index') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl hover:from-purple-100 hover:to-violet-100 transition group border border-purple-100">
                <div class="w-11 h-11 bg-gradient-to-br from-purple-400 to-purple-500 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-user-4 text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-purple-700">પ્રોફાઇલ</p>
                    <p class="text-xs text-gray-500">તમારી માહિતી જુઓ</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
