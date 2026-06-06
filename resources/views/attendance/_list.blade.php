@php
$statusLabels = ['present' => 'હાજર (P)', 'absent' => 'ગેરહાજર (A)', 'absent_with_leave' => 'રજા સાથે (L)', 'medical_leave' => 'માંદગી (S)'];
$statusCodes = ['present' => 'P', 'absent' => 'A', 'absent_with_leave' => 'L', 'medical_leave' => 'S'];
$dotColors = [
    'present' => 'bg-emerald-500',
    'absent' => 'bg-red-500',
    'absent_with_leave' => 'bg-amber-500',
    'medical_leave' => 'bg-blue-500',
];
$dayNames = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];
@endphp

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <span class="font-semibold text-gray-900">{{ $standard->name }} — {{ $class->name }}</span>
            <span class="text-sm text-gray-500 ml-3">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} ({{ $dayNames[\Carbon\Carbon::parse($date)->dayOfWeek] }})</span>
        </div>
        <button type="button" id="saveAttendance" class="px-5 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition shadow-sm flex items-center gap-2">
            <i class="lni lni-floppy-disk-1 text-base"></i> સેવ કરો
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-2 py-3 text-center border-b border-gray-200">ક્ર</th>
                    <th class="px-2 py-3 text-left border-b border-gray-200">GR</th>
                    <th class="px-3 py-3 text-left border-b border-gray-200">નામ</th>
                    <th class="px-2 py-3 text-center border-b border-gray-200">શ્રેણી</th>
                    <th class="px-3 py-3 text-center border-b border-gray-200" colspan="{{ count($lastDates) }}">
                        <span class="text-gray-400">પાછલા દિવસો</span>
                    </th>
                    <th class="px-3 py-3 text-center border-b border-gray-200 bg-teal-50 text-teal-700">
                        આજે <span class="font-normal">({{ \Carbon\Carbon::parse($date)->format('d/m') }})</span>
                    </th>
                </tr>
                <tr class="bg-gray-50 text-[10px] text-gray-500">
                    <th colspan="4"></th>
                    @foreach($lastDates as $ld)
                        <th class="px-1 py-2 text-center border-b border-gray-200 font-medium" title="{{ $ld->format('d/m/Y') }}">
                            {{ $ld->format('d/m') }}<br><span class="text-[9px] text-gray-400">{{ $dayNames[$ld->dayOfWeek] }}</span>
                        </th>
                    @endforeach
                    <th class="px-1 py-2 text-center border-b border-gray-200 bg-teal-50"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $idx => $student)
                    @php $savedStatus = $existing[$student->id]->status ?? null; @endphp
                    <tr class="attendance-row hover:bg-gray-50/50 border-b border-gray-100 last:border-0" data-student-id="{{ $student->id }}">
                        <td class="px-2 py-2 text-center text-gray-500 text-xs">{{ $idx + 1 }}</td>
                        <td class="px-2 py-2 text-gray-600 text-xs whitespace-nowrap">{{ $student->gr_number }}</td>
                        <td class="px-3 py-2 font-medium text-sm {{ $student->sharirik_jaati === 'kumari' ? 'text-red-600' : 'text-gray-800' }} whitespace-nowrap">
                            {{ $student->student_name_gu }}
                        </td>
                        <td class="px-2 py-2 text-center text-xs text-gray-500">{{ $student->category_gu ?? '—' }}</td>

                        {{-- Last 5 days status dots --}}
                        @foreach($lastDates as $ld)
                            @php
                                $key = $student->id . '-' . $ld->format('Y-m-d');
                                $ls = optional($lastAttendance[$key] ?? null)->status;
                            @endphp
                            <td class="px-1 py-2 text-center">
                                @if($ls)
                                    <span class="inline-block w-2.5 h-2.5 rounded-full {{ $dotColors[$ls] ?? 'bg-gray-300' }}" title="{{ $statusLabels[$ls] ?? '' }}"></span>
                                @else
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-200"></span>
                                @endif
                            </td>
                        @endforeach

                        {{-- Today's radio buttons --}}
                        <td class="px-2 py-2 bg-teal-50/30">
                            <div class="flex items-center justify-center gap-1">
                                @foreach(['present', 'absent', 'absent_with_leave', 'medical_leave'] as $st)
                                    @php $isChecked = $savedStatus === $st; @endphp
                                    <button type="button" data-status="{{ $st }}"
                                        class="status-btn px-1.5 py-1 rounded text-[10px] font-medium border transition-all duration-150 cursor-pointer
                                        {{ $isChecked ? 'ring-1 ring-offset-1 ' . ($st === 'present' ? 'text-emerald-700 bg-emerald-50 border-emerald-300 ring-emerald-300' : ($st === 'absent' ? 'text-red-700 bg-red-50 border-red-300 ring-red-300' : ($st === 'absent_with_leave' ? 'text-amber-700 bg-amber-50 border-amber-300 ring-amber-300' : 'text-blue-700 bg-blue-50 border-blue-300 ring-blue-300'))) : 'text-gray-400 border-gray-200 hover:border-gray-300 hover:text-gray-600' }}">
                                        {{ $st === 'present' ? 'હા (P)' : ($st === 'absent' ? 'ગે (A)' : ($st === 'absent_with_leave' ? 'રજા (L)' : 'માં (S)')) }}
                                    </button>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if($students->isEmpty())
                    <tr>
                        <td colspan="{{ 5 + count($lastDates) + 1 }}" class="px-3 py-10 text-center text-gray-400">આ વર્ગમાં કોઈ સક્રિય વિદ્યાર્થી નથી.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

