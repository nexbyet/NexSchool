@extends('layouts.app')
@section('title', 'વર્ગશિક્ષક')
@section('content')
<div class="p-4 md:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">વર્ગશિક્ષક સોંપણી</h1>
                <p class="text-gray-500 mt-1 text-sm">દરેક વર્ગ માટે વર્ગશિક્ષક (મુખ્ય શિક્ષક) ની નિમણૂંક કરો — શૈક્ષણિક વર્ષ મુજબ અલગ અલગ શિક્ષક સોંપી શકાય</p>
            </div>
            <form method="GET" action="{{ route('class-teachers.index') }}">
                <select name="academic_year_id" id="academic_year_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white min-w-[160px] appearance-none cursor-pointer">
                    @foreach ($academicYears as $ay)
                        <option value="{{ $ay->id }}" @if($ay->id == $academicYearId) selected @endif>
                            {{ $ay->year }} @if($ay->is_active) — ચાલુ વર્ષ @endif
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">કુલ વર્ગો:</span>
                    <span class="text-lg font-bold text-gray-900">{{ $totalClasses }}</span>
                </div>
                <div class="w-px h-6 bg-gray-200 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">સોંપાયેલ:</span>
                    <span class="text-lg font-bold text-emerald-600">{{ $assignedCount }}</span>
                </div>
                <div class="w-px h-6 bg-gray-200 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">બાકી:</span>
                    <span class="text-lg font-bold text-amber-600">{{ $totalClasses - $assignedCount }}</span>
                </div>
            </div>
            <div class="w-full sm:w-48 bg-gray-100 rounded-full h-2.5">
                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $totalClasses > 0 ? ($assignedCount / $totalClasses * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    {{-- Standard Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($standards as $std)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                {{-- Card Header --}}
                <div class="px-5 py-3.5 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="lni lni-buildings-1 text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">ધોરણ {{ $std->name }}</h3>
                            @php
                                $stdAssigned = $std->classes->filter(fn($c) => $c->classTeacher && $c->classTeacher->teacher_id)->count();
                            @endphp
                            <p class="text-xs text-gray-400">{{ $stdAssigned }}/{{ $std->classes->count() }} વર્ગો સોંપાયેલ</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium @if($stdAssigned == $std->classes->count() && $std->classes->count() > 0) bg-emerald-100 text-emerald-700 @elseif($stdAssigned > 0) bg-amber-100 text-amber-700 @else bg-gray-100 text-gray-400 @endif">
                        {{ $std->classes->count() == 0 ? '—' : ($stdAssigned == $std->classes->count() ? 'પૂર્ણ' : ($stdAssigned > 0 ? 'આંશિક' : 'બાકી')) }}
                    </span>
                </div>

                {{-- Classes List --}}
                <div class="p-4 space-y-2">
                    @forelse ($std->classes as $cls)
                        @php $ct = $cls->classTeacher; $hasTeacher = $ct && $ct->teacher_id; @endphp
                        <div class="flex items-center justify-between px-4 py-2.5 rounded-lg border transition
                            @if($hasTeacher) border-emerald-200 bg-emerald-50/30 @else border-gray-200 bg-white hover:bg-gray-50 @endif"
                            id="ct-row-{{ $cls->id }}">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-2 h-2 rounded-full flex-shrink-0 @if($hasTeacher) bg-emerald-500 @else bg-gray-300 @endif"></div>
                                <span class="text-sm font-medium text-gray-700">વર્ગ {{ $cls->name }}</span>
                                <span id="ct-label-{{ $cls->id }}" class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                                    @if($hasTeacher) bg-emerald-100 text-emerald-700 @else bg-gray-100 text-gray-400 @endif">
                                    {{ $hasTeacher ? $ct->teacher->name : 'સોંપાયેલ નથી' }}
                                </span>
                            </div>
                            <select onchange="assignTeacher({{ $cls->id }}, this)"
                                    class="ml-2 px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 outline-none bg-white flex-shrink-0 min-w-[130px] max-w-[170px] appearance-none cursor-pointer">
                                <option value="">— પસંદ કરો —</option>
                                @foreach ($teachers as $t)
                                    <option value="{{ $t->id }}" @if($hasTeacher && $ct->teacher_id == $t->id) selected @endif>
                                        {{ $t->name }}{{ $t->status !== 'active' ? ' [inactive]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">આ ધોરણમાં કોઈ વર્ગ નથી</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="lni lni-buildings-1 text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium mb-1">હજી સુધી કોઈ ધોરણ ઉમેરાયું નથી</p>
                    <p class="text-gray-400 text-sm mb-4">પહેલા ધોરણ અને વર્ગ પેજ પર ધોરણો ઉમેરો</p>
                    <a href="{{ route('standards.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        ધોરણ ઉમેરવા જાઓ <i class="lni lni-arrow-right text-sm"></i>
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const academicYearId = {{ $academicYearId ?? 'null' }};

function assignTeacher(classId, select) {
    const teacherId = select.value;
    const row = document.getElementById('ct-row-' + classId);
    const label = document.getElementById('ct-label-' + classId);

    fetch('{{ route("class-teachers.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ class_id: classId, teacher_id: teacherId, academic_year_id: academicYearId }),
    })
    .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
    .then(data => {
        if (data.success) {
            if (data.teacher_name && data.teacher_name !== '—') {
                label.textContent = data.teacher_name;
                label.className = 'text-xs px-2 py-0.5 rounded-full font-medium bg-emerald-100 text-emerald-700';
                row.className = 'flex items-center justify-between px-4 py-2.5 rounded-lg border transition border-emerald-200 bg-emerald-50/30';
                row.querySelector('.w-2').className = 'w-2 h-2 rounded-full flex-shrink-0 bg-emerald-500';
            } else {
                label.textContent = 'સોંપાયેલ નથી';
                label.className = 'text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-400';
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
