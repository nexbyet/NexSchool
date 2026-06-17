@extends('layouts.app')
@section('title', 'જૂની ફી કેરી ફોરવર્ડ')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">જૂની ફી કેરી ફોરવર્ડ</h1>
                <p class="text-orange-200 mt-1 text-sm">અગાઉના વર્ષની બાકી ફી નવા વર્ષમાં ઉમેરો</p>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ (લક્ષ્ય)</label>
                <select id="year-selector" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    @foreach ($academicYears as $y)
                    <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ <span class="text-red-500">*</span></label>
                <select id="standard-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    <option value="">ધોરણ પસંદ કરો</option>
                    @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ</label>
                <select id="class-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    <option value="">બધા વર્ગો</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="searchStudents()" id="search-btn" class="w-full px-4 py-2.5 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition flex items-center justify-center gap-2">
                    <i class="lni lni-search-1 text-sm"></i> વિદ્યાર્થીઓ બતાવો
                </button>
            </div>
        </div>
    </div>

    <div id="results-section" class="space-y-4 hidden"></div>
    <div id="empty-section" class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="lni lni-wallet-1 text-3xl text-orange-400"></i>
        </div>
        <p class="text-gray-500 font-medium">વિદ્યાર્થીઓ જોવા માટે ફિલ્ટર પસંદ કરો</p>
        <p class="text-gray-400 text-sm mt-1">ધોરણ અને વર્ગ પસંદ કરીને સર્ચ બટન દબાવો</p>
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
    var emptySection = document.getElementById('empty-section');

    var currentYearId = parseInt(yearSelector.value);
    var allAcademicYears = [];

    standardSelect.addEventListener('change', function() {
        var stdId = this.value;
        classSelect.innerHTML = '<option value="">બધા વર્ગો</option>';
        if (!stdId) return;
        fetch('{{ url("attendance/register/classes") }}/' + stdId, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].id;
                opt.textContent = data[i].name;
                classSelect.appendChild(opt);
            }
        });
    });

    yearSelector.addEventListener('change', function() {
        currentYearId = parseInt(this.value);
    });

    var searchStudents = function() {
        var stdId = standardSelect.value;
        if (!stdId) { NexSchool.alert.danger('ધોરણ પસંદ કરો.'); return; }
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> શોધાય છે...';
        fetch('{{ route("fees.carry-forward.students") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: currentYearId,
                standard_id: parseInt(stdId),
                class_id: classSelect.value ? parseInt(classSelect.value) : null,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                allAcademicYears = data.academic_years || [];
                renderStudents(data.students || []);
            } else {
                NexSchool.alert.danger(data.message || 'Error');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'Server error'); })
        .finally(function() { searchBtn.disabled = false; searchBtn.innerHTML = '<i class="lni lni-search-1 text-sm"></i> વિદ્યાર્થીઓ બતાવો'; });
    };
    window.searchStudents = searchStudents;

    function getYearOptions(excludeId) {
        var opts = '<option value="">વર્ષ પસંદ કરો</option>';
        for (var i = 0; i < allAcademicYears.length; i++) {
            var y = allAcademicYears[i];
            if (y.id == excludeId) continue;
            opts += '<option value="' + y.id + '">' + y.year + '</option>';
        }
        return opts;
    }

    function renderStudents(students) {
        if (students.length === 0) {
            resultsSection.innerHTML = '<div class="text-center py-12 bg-white rounded-xl border border-gray-200"><p class="text-gray-500 font-medium">કોઈ વિદ્યાર્થી મળ્યા નથી</p></div>';
            resultsSection.classList.remove('hidden');
            emptySection.classList.add('hidden');
            return;
        }
        var html = '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 sticky top-0"><tr>' +
            '<th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રમ</th>' +
            '<th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">GR નંબર</th>' +
            '<th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">નામ</th>' +
            '<th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">બાકી રકમ (₹)</th>' +
            '<th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">સ્રોત વર્ષ</th>' +
            '<th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રિયા</th>' +
        '</tr></thead><tbody class="divide-y divide-gray-100">';

        for (var i = 0; i < students.length; i++) {
            var s = students[i];
            var cf = s.carry_forward;
            html += '<tr class="hover:bg-gray-50 transition" id="student-row-' + s.id + '">';
            html += '<td class="px-4 py-3 text-gray-500">' + (i + 1) + '</td>';
            html += '<td class="px-4 py-3 font-mono text-gray-900">' + s.gr_number + '</td>';
            html += '<td class="px-4 py-3 font-medium text-gray-900">' + (s.full_name_gu || s.full_name_en || '') + '</td>';
            if (cf) {
                html += '<td class="px-4 py-3"><span class="text-red-600 font-semibold" id="cf-amount-' + s.id + '">₹' + cf.amount.toFixed(2) + '</span></td>';
                html += '<td class="px-4 py-3 text-gray-600" id="cf-year-' + s.id + '">' + cf.from_year + '</td>';
                html += '<td class="px-4 py-3 text-center">';
                html += '<button onclick="editCarryForward(' + s.id + ', ' + cf.id + ', ' + cf.amount + ', ' + cf.from_year_id + ')" class="px-2 py-1 text-xs font-medium text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1 text-xs"></i> સુધારો</button> ';
                html += '<button onclick="deleteCarryForward(' + cf.id + ', ' + s.id + ')" class="px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3 text-xs"></i> કાઢો</button>';
            } else {
                html += '<td class="px-4 py-3"><span id="cf-amount-' + s.id + '" class="text-gray-400">—</span></td>';
                html += '<td class="px-4 py-3"><span id="cf-year-' + s.id + '" class="text-gray-400">—</span></td>';
                html += '<td class="px-4 py-3 text-center">';
                html += '<button onclick="openModal(' + s.id + ', \'' + (s.full_name_gu || s.full_name_en || '') + '\')" class="px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition"><i class="lni lni-plus text-xs"></i> ઉમેરો</button>';
            }
            html += '</td></tr>';
        }
        html += '</tbody></table></div></div>';
        resultsSection.innerHTML = html;
        resultsSection.classList.remove('hidden');
        emptySection.classList.add('hidden');
    }

    // Modal for adding/editing carry forward
    var modal = document.createElement('div');
    modal.id = 'cf-modal';
    modal.className = 'fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden';
    modal.style.opacity = '0';
    modal.style.transition = 'opacity 0.2s';
    modal.innerHTML = '<div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">' +
        '<h3 id="cf-modal-title" class="text-lg font-semibold text-gray-900 mb-4">જૂની ફી ઉમેરો</h3>' +
        '<form id="cf-form">' +
            '<input type="hidden" id="cf-student-id">' +
            '<input type="hidden" id="cf-edit-id">' +
            '<div class="space-y-4">' +
                '<div id="cf-student-info" class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-4">' +
                    '<h4 id="cf-student-name" class="font-semibold text-gray-900"></h4>' +
                    '<p id="cf-student-detail" class="text-sm text-gray-500 mt-0.5"></p>' +
                '</div>' +
                '<div>' +
                    '<label class="block text-sm font-medium text-gray-700 mb-1">બાકી રકમ (₹) <span class="text-red-500">*</span></label>' +
                    '<input type="number" id="cf-amount" step="0.01" min="0" placeholder="0.00" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">' +
                '</div>' +
                '<div>' +
                    '<label class="block text-sm font-medium text-gray-700 mb-1">સ્રોત વર્ષ (જૂનું વર્ષ) <span class="text-red-500">*</span></label>' +
                    '<select id="cf-from-year" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">' +
                        '<option value="">વર્ષ પસંદ કરો</option>' +
                    '</select>' +
                    '<p class="text-xs text-gray-400 mt-1">આ રકમ કયા શૈક્ષણિક વર્ષની બાકી છે?</p>' +
                '</div>' +
            '</div>' +
            '<div class="flex items-center justify-end gap-3 mt-6">' +
                '<button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>' +
                '<button type="submit" id="cf-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg focus:ring-4 focus:ring-orange-200 transition flex items-center gap-2">' +
                    '<i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો' +
                '</button>' +
            '</div>' +
        '</form>' +
    '</div>';
    document.body.appendChild(modal);

    var cfModal = document.getElementById('cf-modal');
    var cfForm = document.getElementById('cf-form');
    var cfStudentId = document.getElementById('cf-student-id');
    var cfEditId = document.getElementById('cf-edit-id');
    var cfStudentName = document.getElementById('cf-student-name');
    var cfStudentDetail = document.getElementById('cf-student-detail');
    var cfAmount = document.getElementById('cf-amount');
    var cfFromYear = document.getElementById('cf-from-year');
    var cfSubmitBtn = document.getElementById('cf-submit-btn');
    var cfModalTitle = document.getElementById('cf-modal-title');

    function getStudentsRow() { return null; }

    window.openModal = function(studentId, studentName) {
        cfStudentId.value = studentId;
        cfEditId.value = '';
        cfModalTitle.textContent = 'જૂની ફી ઉમેરો';
        cfStudentName.textContent = studentName;
        cfStudentDetail.textContent = 'GR: ' + (document.querySelector('#student-row-' + studentId + ' .font-mono')?.textContent || '');
        cfAmount.value = '';
        cfFromYear.innerHTML = getYearOptions(currentYearId);
        cfModal.classList.remove('hidden');
        requestAnimationFrame(function() { cfModal.style.opacity = '1'; });
    };

    window.editCarryForward = function(studentId, cfId, amount, fromYearId) {
        cfStudentId.value = studentId;
        cfEditId.value = cfId;
        cfModalTitle.textContent = 'કેરી ફોરવર્ડ સુધારો';
        cfStudentName.textContent = document.querySelector('#student-row-' + studentId + ' td:nth-child(3)')?.textContent || '';
        cfStudentDetail.textContent = 'GR: ' + (document.querySelector('#student-row-' + studentId + ' .font-mono')?.textContent || '');
        cfAmount.value = amount;
        cfFromYear.innerHTML = getYearOptions(currentYearId);
        cfFromYear.value = fromYearId;
        cfModal.classList.remove('hidden');
        requestAnimationFrame(function() { cfModal.style.opacity = '1'; });
    };

    window.closeModal = function() {
        cfModal.style.opacity = '0';
        setTimeout(function() { cfModal.classList.add('hidden'); }, 200);
    };

    cfModal.addEventListener('click', function(e) { if (e.target === cfModal) closeModal(); });

    cfForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var studentId = parseInt(cfStudentId.value);
        var amount = parseFloat(cfAmount.value);
        var fromYearId = parseInt(cfFromYear.value);
        if (!amount || amount <= 0) { NexSchool.alert.danger('માન્ય રકમ દાખલ કરો.'); return; }
        if (!fromYearId) { NexSchool.alert.danger('સ્રોત વર્ષ પસંદ કરો.'); return; }

        cfSubmitBtn.disabled = true;
        cfSubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાઈ રહ્યું છે...';
        fetch('{{ route("fees.carry-forward.store") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                student_id: studentId,
                academic_year_id: currentYearId,
                from_academic_year_id: fromYearId,
                amount: amount,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success(data.message);
                closeModal();
                searchStudents();
            } else {
                NexSchool.alert.danger(data.message || 'Error');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'Server error'); })
        .finally(function() { cfSubmitBtn.disabled = false; cfSubmitBtn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો'; });
    });

    window.deleteCarryForward = function(cfId, studentId) {
        NexSchool.confirm.show('કેરી ફોરવર્ડ કાઢો', 'શું તમે આ વિદ્યાર્થીની જૂની બાકી ફી દૂર કરવા માંગો છો?', 'danger')
        .then(function(confirmed) {
            if (!confirmed) return;
            fetch('{{ url("fees/carry-forward/delete") }}/' + cfId, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            })
            .then(function(r) { if (!r.ok) return r.json().then(function(e) { throw e; }); return r.json(); })
            .then(function(data) {
                if (data.success) { NexSchool.alert.success(data.message); searchStudents(); }
                else { NexSchool.alert.danger(data.message || 'Error'); }
            })
            .catch(function(err) { NexSchool.alert.danger(err.message || 'Server error'); });
        });
    };
})();
</script>
@endpush
