@extends('layouts.app')
@section('title', ($mode === 'edit' ? 'પેટર્ન સુધારો' : 'નવી પેટર્ન'))
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
    $selYear = ($mode === 'edit' && $pattern) ? $pattern->academic_year_id : $activeYearId;
@endphp
<div class="p-4 md:p-6 max-w-6xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('exams.patterns.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition"><i class="lni lni-arrow-left text-sm"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $mode === 'edit' ? 'પરીક્ષા પેટર્ન સુધારો' : 'નવી પરીક્ષા પેટર્ન' }}</h1>
            <p class="text-gray-500 mt-0.5 text-sm">સત્ર-વાર હેડ (પેપર ગુણ + ગણતરી ગુણ) અને ધોરણ × વર્ગ × વિષય પસંદ કરો</p>
        </div>
    </div>

    <form id="pattern-form">
        {{-- Basic info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5 shadow-sm">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">મૂળભૂત માહિતી</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">પેટર્નનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="pattern-name" required value="{{ $mode === 'edit' && $pattern ? $pattern->name : '' }}" placeholder="દા.ત. વાર્ષિક પરીક્ષા પેટર્ન" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <select id="pattern-year" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                        @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}" @if($y->id == $selYear) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વર્ણન (વૈકલ્પિક)</label>
                    <input type="text" id="pattern-desc" value="{{ $mode === 'edit' && $pattern ? $pattern->description : '' }}" placeholder="દા.ત. સરકારી ઠરાવ મુજબ" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="pattern-active" @if(!($mode === 'edit' && $pattern && !$pattern->is_active)) checked @endif class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm font-medium text-gray-700">પેટર્ન સક્રિય</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Semester heads --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5 shadow-sm">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-1">સત્ર-વાર માર્ક્સ હેડ</h2>
            <p class="text-xs text-gray-500 mb-4">પેપર = પેપર કુલ કેટલા ગુણનું · ગણતરી = રિઝલ્ટમાં કેટલા ગુણમાં ગણીને બતાવવું (દા.ત. 80 ગુણનું પેપર → 40માં ગણતરી)</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-sky-200 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-sky-50 border-b border-sky-100">
                        <span class="text-sm font-bold text-sky-800">સત્ર 1</span>
                        <span id="sem1-total" class="text-xs font-bold text-sky-700">પેપર 0 · ગણતરી 0</span>
                    </div>
                    <div class="px-3 pt-2 text-[11px] font-semibold text-gray-400 grid grid-cols-[1fr_72px_72px_32px] gap-2">
                        <span>હેડનું નામ</span><span>પેપર ગુણ</span><span>ગણતરી ગુણ</span><span></span>
                    </div>
                    <div id="sem1-heads" class="p-3 pt-1 space-y-2"></div>
                    <button type="button" onclick="addHeadRow(1)" class="w-full py-2 text-xs font-medium text-sky-600 hover:bg-sky-50 transition flex items-center justify-center gap-1"><i class="lni lni-plus text-xs"></i> હેડ ઉમેરો</button>
                </div>
                <div class="rounded-xl border border-indigo-200 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-indigo-50 border-b border-indigo-100">
                        <span class="text-sm font-bold text-indigo-800">સત્ર 2</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="copySemHeads(1, 2)" title="સત્ર-1 જેવા હેડ સત્ર-2 માં મૂકો" class="text-[11px] font-medium text-indigo-500 hover:text-indigo-700 hover:bg-indigo-100 px-2 py-1 rounded-lg transition flex items-center gap-1"><i class="lni lni-layers-1 text-xs"></i> સત્ર-1 નકલ કરો</button>
                            <span id="sem2-total" class="text-xs font-bold text-indigo-700">પેપર 0 · ગણતરી 0</span>
                        </div>
                    </div>
                    <div class="px-3 pt-2 text-[11px] font-semibold text-gray-400 grid grid-cols-[1fr_72px_72px_32px] gap-2">
                        <span>હેડનું નામ</span><span>પેપર ગુણ</span><span>ગણતરી ગુણ</span><span></span>
                    </div>
                    <div id="sem2-heads" class="p-3 pt-1 space-y-2"></div>
                    <button type="button" onclick="addHeadRow(2)" class="w-full py-2 text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition flex items-center justify-center gap-1"><i class="lni lni-plus text-xs"></i> હેડ ઉમેરો</button>
                </div>
            </div>
        </div>

        {{-- Applies: standard x class x subject --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">આ પેટર્ન ક્યાં લાગુ પાડવી?</h2>
                <span id="apply-count" class="text-xs font-medium text-violet-700 bg-violet-50 px-2.5 py-1 rounded-full">0 પસંદ કર્યા</span>
            </div>
            <p class="text-xs text-gray-500 mb-4">ધોરણ પસંદ કરો → તેના વર્ગો આવશે → વર્ગ પસંદ કરો → તેના વિષયો આવશે. એક જ પેટર્ન અનેક ધોરણ, વર્ગ અને વિષયમાં ચાલશે.</p>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach ($standards as $std)
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-violet-300 hover:bg-violet-50/30 cursor-pointer transition has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50 has-[:checked]:ring-1 has-[:checked]:ring-violet-500">
                    <input type="checkbox" class="pattern-standard rounded border-gray-300 text-violet-600 focus:ring-violet-500" value="{{ $std->id }}" data-name="{{ $std->name }}" onchange="onStandardToggle(this)">
                    <span class="text-sm font-medium text-gray-800">{{ $std->name }}</span>
                </label>
                @endforeach
            </div>
            <div id="applies-wrap" class="space-y-3"></div>
        </div>

        <div class="flex items-center justify-end gap-3 sticky bottom-4">
            <a href="{{ route('exams.patterns.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition shadow-sm">રદ કરો</a>
            <button type="submit" id="pattern-submit-btn" class="px-5 py-2.5 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg focus:ring-4 focus:ring-violet-200 transition flex items-center gap-2 shadow-sm">
                <i class="lni lni-floppy-disk-1 text-sm"></i> {{ $mode === 'edit' ? 'સુધારો સાચવો' : 'પેટર્ન બનાવો' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var isEdit = {{ $mode === 'edit' ? 'true' : 'false' }};
    var editId = {{ $mode === 'edit' && $pattern ? $pattern->id : 'null' }};
    var patternData = @json($patternData);
    var semHeads = { 1: document.getElementById('sem1-heads'), 2: document.getElementById('sem2-heads') };
    var semTotals = { 1: document.getElementById('sem1-total'), 2: document.getElementById('sem2-total') };
    var appliesWrap = document.getElementById('applies-wrap');

    var esc = function(s) { return String(s === undefined || s === null ? '' : s).replace(/"/g, '&quot;').replace(/</g, '&lt;'); };

    /* ---------- Semester heads ---------- */
    var headRowHtml = function(sem, name, total, converted) {
        return '<div class="grid grid-cols-[1fr_72px_72px_32px] gap-2 items-center head-row" data-sem="' + sem + '">' +
            '<input type="text" placeholder="દા.ત. ત્રિમાસિક કસોટી" value="' + esc(name) + '" class="head-name min-w-0 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">' +
            '<input type="number" step="0.01" min="0" placeholder="80" value="' + (total !== undefined && total !== null && total !== '' ? total : '') + '" oninput="updateSemTotals()" class="head-total min-w-0 px-2 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">' +
            '<input type="number" step="0.01" min="0" placeholder="40" value="' + (converted !== undefined && converted !== null && converted !== '' ? converted : '') + '" oninput="updateSemTotals()" class="head-converted min-w-0 px-2 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">' +
            '<button type="button" onclick="removeHeadRow(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>' +
        '</div>';
    };

    var addHeadRow = function(sem, name, total, converted) {
        semHeads[sem].insertAdjacentHTML('beforeend', headRowHtml(sem, name, total, converted));
        updateSemTotals();
    };
    window.addHeadRow = addHeadRow;

    var removeHeadRow = function(btn) {
        var container = btn.closest('[id$="-heads"]');
        if (container.querySelectorAll('.head-row').length <= 1) {
            NexSchool.alert.danger('દરેક સત્રમાં ઓછામાં ઓછો 1 હેડ જરૂરી છે.');
            return;
        }
        btn.closest('.head-row').remove();
        updateSemTotals();
    };
    window.removeHeadRow = removeHeadRow;

    var updateSemTotals = function() {
        for (var sem = 1; sem <= 2; sem++) {
            var t = 0, c = 0;
            var rows = semHeads[sem].querySelectorAll('.head-row');
            for (var i = 0; i < rows.length; i++) {
                t += parseFloat(rows[i].querySelector('.head-total').value) || 0;
                c += parseFloat(rows[i].querySelector('.head-converted').value) || 0;
            }
            semTotals[sem].textContent = 'પેપર ' + t.toFixed(2) + ' · ગણતરી ' + c.toFixed(2);
        }
    };
    window.updateSemTotals = updateSemTotals;

    var collectHeads = function() {
        var heads = [];
        for (var sem = 1; sem <= 2; sem++) {
            var rows = semHeads[sem].querySelectorAll('.head-row');
            for (var i = 0; i < rows.length; i++) {
                var name = rows[i].querySelector('.head-name').value.trim();
                if (!name) continue;
                heads.push({
                    semester: sem,
                    name: name,
                    total_marks: parseFloat(rows[i].querySelector('.head-total').value) || 0,
                    converted_marks: parseFloat(rows[i].querySelector('.head-converted').value) || 0,
                });
            }
        }
        return heads;
    };

    var copySemHeads = function(from, to) {
        var rows = semHeads[from].querySelectorAll('.head-row');
        if (rows.length === 0) { NexSchool.alert.danger('સત્ર-' + from + ' માં કોઈ હેડ નથી.'); return; }
        semHeads[to].innerHTML = '';
        for (var i = 0; i < rows.length; i++) {
            addHeadRow(to,
                rows[i].querySelector('.head-name').value,
                rows[i].querySelector('.head-total').value,
                rows[i].querySelector('.head-converted').value);
        }
        NexSchool.alert.success('સત્ર-' + from + ' ના હેડ સત્ર-' + to + ' માં નાખ્યા.');
    };
    window.copySemHeads = copySemHeads;

    /* ---------- Applies: standard x class x subject ---------- */
    var fetchJson = function(url) {
        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function(res) { if (!res.ok) throw new Error('Error'); return res.json(); });
    };

    var updateApplyCount = function() {
        var n = collectApplies().length;
        document.getElementById('apply-count').textContent = n + ' પસંદ કર્યા';
    };

    var onStandardToggle = function(cb) {
        var stdId = cb.value;
        var stdName = cb.getAttribute('data-name');
        var block = document.getElementById('std-block-' + stdId);
        if (!cb.checked) { if (block) block.remove(); updateApplyCount(); return; }
        if (block) return;
        block = document.createElement('div');
        block.id = 'std-block-' + stdId;
        block.className = 'std-block rounded-xl border border-violet-200 overflow-hidden';
        block.setAttribute('data-std', stdId);
        block.innerHTML = '<div class="flex items-center justify-between px-4 py-2.5 bg-violet-50 border-b border-violet-100">' +
            '<span class="text-sm font-bold text-violet-800">ધોરણ ' + esc(stdName) + ' — વર્ગો</span>' +
            '<label class="flex items-center gap-1.5 text-xs text-violet-600 font-medium cursor-pointer"><input type="checkbox" onchange="toggleStdClasses(' + stdId + ', this)" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500"> બધા વર્ગો</label></div>' +
            '<div class="p-3 class-list"><p class="text-xs text-gray-400">વર્ગો આવે છે...</p></div>';
        appliesWrap.appendChild(block);
        buildStandardClasses(stdId);
    };
    window.onStandardToggle = onStandardToggle;

    var buildStandardClasses = function(stdId) {
        var list = document.querySelector('#std-block-' + stdId + ' .class-list');
        return fetchJson('{{ url("exams/patterns/standard-classes") }}/' + stdId)
        .then(function(data) {
            var classes = data.classes || [];
            if (classes.length === 0) {
                list.innerHTML = '<p class="text-xs text-amber-600">આ ધોરણમાં કોઈ વર્ગ નથી. પહેલા ‘ધોરણ અને વર્ગ’ પેજમાં વર્ગ ઉમેરો.</p>';
                return;
            }
            var html = '';
            for (var i = 0; i < classes.length; i++) {
                html += '<div class="class-block rounded-lg border border-gray-200 mb-2" data-class="' + classes[i].id + '">' +
                    '<label class="flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-50 rounded-lg">' +
                    '<input type="checkbox" class="std-class std-' + stdId + '-class rounded border-gray-300 text-violet-600 focus:ring-violet-500" value="' + classes[i].id + '" onchange="onClassToggle(' + stdId + ', this)">' +
                    '<span class="text-sm font-semibold text-gray-800">વર્ગ ' + esc(classes[i].name) + '</span>' +
                    '<span class="text-[11px] text-gray-400 ml-auto class-sub-count"></span></label>' +
                    '<div class="subject-list px-3 pb-2"></div>' +
                '</div>';
            }
            list.innerHTML = html;
        })
        .catch(function() {
            list.innerHTML = '<p class="text-xs text-red-500">વર્ગો આવવામાં ભૂલ — ફરી પ્રયાસ કરો.</p>';
        });
    };

    var toggleStdClasses = function(stdId, master) {
        var boxes = document.querySelectorAll('.std-' + stdId + '-class');
        for (var i = 0; i < boxes.length; i++) {
            if (boxes[i].checked !== master.checked) {
                boxes[i].checked = master.checked;
                onClassToggle(stdId, boxes[i]);
            }
        }
    };
    window.toggleStdClasses = toggleStdClasses;

    var onClassToggle = function(stdId, cb) {
        var classId = cb.value;
        var block = cb.closest('.class-block');
        var subList = block.querySelector('.subject-list');
        if (!cb.checked) { subList.innerHTML = ''; updateApplyCount(); return; }
        subList.innerHTML = '<p class="text-[11px] text-gray-400 py-1">વિષયો આવે છે...</p>';
        buildClassSubjects(stdId, classId);
    };
    window.onClassToggle = onClassToggle;

    var buildClassSubjects = function(stdId, classId) {
        var block = document.querySelector('#std-block-' + stdId + ' .class-block[data-class="' + classId + '"]');
        if (!block) return Promise.resolve();
        var subList = block.querySelector('.subject-list');
        return fetchJson('{{ url("exams/patterns/standard-subjects") }}/' + stdId)
        .then(function(data) {
            var subs = data.subjects || [];
            if (subs.length === 0) {
                subList.innerHTML = '<p class="text-[11px] text-amber-600 py-1">આ ધોરણને કોઈ વિષય સોંપાયો નથી. પહેલા ‘વિષયો’ પેજમાં ધોરણને વિષયો સોંપો.</p>';
                return;
            }
            var html = '<div class="flex items-center justify-end mb-1.5"><label class="flex items-center gap-1.5 text-[11px] text-violet-600 font-medium cursor-pointer"><input type="checkbox" checked onchange="toggleClassSubjects(' + stdId + ',' + classId + ', this)" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500"> બધા વિષયો</label></div>' +
                '<div class="flex flex-wrap gap-1.5">';
            for (var i = 0; i < subs.length; i++) {
                html += '<label class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:border-violet-300 cursor-pointer transition has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">' +
                    '<input type="checkbox" checked class="cls-subject cls-' + stdId + '-' + classId + '-subject rounded border-gray-300 text-violet-600 focus:ring-violet-500" value="' + subs[i].id + '" onchange="updateApplyCount()">' +
                    '<span class="text-xs font-medium text-gray-700">' + esc(subs[i].name) + '</span></label>';
            }
            html += '</div>';
            subList.innerHTML = html;
            updateApplyCount();
            // edit mode saved selection restore
            var pending = window.__pendingSel && window.__pendingSel[stdId] && window.__pendingSel[stdId][classId];
            if (pending) {
                var boxes = subList.querySelectorAll('.cls-subject');
                for (var b = 0; b < boxes.length; b++) {
                    boxes[b].checked = pending.indexOf(parseInt(boxes[b].value)) !== -1;
                }
                delete window.__pendingSel[stdId][classId];
                updateApplyCount();
            }
        })
        .catch(function() {
            subList.innerHTML = '<p class="text-[11px] text-red-500 py-1">વિષયો આવવામાં ભૂલ — ફરી પ્રયાસ કરો.</p>';
        });
    };

    var toggleClassSubjects = function(stdId, classId, master) {
        var boxes = document.querySelectorAll('.cls-' + stdId + '-' + classId + '-subject');
        for (var i = 0; i < boxes.length; i++) { boxes[i].checked = master.checked; }
        updateApplyCount();
    };
    window.toggleClassSubjects = toggleClassSubjects;

    var collectApplies = function() {
        var rows = [];
        var stdBlocks = appliesWrap.querySelectorAll('.std-block');
        for (var i = 0; i < stdBlocks.length; i++) {
            var stdId = parseInt(stdBlocks[i].getAttribute('data-std'));
            var classBlocks = stdBlocks[i].querySelectorAll('.class-block');
            for (var j = 0; j < classBlocks.length; j++) {
                var cbx = classBlocks[j].querySelector('.std-class');
                if (!cbx || !cbx.checked) continue;
                var classId = parseInt(cbx.value);
                var subs = classBlocks[j].querySelectorAll('.cls-subject:checked');
                for (var k = 0; k < subs.length; k++) {
                    rows.push({ standard_id: stdId, school_class_id: classId, subject_id: parseInt(subs[k].value) });
                }
            }
        }
        return rows;
    };

    /* ---------- Submit ---------- */
    document.getElementById('pattern-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.getElementById('pattern-name').value.trim();
        if (!name) { NexSchool.alert.danger('પેટર્નનું નામ લખો.'); return; }
        var heads = collectHeads();
        var s1 = 0, s2 = 0;
        for (var h = 0; h < heads.length; h++) { if (heads[h].semester === 1) s1++; else s2++; }
        if (s1 === 0 || s2 === 0) { NexSchool.alert.danger('બંને સત્રમાં ઓછામાં ઓછો 1 માર્ક્સ હેડ જરૂરી છે.'); return; }
        var applies = collectApplies();
        if (applies.length === 0) { NexSchool.alert.danger('ઓછામાં ઓછો 1 ધોરણ × વર્ગ × વિષય પસંદ કરો.'); return; }

        var btn = document.getElementById('pattern-submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાય છે...';

        var url = isEdit ? '{{ url("exams/patterns/update") }}/' + editId : '{{ route("exams.patterns.store") }}';
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: parseInt(document.getElementById('pattern-year').value),
                name: name,
                description: document.getElementById('pattern-desc').value.trim() || null,
                is_active: document.getElementById('pattern-active').checked,
                heads: heads,
                applies: applies,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success(data.message || 'સાચવાયું.');
                setTimeout(function() { window.location.href = '{{ route("exams.patterns.index") }}'; }, 700);
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ.');
            }
        })
        .catch(function(err) {
            var msg = (err.errors) ? Object.values(err.errors).flat().join(' ') : (err.message || 'સર્વર ભૂલ');
            NexSchool.alert.danger(msg);
        })
        .finally(function() { btn.disabled = false; btn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> ' + (isEdit ? 'સુધારો સાચવો' : 'પેટર્ન બનાવો'); });
    });

    /* ---------- Init ---------- */
    addHeadRow(1);
    addHeadRow(2);

    if (isEdit && patternData) {
        (function initEdit() {
            var heads = patternData.heads || [];
            var has1 = false, has2 = false;
            // default empty rows kadho, saved nakho
            document.getElementById('sem1-heads').innerHTML = '';
            document.getElementById('sem2-heads').innerHTML = '';
            for (var i = 0; i < heads.length; i++) {
                addHeadRow(heads[i].semester, heads[i].name, heads[i].total_marks, heads[i].converted_marks);
                if (heads[i].semester == 1) has1 = true; else has2 = true;
            }
            if (!has1) addHeadRow(1);
            if (!has2) addHeadRow(2);
            updateSemTotals();

            // pending selections: standard -> class -> [subjects]
            window.__pendingSel = {};
            var grouped = patternData.grouped || {};
            var chain = Promise.resolve();
            Object.keys(grouped).forEach(function(stdId) {
                chain = chain.then(function() {
                    var cb = document.querySelector('.pattern-standard[value="' + stdId + '"]');
                    if (!cb) return null;
                    cb.checked = true;
                    onStandardToggle(cb);
                    return new Promise(function(resolve) {
                        var tries = 0;
                        var timer = setInterval(function() {
                            var list = document.querySelector('#std-block-' + stdId + ' .class-list .class-block');
                            tries++;
                            if (list || tries > 40) {
                                clearInterval(timer);
                                var cchain = Promise.resolve();
                                Object.keys(grouped[stdId].classes || {}).forEach(function(classId) {
                                    cchain = cchain.then(function() {
                                        var ccb = document.querySelector('.std-' + stdId + '-class[value="' + classId + '"]');
                                        if (!ccb) return null;
                                        ccb.checked = true;
                                        if (!window.__pendingSel[stdId]) window.__pendingSel[stdId] = {};
                                        window.__pendingSel[stdId][classId] = grouped[stdId].classes[classId].subjects || [];
                                        return buildClassSubjects(stdId, classId);
                                    });
                                });
                                cchain.then(resolve);
                            }
                        }, 150);
                    });
                });
            });
        })();
    }
})();
</script>
@endpush
