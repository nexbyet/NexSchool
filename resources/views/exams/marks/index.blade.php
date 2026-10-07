@extends('layouts.app')
@section('title', 'ગુણ એન્ટ્રી')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">ગુણ એન્ટ્રી</h1>
            <p class="text-emerald-200 mt-1 text-sm">Excel જેવી ઝડપી ગ્રીડ — Enter/તીરથી ફરો, Excel માંથી paste કરો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ષ</label>
                <select id="f-year" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    @foreach ($academicYears as $y)
                    <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ <span class="text-red-500">*</span></label>
                <select id="f-standard" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">ધોરણ પસંદ કરો</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ <span class="text-red-500">*</span></label>
                <select id="f-class" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">—</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વિષય <span class="text-red-500">*</span></label>
                <select id="f-subject" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">—</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર <span class="text-red-500">*</span></label>
                <select id="f-semester" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="1">સત્ર 1</option>
                    <option value="2">સત્ર 2</option>
                </select>
            </div>
        </div>
        <div id="pattern-picker" class="hidden mt-3 flex flex-wrap items-center gap-2 text-sm">
            <span class="text-xs font-medium text-gray-500">આ વિષય માટે 1થી વધુ પેટર્ન લાગુ છે — પસંદ કરો:</span>
            <span id="pattern-chips" class="flex flex-wrap gap-2"></span>
        </div>
        <div class="flex items-center gap-3 mt-4">
            <button onclick="loadGrid()" id="load-btn" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                <i class="lni lni-table-1 text-sm"></i> ગ્રીડ ખોલો
            </button>
            <p class="text-xs text-gray-400 hidden sm:block">ટિપ: Excel માંથી કોપી કરી ગ્રીડમાં paste કરો · <kbd class="bg-gray-100 px-1 rounded">Enter</kbd> નીચે · <kbd class="bg-gray-100 px-1 rounded">Ctrl+S</kbd> સેવ</p>
        </div>
    </div>

    <div id="grid-section" class="hidden">
        <div class="flex flex-wrap items-center gap-3 mb-3">
            <h2 id="grid-title" class="text-base font-bold text-gray-900"></h2>
            <span id="entered-badge" class="text-xs font-medium text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">0/0 ભર્યા</span>
            <span id="dirty-badge" class="hidden text-xs font-medium text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">● સેવ બાકી</span>
            <button onclick="saveMarks()" id="save-btn" class="ml-auto px-5 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition flex items-center gap-2">
                <i class="lni lni-floppy-disk-1 text-sm"></i> સેવ કરો
            </button>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-auto" style="max-height: 65vh;">
            <table class="border-collapse text-sm" style="min-width: 100%;">
                <thead class="sticky top-0 z-10">
                    <tr id="grid-head-row" class="bg-gray-50"></tr>
                </thead>
                <tbody id="grid-body"></tbody>
            </table>
        </div>
        <div class="sticky bottom-0 mt-3 flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
            <span id="footer-status" class="text-xs text-gray-500">તૈયાર</span>
            <button onclick="saveMarks()" class="ml-auto px-6 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">સેવ કરો</button>
        </div>
    </div>

    <div id="grid-empty" class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="lni lni-table-1 text-3xl text-emerald-400"></i>
        </div>
        <p class="text-gray-500 font-medium">ઉપર પેટર્ન, ધોરણ, વર્ગ, વિષય અને સત્ર પસંદ કરી ગ્રીડ ખોલો</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var fYear = document.getElementById('f-year');
    var fStandard = document.getElementById('f-standard');
    var fClass = document.getElementById('f-class');
    var fSubject = document.getElementById('f-subject');
    var fSemester = document.getElementById('f-semester');
    var gridSection = document.getElementById('grid-section');
    var gridEmpty = document.getElementById('grid-empty');
    var gridHeadRow = document.getElementById('grid-head-row');
    var gridBody = document.getElementById('grid-body');
    var patternPicker = document.getElementById('pattern-picker');
    var patternChips = document.getElementById('pattern-chips');

    var scopeTree = []; // varsh ma laghu pattern tree
    var resolvedPattern = null; // {id, name} — auto-resolved
    var students = [];
    var heads = [];
    var isDirty = false;

    var fetchJson = function(url, opts) {
        opts = opts || {};
        opts.headers = { 'Accept': 'application/json' };
        return fetch(url, opts).then(function(res) { if (!res.ok) throw new Error('Error'); return res.json(); });
    };

    var fillSelect = function(sel, items, label) {
        sel.innerHTML = '<option value="">' + (label || '—') + '</option>';
        for (var i = 0; i < items.length; i++) {
            var opt = document.createElement('option');
            opt.value = items[i].id;
            opt.textContent = items[i].name;
            sel.appendChild(opt);
        }
    };

    /* ---------- Cascade (pattern auto-resolve) ---------- */
    var findStd = function() {
        for (var i = 0; i < scopeTree.length; i++) {
            if (String(scopeTree[i].id) === String(fStandard.value)) return scopeTree[i];
        }
        return null;
    };
    var findSubs = function() {
        var std = findStd();
        if (!std) return [];
        for (var j = 0; j < std.classes.length; j++) {
            if (String(std.classes[j].id) === String(fClass.value)) return std.classes[j].subjects || [];
        }
        return [];
    };

    fYear.addEventListener('change', function() {
        scopeTree = [];
        resolvedPattern = null;
        resetDown('standard');
        hideGrid();
        fetchJson('{{ url("exams/marks/scope") }}/' + fYear.value)
        .then(function(data) {
            scopeTree = data.standards || [];
            fillSelect(fStandard, scopeTree, 'ધોરણ પસંદ કરો');
            if (scopeTree.length === 0) {
                NexSchool.alert.note('આ વર્ષમાં કોઈ પેટર્ન લાગુ નથી — પહેલા પરીક્ષા પેટર્ન બનાવો.');
            }
        })
        .catch(function() { NexSchool.alert.danger('વિગત લોડ કરવામાં ભૂલ.'); });
    });

    fStandard.addEventListener('change', function() {
        resetDown('class');
        hideGrid();
        var std = findStd();
        if (std) fillSelect(fClass, std.classes || [], 'વર્ગ પસંદ કરો');
    });

    fClass.addEventListener('change', function() {
        resetDown('subject');
        hideGrid();
        fillSelect(fSubject, findSubs(), 'વિષય પસંદ કરો');
    });

    fSubject.addEventListener('change', function() {
        hideGrid();
        resolveSubjectPattern();
    });

    fSemester.addEventListener('change', function() { hideGrid(); });

    // vishay pasand thata j pattern auto-resolve (1 hoy to sidhu, 1+ hoy to pasandagi)
    var resolveSubjectPattern = function() {
        resolvedPattern = null;
        patternPicker.classList.add('hidden');
        patternChips.innerHTML = '';
        if (!fSubject.value) return;
        var subs = findSubs();
        var sub = null;
        for (var i = 0; i < subs.length; i++) {
            if (String(subs[i].id) === String(fSubject.value)) { sub = subs[i]; break; }
        }
        if (!sub || !sub.patterns || sub.patterns.length === 0) return;
        if (sub.patterns.length === 1) {
            resolvedPattern = { id: sub.patterns[0].id, name: sub.patterns[0].name };
            return;
        }
        var html = '';
        for (var j = 0; j < sub.patterns.length; j++) {
            html += '<button type="button" data-pid="' + sub.patterns[j].id + '" data-pname="' + sub.patterns[j].name.replace(/"/g, '&quot;') + '" onclick="pickPattern(this)" class="pattern-chip px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-medium text-gray-600 hover:border-emerald-400 transition">' + sub.patterns[j].name + '</button>';
        }
        patternChips.innerHTML = html;
        patternPicker.classList.remove('hidden');
        // paheli ne default pasand
        pickPattern(patternChips.querySelector('.pattern-chip'));
    };

    var pickPattern = function(btn) {
        if (!btn) return;
        resolvedPattern = { id: parseInt(btn.getAttribute('data-pid')), name: btn.getAttribute('data-pname') };
        var chips = patternChips.querySelectorAll('.pattern-chip');
        for (var i = 0; i < chips.length; i++) {
            var on = chips[i] === btn;
            chips[i].classList.toggle('bg-emerald-600', on);
            chips[i].classList.toggle('text-white', on);
            chips[i].classList.toggle('text-gray-600', !on);
            chips[i].style.borderColor = on ? '#059669' : '';
        }
    };
    window.pickPattern = pickPattern;

    var resetDown = function(from) {
        var order = ['standard', 'class', 'subject'];
        var sels = { standard: fStandard, class: fClass, subject: fSubject };
        var labels = { standard: 'ધોરણ પસંદ કરો', class: '—', subject: '—' };
        var start = false;
        for (var i = 0; i < order.length; i++) {
            if (order[i] === from) { start = true; continue; }
            if (start) { sels[order[i]].innerHTML = '<option value="">' + labels[order[i]] + '</option>'; }
        }
        resolvedPattern = null;
        patternPicker.classList.add('hidden');
        patternChips.innerHTML = '';
    };

    var hideGrid = function() {
        gridSection.classList.add('hidden');
        gridEmpty.classList.remove('hidden');
        students = []; heads = []; isDirty = false;
    };

    fYear.dispatchEvent(new Event('change'));

    /* ---------- Grid ---------- */
    var cellId = function(r, c) { return 'cell-' + r + '-' + c; };

    var loadGrid = function() {
        if (!fStandard.value || !fClass.value || !fSubject.value) { NexSchool.alert.danger('ધોરણ, વર્ગ અને વિષય પસંદ કરો.'); return; }
        if (!resolvedPattern) { NexSchool.alert.danger('આ વિષય માટે પેટર્ન મળી નથી — પહેલા પેટર્ન બનાવો.'); return; }
        var doLoad = function() {
            fetch('{{ route("exams.marks.grid") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    pattern_id: resolvedPattern.id,
                    academic_year_id: parseInt(fYear.value),
                    standard_id: parseInt(fStandard.value),
                    school_class_id: parseInt(fClass.value),
                    subject_id: parseInt(fSubject.value),
                    semester: parseInt(fSemester.value),
                }),
            })
            .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
            .then(function(data) {
                if (!data.success) { NexSchool.alert.danger(data.message || 'ભૂલ.'); return; }
                students = data.students || [];
                heads = data.heads || [];
                renderGrid(data.marks || {});
            })
            .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); });
        };
        if (isDirty) {
            NexSchool.confirm.show('સેવ બાકી છે', 'સેવ કર્યા વગરની એન્ટ્રી જતી રહેશે. આગળ વધવું છે?', 'danger', 'હા, આગળ વધો')
            .then(function(ok) { if (ok) doLoad(); });
        } else {
            doLoad();
        }
    };
    window.loadGrid = loadGrid;

    var renderGrid = function(marksMap) {
        if (students.length === 0) {
            gridSection.classList.add('hidden');
            gridEmpty.classList.remove('hidden');
            gridEmpty.querySelector('p').textContent = 'આ વર્ગમાં કોઈ વિદ્યાર્થી નથી';
            return;
        }
        var stdName = fStandard.options[fStandard.selectedIndex].text;
        var clsName = fClass.options[fClass.selectedIndex].text;
        var subName = fSubject.options[fSubject.selectedIndex].text;
        document.getElementById('grid-title').textContent =
            (resolvedPattern ? resolvedPattern.name : '') + ' · ' + stdName + '-' + clsName + ' · ' + subName + ' · સત્ર ' + fSemester.value;

        var h = '<th class="sticky left-0 z-20 bg-gray-50 px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase min-w-[220px] border-b border-r border-gray-200">વિદ્યાર્થી</th>';
        for (var c = 0; c < heads.length; c++) {
            h += '<th class="bg-gray-50 px-2 py-2 text-center border-b border-gray-200 min-w-[110px]">' +
                '<div class="font-semibold text-gray-700 text-xs">' + heads[c].name + '</div>' +
                '<div class="text-[10px] font-medium text-gray-400">પેપર ' + heads[c].total_marks + ' → ગણતરી ' + heads[c].converted_marks + '</div></th>';
        }
        h += '<th class="bg-emerald-50 px-2 py-2 text-center border-b border-gray-200 min-w-[90px]"><div class="font-semibold text-emerald-700 text-xs">કુલ (પેપર)</div></th>';
        h += '<th class="bg-emerald-50 px-2 py-2 text-center border-b border-gray-200 min-w-[90px]"><div class="font-semibold text-emerald-700 text-xs">કુલ (ગણતરી)</div></th>';
        gridHeadRow.innerHTML = h;

        var b = '';
        for (var r = 0; r < students.length; r++) {
            var s = students[r];
            b += '<tr class="hover:bg-emerald-50/40 transition" data-row="' + r + '">';
            b += '<td class="sticky left-0 z-10 bg-white px-3 py-1.5 border-b border-r border-gray-100"><span class="text-xs text-gray-400 mr-1">' + (r + 1) + '</span><span class="text-sm font-medium text-gray-900">' + (s.name || '') + '</span> <span class="text-[10px] text-gray-400">GR:' + s.gr_number + '</span></td>';
            for (var cc = 0; cc < heads.length; cc++) {
                var val = (marksMap[s.id] && marksMap[s.id][heads[cc].id] !== undefined) ? marksMap[s.id][heads[cc].id] : '';
                b += '<td class="px-1 py-1 border-b border-gray-100 text-center">' +
                    '<input id="' + cellId(r, cc) + '" type="number" step="0.01" min="0" max="' + heads[cc].total_marks + '" value="' + val + '" data-r="' + r + '" data-c="' + cc + '" data-student="' + s.id + '" data-head="' + heads[cc].id + '" placeholder="—" ' +
                    'class="mark-cell w-full max-w-[100px] mx-auto px-2 py-1.5 text-center text-sm font-semibold text-gray-900 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">' +
                    '</td>';
            }
            b += '<td class="px-2 py-1.5 text-center border-b border-gray-100 font-bold text-gray-700 text-sm row-paper" data-row-total="' + r + '">0</td>';
            b += '<td class="px-2 py-1.5 text-center border-b border-gray-100 font-bold text-emerald-700 text-sm row-conv" data-row-conv="' + r + '">0</td>';
            b += '</tr>';
        }
        gridBody.innerHTML = b;
        gridSection.classList.remove('hidden');
        gridEmpty.classList.add('hidden');
        setDirty(false);
        refreshTotals();
        var first = document.getElementById(cellId(0, 0));
        if (first) first.focus();
    };

    /* ---------- Totals + validation ---------- */
    var getCell = function(r, c) { return document.getElementById(cellId(r, c)); };

    var cellValue = function(input) {
        if (!input || input.value === '' || input.value === null) return null;
        return parseFloat(input.value);
    };

    var validateCell = function(input) {
        var c = parseInt(input.getAttribute('data-c'));
        var v = cellValue(input);
        var bad = v !== null && (isNaN(v) || v < 0 || v > heads[c].total_marks);
        input.classList.toggle('ring-2', bad);
        input.classList.toggle('ring-red-500', bad);
        input.classList.toggle('border-red-400', bad);
        input.classList.toggle('bg-red-50', bad);
        return !bad;
    };

    var refreshTotals = function() {
        var entered = 0, total = 0;
        for (var r = 0; r < students.length; r++) {
            var paper = 0, conv = 0;
            for (var c = 0; c < heads.length; c++) {
                total++;
                var input = getCell(r, c);
                if (!input) continue;
                var v = cellValue(input);
                if (v !== null && !isNaN(v)) {
                    entered++;
                    paper += v;
                    if (heads[c].total_marks > 0) conv += v * heads[c].converted_marks / heads[c].total_marks;
                }
            }
            var pt = document.querySelector('.row-paper[data-row-total="' + r + '"]');
            var ct = document.querySelector('.row-conv[data-row-conv="' + r + '"]');
            if (pt) pt.textContent = paper.toFixed(2);
            if (ct) ct.textContent = conv.toFixed(2);
        }
        document.getElementById('entered-badge').textContent = entered + '/' + total + ' ભર્યા';
        document.getElementById('footer-status').textContent = entered + ' / ' + total + ' ખાના ભર્યા';
    };

    gridBody.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('mark-cell')) {
            validateCell(e.target);
            refreshTotals();
            setDirty(true);
        }
    });

    gridBody.addEventListener('focusin', function(e) {
        if (e.target && e.target.classList.contains('mark-cell')) { e.target.select(); }
    });

    /* ---------- Excel-like keyboard ---------- */
    gridBody.addEventListener('keydown', function(e) {
        var t = e.target;
        if (!t || !t.classList.contains('mark-cell')) return;
        var r = parseInt(t.getAttribute('data-r'));
        var c = parseInt(t.getAttribute('data-c'));
        var nr = r, nc = c, handled = true;
        if (e.key === 'Enter') { nr = e.shiftKey ? r - 1 : r + 1; }
        else if (e.key === 'ArrowDown') { nr = r + 1; }
        else if (e.key === 'ArrowUp') { nr = r - 1; }
        else if (e.key === 'ArrowRight') { nc = c + 1; }
        else if (e.key === 'ArrowLeft') { nc = c - 1; }
        else { handled = false; }
        if (!handled) return;
        e.preventDefault();
        if (nr < 0) nr = 0;
        if (nc < 0) nc = 0;
        if (nr >= students.length) nr = students.length - 1;
        if (nc >= heads.length) nc = heads.length - 1;
        var next = getCell(nr, nc);
        if (next) { next.focus(); next.select(); }
    });

    /* ---------- Paste from Excel (TSV) ---------- */
    gridBody.addEventListener('paste', function(e) {
        var t = e.target;
        if (!t || !t.classList.contains('mark-cell')) return;
        e.preventDefault();
        var text = (e.clipboardData || window.clipboardData).getData('text');
        if (!text) return;
        var sr = parseInt(t.getAttribute('data-r'));
        var sc = parseInt(t.getAttribute('data-c'));
        var rows = text.replace(/\r/g, '').split('\n');
        for (var i = 0; i < rows.length; i++) {
            if (sr + i >= students.length) break;
            var cols = rows[i].split('\t');
            for (var j = 0; j < cols.length; j++) {
                if (sc + j >= heads.length) break;
                var cell = getCell(sr + i, sc + j);
                if (!cell) continue;
                var v = cols[j].trim().replace(/,/g, '');
                if (v === '' || v.toLowerCase() === 'a' || v === '-') { cell.value = ''; }
                else if (!isNaN(parseFloat(v))) { cell.value = parseFloat(v); }
                validateCell(cell);
            }
        }
        refreshTotals();
        setDirty(true);
        NexSchool.alert.success('Excel ડેટા પેસ્ટ થયો — સેવ કરવાનું ભૂલશો નહીં.');
    });

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            if (!gridSection.classList.contains('hidden')) { e.preventDefault(); saveMarks(); }
        }
    });

    var setDirty = function(d) {
        isDirty = d;
        document.getElementById('dirty-badge').classList.toggle('hidden', !d);
    };

    /* ---------- Save ---------- */
    var saveMarks = function() {
        if (students.length === 0 || heads.length === 0) return;
        var payload = [];
        var firstBad = null;
        for (var r = 0; r < students.length; r++) {
            for (var c = 0; c < heads.length; c++) {
                var input = getCell(r, c);
                if (!input) continue;
                if (!validateCell(input)) {
                    if (!firstBad) firstBad = { r: r, c: c };
                    continue;
                }
                var v = cellValue(input);
                payload.push({
                    student_id: students[r].id,
                    head_id: heads[c].id,
                    marks: v === null ? null : v,
                });
            }
        }
        if (firstBad) {
            var badInput = getCell(firstBad.r, firstBad.c);
            if (badInput) { badInput.focus(); badInput.select(); }
            NexSchool.alert.danger(students[firstBad.r].name + ' — ' + heads[firstBad.c].name + ': 0 થી ' + heads[firstBad.c].total_marks + ' વચ્ચે ગુણ લખો.');
            return;
        }
        var btns = [document.getElementById('save-btn')];
        btns.forEach(function(b) { if (b) { b.disabled = true; } });
        document.getElementById('footer-status').textContent = 'સેવ થાય છે...';
        fetch('{{ route("exams.marks.save") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                pattern_id: resolvedPattern.id,
                academic_year_id: parseInt(fYear.value),
                standard_id: parseInt(fStandard.value),
                school_class_id: parseInt(fClass.value),
                subject_id: parseInt(fSubject.value),
                semester: parseInt(fSemester.value),
                marks: payload,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success(data.message);
                setDirty(false);
                loadGridSilent();
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ.');
            }
        })
        .catch(function(err) {
            var msg = (err.errors) ? Object.values(err.errors).flat().join(' ') : (err.message || 'સર્વર ભૂલ');
            NexSchool.alert.danger(msg);
        })
        .finally(function() {
            btns.forEach(function(b) { if (b) { b.disabled = false; } });
        });
    };
    window.saveMarks = saveMarks;

    // સેવ પછી શાંતિથી grid refresh (prefill sync + scroll保持)
    var loadGridSilent = function() {
        var scroller = gridBody.closest('.overflow-auto');
        var top = scroller ? scroller.scrollTop : 0;
        fetch('{{ route("exams.marks.grid") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                pattern_id: resolvedPattern.id,
                academic_year_id: parseInt(fYear.value),
                standard_id: parseInt(fStandard.value),
                school_class_id: parseInt(fClass.value),
                subject_id: parseInt(fSubject.value),
                semester: parseInt(fSemester.value),
            }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                students = data.students || [];
                heads = data.heads || [];
                renderGridSilent(data.marks || {});
                if (scroller) scroller.scrollTop = top;
            }
        })
        .catch(function() {});
    };

    // focus ગુમાવ્યા વગર re-render (સેવ પછી)
    var renderGridSilent = function(marksMap) {
        for (var r = 0; r < students.length; r++) {
            for (var c = 0; c < heads.length; c++) {
                var input = getCell(r, c);
                if (!input) continue;
                var v = (marksMap[students[r].id] && marksMap[students[r].id][heads[c].id] !== undefined)
                    ? marksMap[students[r].id][heads[c].id] : '';
                input.value = v;
                validateCell(input);
            }
        }
        refreshTotals();
    };
})();
</script>
@endpush
