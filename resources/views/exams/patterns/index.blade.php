@extends('layouts.app')
@section('title', 'પરીક્ષા પેટર્ન')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-500 to-purple-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">પરીક્ષા પેટર્ન</h1>
                <p class="text-violet-200 mt-1 text-sm">સત્ર-વાર ગુણના હેડ અને ધોરણ × વર્ગ × વિષય સાથે પેટર્ન બનાવો</p>
            </div>
            <a href="{{ route('exams.patterns.create') }}" class="px-4 py-2.5 bg-white text-violet-700 text-sm font-semibold rounded-lg hover:bg-violet-50 transition flex items-center gap-2 shadow-sm">
                <i class="lni lni-plus text-sm"></i> નવી પેટર્ન
            </a>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="max-w-xs">
            <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ (ફિલ્ટર)</label>
            <select id="year-filter" onchange="filterByYear()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                <option value="">બધા વર્ષો</option>
                @foreach ($academicYears as $y)
                <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="patterns-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($patterns as $p)
            @php
                $sem1 = $p->heads->where('semester', 1)->values();
                $sem2 = $p->heads->where('semester', 2)->values();
            @endphp
            <div class="pattern-card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" data-year="{{ $p->academic_year_id }}">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-violet-50 to-purple-50/50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $p->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $p->academicYear->year ?? '' }} @if($p->description) · {{ $p->description }} @endif</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($p->is_active)
                            <span class="text-xs font-medium text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">ચાલુ</span>
                            @else
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">બંધ</span>
                            @endif
                            <a href="{{ route('exams.patterns.edit', $p->id) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></a>
                            <button onclick="openCopyModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 text-sky-600 hover:bg-sky-50 rounded-lg transition" title="બીજા વર્ષમાં નકલ"><i class="lni lni-layers-1 text-sm"></i></button>
                            <button onclick="deletePattern({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો"><i class="lni lni-trash-3 text-sm"></i></button>
                        </div>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-lg border border-sky-200 bg-sky-50/50 p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-sky-800">સત્ર 1</span>
                            <span class="text-xs font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">પેપર {{ number_format($sem1->sum('total_marks'), 2) }} → ગણતરી {{ number_format($sem1->sum('converted_marks'), 2) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($sem1 as $h)
                            <span class="text-xs font-medium text-sky-700 bg-white border border-sky-200 px-2 py-0.5 rounded-full">{{ $h->name }} · {{ number_format($h->total_marks, 2) }}→{{ number_format($h->converted_marks, 2) }}</span>
                            @empty
                            <span class="text-xs text-gray-400">—</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50/50 p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-indigo-800">સત્ર 2</span>
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full">પેપર {{ number_format($sem2->sum('total_marks'), 2) }} → ગણતરી {{ number_format($sem2->sum('converted_marks'), 2) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($sem2 as $h)
                            <span class="text-xs font-medium text-indigo-700 bg-white border border-indigo-200 px-2 py-0.5 rounded-full">{{ $h->name }} · {{ number_format($h->total_marks, 2) }}→{{ number_format($h->converted_marks, 2) }}</span>
                            @empty
                            <span class="text-xs text-gray-400">—</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-5 flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $p->standards_count ?? 0 }} ધોરણો · {{ $p->applies_count ?? 0 }} જગ્યાએ લાગુ</span>
                    <a href="{{ route('exams.patterns.edit', $p->id) }}" class="text-violet-600 font-medium hover:text-violet-700">વિગત જુઓ <i class="lni lni-arrow-right text-xs"></i></a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-graduation-cap-1 text-3xl text-violet-400"></i>
                </div>
                <p class="text-gray-500 font-medium">હજી કોઈ પરીક્ષા પેટર્ન બનાવી નથી</p>
                <p class="text-gray-400 text-sm mt-1">ઉપર "નવી પેટર્ન" દબાવીને શરૂ કરો</p>
            </div>
        @endforelse
    </div>
    <div id="no-result" class="hidden text-center py-12 bg-white rounded-xl border border-gray-200 mt-5">
        <p class="text-gray-500 font-medium">આ વર્ષમાં કોઈ પેટર્ન નથી</p>
    </div>
</div>

{{-- Copy Modal --}}
<div id="copy-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">બીજા વર્ષમાં નકલ</h3>
            <button type="button" onclick="closeCopyModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"><i class="lni lni-xmark text-lg"></i></button>
        </div>
        <p id="copy-pattern-name" class="text-sm text-gray-600 mb-3"></p>
        <label class="block text-sm font-medium text-gray-700 mb-1">કયા શૈક્ષણિક વર્ષમાં નકલ કરવી? <span class="text-red-500">*</span></label>
        <select id="copy-year" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition mb-4">
            @foreach ($academicYears as $y)
            <option value="{{ $y->id }}">{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mb-4">નકલ બન્યા પછી સુધારા પેજ ખુલશે — ભૂલ સુધારી શકાશે.</p>
        <div class="flex items-center justify-end gap-3">
            <button type="button" onclick="closeCopyModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
            <button type="button" onclick="doCopy()" id="copy-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition flex items-center gap-2">
                <i class="lni lni-layers-1 text-sm"></i> નકલ બનાવો
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var copyModal = document.getElementById('copy-modal');
    var copyPatternId = null;

    var filterByYear = function() {
        var yf = document.getElementById('year-filter').value;
        var cards = document.querySelectorAll('.pattern-card');
        var visible = 0;
        for (var i = 0; i < cards.length; i++) {
            var show = !yf || cards[i].getAttribute('data-year') === yf;
            cards[i].style.display = show ? '' : 'none';
            if (show) visible++;
        }
        document.getElementById('no-result').classList.toggle('hidden', visible > 0);
    };
    window.filterByYear = filterByYear;
    filterByYear();

    var openCopyModal = function(id, name) {
        copyPatternId = id;
        document.getElementById('copy-pattern-name').textContent = '"' + name + '" ની નકલ';
        copyModal.classList.remove('hidden');
        requestAnimationFrame(function() { copyModal.style.opacity = '1'; });
    };
    window.openCopyModal = openCopyModal;

    var closeCopyModal = function() {
        copyModal.style.opacity = '0';
        setTimeout(function() { copyModal.classList.add('hidden'); }, 200);
    };
    window.closeCopyModal = closeCopyModal;
    copyModal.addEventListener('click', function(e) { if (e.target === copyModal) closeCopyModal(); });

    var doCopy = function() {
        var yearId = document.getElementById('copy-year').value;
        if (!yearId || !copyPatternId) return;
        var btn = document.getElementById('copy-submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> નકલ બને છે...';
        fetch('{{ url("exams/patterns/copy") }}/' + copyPatternId, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ academic_year_id: parseInt(yearId) }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success(data.message);
                setTimeout(function() { window.location.href = '{{ url("exams/patterns") }}/' + data.pattern_id + '/edit'; }, 600);
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ.');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { btn.disabled = false; btn.innerHTML = '<i class="lni lni-layers-1 text-sm"></i> નકલ બનાવો'; });
    };
    window.doCopy = doCopy;

    var deletePattern = function(id, name) {
        NexSchool.confirm.show('પેટર્ન કાઢી નાખવી છે?', '"' + name + '" પેટર્ન, તેના બધા હેડ અને લાગુ પાડેલી વિગતો કાઢી નખાશે. આ કામ પાછું ફેરવી શકાશે નહીં.', 'danger', 'હા, કાઢી નાખો')
        .then(function(ok) {
            if (!ok) return;
            fetch('{{ url("exams/patterns/delete") }}/' + id, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            })
            .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
            .then(function(data) {
                if (data.success) { NexSchool.alert.success(data.message); location.reload(); }
                else { NexSchool.alert.danger(data.message || 'ભૂલ.'); }
            })
            .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); });
        });
    };
    window.deletePattern = deletePattern;
})();
</script>
@endpush
