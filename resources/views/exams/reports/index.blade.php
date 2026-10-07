@extends('layouts.app')
@section('title', 'રિપોર્ટ')
@push('styles')
<style>
    .vhead { writing-mode: vertical-rl; transform: rotate(180deg); white-space: nowrap; max-height: 120px; margin: 0 auto; }
    th.vcol { height: 150px; vertical-align: bottom; }
</style>
@endpush
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-500 to-purple-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">રિપોર્ટ</h1>
            <p class="text-violet-200 mt-1 text-sm">વિષયના હેડ-વાર ગુણ, ગ્રેડ, ટકા, પાસ/નાપાસ, ક્રમ — સત્ર 1, 2 કે બંને</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ષ</label>
                <select id="f-year" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    @foreach ($academicYears as $y)
                    <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ <span class="text-red-500">*</span></label>
                <select id="f-standard" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    <option value="">ધોરણ પસંદ કરો</option>
                    @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ <span class="text-red-500">*</span></label>
                <select id="f-class" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    <option value="">—</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર <span class="text-red-500">*</span></label>
                <select id="f-semester" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    <option value="1">સત્ર 1</option>
                    <option value="2">સત્ર 2</option>
                    <option value="both">બંને (વાર્ષિક)</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadReport()" id="load-btn" class="w-full px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition flex items-center justify-center gap-2">
                    <i class="lni lni-bar-chart-4 text-sm"></i> રિપોર્ટ ખોલો
                </button>
            </div>
        </div>
    </div>

    <div id="scale-warn" class="hidden mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-700">
        આ વર્ષમાં ગ્રેડ સ્કેલ નથી — ગ્રેડ/પાસ-નાપાસ ખાલી દેખાશે. <a href="{{ route('exams.grades.index') }}" class="font-semibold underline">ગ્રેડ બનાવો</a>
    </div>

    <div id="report-section" class="hidden">
        <div class="flex flex-wrap items-center gap-3 mb-3">
            <h2 id="report-title" class="text-base font-bold text-gray-900"></h2>
            <span id="report-count" class="text-xs font-medium text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full"></span>
            <button onclick="printReport()" class="ml-auto px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition flex items-center gap-2">
                <i class="lni lni-printer text-sm"></i> પ્રિન્ટ
            </button>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-auto" style="max-height: 68vh;">
            <table class="border-collapse text-sm whitespace-nowrap" style="min-width: 100%;">
                <thead class="sticky top-0 z-10">
                    <tr id="report-head-group" class="bg-violet-50"></tr>
                    <tr id="report-head" class="bg-gray-50"></tr>
                </thead>
                <tbody id="report-body"></tbody>
            </table>
        </div>
        <p class="text-[11px] text-gray-400 mt-2">હેડર પર ક્લિકથી સોર્ટ થાય · <kbd class="bg-gray-100 px-1 rounded">Ctrl+P</kbd> પ્રિન્ટ · ક્રમ ફક્ત પાસ વિદ્યાર્થીઓને · ખાલી = એન્ટ્રી બાકી</p>
    </div>

    <div id="report-empty" class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="lni lni-bar-chart-4 text-3xl text-violet-400"></i>
        </div>
        <p class="text-gray-500 font-medium">ધોરણ, વર્ગ અને સત્ર પસંદ કરી રિપોર્ટ ખોલો</p>
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
    var fSemester = document.getElementById('f-semester');

    var subjects = [];
    var headCols = [];
    var rows = [];
    var semLabel = '';
    var sortKey = 'rank';
    var sortDir = 1;

    fStandard.addEventListener('change', function() {
        fClass.innerHTML = '<option value="">—</option>';
        document.getElementById('report-section').classList.add('hidden');
        if (!this.value) return;
        fetch('{{ url("attendance/register/classes") }}/' + this.value, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].id;
                opt.textContent = data[i].name;
                fClass.appendChild(opt);
            }
        })
        .catch(function() {});
    });
    fYear.addEventListener('change', function() { document.getElementById('report-section').classList.add('hidden'); });
    fClass.addEventListener('change', function() { document.getElementById('report-section').classList.add('hidden'); });
    fSemester.addEventListener('change', function() { document.getElementById('report-section').classList.add('hidden'); });

    var loadReport = function() {
        if (!fStandard.value || !fClass.value) { NexSchool.alert.danger('ધોરણ અને વર્ગ પસંદ કરો.'); return; }
        var btn = document.getElementById('load-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> બને છે...';
        fetch('{{ route("exams.reports.data") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: parseInt(fYear.value),
                standard_id: parseInt(fStandard.value),
                school_class_id: parseInt(fClass.value),
                semester: fSemester.value,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { NexSchool.alert.danger(data.message || 'ભૂલ.'); return; }
            subjects = data.subjects || [];
            headCols = data.head_cols || [];
            rows = data.rows || [];
            semLabel = data.semester_label || '';
            document.getElementById('scale-warn').classList.toggle('hidden', !!data.has_scale);
            sortKey = 'rank'; sortDir = 1;
            renderReport();
            document.getElementById('report-section').classList.remove('hidden');
            document.getElementById('report-empty').classList.add('hidden');
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { btn.disabled = false; btn.innerHTML = '<i class="lni lni-bar-chart-4 text-sm"></i> રિપોર્ટ ખોલો'; });
    };
    window.loadReport = loadReport;

    var printReport = function() {
        var q = '?academic_year_id=' + fYear.value + '&standard_id=' + fStandard.value +
            '&school_class_id=' + fClass.value + '&semester=' + fSemester.value;
        window.open('{{ route("exams.reports.print") }}' + q, '_blank');
    };
    window.printReport = printReport;

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p') {
            if (!document.getElementById('report-section').classList.contains('hidden')) {
                e.preventDefault();
                printReport();
            }
        }
    });

    var sortArrow = function(key) {
        if (sortKey !== key) return '';
        return sortDir === 1 ? ' ▲' : ' ▼';
    };

    var setSort = function(key) {
        if (sortKey === key) { sortDir = -sortDir; }
        else { sortKey = key; sortDir = 1; }
        renderReport();
    };
    window.setSort = setSort;

    var sortedRows = function() {
        var arr = rows.slice();
        var val = function(r) {
            if (sortKey === 'name') return (r.name || '').toLowerCase();
            if (sortKey === 'gr') return r.gr_number || '';
            if (sortKey === 'total') return r.percent === null ? -1 : r.obtained;
            if (sortKey === 'percent') return r.percent === null ? -1 : r.percent;
            if (sortKey === 'rank') return r.rank === null ? 999999 : r.rank;
            return 0;
        };
        arr.sort(function(a, b) {
            var va = val(a), vb = val(b);
            if (va < vb) return -1 * sortDir;
            if (va > vb) return 1 * sortDir;
            return 0;
        });
        return arr;
    };

    var renderReport = function() {
        var stdName = fStandard.options[fStandard.selectedIndex].text;
        var clsName = fClass.options[fClass.selectedIndex].text;
        document.getElementById('report-title').textContent = stdName + '-' + clsName + ' · ' + semLabel;
        document.getElementById('report-count').textContent = rows.length + ' વિદ્યાર્થી · ' + subjects.length + ' વિષય';

        // Subject-wise + grand totals — headers vapre (PAHELA ganvo, pachhi vapro)
        var subTotals = {};
        for (var th0 = 0; th0 < headCols.length; th0++) {
            var sid0 = headCols[th0].subject_id;
            subTotals[sid0] = (subTotals[sid0] || 0) + headCols[th0].converted;
        }
        var grandTotal = 0;
        for (var gk in subTotals) { grandTotal += subTotals[gk]; }

        // Group header: subject groups
        var g = '<th rowspan="2" class="px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase bg-violet-50 border-b border-r border-gray-200 sticky left-0 z-20 min-w-[200px]">ક્રમ · <span class="cursor-pointer hover:text-violet-700" onclick="setSort(\'gr\')">GR' + sortArrow('gr') + '</span> · <span class="cursor-pointer hover:text-violet-700" onclick="setSort(\'name\')">નામ' + sortArrow('name') + '</span></th>';
        var subIdx = {};
        for (var s = 0; s < subjects.length; s++) {
            var nHeads = 0;
            for (var h = 0; h < headCols.length; h++) {
                if (headCols[h].subject_id === subjects[s].id) nHeads++;
            }
            subIdx[subjects[s].id] = s;
            g += '<th colspan="' + (nHeads + 2) + '" class="px-2 py-2 text-center font-bold text-violet-800 text-xs uppercase bg-violet-50 border-b border-l border-gray-200">' + subjects[s].name + '</th>';
        }
        g += '<th rowspan="2" class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase bg-violet-50 border-b border-gray-200 cursor-pointer hover:text-violet-700" onclick="setSort(\'total\')">કુલ / ' + Math.round(grandTotal) + sortArrow('total') + '</th>' +
            '<th rowspan="2" class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase bg-violet-50 border-b border-gray-200 cursor-pointer hover:text-violet-700" onclick="setSort(\'percent\')">ટકા' + sortArrow('percent') + '</th>' +
            '<th rowspan="2" class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase bg-violet-50 border-b border-gray-200">ગ્રેડ</th>' +
            '<th rowspan="2" class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase bg-violet-50 border-b border-gray-200">પાસ?</th>' +
            '<th rowspan="2" class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase bg-violet-50 border-b border-gray-200 cursor-pointer hover:text-violet-700" onclick="setSort(\'rank\')">ક્રમ' + sortArrow('rank') + '</th>';
        document.getElementById('report-head-group').innerHTML = g;

        // Head row: per-head columns (vertical names) + per-subject કુલ/ગ્રેડ
        var h = '';
        for (var hh = 0; hh < headCols.length; hh++) {
            var hc = headCols[hh];
            h += '<th class="vcol px-1 py-2 text-center bg-gray-50 border-b border-gray-200" title="પેપર ' + Math.round(hc.total) + ' → ગણતરી ' + Math.round(hc.converted) + '"><div class="vhead font-semibold text-gray-600 text-[11px]">' + hc.head_name + '·S' + hc.semester + '</div><div class="font-bold text-gray-500 text-[10px] mt-1">' + Math.round(hc.converted) + '</div></th>';
            var isLastOfSub = hh + 1 >= headCols.length || headCols[hh + 1].subject_id !== hc.subject_id;
            if (isLastOfSub) {
                h += '<th class="vcol px-1 py-2 text-center bg-emerald-50/60 border-b border-gray-200"><div class="vhead font-bold text-emerald-700 text-[11px]">કુલ / ' + Math.round(subTotals[hc.subject_id] || 0) + '</div></th>';
                h += '<th class="px-2 py-2 text-center font-bold text-emerald-700 text-[11px] bg-emerald-50/60 border-b border-l border-gray-200">ગ્રેડ</th>';
            }
        }
        document.getElementById('report-head').innerHTML = h;

        var b = '';
        var list = sortedRows();
        for (var i = 0; i < list.length; i++) {
            var r = list[i];
            var rowBg = r.pass ? '' : (r.has_any ? 'bg-red-50/50' : 'bg-gray-50/50');
            b += '<tr class="hover:bg-violet-50/40 transition ' + rowBg + '">';
            b += '<td class="px-3 py-2 bg-white border-r border-gray-100 sticky left-0 z-10"><span class="text-xs text-gray-400 mr-1">' + (i + 1) + '</span><span class="font-medium text-gray-900">' + (r.name || '') + '</span> <span class="text-[10px] text-gray-400 font-mono">GR:' + r.gr_number + '</span></td>';
            for (var c = 0; c < headCols.length; c++) {
                var v = r.cells[c]; // ganatari (converted) marks
                var raw = v === null ? null : (headCols[c].converted > 0 ? (v * headCols[c].total / headCols[c].converted) : 0);
                if (v === null) {
                    b += '<td class="px-1 py-2 text-center text-gray-300">—</td>';
                } else {
                    b += '<td class="px-1 py-2 text-center" title="પેપર: ' + Math.round(raw) + ' / ' + Math.round(headCols[c].total) + '"><div class="font-bold text-gray-800 text-[13px]">' + Math.round(v) + '</div><div class="text-[9px] text-gray-400">' + Math.round(raw) + '</div></td>';
                }
                var lastSub = c + 1 >= headCols.length || headCols[c + 1].subject_id !== headCols[c].subject_id;
                if (lastSub) {
                    var si = subIdx[headCols[c].subject_id];
                    var sub = (si !== undefined && r.subs) ? r.subs[si] : null;
                    if (!sub) {
                        b += '<td class="px-2 py-2 text-center text-gray-300">—</td><td class="px-2 py-2 text-center text-gray-300 border-l border-gray-100">—</td>';
                    } else {
                        var badge = sub.pass
                            ? '<span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">' + (sub.grade || '') + '</span>'
                            : '<span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">' + (sub.grade || '') + '</span>';
                        b += '<td class="px-2 py-2 text-center font-bold ' + (sub.pass ? 'text-gray-900' : 'text-red-600') + '">' + Math.round(sub.obtained) + '</td>';
                        b += '<td class="px-2 py-2 text-center border-l border-gray-100">' + badge + '</td>';
                    }
                }
            }
            b += '<td class="px-3 py-2 text-center font-bold text-gray-900">' + (r.has_any ? Math.round(r.obtained) : '—') + '</td>';
            b += '<td class="px-3 py-2 text-center font-bold ' + (r.pass ? 'text-emerald-700' : (r.has_any ? 'text-red-600' : 'text-gray-400')) + '">' + (r.percent === null ? '—' : r.percent.toFixed(2) + '%') + '</td>';
            b += '<td class="px-3 py-2 text-center font-bold text-gray-900">' + (r.grade || '—') + '</td>';
            b += '<td class="px-3 py-2 text-center">' + (!r.has_any ? '<span class="text-[11px] text-gray-400">બાકી</span>' : (r.pass ? '<span class="text-[11px] font-bold text-emerald-700">પાસ</span>' : '<span class="text-[11px] font-bold text-red-600">નાપાસ</span>')) + '</td>';
            b += '<td class="px-3 py-2 text-center font-bold ' + (r.rank ? 'text-violet-700' : 'text-gray-300') + '">' + (r.rank || '—') + '</td>';
            b += '</tr>';
        }
        if (list.length === 0) {
            b = '<tr><td colspan="' + (6 + headCols.length + subjects.length * 2) + '" class="px-4 py-12 text-center text-gray-400">કોઈ વિદ્યાર્થી નથી</td></tr>';
        }
        document.getElementById('report-body').innerHTML = b;
    };
})();
</script>
@endpush
