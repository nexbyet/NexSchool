@extends('layouts.app')
@section('title', 'ફી વસૂલાત')
@section('content')
@php
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
    $methodLabels = ['cash' => 'રોકડા', 'bank' => 'બેંક ટ્રાન્સફર', 'cheque' => 'ચેક', 'online' => 'ઓનલાઇન'];
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ફી વસૂલાત</h1>
                <p class="text-emerald-200 mt-1 text-sm">વિદ્યાર્થીઓ પાસેથી ફી વસૂલ કરો</p>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                <select id="year-selector" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    @foreach ($academicYears as $y)
                    <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ <span class="text-red-500">*</span></label>
                <select id="standard-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">ધોરણ પસંદ કરો</option>
                    @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ</label>
                <select id="class-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">બધા વર્ગો</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                <select id="semester-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">બધા</option>
                    <option value="1">સત્ર 1</option>
                    <option value="2">સત્ર 2</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">શોધો (નામ / GR)</label>
                <input type="text" id="search-input" placeholder="વિદ્યાર્થીનું નામ અથવા GR નંબર" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
            </div>
            <div class="flex items-end">
                <button onclick="searchStudents()" id="search-btn" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <i class="lni lni-search-1 text-sm"></i> શોધો
                </button>
            </div>
        </div>
    </div>

    <div id="results-section" class="space-y-4"></div>

    <div id="empty-section" class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="lni lni-wallet-1 text-3xl text-emerald-400"></i>
        </div>
        <p class="text-gray-500 font-medium">વિદ્યાર્થીઓ શોધવા માટે ફિલ્ટર પસંદ કરો</p>
        <p class="text-gray-400 text-sm mt-1">ધોરણ અને વર્ગ પસંદ કરીને શોધો બટન દબાવો</p>
    </div>
</div>

<div id="payment-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">ફી ભરો</h3>
        <form id="payment-form">
            <input type="hidden" id="pay-student-id">
            <input type="hidden" id="pay-academic-year-id" value="{{ $activeYearId }}">
            <div id="pay-student-info" class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-4 mb-4">
                <h4 id="pay-student-name" class="font-semibold text-gray-900"></h4>
                <p id="pay-student-gr" class="text-sm text-gray-500 mt-0.5"></p>
                <p id="pay-student-class" class="text-sm text-gray-500"></p>
            </div>
            <div id="pay-fees-container" class="space-y-3 mb-4"></div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ચુકવણી તારીખ <span class="text-red-500">*</span></label>
                    <input type="date" id="pay-date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ચુકવણી પદ્ધતિ <span class="text-red-500">*</span></label>
                    <select id="pay-method" onchange="toggleRefField()" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                        <option value="cash">રોકડા</option>
                        <option value="bank">બેંક ટ્રાન્સફર</option>
                        <option value="cheque">ચેક</option>
                        <option value="online">ઓનલાઇન</option>
                    </select>
                </div>
                <div id="ref-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">સંદર્ભ નંબર</label>
                    <input type="text" id="pay-reference" placeholder="ચેક નંબર / ટ્રાન્ઝેક્શન ID" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">નોંધ</label>
                    <textarea id="pay-notes" rows="2" placeholder="વૈકલ્પિક" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="pay-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg focus:ring-4 focus:ring-emerald-200 transition flex items-center gap-2">
                    <i class="lni lni-check-circle-1 text-sm"></i> ચુકવણી કરો
                </button>
            </div>
        </form>
    </div>
</div>

<div id="history-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">ચુકવણી ઇતિહાસ</h3>
            <button type="button" onclick="closeHistoryModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"><i class="lni lni-xmark text-lg"></i></button>
        </div>
        <div id="history-content"></div>
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
    var semesterSelect = document.getElementById('semester-select');
    var searchInput = document.getElementById('search-input');
    var searchBtn = document.getElementById('search-btn');
    var resultsSection = document.getElementById('results-section');
    var emptySection = document.getElementById('empty-section');
    var paymentModal = document.getElementById('payment-modal');
    var paymentForm = document.getElementById('payment-form');
    var payStudentId = document.getElementById('pay-student-id');
    var payAcademicYearId = document.getElementById('pay-academic-year-id');
    var payStudentName = document.getElementById('pay-student-name');
    var payStudentGr = document.getElementById('pay-student-gr');
    var payStudentClass = document.getElementById('pay-student-class');
    var payFeesContainer = document.getElementById('pay-fees-container');
    var payDate = document.getElementById('pay-date');
    var payMethod = document.getElementById('pay-method');
    var payReference = document.getElementById('pay-reference');
    var payNotes = document.getElementById('pay-notes');
    var paySubmitBtn = document.getElementById('pay-submit-btn');
    var refField = document.getElementById('ref-field');
    var historyModal = document.getElementById('history-modal');
    var historyContent = document.getElementById('history-content');

    var currentYearId = parseInt(yearSelector.value);
    var feeTypeLabels = {'tuition': 'શાળા ફી', 'transport': 'બસ ફી', 'other': 'અન્ય', 'carry_forward': 'કેરી ફોરવર્ડ'};
    var methodLabels = {'cash': 'રોકડા', 'bank': 'બેંક ટ્રાન્સફર', 'cheque': 'ચેક', 'online': 'ઓનલાઇન'};

    function getFeeType(fee) {
        return !fee.fee_structure ? 'carry_forward' : (fee.fee_structure.type || 'other');
    }
    function getFeeTypeLabel(fee) {
        var t = getFeeType(fee);
        return t === 'carry_forward' ? 'કેરી ફોરવર્ડ' : (feeTypeLabels[t] || 'અન્ય');
    }
    function getFeeTypeColor(fee) {
        var t = getFeeType(fee);
        if (t === 'carry_forward') return 'bg-orange-100 text-orange-700';
        if (t === 'tuition') return 'bg-indigo-100 text-indigo-700';
        if (t === 'transport') return 'bg-cyan-100 text-cyan-700';
        return 'bg-gray-100 text-gray-700';
    }
    function getSemBadge(fee) {
        if (!fee.fee_structure) return '';
        return fee.semester
            ? '<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold bg-sky-100 text-sky-700">' + fee.semester + '</span>'
            : '<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">વા</span>';
    }

    payDate.value = new Date().toISOString().split('T')[0];

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
        })
        .catch(function() {});
    });

    var toggleRefField = function() {
        refField.style.display = (payMethod.value === 'cash') ? 'none' : 'block';
    };
    window.toggleRefField = toggleRefField;
    toggleRefField();

    var searchStudents = function() {
        var stdId = standardSelect.value;
        if (!stdId) { NexSchool.alert.danger('કૃપા કરીને ધોરણ પસંદ કરો.'); return; }
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> શોધાય છે...';
        fetch('{{ route("fees.collection.students") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academic_year_id: currentYearId,
                standard_id: parseInt(stdId),
                class_id: classSelect.value ? parseInt(classSelect.value) : null,
                semester: semesterSelect.value || null,
                search: searchInput.value || null,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                renderStudents(data.students || []);
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ આવી.');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { searchBtn.disabled = false; searchBtn.innerHTML = '<i class="lni lni-search-1 text-sm"></i> શોધો'; });
    };
    window.searchStudents = searchStudents;

    var renderStudents = function(students) {
        if (students.length === 0) {
            resultsSection.innerHTML = '<div class="text-center py-12 bg-white rounded-xl border border-gray-200"><p class="text-gray-500 font-medium">કોઈ વિદ્યાર્થી મળ્યા નથી</p></div>';
            resultsSection.classList.remove('hidden');
            emptySection.classList.add('hidden');
            return;
        }
        var html = '';
        for (var i = 0; i < students.length; i++) {
            var s = students[i];
            var fees = s.fees || [];
            var stdClass = (s.standard || '') + ' - ' + (s.class || '');
            var hasDue = false;
            for (var j = 0; j < fees.length; j++) {
                var due = parseFloat(fees[j].due_amount) || 0;
                if (due > 0 && !fees[j].is_waived) { hasDue = true; break; }
            }

            html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">';
            html += '<div class="p-4 sm:p-5">';
            html += '<div class="flex items-center justify-between mb-3">';
            html += '<div class="flex items-center gap-3">';
            html += '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center flex-shrink-0"><span class="font-bold text-emerald-700 text-sm">' + (s.full_name_gu ? s.full_name_gu.charAt(0) : '?') + '</span></div>';
            html += '<div><h4 class="font-semibold text-gray-900 text-sm">' + (s.full_name_gu || s.full_name_en || '') + '</h4><p class="text-xs text-gray-500">GR: ' + s.gr_number + ' | ' + stdClass + '</p></div>';
            html += '</div>';
            if (hasDue) {
                html += '<button onclick="openPaymentModal(' + s.id + ')" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition flex items-center gap-1.5 flex-shrink-0"><i class="lni lni-wallet-1 text-sm"></i> ફી ભરો</button>';
            }
            html += '</div>';

            if (fees.length === 0) {
                html += '<span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">ફી સોંપાઈ નથી</span>';
            } else {
                for (var j = 0; j < fees.length; j++) {
                    var fee = fees[j];
                    var feeType = getFeeType(fee);
                    var feeTypeLabel = getFeeTypeLabel(fee);
                    var feeTypeColor = getFeeTypeColor(fee);
                    var netAmt = parseFloat(fee.net_amount) || 0;
                    var paidAmt = parseFloat(fee.paid_amount) || 0;
                    var dueAmt = parseFloat(fee.due_amount) || 0;
                    var pct = netAmt > 0 ? Math.round((paidAmt / netAmt) * 100) : 0;
                    var barColor = pct >= 100 ? 'bg-emerald-500' : (pct >= 50 ? 'bg-amber-500' : 'bg-red-500');
                    var isWaived = !!fee.is_waived;

                    html += '<div class="flex items-center justify-between py-2 ' + (j > 0 ? 'border-t border-gray-100' : '') + '">';
                    html += '<div class="flex-1 min-w-0">';
                    html += '<div class="flex items-center gap-2">';
                    var semBadge = getSemBadge(fee);
                    html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + feeTypeColor + '">' + feeTypeLabel + semBadge + '</span>';
                    if (isWaived) html += '<span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">માફ</span>';
                    html += '</div>';
                    html += '<div class="flex items-center gap-3 mt-1 flex-wrap">';
                    html += '<span class="text-sm ' + (dueAmt > 0 ? 'text-red-600 font-semibold' : 'text-emerald-600 font-semibold') + '">બાકી: ₹' + dueAmt.toFixed(2) + '</span>';
                    html += '<div class="flex items-center gap-2 flex-1 min-w-[120px] max-w-[200px]"><div class="flex-1 bg-gray-200 rounded-full h-2"><div class="' + barColor + ' h-2 rounded-full transition-all" style="width:' + pct + '%"></div></div><span class="text-xs text-gray-500 w-8 text-right">' + pct + '%</span></div>';
                    html += '</div></div>';
                    html += '<div class="flex-shrink-0 ml-3">';
                    html += '<button onclick="showHistory(' + s.id + ', ' + fee.id + ')" class="px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition flex items-center gap-1"><i class="lni lni-layout-9 text-xs"></i> ઇતિહાસ</button>';
                    html += '</div></div>';
                }
            }
            html += '</div></div>';
        }
        resultsSection.innerHTML = html;
        resultsSection.classList.remove('hidden');
        emptySection.classList.add('hidden');
    };

    var openPaymentModal = function(studentId) {
        fetch('{{ route("fees.collection.history") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId, academic_year_id: currentYearId, semester: semesterSelect.value || null }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) { NexSchool.alert.danger(data.message || 'ભૂલ'); return; }
            var student = data.student || {};
            var fees = data.fees || [];
            payStudentId.value = studentId;
            payStudentName.textContent = student.full_name_gu || student.full_name_en || 'વિદ્યાર્થી';
            payStudentGr.textContent = 'GR: ' + (student.gr_number || '');
            var std = (student.current_standard) || {};
            var cls = (student.current_class) || {};
            payStudentClass.textContent = (std.name || '') + ' - ' + (cls.name || '');

            var feeHtml = '';
            for (var i = 0; i < fees.length; i++) {
                var fee = fees[i];
                var feeType = getFeeType(fee);
                var label = getFeeTypeLabel(fee);
                var feeTypeColor = getFeeTypeColor(fee);
                var netAmt = parseFloat(fee.net_amount) || 0;
                var paidAmt = parseFloat(fee.paid_amount) || 0;
                var dueAmt = parseFloat(fee.due_amount) || 0;
                var isWaived = !!fee.is_waived;
                if (isWaived || dueAmt <= 0) continue;

                var semBadge = getSemBadge(fee);
                feeHtml += '<div class="border border-gray-200 rounded-lg p-3">';
                feeHtml += '<div class="flex items-center justify-between mb-2">';
                feeHtml += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + feeTypeColor + '">' + label + semBadge + '</span>';
                feeHtml += '<span class="text-xs text-gray-500">નિર્ધારિત: ₹' + netAmt.toFixed(2) + '</span>';
                feeHtml += '</div>';
                feeHtml += '<div class="flex items-center justify-between text-xs text-gray-500 mb-2">';
                feeHtml += '<span>ચૂકવેલ: ₹' + paidAmt.toFixed(2) + '</span>';
                feeHtml += '<span class="font-semibold text-red-600">બાકી: ₹' + dueAmt.toFixed(2) + '</span>';
                feeHtml += '</div>';
                feeHtml += '<input type="hidden" name="fee_ids[]" value="' + fee.id + '" class="pay-fee-id">';
                feeHtml += '<div>';
                feeHtml += '<label class="block text-xs font-medium text-gray-600 mb-1">આજે ચૂકવવાની રકમ</label>';
                feeHtml += '<input type="number" step="0.01" min="0" max="' + dueAmt.toFixed(2) + '" placeholder="રકમ દાખલ કરો" class="pay-amount w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition" data-max="' + dueAmt.toFixed(2) + '">';
                feeHtml += '</div></div>';
            }

            if (!feeHtml) {
                feeHtml = '<div class="text-center py-4 text-gray-500 text-sm">બાકી કોઈ ફી નથી</div>';
            }

            payFeesContainer.innerHTML = feeHtml;
            payMethod.value = 'cash';
            payReference.value = '';
            payNotes.value = '';
            toggleRefField();
            paymentModal.classList.remove('hidden');
            requestAnimationFrame(function() { paymentModal.style.opacity = '1'; });
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'ડેટા મેળવવામાં ભૂલ.'); });
    };
    window.openPaymentModal = openPaymentModal;

    var closePaymentModal = function() {
        paymentModal.style.opacity = '0';
        setTimeout(function() { paymentModal.classList.add('hidden'); }, 200);
    };
    window.closePaymentModal = closePaymentModal;

    paymentModal.addEventListener('click', function(e) { if (e.target === paymentModal) closePaymentModal(); });

    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var feeInputs = document.querySelectorAll('.pay-fee-id');
        var amountInputs = document.querySelectorAll('.pay-amount');
        var payments = [];
        var hasAny = false;

        for (var i = 0; i < feeInputs.length; i++) {
            var amt = parseFloat(amountInputs[i].value) || 0;
            if (amt <= 0) continue;
            payments.push({
                student_fee_id: parseInt(feeInputs[i].value),
                amount_paid: amt,
            });
            hasAny = true;
        }

        if (!hasAny) { NexSchool.alert.danger('કૃપા કરીને ઓછામાં ઓછી એક ફી માટે રકમ દાખલ કરો.'); return; }

        paySubmitBtn.disabled = true;
        paySubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> પ્રક્રિયા થાય છે...';

        fetch('{{ route("fees.collection.pay-multi") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                student_id: parseInt(payStudentId.value),
                academic_year_id: currentYearId,
                payment_date: payDate.value,
                payment_method: payMethod.value,
                reference_number: payReference.value || null,
                notes: payNotes.value || null,
                payments: payments,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) {
                NexSchool.alert.success('ચુકવણી સફળ!');
                closePaymentModal();
                searchStudents();
                var ids = (data.payment_ids || []).join(',');
                window.open('{{ url("fees/collection/receipt") }}/' + parseInt(payStudentId.value) + '/' + currentYearId + '?payment_ids=' + ids, '_blank');
            } else {
                NexSchool.alert.danger(data.message || 'ચુકવણી નિષ્ફળ.');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { paySubmitBtn.disabled = false; paySubmitBtn.innerHTML = '<i class="lni lni-check-circle-1 text-sm"></i> ચુકવણી કરો'; });
    });

    var showHistory = function(studentId, studentFeeId) {
        fetch('{{ route("fees.collection.history") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId, student_fee_id: studentFeeId, academic_year_id: currentYearId, semester: semesterSelect.value || null }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) { NexSchool.alert.danger(data.message || 'ભૂલ'); return; }
            var payments = data.payments || [];
            var fee = data.fee || {};
            var feeType = !fee.fee_structure ? 'carry_forward' : (fee.fee_structure?.type || '');
            var feeLabel = feeType === 'carry_forward' ? 'કેરી ફોરવર્ડ' : (feeType ? feeTypeLabels[feeType] || 'અન્ય' : '');
            var html = '';
            if (payments.length === 0) {
                html = '<div class="text-center py-8"><p class="text-gray-500">હજી સુધી કોઈ ચુકવણી નથી</p></div>';
            } else {
                html = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">તારીખ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">રસીદ નંબર</th><th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wider">રકમ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">પદ્ધતિ</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">સંદર્ભ નંબર</th><th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">નોંધ</th><th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">પ્રિન્ટ</th></tr></thead><tbody class="divide-y divide-gray-100">';
                for (var i = 0; i < payments.length; i++) {
                    var p = payments[i];
                    var payDateStr = p.payment_date ? p.payment_date.substring(0, 10) : '';
                    html += '<tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-900">' + payDateStr + '</td><td class="px-4 py-3 font-mono text-xs text-gray-700">' + (p.receipt_number || '—') + '</td><td class="px-4 py-3 text-right font-semibold text-emerald-700">₹' + (parseFloat(p.amount_paid) || 0).toFixed(2) + '</td><td class="px-4 py-3">' + (methodLabels[p.payment_method] || p.payment_method) + '</td><td class="px-4 py-3 text-gray-500">' + (p.reference_number || '—') + '</td><td class="px-4 py-3 text-gray-500 max-w-xs truncate">' + (p.notes || '—') + '</td><td class="px-4 py-3 text-center"><a href="{{ url("fees/collection/receipt") }}/' + studentId + '/' + currentYearId + '?payment_id=' + p.id + '" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 px-2 py-1 rounded-lg transition"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ</a></td></tr>';
                }
                html += '</tbody></table></div>';
            }
            historyContent.innerHTML = html;
            historyModal.classList.remove('hidden');
            requestAnimationFrame(function() { historyModal.style.opacity = '1'; });
        })
        .catch(function(err) { NexSchool.alert.danger(err.message); });
    };
    window.showHistory = showHistory;

    var closeHistoryModal = function() {
        historyModal.style.opacity = '0';
        setTimeout(function() { historyModal.classList.add('hidden'); }, 200);
    };
    window.closeHistoryModal = closeHistoryModal;
    historyModal.addEventListener('click', function(e) { if (e.target === historyModal) closeHistoryModal(); });
})();
</script>
@endpush

