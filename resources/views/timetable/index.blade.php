@extends('layouts.app')

@section('title', 'ટાઇમટેબલ')

@section('content')
<div x-data="{ activeTab: localStorage.getItem('tt_tab') || 'builder', activeDay: parseInt(localStorage.getItem('tt_day')) || 1, init() { this.$watch('activeTab', v => localStorage.setItem('tt_tab', v)); this.$watch('activeDay', v => localStorage.setItem('tt_day', v)); } }" class="min-h-screen bg-gray-50">
    <div class="p-4 lg:p-6 space-y-5">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-cyan-700 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="lni lni-calendar-days text-white text-sm"></i>
                    </span>
                    ટાઇમટેબલ
                </h1>
                @if($activeYear)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $activeYear->year }} ({{ $activeYear->name }})</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <select id="ay_select" class="text-sm border-gray-300 rounded-lg px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500 bg-white">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" @selected($ay->id == $academicYearId)>{{ $ay->year }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded border border-gray-200">
                    <span id="totalEntries">{{ $allEntries->count() }}</span> એન્ટ્રી
                </span>
            </div>
        </div>

        @if(!$activeYear)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
                <p class="text-amber-700 font-medium">કૃપા કરીને પહેલા શૈક્ષણિક વર્ષ સક્રિય કરો.</p>
            </div>
        @else

        {{-- Tabs --}}
        <div class="flex gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 w-fit">
            <button @click="activeTab = 'builder'" :class="activeTab === 'builder' ? 'bg-cyan-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2">
                <i class="lni lni-layout-9 text-base"></i> ટાઇમટેબલ બિલ્ડર
            </button>
            <button @click="activeTab = 'periods'" :class="activeTab === 'periods' ? 'bg-cyan-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2">
                <i class="lni lni-alarm-1 text-base"></i> પીરિયડ મેનેજમેન્ટ <span class="bg-white/20 text-xs px-1.5 py-0.5 rounded-full" x-show="activeTab === 'periods'">{{ $slots->count() }}</span>
            </button>
        </div>

        {{-- ==================== TAB 1: BUILDER ==================== --}}
        <div x-show="activeTab === 'builder'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            @if(!$slots->count())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-amber-50 rounded-full flex items-center justify-center mb-4">
                        <i class="lni lni-alarm-1 text-2xl text-amber-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">કોઈ પીરિયડ ઉમેરાયા નથી.</p>
                    <p class="text-sm text-gray-400 mt-1 mb-4">પહેલા નવા પીરિયડ ઉમેરો.</p>
                    @if(!$readOnly)
                    <button onclick="openSlotModal()" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg text-sm font-medium hover:from-cyan-600 hover:to-cyan-700 transition shadow-sm inline-flex items-center gap-2">
                        <i class="lni lni-plus text-sm"></i> નવો પીરિયડ ઉમેરો
                    </button>
                    @endif
                </div>
            @else
                {{-- Day Tabs --}}
                <div class="flex flex-wrap gap-1.5 bg-white p-1.5 rounded-xl shadow-sm border border-gray-200">
                    @foreach($days as $num => $name)
                        <button @click="activeDay = {{ $num }}" :class="activeDay === {{ $num }} ? 'bg-cyan-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                            {{ $name }}
                        </button>
                    @endforeach
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-3 bg-white rounded-xl shadow-sm border border-gray-200 p-3">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded border-2 border-solid border-cyan-300 bg-cyan-50"></span> સેટ થયેલ</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded border-2 border-dashed border-amber-300 bg-amber-50"></span> ખાલી</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded border-2 border-dashed border-gray-300 bg-white"></span> કોઈ વિષય નથી</span>
                    </div>
                    @if(!$readOnly)
                    <div class="flex items-center gap-2">
                        <button id="copyDayBtn" class="px-3 py-1.5 text-xs font-medium bg-cyan-50 text-cyan-700 border border-cyan-200 rounded-lg hover:bg-cyan-100 transition flex items-center gap-1.5">
                            <i class="lni lni-copy text-[10px]"></i> બધા દિવસોમાં કોપી કરો
                        </button>
                        <button id="clearAllBtn" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center gap-1.5">
                            <i class="lni lni-trash-3 text-[10px]"></i> બધું સાફ કરો
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Master Timetable Table --}}
                @foreach($days as $dayNum => $dayName)
                <div x-show="activeDay === {{ $dayNum }}" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto" style="max-height:75vh;overflow-y:auto">
                            <table class="w-full text-xs border-collapse" id="tt-table-day-{{ $dayNum }}">
                                <thead class="sticky top-0 z-10">
                                    {{-- Standard group headers --}}
                                    <tr>
                                        <th class="px-3 py-2.5 text-left font-semibold text-white bg-gradient-to-b from-cyan-600 to-cyan-700 border-r border-cyan-500 w-20" rowspan="2">પીરિયડ</th>
                                        <th class="px-3 py-2.5 text-left font-semibold text-white bg-gradient-to-b from-cyan-600 to-cyan-700 border-r border-cyan-500 w-24" rowspan="2">સમય</th>
                                        @foreach($standards as $std)
                                            @php $classCount = $std->classes->count(); @endphp
                                            <th colspan="{{ $classCount ?: 1 }}" class="px-3 py-2.5 text-center font-semibold text-white bg-gradient-to-b from-cyan-600 to-cyan-700 border-r border-cyan-500">
                                                <span>{{ $std->name }}</span>
                                                @if($classCount > 0)<span class="block text-[10px] font-normal text-cyan-200 mt-0.5">{{ $classCount }} વર્ગ</span>@endif
                                            </th>
                                        @endforeach
                                    </tr>
                                    {{-- Class headers (period/time columns already spanned by row 1) --}}
                                    <tr class="bg-gradient-to-b from-cyan-50 to-white">
                                        @foreach($standards as $std)
                                            @if($std->classes->count())
                                                @foreach($std->classes as $cls)
                                                    <th class="px-2 py-2 text-center font-semibold text-cyan-900 border-r border-cyan-100 border-b border-cyan-100 shadow-sm">{{ $cls->name }}</th>
                                                @endforeach
                                            @else
                                                <th class="px-2 py-2 text-center text-gray-400 border-r border-cyan-100 border-b border-cyan-100 italic">—</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($slots as $slot)
                                        <tr class="hover:bg-white/60 transition-all duration-100 @if($slot->is_break) bg-amber-50/30 @endif">
                                            <td class="px-3 py-2.5 text-sm font-semibold text-gray-900 whitespace-nowrap border-r border-gray-100 bg-gray-50/50">
                                                <div class="flex items-center gap-1.5">
                                                    @if($slot->is_break)<i class="lni lni-coffee-cup-2 text-amber-400 text-xs"></i>@endif
                                                    {{ $slot->name_gu }}
                                                </div>
                                                @if($slot->is_break)<span class="text-[10px] text-amber-500 font-medium">વિરામ</span>@endif
                                            </td>
                                            <td class="px-3 py-2.5 whitespace-nowrap border-r border-gray-100 bg-gray-50/50">
                                                <span class="text-[11px] font-medium text-gray-600">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') }}</span>
                                                <span class="text-gray-300 mx-1">–</span>
                                                <span class="text-[11px] font-medium text-gray-600">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i') }}</span>
                                            </td>
                                            @foreach($standards as $std)
                                                @forelse($std->classes as $cls)
                                                    @php
                                                        $key = $dayNum . '-' . $slot->id . '-' . $std->id . '-' . $cls->id;
                                                        $entry = $allEntries->get($key);
                                                        $isConflict = isset($conflicts[$dayNum . '-' . $slot->id . '-' . $std->id . '-' . $cls->id]);
                                                        $stdSubj = $standardSubjects[$std->id] ?? collect();
                                                    @endphp
                                                    <td class="px-1.5 py-1.5 border-r border-gray-50 @if($isConflict) bg-red-50/60 @endif" style="min-width:120px">
                                                        @if($slot->is_break)
                                                            <div class="flex items-center justify-center h-full py-2">
                                                                <span class="text-[11px] text-amber-400 font-medium">— વિરામ —</span>
                                                            </div>
                                                        @else
                                                              <div class="cell-entry min-h-[48px] flex flex-col items-center justify-center gap-1 rounded-xl border transition-all duration-150 px-2 py-2 relative
                                                                  @if($entry) border-cyan-200 bg-white shadow-sm @if(!$readOnly) cursor-pointer hover:shadow-md hover:border-cyan-400 hover:bg-cyan-50/80 @endif
                                                                  @elseif($stdSubj->count()) border-dashed border-amber-200 bg-amber-50/30 @if(!$readOnly) hover:shadow-sm hover:border-amber-400 hover:bg-amber-50/80 cursor-pointer @endif
                                                                  @else border-dashed border-gray-200 bg-white @if(!$readOnly) hover:bg-gray-50 hover:border-gray-300 cursor-pointer @endif
                                                                  @endif
                                                                  @if($isConflict) !border-2 !border-solid !border-red-300 !bg-red-50 @if(!$readOnly) hover:!bg-red-100/80 hover:!shadow-sm @endif @endif"
                                                                  @if(!$readOnly) onclick="openCellEditor(this, {{ $dayNum }}, {{ $slot->id }}, {{ $std->id }}, {{ $cls->id }})" @endif
                                                                  data-entry='@json($entry, JSON_HEX_APOS)'>
                                                                  @if($entry)
                                                                      <span class="text-[11px] font-bold text-gray-800 leading-tight text-center px-1.5 py-0.5 bg-white rounded-md shadow-xs">{{ $entry->subject?->name ?? '—' }}</span>
                                                                      <span class="text-[10px] text-gray-500 flex items-center gap-1 leading-tight">
                                                                          <i class="lni lni-user-4 text-[9px]"></i>
                                                                          {{ $entry->teacher?->name ?? '—' }}
                                                                      </span>
                                                                      @if($isConflict)
                                                                          <span class="text-[8px] text-red-500 font-semibold mt-0.5 flex items-center gap-0.5">
                                                                              <i class="lni lni-ban-2 text-[9px]"></i> સમય વિરોધાભાસ
                                                                          </span>
                                                                      @endif
                                                                      @if(!$readOnly)
                                                                      <button type="button" onclick="event.stopPropagation(); clearCell({{ $dayNum }}, {{ $slot->id }}, {{ $std->id }}, {{ $cls->id }})" class="absolute -top-1.5 -right-1.5 w-5 h-5 flex items-center justify-center bg-white border border-red-200 text-red-500 rounded-full shadow-sm hover:bg-red-50 hover:border-red-400 hover:text-red-700 transition z-10" title="આ સેલ સાફ કરો">
                                                                          <i class="lni lni-trash-3 text-[9px]"></i>
                                                                      </button>
                                                                      @endif
                                                                  @elseif($stdSubj->count() && !$readOnly)
                                                                     <span class="text-[11px] text-amber-500 font-medium flex items-center gap-1">
                                                                         <i class="lni lni-plus text-xs"></i> ઉમેરો
                                                                     </span>
                                                                 @else
                                                                     <span class="text-[11px] text-gray-300">—</span>
                                                                 @endif
                                                             </div>
                                                        @endif
                                                    </td>
                                                @empty
                                                    <td class="px-3 py-2.5 text-center text-gray-300 border-r border-gray-50 text-[11px] bg-gray-50/20">—</td>
                                                @endforelse
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- ==================== TAB 2: PERIOD MANAGEMENT ==================== --}}
        <div x-show="activeTab === 'periods'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="space-y-4">

                {{-- Stats + Add button --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-2.5">
                            <span class="text-[11px] text-gray-500">કુલ પીરિયડ</span>
                            <p class="text-lg font-bold text-gray-900">{{ $slots->count() }}</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-2.5">
                            <span class="text-[11px] text-gray-500">વિરામ</span>
                            <p class="text-lg font-bold text-amber-500">{{ $slots->where('is_break', true)->count() }}</p>
                        </div>
                    </div>
                    @if(!$readOnly)
                    <button onclick="openSlotModal()" class="px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg text-sm font-medium hover:from-cyan-600 hover:to-cyan-700 transition shadow-sm flex items-center gap-2">
                        <i class="lni lni-plus text-sm"></i> નવો પીરિયડ
                    </button>
                    @endif
                </div>

                {{-- Periods Table --}}
                @if($slots->count())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white">
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider w-16">ક્રમ</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">નામ</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">સમય</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">શનિવાર</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">પ્રકાર</th>
                                        @if(!$readOnly)<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">ક્રિયા</th>@endif
                                    </tr>
                                </thead>
                                <tbody id="slotTableBody" class="divide-y divide-gray-100">
                                    @foreach($slots as $idx => $slot)
                                        <tr class="hover:bg-gray-50 transition slot-row @if($slot->is_break) bg-amber-50/30 @endif" data-id="{{ $slot->id }}">
                                            <td class="px-4 py-3 text-sm text-gray-500 drag-handle @if(!$readOnly) cursor-grab @endif">
                                                <i class="lni lni-arrow-all-direction text-gray-300 text-base"></i>
                                                <span class="ml-1 font-medium">{{ $idx + 1 }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-gray-900">{{ $slot->name_gu }}</span>
                                                <span class="text-[11px] text-gray-400 block">{{ $slot->name_en }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                @if($slot->saturday_start_time)
                                                    {{ \Carbon\Carbon::parse($slot->saturday_start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->saturday_end_time)->format('h:i A') }}
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($slot->is_break)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-100 text-amber-700"><i class="lni lni-coffee-cup-2 text-[10px] mr-1"></i> વિરામ</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-cyan-100 text-cyan-700"><i class="lni lni-book-1 text-[10px] mr-1"></i> પીરિયડ</span>
                                                @endif
                                            </td>
                                            @if(!$readOnly)
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <button onclick="editSlot({{ $slot->id }})" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-50 rounded-lg transition" title="સંપાદિત કરો">
                                                    <i class="lni lni-pencil-1 text-sm"></i>
                                                </button>
                                                <button onclick="deleteSlot({{ $slot->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો">
                                                    <i class="lni lni-trash-3 text-sm"></i>
                                                </button>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="lni lni-file-question text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 font-medium">કોઈ પીરિયડ ઉમેરાયા નથી.</p>
                        <p class="text-sm text-gray-400 mt-1">ઉપરના બટનથી નવો પીરિયડ ઉમેરો.</p>
                    </div>
                @endif
            </div>
        </div>

        @endif {{-- activeYear --}}
    </div>
</div>

{{-- Period Modal --}}
<div id="slotModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeSlotModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white" id="slotModalTitle">નવો પીરિયડ</h3>
                    <button onclick="closeSlotModal()" class="text-white/80 hover:text-white transition p-1">
                        <i class="lni lni-xmark text-lg"></i>
                    </button>
                </div>
            </div>
            <form id="slotForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="slot_id" id="slot_id">
                <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">નામ (ગુજરાતી) <span class="text-red-400">*</span></label>
                        <input type="text" name="name_gu" id="name_gu" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">નામ (English) <span class="text-red-400">*</span></label>
                        <input type="text" name="name_en" id="name_en" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">શરૂઆત <span class="text-red-400">*</span></label>
                        <input type="time" name="start_time" id="start_time" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">પૂરો થવાનો સમય <span class="text-red-400">*</span></label>
                        <input type="time" name="end_time" id="end_time" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">શનિવાર માટે અલગ સમય</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="time" name="saturday_start_time" id="saturday_start_time" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500">
                        <input type="time" name="saturday_end_time" id="saturday_end_time" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_break" id="is_break" value="1" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-checked:bg-amber-400 peer-focus:outline-none rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                    <span class="text-sm text-gray-600">વિરામ/પ્રાર્થના પીરિયડ</span>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeSlotModal()" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">રદ કરો</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg text-sm font-medium hover:from-cyan-600 hover:to-cyan-700 transition shadow-sm flex items-center gap-2">
                        <i class="lni lni-floppy-disk-1 text-sm"></i> <span id="slotSubmitText">સેવ કરો</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cell Editor Modal --}}
<div id="cellModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCellEditor()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">સેલ એડિટ કરો</h3>
                    <button onclick="closeCellEditor()" class="text-white/80 hover:text-white transition p-1">
                        <i class="lni lni-xmark text-lg"></i>
                    </button>
                </div>
            </div>
            <form id="cellForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="cell_academic_year_id" id="cell_academic_year_id" value="{{ $academicYearId }}">
                <input type="hidden" name="cell_standard_id" id="cell_standard_id">
                <input type="hidden" name="cell_class_id" id="cell_class_id">
                <input type="hidden" name="cell_day_of_week" id="cell_day_of_week">
                <input type="hidden" name="cell_timetable_slot_id" id="cell_timetable_slot_id">

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">આ ધોરણના વિષયો</label>
                    <select name="cell_subject_id" id="cell_subject_id" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">— વિષય પસંદ કરો —</option>
                    </select>
                    <p id="noSubjectsMsg" class="hidden text-[11px] text-amber-500 mt-1">આ ધોરણમાં કોઈ વિષય ઉમેરાયા નથી.</p>
                </div>

                <div id="teacherSection" class="hidden">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">શિક્ષક</label>
                    <select name="cell_teacher_id" id="cell_teacher_id" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">— શિક્ષક પસંદ કરો —</option>
                    </select>
                    <p id="autoTeacherMsg" class="hidden text-[11px] text-emerald-500 mt-1 flex items-center gap-1">
                        <i class="lni lni-check-circle-1 text-[10px]"></i> <span id="autoTeacherText"></span>
                    </p>
                    <p id="noTeacherMsg" class="hidden text-[11px] text-amber-500 mt-1">આ વિષય માટે કોઈ શિક્ષક સોંપાયા નથી.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeCellEditor()" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">રદ કરો</button>
                    <button type="submit" id="cellSaveBtn" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg text-sm font-medium hover:from-cyan-600 hover:to-cyan-700 transition shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="lni lni-floppy-disk-1 text-sm"></i> સેવ કરો
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if($activeYear)
    // ========== DATA ==========
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const subjectAssignments = @json($subjectAssignmentsJs);
    const stdSubjects = @json($standardSubjectsJs);

    // ========== NAVIGATION ==========
    const aySelect = document.getElementById('ay_select');
    aySelect.addEventListener('change', function() {
        window.location.href = '{{ route("timetable.index") }}?academic_year_id=' + this.value;
    });

    // ========== PERIOD MANAGEMENT ==========
    function openSlotModal() {
        document.getElementById('slot_id').value = '';
        document.getElementById('slotForm').reset();
        document.getElementById('slotModalTitle').textContent = 'નવો પીરિયડ';
        document.getElementById('slotSubmitText').textContent = 'સેવ કરો';
        document.getElementById('slotModal').classList.remove('hidden');
    }

    function closeSlotModal() {
        document.getElementById('slotModal').classList.add('hidden');
    }

    function editSlot(id) {
        fetch('/timetable/slots/' + id).then(r => r.json()).then(data => {
            const s = data.slot;
            document.getElementById('slot_id').value = s.id;
            document.getElementById('name_gu').value = s.name_gu;
            document.getElementById('name_en').value = s.name_en;
            document.getElementById('start_time').value = s.start_time.substring(0, 5);
            document.getElementById('end_time').value = s.end_time.substring(0, 5);
            document.getElementById('saturday_start_time').value = s.saturday_start_time ? s.saturday_start_time.substring(0, 5) : '';
            document.getElementById('saturday_end_time').value = s.saturday_end_time ? s.saturday_end_time.substring(0, 5) : '';
            document.getElementById('is_break').checked = s.is_break;
            document.getElementById('slotModalTitle').textContent = 'પીરિયડ એડિટ કરો';
            document.getElementById('slotSubmitText').textContent = 'અપડેટ કરો';
            document.getElementById('slotModal').classList.remove('hidden');
        }).catch(err => {
            NexSchool.alert.danger('પીરિયડ ડેટા લાવવામાં ભૂલ: ' + err.message);
        });
    }

    function deleteSlot(id) {
        NexSchool.confirm.show('શું તમે ખરેખર આ પીરિયડ કાઢી નાખવા માંગો છો?', '', 'danger', 'હા, કાઢી નાખો').then(confirmed => {
            if (!confirmed) return;
            fetch('/timetable/slots/' + id, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
            .then(r => r.json()).then(data => {
                NexSchool.alert.success(data.message);
                window.location.reload();
            }).catch(err => {
                NexSchool.alert.danger('પીરિયડ કાઢવામાં ભૂલ: ' + err.message);
            });
        });
    }

    document.getElementById('slotForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('slot_id').value;
        const url = id ? ('/timetable/slots/' + id) : '/timetable/slots';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(Object.fromEntries(new FormData(this)))
        })
        .then(r => r.json()).then(data => {
            if (data.success) {
                NexSchool.alert.success(data.message);
                closeSlotModal();
                window.location.reload();
            }
        }).catch(err => {
            NexSchool.alert.danger('પીરિયડ સેવ કરવામાં ભૂલ: ' + err.message);
        });
    });

    // SortableJS for slot reorder
    @if($slots->count() > 1 && !$readOnly)
    const slotBody = document.getElementById('slotTableBody');
    if (slotBody) {
        new Sortable(slotBody, {
            handle: '.drag-handle',
            animation: 200,
            onEnd: function() {
                const items = [];
                slotBody.querySelectorAll('.slot-row').forEach((row, idx) => {
                    items.push({ id: row.dataset.id, sort_order: idx + 1 });
                });
                fetch('/timetable/slots/reorder', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ slots: items })
                }).then(r => r.json()).then(data => {
                    if (data.success) window.location.reload();
                }).catch(err => {
                    NexSchool.alert.danger('ક્રમ બદલવામાં ભૂલ: ' + err.message);
                });
            }
        });
    }
    @endif

    // ========== CELL EDITOR ==========
    let currentCellEl = null;
    let currentCellData = {};

    function openCellEditor(cellEl, day, slotId, stdId, clsId) {
        currentCellEl = cellEl;
        currentCellData = { day, slotId, stdId, clsId };

        document.getElementById('cell_day_of_week').value = day;
        document.getElementById('cell_timetable_slot_id').value = slotId;
        document.getElementById('cell_standard_id').value = stdId;
        document.getElementById('cell_class_id').value = clsId;

        const subjSelect = document.getElementById('cell_subject_id');
        const teacherSelect = document.getElementById('cell_teacher_id');
        const teacherSection = document.getElementById('teacherSection');
        const noSubjectsMsg = document.getElementById('noSubjectsMsg');
        const noTeacherMsg = document.getElementById('noTeacherMsg');
        const autoTeacherMsg = document.getElementById('autoTeacherMsg');
        const autoTeacherText = document.getElementById('autoTeacherText');
        const cellSaveBtn = document.getElementById('cellSaveBtn');
        const modal = document.getElementById('cellModal');

        subjSelect.innerHTML = '<option value="">— વિષય પસંદ કરો —</option>';
        teacherSelect.innerHTML = '<option value="">— શિક્ષક પસંદ કરો —</option>';
        teacherSection.classList.add('hidden');
        noSubjectsMsg.classList.add('hidden');
        noTeacherMsg.classList.add('hidden');
        autoTeacherMsg.classList.add('hidden');
        cellSaveBtn.disabled = true;
        cellSaveBtn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સેવ કરો';

        // Load subjects for this standard
        const subjects = stdSubjects[stdId] || {};
        const subjectIds = Object.keys(subjects);

        if (subjectIds.length === 0) {
            noSubjectsMsg.classList.remove('hidden');
            modal.classList.remove('hidden');
            return;
        }

        subjectIds.forEach(id => {
            const s = subjects[id];
            subjSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
        });
        cellSaveBtn.disabled = false;

        // Show the modal
        modal.classList.remove('hidden');

        // Try to pre-select from existing entry
        try {
            const entryData = cellEl.dataset.entry;
            if (entryData && entryData !== 'null') {
                const entry = JSON.parse(entryData);
                if (entry && entry.subject_id) {
                    subjSelect.value = entry.subject_id;
                    loadTeachers(subjSelect.value, stdId, clsId, entry.teacher_id || null);
                }
            }
        } catch(e) {}
    }

    function closeCellEditor() {
        document.getElementById('cellModal').classList.add('hidden');
    }

    document.getElementById('cell_subject_id').addEventListener('change', function() {
        const stdId = parseInt(document.getElementById('cell_standard_id').value);
        const clsId = parseInt(document.getElementById('cell_class_id').value);
        loadTeachers(this.value, stdId, clsId, null);
    });

    function loadTeachers(subjectId, stdId, clsId, preselectedTeacherId) {
        const teacherSelect = document.getElementById('cell_teacher_id');
        const teacherSection = document.getElementById('teacherSection');
        const noTeacherMsg = document.getElementById('noTeacherMsg');
        const autoTeacherMsg = document.getElementById('autoTeacherMsg');
        const autoTeacherText = document.getElementById('autoTeacherText');

        teacherSelect.innerHTML = '<option value="">— શિક્ષક પસંદ કરો —</option>';
        teacherSection.classList.add('hidden');
        noTeacherMsg.classList.add('hidden');
        autoTeacherMsg.classList.add('hidden');

        if (!subjectId) return;

        const exactKey = stdId + '-' + clsId + '-' + subjectId;
        const nullKey = stdId + '-0-' + subjectId;
        const assignments = subjectAssignments[exactKey] || subjectAssignments[nullKey] || [];

        if (assignments.length === 0) {
            teacherSection.classList.remove('hidden');
            noTeacherMsg.classList.remove('hidden');
            return;
        }

        teacherSection.classList.remove('hidden');

        assignments.forEach(a => {
            if (a.teacher) {
                teacherSelect.innerHTML += `<option value="${a.teacher.id}">${a.teacher.name}</option>`;
            }
        });

        if (assignments.length === 1 && assignments[0].teacher) {
            // Auto-select if only one teacher
            teacherSelect.value = assignments[0].teacher.id;
            autoTeacherText.textContent = 'આ વિષય માટે ' + assignments[0].teacher.name + ' ઓટો-સિલેક્ટ થયા.';
            autoTeacherMsg.classList.remove('hidden');
        } else if (preselectedTeacherId) {
            teacherSelect.value = preselectedTeacherId;
        }
    }

    // Direct cell clear without modal
    function clearCell(day, slotId, stdId, classId) {
        fetch('/timetable/entries/update', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                academic_year_id: @json($academicYearId),
                day_of_week: day,
                timetable_slot_id: slotId,
                standard_id: stdId,
                school_class_id: classId,
                subject_id: null,
                teacher_id: null,
            })
        })
        .then(r => r.json()).then(res => {
            if (res.success) {
                NexSchool.alert.success(res.message);
                window.location.reload();
            } else {
                NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
            }
        }).catch(err => {
            NexSchool.alert.danger('સર્વર ભૂલ: ' + err.message);
        });
    }

    document.getElementById('cellForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('cellSaveBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="lni lni-spinner-3 text-xs animate-spin"></i> સેવ થઈ રહ્યું...';

        const fd = new FormData(this);
        const data = {
            academic_year_id: fd.get('cell_academic_year_id'),
            standard_id: fd.get('cell_standard_id'),
            school_class_id: fd.get('cell_class_id'),
            day_of_week: fd.get('cell_day_of_week'),
            timetable_slot_id: fd.get('cell_timetable_slot_id'),
            subject_id: fd.get('cell_subject_id') || null,
            teacher_id: fd.get('cell_teacher_id') || null,
        };

        fetch('/timetable/entries/update', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data)
        })
        .then(r => r.json()).then(res => {
            if (res.success) {
                NexSchool.alert.success(res.message);
                closeCellEditor();
                window.location.reload();
            } else {
                NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
                btn.disabled = false;
                btn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સેવ કરો';
            }
        })
        .catch(err => {
            NexSchool.alert.danger('સર્વર ભૂલ: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સેવ કરો';
        });
    });

    // ========== CLEAR ALL ==========
    document.getElementById('clearAllBtn')?.addEventListener('click', function() {
        NexSchool.confirm.show('બધા ધોરણો અને વર્ગોના ટાઇમટેબલ સાફ કરવા છે?', '', 'danger', 'હા, બધું સાફ કરો').then(confirmed => {
            if (!confirmed) return;
            fetch('/timetable/entries/clear', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ academic_year_id: @json($academicYearId) })
            })
            .then(r => r.json()).then(data => {
                NexSchool.alert.success(data.message);
                window.location.reload();
            }).catch(err => {
                NexSchool.alert.danger('સાફ કરવામાં ભૂલ: ' + err.message);
            });
        });
    });

    // ========== COPY DAY TO ALL DAYS ==========
    document.getElementById('copyDayBtn')?.addEventListener('click', function() {
        const btn = this;
        const activeDay = parseInt(localStorage.getItem('tt_day')) || 1;
        const dayNames = @json($days);
        NexSchool.confirm.show('બધા દિવસોમાં કોપી કરો', 'શું તમે ' + dayNames[activeDay] + ' નું ટાઇમટેબલ બધા દિવસોમાં કોપી કરવા માંગો છો?', 'info', 'હા, કોપી કરો').then(confirmed => {
            if (!confirmed) return;
            btn.disabled = true;
            btn.innerHTML = '<i class="lni lni-spinner-3 text-xs animate-spin"></i> કોપી થઈ રહ્યું...';
            fetch('/timetable/entries/copy-all', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ academic_year_id: @json($academicYearId), from_day: activeDay })
            })
            .then(r => r.json()).then(data => {
                NexSchool.alert.success(data.message);
                window.location.reload();
            }).catch(err => {
                NexSchool.alert.danger('કોપી કરવામાં ભૂલ: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="lni lni-copy text-[10px]"></i> બધા દિવસોમાં કોપી કરો';
            });
        });
    });

    @endif
</script>
@endpush
@endsection
