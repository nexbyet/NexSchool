@extends('layouts.app')
@section('title', 'વિષય શિક્ષક સોંપણી')
@section('content')
<div class="p-4 md:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">વિષય શિક્ષક સોંપણી</h1>
                <p class="text-gray-500 mt-1 text-sm">ધોરણ અને વર્ગ મુજબ વિષયના શિક્ષકો સોંપો — શૈક્ષણિક વર્ષ મુજબ અલગ અલગ હોઈ શકે</p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('subject-assignments.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-auto">
                <label for="academic_year_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">શૈક્ષણિક વર્ષ</label>
                <select name="academic_year_id" id="academic_year_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white min-w-[160px] appearance-none cursor-pointer">
                    @foreach ($academicYears as $ay)
                        <option value="{{ $ay->id }}" @if($ay->id == $academicYearId) selected @endif>
                            {{ $ay->year }} @if($ay->is_active) — ચાલુ વર્ષ @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="standard_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">ધોરણ</label>
                <select name="standard_id" id="standard_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white min-w-[160px] appearance-none cursor-pointer">
                    <option value="">— ધોરણ પસંદ કરો —</option>
                    @foreach ($standards as $std)
                        <option value="{{ $std->id }}" @if($std->id == $standardId) selected @endif>ધોરણ {{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if ($standardId && $academicYearId)
        @if ($subjects->isEmpty())
            <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="lni lni-book-1 text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">આ ધોરણમાં કોઈ વિષય સોંપાયેલ નથી</p>
                <p class="text-gray-400 text-sm mb-4">પહેલા વિષયો પેજ પર જઈને આ ધોરણને વિષયો સોંપો</p>
                <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="lni lni-arrow-right text-sm"></i> વિષયો સોંપવા જાઓ
                </a>
            </div>
        @elseif ($classes->isEmpty())
            <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="lni lni-buildings-1 text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">આ ધોરણમાં કોઈ વર્ગ નથી</p>
                <p class="text-gray-400 text-sm mt-1">પહેલા ધોરણ અને વર્ગ પેજ પર વર્ગો ઉમેરો</p>
            </div>
        @else
            {{-- Stats Bar --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">કુલ સ્લોટ્સ:</span>
                            <span class="text-lg font-bold text-gray-900">{{ $totalSlots }}</span>
                        </div>
                        <div class="w-px h-6 bg-gray-200 hidden sm:block"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">સોંપાયેલ:</span>
                            <span class="text-lg font-bold text-emerald-600">{{ $assignedCount }}</span>
                        </div>
                        <div class="w-px h-6 bg-gray-200 hidden sm:block"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">બાકી:</span>
                            <span class="text-lg font-bold text-amber-600">{{ $totalSlots - $assignedCount }}</span>
                        </div>
                    </div>
                    <div class="w-full sm:w-48 bg-gray-100 rounded-full h-2.5">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $totalSlots > 0 ? ($assignedCount / $totalSlots * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Class Cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach ($classes as $cls)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        {{-- Card Header --}}
                        <div class="px-5 py-3.5 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="lni lni-user-multiple-4 text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">ધોરણ {{ $stdName ?? '' }} — વર્ગ {{ $cls->name }}</h3>
                                    @php
                                        $clsAssigned = $subjects->filter(fn($s) => $assignments->has($s->id . '-' . $cls->id) && $assignments->get($s->id . '-' . $cls->id)->teacher_id)->count();
                                    @endphp
                                    <p class="text-xs text-gray-400">{{ $clsAssigned }}/{{ $subjects->count() }} વિષયો સોંપાયેલ</p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full font-medium @if($clsAssigned == $subjects->count()) bg-emerald-100 text-emerald-700 @else bg-amber-100 text-amber-700 @endif">
                                {{ $clsAssigned == $subjects->count() ? 'પૂર્ણ' : ($clsAssigned > 0 ? 'આંશિક' : 'બાકી') }}
                            </span>
                        </div>

                        {{-- Subjects List --}}
                        <div class="p-4 space-y-2">
                            @foreach ($subjects as $sub)
                                @php
                                    $key = $sub->id . '-' . $cls->id;
                                    $assigned = $assignments->get($key);
                                    $hasTeacher = $assigned && $assigned->teacher_id;
                                @endphp
                                <div class="flex items-center justify-between px-4 py-2.5 rounded-lg border transition
                                    @if($hasTeacher) border-emerald-200 bg-emerald-50/30 @else border-gray-200 bg-white hover:bg-gray-50 @endif"
                                    id="sta-row-{{ $sub->id }}-{{ $cls->id }}">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <div class="w-2 h-2 rounded-full flex-shrink-0 @if($hasTeacher) bg-emerald-500 @else bg-gray-300 @endif"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $sub->name }}</span>
                                        <span id="sta-badge-{{ $sub->id }}-{{ $cls->id }}" class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                                            @if($hasTeacher) bg-emerald-100 text-emerald-700 @else bg-gray-100 text-gray-400 @endif">
                                            {{ $hasTeacher ? $assigned->teacher->name : '—' }}
                                        </span>
                                    </div>
                                    <select onchange="assignTeacher({{ $sub->id }}, {{ $cls->id }}, this)"
                                            class="ml-2 px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 outline-none bg-white flex-shrink-0 min-w-[130px] max-w-[160px] appearance-none cursor-pointer teacher-select"
                                            data-has-teacher="{{ $hasTeacher ? '1' : '0' }}">
                                        <option value="">— ના —</option>
                                        @foreach ($allTeachers as $t)
                                            <option value="{{ $t->id }}" @if($hasTeacher && $assigned->teacher_id == $t->id) selected @endif>
                                                {{ $t->name }}{{ $t->status !== 'active' ? ' [inactive]' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 bg-white rounded-xl border border-gray-200">
            <div class="w-20 h-20 mx-auto mb-5 bg-indigo-50 rounded-full flex items-center justify-center">
                <i class="lni lni-arrow-upward text-3xl text-indigo-400"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">શરૂ કરવા માટે ધોરણ પસંદ કરો</h2>
            <p class="text-gray-400 max-w-md mx-auto">ઉપર શૈક્ષણિક વર્ષ અને ધોરણ પસંદ કર્યા પછી દરેક વર્ગના વિષયો માટે શિક્ષકો સોંપી શકાશે</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const academicYearId = {{ $academicYearId ?? 'null' }};

function assignTeacher(subjectId, classId, select) {
    const teacherId = select.value;
    const row = document.getElementById('sta-row-' + subjectId + '-' + classId);
    const badge = document.getElementById('sta-badge-' + subjectId + '-' + classId);

    fetch('{{ route("subject-assignments.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            subject_id: subjectId,
            teacher_id: teacherId,
            standard_id: {{ $standardId ?? 'null' }},
            class_id: classId,
            academic_year_id: academicYearId,
        }),
    })
    .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
    .then(data => {
        if (data.success) {
            if (data.teacher_id) {
                badge.textContent = data.teacher_name;
                badge.className = 'text-xs px-2 py-0.5 rounded-full font-medium bg-emerald-100 text-emerald-700';
                row.className = 'flex items-center justify-between px-4 py-2.5 rounded-lg border transition border-emerald-200 bg-emerald-50/30';
                row.querySelector('.w-2').className = 'w-2 h-2 rounded-full flex-shrink-0 bg-emerald-500';
            } else {
                badge.textContent = '—';
                badge.className = 'text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-400';
                row.className = 'flex items-center justify-between px-4 py-2.5 rounded-lg border transition border-gray-200 bg-white hover:bg-gray-50';
                row.querySelector('.w-2').className = 'w-2 h-2 rounded-full flex-shrink-0 bg-gray-300';
            }
            NexSchool.alert.success(data.message);
        } else {
            NexSchool.alert.danger(data.message || 'ભૂલ આવી.');
        }
    })
    .catch(err => NexSchool.alert.danger('સર્વર ભૂલ: ' + err.message));
}
</script>
@endpush
