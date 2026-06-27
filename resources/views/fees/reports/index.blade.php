@extends('layouts.app')
@section('title', 'ફી રિપોર્ટ્સ')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
    $methodLabels = ['cash' => 'રોકડા', 'bank' => 'બેંક ટ્રાન્સફર', 'cheque' => 'ચેક', 'online' => 'ઓનલાઇન'];
@endphp
<style>
    @media print {
        .no-print { display: none !important; }
        .print-area { display: block !important; }
        body { font-size: 12px; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
    }
</style>
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-rose-500 to-pink-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">ફી રિપોર્ટ્સ</h1>
            <p class="text-rose-200 mt-1 text-sm">ફી સંબંધિત વિવિધ રિપોર્ટ્સ જુઓ</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <button onclick="switchTab('summary')" id="tab-summary" class="tab-btn px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-rose-600 hover:border-rose-300 transition">સારાંશ</button>
            <button onclick="switchTab('due-list')" id="tab-due-list" class="tab-btn px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-rose-600 hover:border-rose-300 transition">બાકી યાદી</button>
            <button onclick="switchTab('collection-report')" id="tab-collection-report" class="tab-btn px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-rose-600 hover:border-rose-300 transition">વસૂલાત રિપોર્ટ</button>
            <button onclick="switchTab('statement')" id="tab-statement" class="tab-btn px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-rose-600 hover:border-rose-300 transition">સ્ટેટમેન્ટ</button>
        </div>
    </div>

    <div id="tab-summary-panel" class="tab-panel">
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <i class="lni lni-calendar-days text-gray-400 text-sm"></i>
                    <label class="text-sm font-medium text-gray-700">શૈક્ષણિક વર્ષ:</label>
                </div>
                <select id="summary-year" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                    @foreach ($academicYears as $y)
                    <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                    @endforeach
                </select>
                <select id="summary-semester" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                    <option value="">બધા સત્ર</option>
                    <option value="1">સત્ર 1</option>
                    <option value="2">સત્ર 2</option>
                </select>
                <button onclick="loadSummary()" class="px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition flex items-center gap-2">
                    <i class="lni lni-search-1 text-sm"></i> સારાંશ બતાવો
                </button>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mb-2 no-print">
            <button onclick="printReport('summary')" class="px-3 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ કરો</button>
        </div>
        <div id="summary-content" class="print-area"></div>
    </div>

    <div id="tab-due-list-panel" class="tab-panel hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                    <select id="due-year" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                    <select id="due-semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">બધા</option>
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ</label>
                    <select id="due-standard" onchange="loadDueClasses()" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">બધા ધોરણો</option>
                        @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ</label>
                    <select id="due-class" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">બધા વર્ગો</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="loadDueList()" class="w-full px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition flex items-center justify-center gap-2">
                        <i class="lni lni-layout-9 text-sm"></i> બાકી યાદી બતાવો
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mb-2 no-print">
            <button onclick="printReport('due')" class="px-3 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ કરો</button>
        </div>
        <div id="due-content" class="print-area"></div>
    </div>

    <div id="tab-collection-report-panel" class="tab-panel hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                    <select id="cr-year" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                    <select id="cr-semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">બધા</option>
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">તારીખથી <span class="text-red-500">*</span></label>
                    <input type="date" id="cr-from-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">તારીખ સુધી <span class="text-red-500">*</span></label>
                    <input type="date" id="cr-to-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ચુકવણી પદ્ધતિ</label>
                    <select id="cr-method" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">બધી પદ્ધતિઓ</option>
                        <option value="cash">રોકડા</option>
                        <option value="bank">બેંક ટ્રાન્સફર</option>
                        <option value="cheque">ચેક</option>
                        <option value="online">ઓનલાઇન</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="loadCollectionReport()" class="w-full px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition flex items-center justify-center gap-2">
                        <i class="lni lni-search-1 text-sm"></i> રિપોર્ટ બતાવો
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mb-2 no-print">
            <button onclick="printReport('collection')" class="px-3 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ કરો</button>
        </div>
        <div id="cr-content" class="print-area"></div>
    </div>

    <div id="tab-statement-panel" class="tab-panel hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                    <select id="stmt-year" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                    <select id="stmt-semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">બધા</option>
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વિદ્યાર્થી શોધો</label>
                    <select id="stmt-student" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition">
                        <option value="">— નામ / GR પસંદ કરો —</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="loadStatement()" class="px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition flex items-center gap-2">
                        <i class="lni lni-search-1 text-sm"></i> વિધાન બતાવો
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mb-2 no-print">
            <button onclick="printReport('statement')" class="px-3 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ કરો</button>
        </div>
        <div id="stmt-content" class="print-area"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var methodLabels = {'cash': 'રોકડા', 'bank': 'બેંક ટ્રાન્સફર', 'cheque': 'ચેક', 'online': 'ઓનલાઇન'};
    var feeTypeLabels = {'tuition': 'શાળા ફી', 'transport': 'બસ ફી', 'other': 'અન્ય'};

    var switchTab = function(tab) {
        var tabs = ['summary', 'due-list', 'collection-report', 'statement'];
        for (var i = 0; i < tabs.length; i++) {
            var panel = document.getElementById('tab-' + tabs[i] + '-panel');
            var btn = document.getElementById('tab-' + tabs[i]);
            if (panel) panel.classList.add('hidden');
            if (btn) {
                btn.classList.remove('border-rose-500', 'text-rose-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            }
        }
        var activePanel = document.getElementById('tab-' + tab + '-panel');
        var activeBtn = document.getElementById('tab-' + tab);
        if (activePanel) activePanel.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-rose-500', 'text-rose-600');
        }
        if (tab === 'summary' && !document.getElementById('summary-content').innerHTML.trim()) loadSummary();
        if (tab === 'due-list') loadDueClasses();
    };

    window.switchTab = switchTab;

    var loadSummary = function() {
        var yearId = parseInt(document.getElementById('summary-year').value);
        var sem = document.getElementById('summary-semester').value || null;
        var content = document.getElementById('summary-content');
        content.innerHTML = '<div class="text-center py-8"><i class="lni lni-spinner-3 text-2xl animate-spin text-rose-500"></i></div>';
        fetch('{{ route("fees.reports.summary") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ academic_year_id: yearId, semester: sem }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { content.innerHTML = '<p class="text-gray-500 text-center py-8">' + (data.message || 'ભૂલ') + '</p>'; return; }
            var html = '';
            html += '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">';
            html += '<div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center"><i class="lni lni-book-1 text-indigo-600 text-lg"></i></div><div><p class="text-xs text-gray-500 font-medium">કુલ સોંપાયેલ ફી</p><p class="text-xl font-bold text-gray-900">₹' + (data.total_assigned || 0).toFixed(2) + '</p></div></div></div>';
            html += '<div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="lni lni-check-circle-1 text-emerald-600 text-lg"></i></div><div><p class="text-xs text-gray-500 font-medium">કુલ વસૂલાયેલ ફી</p><p class="text-xl font-bold text-emerald-700">₹' + (data.total_collected || 0).toFixed(2) + '</p></div></div></div>';
            html += '<div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center"><i class="lni lni-ban-2 text-red-600 text-lg"></i></div><div><p class="text-xs text-gray-500 font-medium">કુલ બાકી ફી</p><p class="text-xl font-bold text-red-700">₹' + (data.total_due || 0).toFixed(2) + '</p></div></div></div>';
            html += '<div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center"><i class="lni lni-box-gift-1 text-amber-600 text-lg"></i></div><div><p class="text-xs text-gray-500 font-medium">કુલ છૂટ</p><p class="text-xl font-bold text-amber-700">₹' + (data.total_concession || 0).toFixed(2) + '</p></div></div></div>';
            html += '</div>';

            var perStd = data.per_standard || [];
            var byType = data.by_type || {};
            var typeKeys = Object.keys(byType);
            if (typeKeys.length > 0) {
                html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-4">';
                html += '<div class="p-3 border-b border-gray-200"><h4 class="font-semibold text-gray-800 text-sm">ફી પ્રકાર મુજબ સારાંશ</h4></div>';
                html += '<div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ફી પ્રકાર</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">સોંપાયેલ</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">વસૂલાયેલ</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">બાકી</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">છૂટ</th></tr></thead><tbody class="divide-y divide-gray-100">';
                for (var ti = 0; ti < typeKeys.length; ti++) {
                    var tk = typeKeys[ti];
                    var tv = byType[tk];
                    html += '<tr class="hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900">' + (feeTypeLabels[tk] || tk) + '</td><td class="px-4 py-3 text-right text-gray-900">₹' + (tv.assigned || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right text-emerald-700">₹' + (tv.collected || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right text-red-700">₹' + (tv.due || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right text-amber-700">₹' + (tv.concession || 0).toFixed(2) + '</td></tr>';
                }
                html += '</tbody></table></div></div>';
            }
            if (perStd.length > 0) {
                html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">';
                html += '<div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ધોરણ</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">સોંપાયેલ ફી</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">વસૂલાયેલ ફી</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">બાકી ફી</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">છૂટ</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">% વસૂલાત</th></tr></thead><tbody class="divide-y divide-gray-100">';
                for (var i = 0; i < perStd.length; i++) {
                    var ps = perStd[i];
                    var pct = ps.assigned > 0 ? ((ps.collected / ps.assigned) * 100).toFixed(1) : '0.0';
                    html += '<tr class="hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900">' + (ps.standard || '') + '</td><td class="px-4 py-3 text-right text-gray-900">₹' + (ps.assigned || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right text-emerald-700">₹' + (ps.collected || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right text-red-700">₹' + (ps.due || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right text-amber-700">₹' + (ps.concession || 0).toFixed(2) + '</td><td class="px-4 py-3 text-right font-semibold text-gray-900">' + pct + '%</td></tr>';
                }
                html += '</tbody></table></div></div>';
            } else {
                html += '<div class="text-center py-8 bg-white rounded-xl border border-gray-200"><p class="text-gray-500">કોઈ ડેટા ઉપલબ્ધ નથી</p></div>';
            }
            content.innerHTML = html;
        })
        .catch(function(err) { content.innerHTML = '<p class="text-red-500 text-center py-8">' + (err.message || 'સર્વર ભૂલ') + '</p>'; });
    };

    window.loadSummary = loadSummary;

    var loadDueClasses = function() {
        var stdId = document.getElementById('due-standard').value;
        var cls = document.getElementById('due-class');
        cls.innerHTML = '<option value="">બધા વર્ગો</option>';
        if (!stdId) return;
        fetch('{{ url("attendance/register/classes") }}/' + stdId, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].id;
                opt.textContent = data[i].name;
                cls.appendChild(opt);
            }
        })
        .catch(function() {});
    };

    window.loadDueClasses = loadDueClasses;

    var loadDueList = function() {
        var yearId = parseInt(document.getElementById('due-year').value);
        var sem = document.getElementById('due-semester').value || null;
        var stdId = document.getElementById('due-standard').value;
        var clsId = document.getElementById('due-class').value;
        var content = document.getElementById('due-content');
        content.innerHTML = '<div class="text-center py-8"><i class="lni lni-spinner-3 text-2xl animate-spin text-rose-500"></i></div>';
        fetch('{{ route("fees.reports.due-list") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: yearId,
                semester: sem,
                standard_id: stdId ? parseInt(stdId) : null,
                class_id: clsId ? parseInt(clsId) : null,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { content.innerHTML = '<p class="text-gray-500 text-center py-8">' + (data.message || 'ભૂલ') + '</p>'; return; }
            var students = data.students || [];
            if (students.length === 0) {
                content.innerHTML = '<div class="text-center py-12 bg-white rounded-xl border border-gray-200"><p class="text-gray-500 font-medium">કોઈ બાકી વિદ્યાર્થી નથી</p></div>';
                return;
            }
            // Build column order: sorted by semester then fee type
            var typeOrder = ['tuition', 'transport', 'other'];
            var cols = [];
            for (var ti = 0; ti < typeOrder.length; ti++) {
                var t = typeOrder[ti];
                if (data.fee_types.indexOf(t) !== -1) {
                    for (var si = 0; si < data.semesters.length; si++) {
                        var s = data.semesters[si];
                        cols.push({ key: 'sem_' + s + '_' + t, label: 'સત્ર ' + s + ' - ' + (data.type_labels[t] || t) });
                    }
                }
            }
            var html = '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50">';
            html += '<tr><th class="px-3 py-2.5 text-left text-gray-600 text-xs font-semibold uppercase tracking-wider" rowspan="2">ક્રમ</th><th class="px-3 py-2.5 text-left text-gray-600 text-xs font-semibold uppercase tracking-wider" rowspan="2">GR</th><th class="px-3 py-2.5 text-left text-gray-600 text-xs font-semibold uppercase tracking-wider" rowspan="2">નામ</th><th class="px-3 py-2.5 text-left text-gray-600 text-xs font-semibold uppercase tracking-wider" rowspan="2">પિતાનું નામ</th><th class="px-3 py-2.5 text-left text-gray-600 text-xs font-semibold uppercase tracking-wider" rowspan="2">ધોરણ-વર્ગ</th><th class="px-3 py-2.5 text-center text-gray-600 text-xs font-semibold uppercase tracking-wider" colspan="' + cols.length + '">બાકી ફી (ફી પ્રકાર મુજબ)</th><th class="px-3 py-2.5 text-right text-gray-600 text-xs font-semibold uppercase tracking-wider" rowspan="2">કુલ બાકી</th></tr>';
            html += '<tr>';
            for (var ci = 0; ci < cols.length; ci++) {
                html += '<th class="px-3 py-2.5 text-right text-gray-600 text-xs font-semibold uppercase tracking-wider" style="min-width:90px">' + cols[ci].label + '</th>';
            }
            html += '</tr></thead><tbody class="divide-y divide-gray-100">';
            for (var i = 0; i < students.length; i++) {
                var s = students[i];
                var stdName = s.student && s.student.current_standard ? s.student.current_standard.name : '';
                var clsName = s.student && s.student.current_class ? s.student.current_class.name : '';
                html += '<tr class="hover:bg-gray-50"><td class="px-3 py-2.5 text-gray-500">' + (i + 1) + '</td><td class="px-3 py-2.5 font-mono text-gray-900">' + (s.gr_number || '') + '</td><td class="px-3 py-2.5 font-medium text-gray-900">' + (s.full_name_gu || s.full_name_en || '') + '</td><td class="px-3 py-2.5 text-gray-600">' + (s.father_name_gu || s.father_name_en || '') + '</td><td class="px-3 py-2.5 text-gray-600">' + stdName + ' - ' + clsName + '</td>';
                for (var ci = 0; ci < cols.length; ci++) {
                    var entry = s.entries && s.entries[cols[ci].key] ? s.entries[cols[ci].key] : null;
                    html += '<td class="px-3 py-2.5 text-right text-gray-900">' + (entry ? '₹' + entry.due_amount.toFixed(2) : '—') + '</td>';
                }
                html += '<td class="px-3 py-2.5 text-right font-bold text-red-700">₹' + (s.total_due || 0).toFixed(2) + '</td></tr>';
            }
            html += '</tbody></table></div></div>';
            content.innerHTML = html;
        })
        .catch(function(err) { content.innerHTML = '<p class="text-red-500 text-center py-8">' + (err.message || 'સર્વર ભૂલ') + '</p>'; });
    };

    window.loadDueList = loadDueList;

    var loadCollectionReport = function() {
        var yearId = parseInt(document.getElementById('cr-year').value);
        var sem = document.getElementById('cr-semester').value || null;
        var fromDate = document.getElementById('cr-from-date').value;
        var toDate = document.getElementById('cr-to-date').value;
        var method = document.getElementById('cr-method').value;
        if (!fromDate || !toDate) { NexSchool.alert.danger('કૃપા કરીને તારીખથી અને તારીખ સુધી પસંદ કરો.'); return; }
        var content = document.getElementById('cr-content');
        content.innerHTML = '<div class="text-center py-8"><i class="lni lni-spinner-3 text-2xl animate-spin text-rose-500"></i></div>';
        fetch('{{ route("fees.reports.collection") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: yearId,
                semester: sem,
                from_date: fromDate,
                to_date: toDate,
                payment_method: method || null,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { content.innerHTML = '<p class="text-gray-500 text-center py-8">' + (data.message || 'ભૂલ') + '</p>'; return; }
            var payments = data.payments || [];
            if (payments.length === 0) {
                content.innerHTML = '<div class="text-center py-12 bg-white rounded-xl border border-gray-200"><p class="text-gray-500 font-medium">આ સમયગાળામાં કોઈ વસૂલાત નથી</p></div>';
                return;
            }
            var html = '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">તારીખ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">રસીદ નંબર</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">વિદ્યાર્થી</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">GR</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ધોરણ-વર્ગ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">સત્ર</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">રકમ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">પદ્ધતિ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">સંદર્ભ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">લેનાર</th></tr></thead><tbody class="divide-y divide-gray-100">';
            var totalAmt = 0;
            for (var i = 0; i < payments.length; i++) {
                var p = payments[i];
                var payDateStr = p.payment_date ? p.payment_date.substring(0, 10) : '';
                var stud = p.student || {};
                var stdName = stud.current_standard ? stud.current_standard.name : '';
                var clsName = stud.current_class ? stud.current_class.name : '';
                var receiver = p.receiver ? p.receiver.name : '—';
                var amt = parseFloat(p.amount_paid) || 0;
                totalAmt += amt;
                var semHtml = p.semester ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">સત્ર ' + p.semester + '</span>' : '<span class="text-gray-400">—</span>';
                html += '<tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-900">' + payDateStr + '</td><td class="px-4 py-3 font-mono text-xs text-gray-700">' + (p.receipt_number || '—') + '</td><td class="px-4 py-3 font-medium text-gray-900">' + (stud.full_name_gu || stud.full_name_en || '') + '</td><td class="px-4 py-3 text-gray-600">' + (stud.gr_number || '') + '</td><td class="px-4 py-3 text-gray-500 text-xs">' + stdName + ' - ' + clsName + '</td><td class="px-4 py-3">' + semHtml + '</td><td class="px-4 py-3 text-right font-semibold text-emerald-700">₹' + amt.toFixed(2) + '</td><td class="px-4 py-3">' + (methodLabels[p.payment_method] || p.payment_method) + '</td><td class="px-4 py-3 text-gray-500">' + (p.reference_number || '—') + '</td><td class="px-4 py-3 text-gray-600">' + receiver + '</td></tr>';
            }
            html += '</tbody><tfoot><tr class="bg-gray-50 font-bold"><td colspan="6" class="px-4 py-3 text-right text-gray-900">કુલ</td><td class="px-4 py-3 text-right text-emerald-700">₹' + totalAmt.toFixed(2) + '</td><td colspan="3" class="px-4 py-3"></td></tr></tfoot></table></div></div>';
            content.innerHTML = html;
        })
        .catch(function(err) { content.innerHTML = '<p class="text-red-500 text-center py-8">' + (err.message || 'સર્વર ભૂલ') + '</p>'; });
    };

    window.loadCollectionReport = loadCollectionReport;

    var studentsLoaded = false;
    var stmtSearchTimeout = null;
    document.getElementById('stmt-year').addEventListener('change', function() {
        studentsLoaded = false;
        document.getElementById('stmt-student').innerHTML = '<option value="">— નામ / GR પસંદ કરો —</option>';
    });

    document.getElementById('stmt-student').addEventListener('focus', function() {
        if (studentsLoaded) return;
        var sel = this;
        fetch('{{ route("fees.reports.search-students") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ search: '' }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) return;
            var students = data.students || [];
            sel.innerHTML = '<option value="">— નામ / GR પસંદ કરો —</option>';
            for (var i = 0; i < students.length; i++) {
                if (!students[i].id) continue;
                var opt = document.createElement('option');
                opt.value = students[i].id;
                opt.textContent = (students[i].full_name_gu || students[i].full_name_en || '') + ' [' + students[i].gr_number + ']';
                sel.appendChild(opt);
            }
            studentsLoaded = true;
        })
        .catch(function() {});
    });

    document.getElementById('stmt-student').addEventListener('input', function() {
        var sel = this;
        var val = sel.value;
        if (val.length < 2) return;
        clearTimeout(stmtSearchTimeout);
        stmtSearchTimeout = setTimeout(function() {
            fetch('{{ route("fees.reports.search-students") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({ search: val }),
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) return;
                var students = data.students || [];
                sel.innerHTML = '<option value="">— નામ / GR પસંદ કરો —</option>';
                for (var i = 0; i < students.length; i++) {
                    if (!students[i].id) continue;
                    var opt = document.createElement('option');
                    opt.value = students[i].id;
                    opt.textContent = (students[i].full_name_gu || students[i].full_name_en || '') + ' [' + students[i].gr_number + ']';
                    sel.appendChild(opt);
                }
            })
            .catch(function() {});
        }, 300);
    });

    var loadStatement = function() {
        var yearId = parseInt(document.getElementById('stmt-year').value);
        var sem = document.getElementById('stmt-semester').value || null;
        var studentId = document.getElementById('stmt-student').value;
        if (!studentId) { NexSchool.alert.danger('કૃપા કરીને વિદ્યાર્થી પસંદ કરો.'); return; }
        var content = document.getElementById('stmt-content');
        content.innerHTML = '<div class="text-center py-8"><i class="lni lni-spinner-3 text-2xl animate-spin text-rose-500"></i></div>';
        fetch('{{ route("fees.reports.statement") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: parseInt(studentId), academic_year_id: yearId, semester: sem }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { content.innerHTML = '<p class="text-gray-500 text-center py-8">' + (data.message || 'ભૂલ') + '</p>'; return; }
            var student = data.student || {};
            var fees = data.fees || [];
            var payments = data.payments || [];
            var carryForwards = data.carry_forwards || [];
            var totalPaid = data.total_paid || 0;
            var totalCarry = data.total_carry_forward || 0;
            var netFee = data.net_fee || 0;
            var dueAmount = data.due_amount || 0;
            var html = '';
            html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-4">';
            html += '<div class="p-4 bg-gradient-to-r from-rose-50 to-pink-50 border-b border-gray-200">';
            html += '<div class="flex items-center gap-3">';
            html += '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center"><span class="font-bold text-rose-700 text-lg">' + (student.full_name_gu ? student.full_name_gu.charAt(0) : '?') + '</span></div>';
            html += '<div><h4 class="font-semibold text-gray-900">' + (student.full_name_gu || student.full_name_en || '') + '</h4><p class="text-sm text-gray-500">GR: ' + (student.gr_number || '') + '</p></div>';
            html += '</div></div>';
            html += '<div class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">';
            var totalNetAll = 0;
            for (var fi = 0; fi < fees.length; fi++) {
                var f = fees[fi];
                var ft = f.fee_structure ? f.fee_structure.type : 'other';
                var fl = feeTypeLabels[ft] || ft;
                totalNetAll += parseFloat(f.net_amount) || 0;
                var semBadge = f.semester ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-700 ml-1">સત્ર ' + f.semester + '</span>' : '';
                html += '<div class="bg-gray-50 rounded-lg p-3 text-center"><p class="text-xs text-gray-500">' + fl + semBadge + '</p><p class="text-lg font-bold text-gray-900">₹' + (parseFloat(f.net_amount) || 0).toFixed(2) + '</p></div>';
            }
            html += '<div class="bg-gray-50 rounded-lg p-3 text-center"><p class="text-xs text-gray-500">બાકી</p><p class="text-lg font-bold ' + (dueAmount > 0 ? 'text-red-700' : 'text-emerald-700') + '">₹' + dueAmount.toFixed(2) + '</p></div>';
            html += '</div></div>';

            if (carryForwards.length > 0) {
                html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-4"><div class="p-4 border-b border-gray-200"><h4 class="font-semibold text-gray-900">કેરી ફોરવર્ડ</h4></div><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">વર્ષથી</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">વર્ષ સુધી</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">રકમ</th></tr></thead><tbody class="divide-y divide-gray-100">';
                for (var i = 0; i < carryForwards.length; i++) {
                    var cf = carryForwards[i];
                    html += '<tr><td class="px-4 py-3">' + (cf.from_academic_year ? cf.from_academic_year.year : '') + '</td><td class="px-4 py-3">' + (cf.to_academic_year ? cf.to_academic_year.year : '') + '</td><td class="px-4 py-3 text-right font-medium text-amber-700">₹' + (parseFloat(cf.amount) || 0).toFixed(2) + '</td></tr>';
                }
                html += '</tbody></table></div></div>';
            }

            if (payments.length > 0) {
                html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"><div class="p-4 border-b border-gray-200"><h4 class="font-semibold text-gray-900">ચુકવણી ઇતિહાસ</h4></div><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">તારીખ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">રસીદ નંબર</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">રકમ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">સત્ર</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">પદ્ધતિ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">સંદર્ભ</th></tr></thead><tbody class="divide-y divide-gray-100">';
                for (var i = 0; i < payments.length; i++) {
                    var p = payments[i];
                    var payDateStr = p.payment_date ? p.payment_date.substring(0, 10) : '';
                    var semHtml = p.semester ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">સત્ર ' + p.semester + '</span>' : '<span class="text-gray-400">—</span>';
                    html += '<tr class="hover:bg-gray-50"><td class="px-4 py-3">' + payDateStr + '</td><td class="px-4 py-3 font-mono text-xs text-gray-700">' + (p.receipt_number || '—') + '</td><td class="px-4 py-3 text-right font-semibold text-emerald-700">₹' + (parseFloat(p.amount_paid) || 0).toFixed(2) + '</td><td class="px-4 py-3">' + semHtml + '</td><td class="px-4 py-3">' + (methodLabels[p.payment_method] || p.payment_method) + '</td><td class="px-4 py-3 text-gray-500">' + (p.reference_number || '—') + '</td></tr>';
                }
                html += '</tbody></table></div></div>';
            } else {
                html += '<div class="text-center py-8 bg-white rounded-xl border border-gray-200"><p class="text-gray-500">કોઈ ચુકવણી ઇતિહાસ નથી</p></div>';
            }
            content.innerHTML = html;
        })
        .catch(function(err) { content.innerHTML = '<p class="text-red-500 text-center py-8">' + (err.message || 'સર્વર ભૂલ') + '</p>'; });
    };

    window.loadStatement = loadStatement;

    window.printReport = function(type) {
        var yearId, sem, url;
        if (type === 'summary') {
            yearId = parseInt(document.getElementById('summary-year').value);
            sem = document.getElementById('summary-semester').value || null;
            url = '{{ route("fees.reports.print-summary") }}?academic_year_id=' + yearId + '&semester=' + (sem || '');
        } else if (type === 'due') {
            yearId = parseInt(document.getElementById('due-year').value);
            sem = document.getElementById('due-semester').value || null;
            var stdId = document.getElementById('due-standard').value || '';
            var clsId = document.getElementById('due-class').value || '';
            url = '{{ route("fees.reports.print-due-list") }}?academic_year_id=' + yearId + '&semester=' + (sem || '') + '&standard_id=' + stdId + '&class_id=' + clsId;
        } else if (type === 'collection') {
            yearId = parseInt(document.getElementById('cr-year').value);
            sem = document.getElementById('cr-semester').value || null;
            var fromDate = document.getElementById('cr-from-date').value || '';
            var toDate = document.getElementById('cr-to-date').value || '';
            var method = document.getElementById('cr-method').value || '';
            if (!fromDate || !toDate) { NexSchool.alert.danger('તારીખથી અને તારીખ સુધી પસંદ કરો.'); return; }
            url = '{{ route("fees.reports.print-collection") }}?academic_year_id=' + yearId + '&semester=' + (sem || '') + '&from_date=' + fromDate + '&to_date=' + toDate + '&payment_method=' + method;
        } else if (type === 'statement') {
            yearId = parseInt(document.getElementById('stmt-year').value);
            sem = document.getElementById('stmt-semester').value || null;
            var studentId = document.getElementById('stmt-student').value || '';
            if (!studentId) { NexSchool.alert.danger('વિદ્યાર્થી પસંદ કરો.'); return; }
            url = '{{ route("fees.reports.print-statement") }}?academic_year_id=' + yearId + '&semester=' + (sem || '') + '&student_id=' + studentId;
        }
        if (url) window.open(url, '_blank');
    };

    setTimeout(function() { loadSummary(); }, 100);
})();
</script>
@endpush

