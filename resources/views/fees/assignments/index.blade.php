@extends('layouts.app')
@section('title', 'ફી સોંપણી')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-500 to-blue-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ફી સોંપણી</h1>
                <p class="text-indigo-200 mt-1 text-sm">વિદ્યાર્થીઓને ફી એસાઇન કરો</p>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                <select id="year-selector" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    @foreach ($academicYears as $y)
                    <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ <span class="text-red-500">*</span></label>
                <select id="standard-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    <option value="">ધોરણ પસંદ કરો</option>
                    @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ</label>
                <select id="class-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    <option value="">બધા વર્ગો</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="searchStudents()" id="search-btn" class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="lni lni-search-1 text-sm"></i> વિદ્યાર્થીઓ બતાવો
                </button>
            </div>
        </div>

        <div id="fee-structures-section" class="hidden mt-4 pt-4 border-t border-gray-100">
            <label class="block text-sm font-semibold text-gray-700 mb-2">ફી માળખાં (એસાઇન કરવા માટે પસંદ કરો)</label>
            <div id="fee-structure-checkboxes" class="flex flex-wrap gap-3"></div>
        </div>
    </div>

    <div id="results-section" class="hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold text-gray-900" id="student-count">વિદ્યાર્થીઓ (0)</h2>
                <button onclick="showUnassignedOnly()" id="unassigned-toggle" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition flex items-center gap-1">
                    <i class="lni lni-funnel text-xs"></i> ફક્ત અસાઇન ન થયેલા
                </button>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="quickAssignAll()" id="quick-assign-all" class="hidden px-4 py-2 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition flex items-center gap-1 disabled:opacity-50">
                    <i class="lni lni-plus text-xs"></i> બધાને ફી સોંપો
                </button>
                <button onclick="openBulkModal()" id="bulk-assign-btn" class="px-4 py-2 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="lni lni-plus text-xs"></i> બલ્ક એસાઇન કરો
                </button>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રમ</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">GR નંબર</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">નામ</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">પિતાનું નામ</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ફી પ્રકાર</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">કુલ ફી</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">છૂટ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">ચોખ્ખી ફી</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">કુલ</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રિયા</th>
                        </tr>
                    </thead>
                    <tbody id="students-tbody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="empty-section" class="hidden text-center py-16 bg-white rounded-xl border border-gray-200">
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="lni lni-search-1 text-3xl text-indigo-400"></i>
        </div>
        <p class="text-gray-500 font-medium">વિદ્યાર્થીઓ જોવા માટે ઉપર ફિલ્ટર પસંદ કરો</p>
        <p class="text-gray-400 text-sm mt-1">ધોરણ અને વર્ગ પસંદ કરીને સર્ચ બટન દબાવો</p>
    </div>
</div>

<div id="assign-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 id="assign-modal-title" class="text-lg font-semibold text-gray-900 mb-4">ફી સોંપો</h3>
        <form id="assign-form">
            <input type="hidden" id="assign-edit-id">
            <div class="space-y-4">
                <div class="bg-indigo-50 rounded-lg p-3">
                    <p class="text-sm text-indigo-800 font-medium" id="selected-count-msg">0 વિદ્યાર્થીઓ પસંદ કર્યા</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ફી માળખાં જે સોંપાશે</label>
                    <div id="modal-fee-structures-list" class="space-y-2"></div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="waive-checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm font-medium text-gray-700">બધી ફી માફ (સંપૂર્ણ માફી)</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">સરખી છૂટ (₹) — વૈકલ્પિક (બધા માળખાં પર સમાન દર)</label>
                        <input type="number" id="concession-input" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 text-sm space-y-1">
                    <div class="flex justify-between"><span class="text-gray-600">કુલ રકમ:</span><span id="summary-total" class="font-semibold text-gray-900">₹0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">બાકાત હેડ્સ:</span><span id="summary-excluded" class="font-medium text-red-600">₹0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">છૂટ:</span><span id="summary-concession" class="font-medium text-orange-600">₹0.00</span></div>
                    <div class="flex justify-between border-t border-gray-200 pt-1"><span class="font-semibold text-gray-800">ચોખ્ખી ફી:</span><span id="summary-net" class="font-bold text-indigo-700">₹0.00</span></div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="assign-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition flex items-center gap-2">
                    <i class="lni lni-floppy-disk-1 text-sm"></i> ફી સોંપો
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
    var yearSelector = document.getElementById('year-selector');
    var standardSelect = document.getElementById('standard-select');
    var classSelect = document.getElementById('class-select');
    var searchBtn = document.getElementById('search-btn');
    var resultsSection = document.getElementById('results-section');
    var studentsTbody = document.getElementById('students-tbody');
    var emptySection = document.getElementById('empty-section');
    var selectAll = document.getElementById('select-all');
    var bulkAssignBtn = document.getElementById('bulk-assign-btn');
    var quickAssignAll = document.getElementById('quick-assign-all');
    var studentCount = document.getElementById('student-count');
    var unassignedToggle = document.getElementById('unassigned-toggle');
    var feeStructuresSection = document.getElementById('fee-structures-section');
    var feeStructureCheckboxes = document.getElementById('fee-structure-checkboxes');

    var assignModal = document.getElementById('assign-modal');
    var assignForm = document.getElementById('assign-form');
    var assignModalTitle = document.getElementById('assign-modal-title');
    var selectedCountMsg = document.getElementById('selected-count-msg');
    var modalFeeStructuresList = document.getElementById('modal-fee-structures-list');
    var waiveCheckbox = document.getElementById('waive-checkbox');
    var concessionInput = document.getElementById('concession-input');
    var assignSubmitBtn = document.getElementById('assign-submit-btn');
    var summaryTotal = document.getElementById('summary-total');
    var summaryExcluded = document.getElementById('summary-excluded');
    var summaryConcession = document.getElementById('summary-concession');
    var summaryNet = document.getElementById('summary-net');
    var assignEditId = document.getElementById('assign-edit-id');
    var modalFeeHeads = document.getElementById('modal-fee-structures-list'); // reuse for head event delegation

    var currentStudents = [];
    var selectedStudentIds = [];
    var isUnassignedFilter = false;
    var currentYearId = parseInt(yearSelector.value);
    var currentStandardId = '';
    var currentClassId = '';
    var currentStructures = [];

    var feeTypeColors = {'tuition': 'bg-indigo-100 text-indigo-700', 'transport': 'bg-cyan-100 text-cyan-700', 'other': 'bg-gray-100 text-gray-700'};
    var feeTypeLabels = {'tuition': 'શાળા ફી', 'transport': 'બસ ફી', 'other': 'અન્ય'};

    function getSelectedStructureIds() {
        var ids = [];
        var cbs = feeStructureCheckboxes.querySelectorAll('input[type="checkbox"]:checked');
        for (var i = 0; i < cbs.length; i++) {
            ids.push(parseInt(cbs[i].value));
        }
        return ids;
    }

    function getStructureDataById(id) {
        for (var i = 0; i < currentStructures.length; i++) {
            if (currentStructures[i].id === id) return currentStructures[i];
        }
        return null;
    }

    function fetchStructuresAndPopulate(yearId, stdId) {
        if (!stdId) {
            feeStructuresSection.classList.add('hidden');
            return;
        }
        fetch('{{ url("fees/structures/by-year") }}/' + yearId, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { if (!res.ok) throw new Error('Error'); return res.json(); })
        .then(function(structures) {
            currentStructures = structures;
            feeStructureCheckboxes.innerHTML = '';
            var count = 0;
            for (var i = 0; i < structures.length; i++) {
                var stds = structures[i].standards || [];
                var hasStd = stds.some(function(st) { return (typeof st === 'object' ? st.id : st) == stdId; });
                if (!hasStd) continue;
                count++;
                var lbl = document.createElement('label');
                lbl.className = 'flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500';
                var semLabel = structures[i].semester ? '<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold bg-sky-100 text-sky-700">' + structures[i].semester + '</span>' : '<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">' + 'વા' + '</span>';
                var typeLabel = feeTypeLabels[structures[i].type] || structures[i].type;
                var amt = (parseFloat(structures[i].total_amount) || 0).toFixed(2);
                lbl.innerHTML = '<input type="checkbox" class="structure-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="' + structures[i].id + '" data-total="' + (structures[i].total_amount || 0) + '" data-type="' + (structures[i].type || '') + '"> <span class="text-sm font-medium text-gray-800">' + typeLabel + semLabel + '</span> <span class="text-xs text-gray-400">₹' + amt + '</span>';
                feeStructureCheckboxes.appendChild(lbl);
            }
            if (count > 0) {
                feeStructuresSection.classList.remove('hidden');
            } else {
                feeStructuresSection.classList.add('hidden');
            }
        })
        .catch(function() {
            feeStructureCheckboxes.innerHTML = '<p class="text-sm text-gray-400">ફી માળખાં લોડ કરવામાં ભૂલ</p>';
        });
    }

    standardSelect.addEventListener('change', function() {
        var stdId = this.value;
        currentStandardId = stdId;
        classSelect.innerHTML = '<option value="">બધા વર્ગો</option>';
        if (!stdId) { searchBtn.disabled = true; return; }
        searchBtn.disabled = false;
        fetch('{{ url("attendance/register/classes") }}/' + stdId, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { if (!res.ok) throw new Error('Error'); return res.json(); })
        .then(function(data) {
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].id;
                opt.textContent = data[i].name;
                classSelect.appendChild(opt);
            }
        })
        .catch(function() {});
        fetchStructuresAndPopulate(currentYearId, stdId);
    });

    yearSelector.addEventListener('change', function() {
        currentYearId = parseInt(this.value);
        if (currentStandardId) {
            fetchStructuresAndPopulate(currentYearId, currentStandardId);
        }
    });

    var showUnassignedOnly = function() {
        isUnassignedFilter = !isUnassignedFilter;
        if (isUnassignedFilter) {
            unassignedToggle.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            unassignedToggle.classList.remove('text-gray-600', 'border-gray-300');
        } else {
            unassignedToggle.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            unassignedToggle.classList.add('text-gray-600', 'border-gray-300');
        }
        renderTable(currentStudents);
    };

    window.showUnassignedOnly = showUnassignedOnly;

    var searchStudents = function() {
        if (!currentStandardId) { NexSchool.alert.danger('કૃપા કરીને ધોરણ પસંદ કરો.'); return; }
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> શોધાય છે...';
        fetch('{{ route("fees.assignments.students") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: currentYearId,
                standard_id: parseInt(currentStandardId),
                class_id: classSelect.value ? parseInt(classSelect.value) : null,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                currentStudents = data.students || [];
                renderTable(currentStudents);
                resultsSection.classList.remove('hidden');
                emptySection.classList.add('hidden');
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ આવી.');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { searchBtn.disabled = false; searchBtn.innerHTML = '<i class="lni lni-search-1 text-sm"></i> વિદ્યાર્થીઓ બતાવો'; });
    };

    window.searchStudents = searchStudents;

    function getStudentFeeForStructure(student, structureId) {
        if (!structureId || !student.fees) return null;
        for (var i = 0; i < student.fees.length; i++) {
            if (student.fees[i].fee_structure_id == structureId) return student.fees[i];
        }
        return null;
    }

    function studentHasAllSelectedFees(student) {
        var selIds = getSelectedStructureIds();
        if (selIds.length === 0) return true;
        for (var i = 0; i < selIds.length; i++) {
            if (!getStudentFeeForStructure(student, selIds[i])) return false;
        }
        return true;
    }

    var renderTable = function(students) {
        var filtered = students;
        if (isUnassignedFilter) {
            filtered = [];
            for (var i = 0; i < students.length; i++) {
                if (!studentHasAllSelectedFees(students[i])) {
                    filtered.push(students[i]);
                }
            }
        }
        studentCount.textContent = 'વિદ્યાર્થીઓ (' + filtered.length + ')';
        updateBulkButton();
        var hasUnassigned = false;
        for (var i = 0; i < filtered.length; i++) {
            if (!studentHasAllSelectedFees(filtered[i])) { hasUnassigned = true; break; }
        }
        quickAssignAll.classList.toggle('hidden', !hasUnassigned);

        var html = '';
        for (var i = 0; i < filtered.length; i++) {
            var s = filtered[i];
            var idx = i + 1;

            var allFeesHtml = '';
            var totalCombinedNet = 0;
            var feeCount = 0;
            for (var fi = 0; fi < (s.fees || []).length; fi++) {
                var sf = s.fees[fi];
                var ft = (sf.fee_structure && sf.fee_structure.type) ? sf.fee_structure.type : 'other';
                var fl = feeTypeLabels[ft] || ft;
                var semTxt = sf.semester ? '<sup class="text-[9px] font-bold ml-0.5" style="color:inherit">' + sf.semester + '</sup>' : '';
                totalCombinedNet += parseFloat(sf.net_amount) || 0;
                feeCount++;
                var amt = (parseFloat(sf.net_amount) || 0).toFixed(2);
                allFeesHtml += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ' + (feeTypeColors[ft] || 'bg-gray-100 text-gray-700') + '">' + fl + semTxt + ' ₹' + amt + '</span> ';
            }
            if (!allFeesHtml) {
                allFeesHtml = '<span class="text-xs text-gray-400">—</span>';
            }

            var checked = '';
            for (var j = 0; j < selectedStudentIds.length; j++) {
                if (selectedStudentIds[j] === s.id) { checked = 'checked'; break; }
            }
            var selStructIds = getSelectedStructureIds();
            var hasAllSelectedFees = (selStructIds.length > 0) && studentHasAllSelectedFees(s);
            var checkboxDisabled = hasAllSelectedFees ? 'disabled' : '';
            var actionBtn = '<div class="flex items-center justify-center gap-1">';
            // Show "ફી સોંપો" if missing any selected structure
            if (selStructIds.length > 0 && !studentHasAllSelectedFees(s)) {
                actionBtn += '<button onclick="assignSingle(' + s.id + ')" class="px-1.5 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="ફી સોંપો"><i class="lni lni-plus text-xs"></i></button> ';
            }
            for (var fi2 = 0; fi2 < (s.fees || []).length; fi2++) {
                var sf2 = s.fees[fi2];
                var ft2 = (sf2.fee_structure && sf2.fee_structure.type) ? sf2.fee_structure.type : 'other';
                actionBtn += '<button onclick="editAssignment(' + s.id + ',' + sf2.id + ')" class="px-1.5 py-1 text-xs font-medium text-amber-600 hover:bg-amber-50 rounded-lg transition" title="' + (feeTypeLabels[ft2] || ft2) + ' સુધારો"><i class="lni lni-pencil-1 text-xs"></i></button> ';
                actionBtn += '<button onclick="unassignStudent(' + s.id + ',' + sf2.id + ')" class="px-1.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition" title="' + (feeTypeLabels[ft2] || ft2) + ' પાછી લો"><i class="lni lni-trash-3 text-xs"></i></button>';
            }
            if (actionBtn === '<div class="flex items-center justify-center gap-1">') {
                actionBtn += '<span class="text-xs text-gray-400">—</span>';
            }
            actionBtn += '</div>';
            html += '<tr class="hover:bg-gray-50 transition">';
            html += '<td class="px-4 py-3"><input type="checkbox" class="student-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="' + s.id + '" ' + checked + ' ' + checkboxDisabled + ' onchange="updateBulkButton()"></td>';
            html += '<td class="px-4 py-3 text-gray-500">' + idx + '</td>';
            html += '<td class="px-4 py-3 font-mono text-gray-900">' + s.gr_number + '</td>';
            html += '<td class="px-4 py-3 font-medium text-gray-900">' + (s.full_name_gu || s.full_name_en || '') + '</td>';
            html += '<td class="px-4 py-3 text-gray-600">' + (s.father_name_gu || s.father_name_en || '') + '</td>';
            html += '<td class="px-4 py-3">' + allFeesHtml + '</td>';
            html += '<td class="px-4 py-3 text-right text-gray-900">—</td>';
            html += '<td class="px-4 py-3 text-right text-gray-900">—</td>';
            html += '<td class="px-4 py-3 text-right font-semibold text-gray-900">—</td>';
            html += '<td class="px-4 py-3 text-right text-xs text-indigo-600 font-medium">' + (feeCount > 0 ? 'કુલ ₹' + totalCombinedNet.toFixed(2) : '') + '</td>';
            html += '<td class="px-4 py-3 text-center">' + actionBtn + '</td>';
            html += '</tr>';
        }
        if (filtered.length === 0) {
            html = '<tr><td colspan="11" class="px-4 py-16 text-center"><div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl flex items-center justify-center"><i class="lni lni-search-1 text-2xl text-gray-400"></i></div><p class="text-gray-500 font-medium">કોઈ વિદ્યાર્થી મળ્યા નથી</p></td></tr>';
        }
        studentsTbody.innerHTML = html;
        selectAll.checked = false;
    };

    var toggleSelectAll = function(cb) {
        var checkboxes = studentsTbody.querySelectorAll('.student-checkbox:not(:disabled)');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = cb.checked;
        }
        updateBulkButton();
    };

    window.toggleSelectAll = toggleSelectAll;

    var updateBulkButton = function() {
        selectedStudentIds = [];
        var checkboxes = studentsTbody.querySelectorAll('.student-checkbox:checked');
        for (var i = 0; i < checkboxes.length; i++) {
            selectedStudentIds.push(parseInt(checkboxes[i].value));
        }
        bulkAssignBtn.disabled = selectedStudentIds.length === 0;
    };

    window.updateBulkButton = updateBulkButton;

    var structureHeadsCache = {};

    function fetchStructureHeads(structureId) {
        if (structureHeadsCache[structureId]) {
            return Promise.resolve(structureHeadsCache[structureId]);
        }
        return fetch('{{ url("fees/structures") }}/' + structureId, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            var heads = data.details || [];
            structureHeadsCache[structureId] = heads;
            return heads;
        });
    }

    /* Build the modal fee-structures list with per-structure head checkboxes */
    function buildModalStructures(selIds, existingData) {
        // existingData: { structureId: { waived, concession, excludedHeads } } for edit mode
        modalFeeStructuresList.innerHTML = '';
        var promises = [];
        for (var i = 0; i < selIds.length; i++) {
            (function(fsId) {
                promises.push(
                    fetchStructureHeads(fsId).then(function(heads) {
                        return { structureId: fsId, heads: heads };
                    })
                );
            })(selIds[i]);
        }
        Promise.all(promises).then(function(results) {
            var allCount = 0;
            for (var ri = 0; ri < results.length; ri++) {
                var sid = results[ri].structureId;
                var heads = results[ri].heads;
                var struct = getStructureDataById(sid);
                var typeLabel = struct ? (feeTypeLabels[struct.type] || struct.type) : ('ID ' + sid);
                var amt = struct ? ((parseFloat(struct.total_amount) || 0).toFixed(2)) : '0.00';
                var exData = existingData ? existingData[sid] : null;
                var block = document.createElement('div');
                block.className = 'structure-block border border-gray-200 rounded-lg p-3';
                block.setAttribute('data-structure-id', sid);
                var totalHeads = 0;
                for (var hi = 0; hi < heads.length; hi++) {
                    totalHeads += parseFloat(heads[hi].amount) || 0;
                }
                var semLabel = struct && struct.semester
                    ? '<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold bg-sky-100 text-sky-700">' + struct.semester + '</span>'
                    : '<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">વા</span>';
                block.innerHTML = '<div class="flex items-center justify-between mb-2">' +
                    '<span class="text-sm font-semibold text-indigo-800">' + typeLabel + semLabel + '</span>' +
                    '<span class="text-sm font-medium text-gray-600">₹' + amt + '</span>' +
                '</div>' +
                '<div class="space-y-1.5 pl-1">' +
                    '<p class="text-xs text-gray-400 mb-1">ફી હેડ્સ (બાકાત માટે અનચેક કરો):</p>' +
                    '<div class="structure-heads space-y-1"></div>' +
                '</div>';
                var headsContainer = block.querySelector('.structure-heads');
                for (var hi = 0; hi < heads.length; hi++) {
                    var h = heads[hi];
                    var headName = (h.fee_head && h.fee_head.name_gu) ? h.fee_head.name_gu : (h.fee_head ? h.fee_head.name_en : 'હેડ');
                    var excludedIds = exData ? (exData.excludedHeads || []) : [];
                    var isExcluded = excludedIds.indexOf(h.fee_head_id) !== -1;
                    var lbl = document.createElement('label');
                    lbl.className = 'flex items-center gap-2 px-2 py-1 rounded hover:bg-gray-50 cursor-pointer';
                    lbl.innerHTML = '<input type="checkbox" class="fee-head-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-structure-id="' + sid + '" value="' + h.fee_head_id + '" data-amount="' + (parseFloat(h.amount) || 0) + '" ' + (isExcluded ? '' : 'checked') + ' onchange="updateModalSummary()"> <span class="text-sm text-gray-700">' + headName + '</span> <span class="text-xs text-gray-400 ml-auto">₹' + (parseFloat(h.amount) || 0).toFixed(2) + '</span>';
                    headsContainer.appendChild(lbl);
                }
                modalFeeStructuresList.appendChild(block);
                allCount++;
            }
            if (allCount === 0) {
                modalFeeStructuresList.innerHTML = '<p class="text-sm text-gray-400 py-4 text-center">કોઈ ફી માળખું પસંદ થયું નથી</p>';
            }
            updateModalSummary();
        });
    }

    function updateModalSummary() {
        var total = 0;
        var excludedTotal = 0;
        var headCheckboxes = modalFeeStructuresList.querySelectorAll('.fee-head-checkbox');
        for (var i = 0; i < headCheckboxes.length; i++) {
            var amt = parseFloat(headCheckboxes[i].getAttribute('data-amount')) || 0;
            total += amt;
            if (!headCheckboxes[i].checked) excludedTotal += amt;
        }
        var concession = parseFloat(concessionInput.value) || 0;
        var waived = waiveCheckbox.checked;
        var net = waived ? 0 : (total - excludedTotal - concession);
        if (net < 0) net = 0;
        summaryTotal.textContent = '₹' + total.toFixed(2);
        summaryExcluded.textContent = '₹' + excludedTotal.toFixed(2);
        summaryConcession.textContent = '₹' + concession.toFixed(2);
        summaryNet.textContent = '₹' + net.toFixed(2);
    }
    window.updateModalSummary = updateModalSummary;

    var openBulkModal = function() {
        if (selectedStudentIds.length === 0) { NexSchool.alert.danger('કૃપા કરીને વિદ્યાર્થીઓ પસંદ કરો.'); return; }
        var selIds = getSelectedStructureIds();
        if (selIds.length === 0) { NexSchool.alert.danger('કૃપા કરીને ઉપર ફી માળખાં પસંદ કરો.'); return; }
        assignEditId.value = '';
        assignModalTitle.textContent = 'બલ્ક ફી સોંપો';
        selectedCountMsg.textContent = selectedStudentIds.length + ' વિદ્યાર્થીઓ પસંદ કર્યા';
        waiveCheckbox.checked = false;
        concessionInput.value = '';
        buildModalStructures(selIds, null);
        assignModal.classList.remove('hidden');
        requestAnimationFrame(function() { assignModal.style.opacity = '1'; });
    };

    window.openBulkModal = openBulkModal;

    var assignSingle = function(studentId) {
        var selIds = getSelectedStructureIds();
        if (selIds.length === 0) { NexSchool.alert.danger('કૃપા કરીને ઉપર ફી માળખાં પસંદ કરો.'); return; }
        selectedStudentIds = [studentId];
        assignEditId.value = '';
        assignModalTitle.textContent = 'વ્યક્તિગત ફી સોંપો';
        selectedCountMsg.textContent = '1 વિદ્યાર્થી પસંદ કર્યો';
        waiveCheckbox.checked = false;
        concessionInput.value = '';
        buildModalStructures(selIds, null);
        assignModal.classList.remove('hidden');
        requestAnimationFrame(function() { assignModal.style.opacity = '1'; });
    };

    window.assignSingle = assignSingle;

    window.editAssignment = function(studentId, feeId) {
        fetch('{{ url("fees/assignments/students") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: currentYearId,
                standard_id: parseInt(currentStandardId),
                class_id: null,
            }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success || !data.students) return;
            var studentData = null;
            for (var i = 0; i < data.students.length; i++) {
                if (data.students[i].id === studentId) { studentData = data.students[i]; break; }
            }
            if (!studentData) { NexSchool.alert.danger('Student not found.'); return; }
            var fee = null;
            for (var i = 0; i < (studentData.fees || []).length; i++) {
                if (studentData.fees[i].id === feeId) { fee = studentData.fees[i]; break; }
            }
            if (!fee) { NexSchool.alert.danger('Fee assignment not found.'); return; }
            selectedStudentIds = [studentId];
            assignEditId.value = fee.id;
            assignModalTitle.textContent = 'ફી એસાઇનમેન્ટ સુધારો';
            selectedCountMsg.textContent = '1 વિદ્યાર્થી — ' + (studentData.full_name_gu || studentData.full_name_en || '');
            waiveCheckbox.checked = fee.is_waived || false;
            concessionInput.value = fee.concession_amount || '';
            var structId = fee.fee_structure_id;
            var exclHeads = fee.excluded_fee_heads || [];
            var existingData = {};
            existingData[structId] = { waived: fee.is_waived, concession: fee.concession_amount, excludedHeads: exclHeads };
            buildModalStructures([structId], existingData);
            assignModal.classList.remove('hidden');
            requestAnimationFrame(function() { assignModal.style.opacity = '1'; });
        })
        .catch(function() { NexSchool.alert.danger('Data fetch error.'); });
    };

    window.unassignStudent = function(studentId, feeId) {
        NexSchool.confirm.show('ફી પાછી લો', 'શું તમે આ વિદ્યાર્થીની ફી સોંપણી પાછી લેવા માંગો છો?', 'danger')
        .then(function(confirmed) {
            if (!confirmed) return;
            fetch('{{ url("fees/assignments/remove") }}/' + feeId, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            })
            .then(function(r) { if (!r.ok) return r.json().then(function(e) { throw e; }); return r.json(); })
            .then(function(resp) {
                if (resp.success) { NexSchool.alert.success(resp.message); searchStudents(); }
                else { NexSchool.alert.danger(resp.message || 'Error'); }
            })
            .catch(function(err) { NexSchool.alert.danger(err.message || 'Error'); });
        });
    };

    var closeAssignModal = function() {
        assignModal.style.opacity = '0';
        setTimeout(function() { assignModal.classList.add('hidden'); }, 200);
    };

    window.closeAssignModal = closeAssignModal;

    assignModal.addEventListener('click', function(e) { if (e.target === assignModal) closeAssignModal(); });

    waiveCheckbox.addEventListener('change', updateModalSummary);
    concessionInput.addEventListener('input', updateModalSummary);

    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var selIds = getSelectedStructureIds();
        if (selIds.length === 0 && !assignEditId.value) { NexSchool.alert.danger('ફી માળખાં પસંદ કરો.'); return; }
        if (selectedStudentIds.length === 0) { NexSchool.alert.danger('કોઈ વિદ્યાર્થી પસંદ નથી.'); return; }

        // Collect excluded heads per structure
        var excludedHeadsByStruct = {};
        var headCheckboxes = modalFeeStructuresList.querySelectorAll('.fee-head-checkbox');
        for (var i = 0; i < headCheckboxes.length; i++) {
            var sid = parseInt(headCheckboxes[i].getAttribute('data-structure-id'));
            if (!headCheckboxes[i].checked) {
                if (!excludedHeadsByStruct[sid]) excludedHeadsByStruct[sid] = [];
                excludedHeadsByStruct[sid].push(parseInt(headCheckboxes[i].value));
            }
        }

        assignSubmitBtn.disabled = true;
        assignSubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાઈ રહ્યું છે...';
        var isEdit = !!assignEditId.value;
        var url = isEdit ? '{{ url("fees/assignments/update") }}/' + assignEditId.value : '{{ route("fees.assignments.bulk") }}';
        var bodyData = {
            academic_year_id: currentYearId,
            standard_id: parseInt(currentStandardId),
            class_id: classSelect.value ? parseInt(classSelect.value) : null,
            fee_structure_ids: isEdit ? [parseInt(assignEditId.value)] : selIds,
            concession_amount: concessionInput.value ? parseFloat(concessionInput.value) : 0,
            is_waived: waiveCheckbox.checked,
            excluded_fee_heads: excludedHeadsByStruct,
            student_ids: selectedStudentIds,
        };
        if (isEdit) {
            var singleSid = parseInt(Object.keys(excludedHeadsByStruct)[0] || 0);
            bodyData.excluded_fee_heads = excludedHeadsByStruct[singleSid] || [];
            delete bodyData.fee_structure_ids;
        }
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify(bodyData),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success(data.message || 'ફી સોંપાઈ.');
                closeAssignModal();
                searchStudents();
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ આવી.');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { assignSubmitBtn.disabled = false; assignSubmitBtn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> ફી સોંપો'; });
    });

    var doQuickAssign = function() {
        var selIds = getSelectedStructureIds();
        if (selIds.length === 0) { NexSchool.alert.danger('કૃપા કરીને ઉપર ફી માળખાં પસંદ કરો.'); return; }
        var unassignedIds = [];
        for (var i = 0; i < currentStudents.length; i++) {
            if (!studentHasAllSelectedFees(currentStudents[i])) {
                unassignedIds.push(currentStudents[i].id);
            }
        }
        if (unassignedIds.length === 0) { NexSchool.alert.note('બધા વિદ્યાર્થીઓને પહેલેથી ફી સોંપાઈ ચૂકી છે.'); return; }
        assignEditId.value = '';
        assignModalTitle.textContent = 'બધાને ફી સોંપો';
        selectedCountMsg.textContent = unassignedIds.length + ' વિદ્યાર્થીઓ (અસાઇન નથી)';
        waiveCheckbox.checked = false;
        concessionInput.value = '';
        selectedStudentIds = unassignedIds;
        buildModalStructures(selIds, null);
        assignModal.classList.remove('hidden');
        requestAnimationFrame(function() { assignModal.style.opacity = '1'; });
    };

    window.quickAssignAll = doQuickAssign;

    var initialLoad = function() {
        resultsSection.classList.add('hidden');
        emptySection.classList.remove('hidden');
        searchBtn.disabled = true;
    };
    initialLoad();
})();
</script>
@endpush
