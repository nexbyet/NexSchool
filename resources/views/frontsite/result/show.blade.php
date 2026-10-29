@extends('frontsite.layouts.master')
@section('title', 'પરિણામ — ' . ($student->full_name_gu ?: $student->full_name_en))
@section('content')
@php
    $r = $rows[0] ?? null;
    $semText = $semester === 'both' ? 'બંને (વાર્ષિક)' : ('સત્ર ' . $semester);
@endphp
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 md:p-8">
        <div class="text-center mb-6">
            @if($school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="h-14 mx-auto mb-2">
            @endif
            <h1 class="text-xl font-bold text-gray-900">{{ $school->school_name_gu ?? 'શાળા' }}</h1>
            <p class="text-xs text-gray-500">{{ $school->address ?? '' }}</p>
            <h2 class="text-base font-bold mt-2 text-emerald-700">પરિણામ (Result) — {{ $academicYear->year ?? '' }}</h2>
        </div>

        @if (!$r)
            <div class="text-center text-gray-500 py-8">Result data not found.</div>
        @else
            <div class="grid grid-cols-2 gap-3 mb-6 text-sm">
                <div><span class="font-semibold text-gray-600">નામ:</span> {{ $r['name'] }}</div>
                <div><span class="font-semibold text-gray-600">GR નંબર:</span> {{ $r['gr_number'] }}</div>
                <div><span class="font-semibold text-gray-600">ધોરણ-વર્ગ:</span> {{ $stdName ?? '' }}-{{ $clsName ?? '' }}</div>
                <div><span class="font-semibold text-gray-600">સત્ર:</span> {{ $semText }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-3 py-2 text-left border-b border-gray-200">#</th>
                            <th class="px-3 py-2 text-left border-b border-gray-200">વિષય</th>
                            <th class="px-3 py-2 text-center border-b border-gray-200">મેળવેલા</th>
                            <th class="px-3 py-2 text-center border-b border-gray-200">કુલ</th>
                            <th class="px-3 py-2 text-center border-b border-gray-200">ગ્રેડ</th>
                            <th class="px-3 py-2 text-center border-b border-gray-200">પાસ/નાપાસ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $i => $sub)
                            @php
                                $s = (isset($r['subs'][$i]) && $r['subs'][$i]) ? $r['subs'][$i] : null;
                            @endphp
                            <tr class="border-b border-gray-100">
                                <td class="px-3 py-2">{{ $i + 1 }}</td>
                                <td class="px-3 py-2 font-medium">{{ $sub['name'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $s ? number_format($s['obtained'], 0) : '—' }}</td>
                                <td class="px-3 py-2 text-center">{{ $s ? number_format($s['total'], 0) : '—' }}</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $s['grade'] ?? '—' }}</td>
                                <td class="px-3 py-2 text-center">
                                    @if (!$s)
                                        <span class="text-gray-400">બાકી</span>
                                    @elseif ($s['pass'])
                                        <span class="text-emerald-700 font-bold">પાસ</span>
                                    @else
                                        <span class="text-red-600 font-bold">નાપાસ</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-emerald-50 font-bold">
                            <td colspan="2" class="px-3 py-2 text-right">કુલ</td>
                            <td class="px-3 py-2 text-center">{{ $r['has_any'] ? number_format($r['obtained'], 0) : '—' }}</td>
                            <td class="px-3 py-2 text-center">{{ $r['has_any'] ? number_format($r['total_marks'], 0) : '—' }}</td>
                            <td class="px-3 py-2 text-center">{{ $r['percent'] === null ? '—' : number_format($r['percent'], 2) . '%' }}</td>
                            <td class="px-3 py-2 text-center">{{ $r['grade'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-center">
                                @if (!$r['has_all'])
                                    <span class="text-gray-400">બાકી</span>
                                @elseif ($r['pass'])
                                    <span class="text-emerald-700">પાસ</span>
                                @else
                                    <span class="text-red-600">નાપાસ</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500">ટકા</div>
                    <div class="font-bold text-lg">{{ $r['percent'] === null ? '—' : number_format($r['percent'], 2) . '%' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500">ગ્રેડ</div>
                    <div class="font-bold text-lg">{{ $r['grade'] ?? '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500">પરિણામ</div>
                    <div class="font-bold text-lg {{ $r['pass'] ? 'text-emerald-700' : ($r['has_all'] ? 'text-red-600' : 'text-gray-400') }}">
                        @if (!$r['has_all']) બાકી @elseif ($r['pass']) પાસ @else નાપાસ @endif
                    </div>
                </div>
            </div>

            <p class="text-[10px] text-gray-400 mt-4 text-center">આ પરિણામ કમ્પ્યુટર દ્વારા જનરેટ થયેલ છે.</p>
        @endif

        <div class="mt-6 text-center no-print">
            <a href="{{ url('/result') }}" class="inline-block px-5 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">ફરી શોધો</a>
        </div>
    </div>
</div>
@endsection