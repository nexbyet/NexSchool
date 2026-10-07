@extends('layouts.app')
@section('title', 'ગ્રેડ વ્યવસ્થાપન')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ગ્રેડ વ્યવસ્થાપન</h1>
                <p class="text-amber-200 mt-1 text-sm">100 માંથી ગ્રેડ સેટ કરો — રિઝલ્ટમાં ટકા મુજબ ગ્રેડ ગણાશે</p>
            </div>
            <button onclick="openGradeModal()" class="px-4 py-2.5 bg-white text-orange-700 text-sm font-semibold rounded-lg hover:bg-orange-50 transition flex items-center gap-2 shadow-sm">
                <i class="lni lni-plus text-sm"></i> નવો સ્કેલ
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="max-w-xs">
            <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ (ફિલ્ટર)</label>
            <select id="year-filter" onchange="filterByYear()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                <option value="">બધા વર્ષો</option>
                @foreach ($academicYears as $y)
                <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="scales-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($scales as $s)
            <div class="scale-card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" data-year="{{ $s->academic_year_id }}">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50/50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $s->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $s->academicYear->year ?? '' }} · {{ $s->slabs->count() }} સ્લેબ</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($s->is_active)
                            <span class="text-xs font-medium text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">ચાલુ</span>
                            @else
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">બંધ</span>
                            @endif
                            <button onclick="editScale({{ $s->id }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></button>
                            <button onclick="deleteScale({{ $s->id }}, '{{ addslashes($s->name) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો"><i class="lni lni-trash-3 text-sm"></i></button>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($s->slabs as $sl)
                        <span class="text-xs font-medium px-2 py-1 rounded-full border @if(!$sl->is_pass) border-red-300 bg-red-50 text-red-700 @else border-gray-200 bg-gray-50 text-gray-700 @endif" title="@if(!$sl->is_pass)નાપાસ@elseપાસ@endif">{{ $sl->grade }} · {{ number_format($sl->min_percent, 2) }}–{{ number_format($sl->max_percent, 2) }}</span>
                        @empty
                        <span class="text-xs text-gray-400">સ્લેબ નથી</span>
                        @endforelse
                    </div>
                    @if($s->slabs->where('is_pass', false)->count())
                    <p class="text-[11px] text-red-500 mt-2">લાલ બેજ = નાપાસ ગ્રેડ</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-trophy-1 text-3xl text-amber-500"></i>
                </div>
                <p class="text-gray-500 font-medium">હજી કોઈ ગ્રેડ સ્કેલ બનાવ્યો નથી</p>
                <p class="text-gray-400 text-sm mt-1">ઉપર "નવો સ્કેલ" દબાવીને શરૂ કરો</p>
            </div>
        @endforelse
    </div>
    <div id="no-result" class="hidden text-center py-12 bg-white rounded-xl border border-gray-200 mt-5">
        <p class="text-gray-500 font-medium">આ વર્ષમાં કોઈ સ્કેલ નથી</p>
    </div>
</div>

{{-- Editor Modal --}}
<div id="grade-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 id="grade-modal-title" class="text-lg font-semibold text-gray-900">નવો ગ્રેડ સ્કેલ</h3>
            <button type="button" onclick="closeGradeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"><i class="lni lni-xmark text-lg"></i></button>
        </div>
        <form id="grade-form">
            <input type="hidden" id="scale-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સ્કેલનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="scale-name" required placeholder="દા.ત. મુખ્ય ગ્રેડ સ્કેલ" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <select id="scale-year" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer mb-4">
                <input type="checkbox" id="scale-active" checked class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-medium text-gray-700">સ્કેલ સક્રિય (રિઝલ્ટમાં આ વપરાશે)</span>
            </label>

            <div class="rounded-xl border border-gray-200 overflow-hidden mb-3">
                <div class="flex items-center justify-between px-4 py-2.5 bg-orange-50 border-b border-orange-100">
                    <span class="text-sm font-bold text-orange-800">ગ્રેડ સ્લેબ (100 માંથી)</span>
                    <span id="coverage-hint" class="text-xs font-medium text-gray-500">કવરેજ: —</span>
                </div>
                <div class="px-3 pt-2 text-[11px] font-semibold text-gray-400 hidden sm:grid grid-cols-[70px_1fr_1fr_64px_32px] gap-2">
                    <span>ગ્રેડ</span><span>શરૂ %</span><span>અંત %</span><span>પાસ?</span><span></span>
                </div>
                <div id="slab-rows" class="p-3 pt-1 space-y-2"></div>
                <div class="flex items-center gap-2 p-3 pt-0">
                    <button type="button" onclick="addSlabRow()" class="flex-1 py-2 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition flex items-center justify-center gap-1"><i class="lni lni-plus text-xs"></i> સ્લેબ ઉમેરો</button>
                    <button type="button" onclick="fillSample()" class="flex-1 py-2 text-xs font-medium text-sky-600 hover:bg-sky-50 rounded-lg transition flex items-center justify-center gap-1"><i class="lni lni-magic text-xs"></i> નમૂનો ભરો</button>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mb-4">નિયમ: પહેલો 0% થી, છેલ્લો 100% સુધી, વચ્ચે ખાલી જગ્યા/અથડામણ નહીં. દા.ત. 80નું પેપરમાં 65.5 = 81.88% → A2.</p>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeGradeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="grade-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-lg focus:ring-4 focus:ring-orange-200 transition flex items-center gap-2">
                    <i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var gradeModal = document.getElementById('grade-modal');
    var gradeForm = document.getElementById('grade-form');
    var scaleId = document.getElementById('scale-id');
    var scaleName = document.getElementById('scale-name');
    var scaleYear = document.getElementById('scale-year');
    var scaleActive = document.getElementById('scale-active');
    var gradeTitle = document.getElementById('grade-modal-title');
    var gradeSubmitBtn = document.getElementById('grade-submit-btn');
    var slabRows = document.getElementById('slab-rows');
    var coverageHint = document.getElementById('coverage-hint');

    var esc = function(s) { return String(s === undefined || s === null ? '' : s).replace(/"/g, '&quot;').replace(/</g, '&lt;'); };

    var slabRowHtml = function(grade, min, max, pass) {
        return '<div class="slab-row grid grid-cols-2 sm:grid-cols-[70px_1fr_1fr_64px_32px] gap-2 items-center rounded-lg border border-gray-100 p-2 sm:p-0 sm:border-0">' +
            '<input type="text" placeholder="ગ્રેડ (A1)" value="' + esc(grade) + '" class="slab-grade col-span-2 sm:col-span-1 min-w-0 px-2 py-2 border border-gray-300 rounded-lg text-sm font-bold sm:text-center focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">' +
            '<input type="number" step="0.01" min="0" max="100" placeholder="શરૂ % (91)" value="' + (min !== undefined && min !== null && min !== '' ? min : '') + '" oninput="updateCoverage()" class="slab-min min-w-0 px-2 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">' +
            '<input type="number" step="0.01" min="0" max="100" placeholder="અંત % (100)" value="' + (max !== undefined && max !== null && max !== '' ? max : '') + '" oninput="updateCoverage()" class="slab-max min-w-0 px-2 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">' +
            '<div class="col-span-2 sm:col-span-1 flex items-center justify-between sm:justify-center gap-2">' +
            '<label class="flex items-center gap-1 cursor-pointer" title="પાસ ગ્રેડ?"><input type="checkbox" ' + (pass === false ? '' : 'checked') + ' class="slab-pass rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"><span class="text-[11px] text-gray-500">પાસ</span></label>' +
            '<button type="button" onclick="removeSlabRow(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition sm:justify-self-end" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>' +
            '</div>' +
        '</div>';
    };

    var addSlabRow = function(grade, min, max, pass) {
        slabRows.insertAdjacentHTML('beforeend', slabRowHtml(grade, min, max, pass));
        updateCoverage();
    };
    window.addSlabRow = addSlabRow;

    var removeSlabRow = function(btn) {
        if (slabRows.querySelectorAll('.slab-row').length <= 1) {
            NexSchool.alert.danger('ઓછામાં ઓછો 1 સ્લેબ જરૂરી છે.');
            return;
        }
        btn.closest('.slab-row').remove();
        updateCoverage();
    };
    window.removeSlabRow = removeSlabRow;

    // GSEB-style નમૂનો
    var fillSample = function() {
        var sample = [
            ['A1', 91, 100, true], ['A2', 81, 90, true], ['B1', 71, 80, true],
            ['B2', 61, 70, true], ['C1', 51, 60, true], ['C2', 41, 50, true],
            ['D', 33, 40, true], ['E1', 21, 32, false], ['E2', 0, 20, false],
        ];
        slabRows.innerHTML = '';
        for (var i = 0; i < sample.length; i++) {
            addSlabRow(sample[i][0], sample[i][1], sample[i][2], sample[i][3]);
        }
        NexSchool.alert.success('નમૂનો ભર્યો — જરૂર મુજબ સુધારો.');
    };
    window.fillSample = fillSample;

    var collectSlabs = function() {
        var rows = slabRows.querySelectorAll('.slab-row');
        var slabs = [];
        for (var i = 0; i < rows.length; i++) {
            var grade = rows[i].querySelector('.slab-grade').value.trim();
            if (!grade) continue;
            slabs.push({
                grade: grade,
                min_percent: parseFloat(rows[i].querySelector('.slab-min').value) || 0,
                max_percent: parseFloat(rows[i].querySelector('.slab-max').value) || 0,
                is_pass: rows[i].querySelector('.slab-pass').checked,
            });
        }
        return slabs;
    };

    var updateCoverage = function() {
        var slabs = collectSlabs().sort(function(a, b) { return a.min_percent - b.min_percent; });
        if (slabs.length === 0) { coverageHint.textContent = 'કવરેજ: —'; return; }
        var problems = [];
        if (slabs[0].min_percent !== 0) problems.push('0% થી શરૂ નથી');
        if (slabs[slabs.length - 1].max_percent !== 100) problems.push('100% સુધી નથી');
        for (var i = 1; i < slabs.length; i++) {
            var gap = Math.round((slabs[i].min_percent - slabs[i - 1].max_percent) * 100) / 100;
            if (gap < 0) problems.push('અથડામણ: ' + slabs[i].grade);
            else if (gap > 1) problems.push('ખાલી જગ્યા: ' + slabs[i - 1].grade + ' પછી');
        }
        if (problems.length === 0) {
            coverageHint.textContent = 'કવરેજ: 0→100 ✓';
            coverageHint.className = 'text-xs font-bold text-emerald-600';
        } else {
            coverageHint.textContent = 'કવરેજ: ' + problems[0];
            coverageHint.className = 'text-xs font-bold text-red-500';
        }
    };
    window.updateCoverage = updateCoverage;

    var openGradeModal = function() {
        scaleId.value = '';
        gradeTitle.textContent = 'નવો ગ્રેડ સ્કેલ';
        gradeForm.reset();
        slabRows.innerHTML = '';
        fillSample();
        gradeModal.classList.remove('hidden');
        requestAnimationFrame(function() { gradeModal.style.opacity = '1'; });
    };
    window.openGradeModal = openGradeModal;

    var closeGradeModal = function() {
        gradeModal.style.opacity = '0';
        setTimeout(function() { gradeModal.classList.add('hidden'); }, 200);
    };
    window.closeGradeModal = closeGradeModal;
    gradeModal.addEventListener('click', function(e) { if (e.target === gradeModal) closeGradeModal(); });

    var editScale = function(id) {
        fetch('{{ url("exams/grades") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { if (!res.ok) throw new Error('Error'); return res.json(); })
        .then(function(data) {
            if (!data.success) return;
            var s = data.scale;
            scaleId.value = s.id;
            gradeTitle.textContent = 'ગ્રેડ સ્કેલ સુધારો';
            scaleName.value = s.name || '';
            scaleYear.value = s.academic_year_id;
            scaleActive.checked = !!s.is_active;
            slabRows.innerHTML = '';
            var slabs = (s.slabs || []).slice().sort(function(a, b) { return b.min_percent - a.min_percent; });
            for (var i = 0; i < slabs.length; i++) {
                addSlabRow(slabs[i].grade, slabs[i].min_percent, slabs[i].max_percent, !!slabs[i].is_pass);
            }
            updateCoverage();
            gradeModal.classList.remove('hidden');
            requestAnimationFrame(function() { gradeModal.style.opacity = '1'; });
        })
        .catch(function() { NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'); });
    };
    window.editScale = editScale;

    var deleteScale = function(id, name) {
        NexSchool.confirm.show('સ્કેલ કાઢી નાખવો છે?', '"' + name + '" અને તેના બધા સ્લેબ કાઢી નખાશે.', 'danger', 'હા, કાઢી નાખો')
        .then(function(ok) {
            if (!ok) return;
            fetch('{{ url("exams/grades/delete") }}/' + id, {
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
    window.deleteScale = deleteScale;

    var filterByYear = function() {
        var yf = document.getElementById('year-filter').value;
        var cards = document.querySelectorAll('.scale-card');
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

    gradeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var slabs = collectSlabs();
        if (slabs.length === 0) { NexSchool.alert.danger('ઓછામાં ઓછો 1 સ્લેબ જરૂરી છે.'); return; }
        var isEdit = !!scaleId.value;
        var url = isEdit ? '{{ url("exams/grades/update") }}/' + scaleId.value : '{{ route("exams.grades.store") }}';
        gradeSubmitBtn.disabled = true;
        gradeSubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાય છે...';
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: parseInt(scaleYear.value),
                name: scaleName.value.trim(),
                is_active: scaleActive.checked,
                slabs: slabs,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success(data.message);
                closeGradeModal();
                setTimeout(function() { location.reload(); }, 600);
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ.');
            }
        })
        .catch(function(err) {
            var msg = (err.errors) ? Object.values(err.errors).flat().join(' ') : (err.message || 'સર્વર ભૂલ');
            NexSchool.alert.danger(msg);
        })
        .finally(function() { gradeSubmitBtn.disabled = false; gradeSubmitBtn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો'; });
    });
})();
</script>
@endpush
