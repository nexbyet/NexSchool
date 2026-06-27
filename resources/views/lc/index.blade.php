@extends('layouts.app')
@section('title', 'LC — શાળા છોડવાનું પ્રમાણપત્ર')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-600 to-slate-800 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">LC — શાળા છોડવાનું પ્રમાણપત્ર</h1>
                <p class="text-slate-300 mt-1 text-sm">વિદ્યાર્થીઓને શાળા છોડવાનું પ્રમાણપત્ર આપો</p>
            </div>
            <a href="{{ route('lc.register') }}" target="_blank" class="px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition flex items-center gap-2">
                <i class="lni lni-printer-1 text-base"></i> LC રજીસ્ટર
            </a>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Search Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">GR નંબર</label>
                <input type="text" id="search-gr" placeholder="GR નંબર સીધો સર્ચ કરો..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm w-48 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
            </div>
            <div class="w-px h-8 bg-gray-200 self-center"></div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">ધોરણ</label>
                <select id="search-standard" onchange="loadClasses(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                    <option value="">બધા ધોરણ</option>
                    @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">વર્ગ</label>
                <select id="search-class" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                    <option value="">બધા વર્ગ</option>
                </select>
            </div>
            <button onclick="searchStudents()" class="px-5 py-2 bg-slate-700 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition flex items-center gap-2 shadow-sm">
                <i class="lni lni-search-1 text-sm"></i> શોધો
            </button>
        </div>
    </div>

    {{-- Results --}}
    <div id="results-container" class="space-y-3">
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="lni lni-search-1 text-3xl text-slate-300"></i>
            </div>
            <p class="text-gray-500 font-medium">વિદ્યાર્થી શોધવા ઉપરના ફિલ્ટરનો ઉપયોગ કરો</p>
            <p class="text-gray-400 text-sm mt-1">GR નંબર અથવા ધોરણ-વર્ગ પસંદ કરીને શોધો</p>
        </div>
    </div>
</div>

{{-- LC Modal --}}
<div id="lc-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">LC જારી કરો</h3>
        <form id="lc-form">
            <input type="hidden" id="lc-student-id">
            <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <div class="flex items-center gap-3">
                    <div id="lc-student-avatar" class="w-12 h-12 rounded-xl bg-slate-200 flex items-center justify-center">
                        <i class="lni lni-user-4 text-slate-500 text-xl"></i>
                    </div>
                    <div>
                        <p id="lc-student-name" class="font-semibold text-gray-900"></p>
                        <p id="lc-student-info" class="text-sm text-gray-500"></p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LC નંબર <span class="text-red-500">*</span></label>
                    <input type="text" id="lc_number" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શાળા છોડવાની તારીખ <span class="text-red-500">*</span></label>
                    <input type="text" id="leaving_date" placeholder="dd/mm/yyyy" required class="date-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LC જારી કરવાની તારીખ</label>
                    <input type="text" id="lc_issue_date" placeholder="dd/mm/yyyy" class="date-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શાળા છોડતી વખતનું ધોરણ</label>
                    <select id="leaving_standard_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                        <option value="">— પસંદ કરો —</option>
                        @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">કુલ હાજરી દિવસો</label>
                    <input type="number" id="attendance_days" min="0" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શાળા છોડવાનું કારણ (ગુજરાતી)</label>
                    <input type="text" id="leaving_reason_gu" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શાળા છોડવાનું કારણ (English)</label>
                    <input type="text" id="leaving_reason_en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">નોંધ</label>
                <textarea id="leaving_remarks" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="lc-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 rounded-lg focus:ring-4 focus:ring-slate-200 transition flex items-center gap-2">
                    <i class="lni lni-checkmark-circle-1 text-sm"></i> LC જારી કરો
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
    var resultsContainer = document.getElementById('results-container');
    var lcModal = document.getElementById('lc-modal');
    var lcForm = document.getElementById('lc-form');
    var lcSubmitBtn = document.getElementById('lc-submit-btn');

    function loadClasses(standardId) {
        var classSelect = document.getElementById('search-class');
        classSelect.innerHTML = '<option value="">બધા વર્ગ</option>';
        if (!standardId) return;
        fetch('{{ url("lc/classes") }}/' + standardId, { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].id;
                opt.textContent = data[i].name;
                classSelect.appendChild(opt);
            }
        });
    }

    window.loadClasses = loadClasses;

    function searchStudents() {
        var gr = document.getElementById('search-gr').value.trim();
        var standardId = document.getElementById('search-standard').value;
        var classId = document.getElementById('search-class').value;
        var params = new URLSearchParams();
        if (gr) params.set('gr_number', gr);
        if (standardId) params.set('standard_id', standardId);
        if (classId) params.set('class_id', classId);
        resultsContainer.innerHTML = '<div class="text-center py-10"><i class="lni lni-spinner-3 text-2xl text-slate-400 animate-spin inline-block"></i><p class="text-gray-500 mt-2">શોધી રહ્યા છે...</p></div>';
        fetch('{{ route("lc.search") }}?' + params.toString(), { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.success || !res.students.length) {
                resultsContainer.innerHTML = '<div class="text-center py-16 bg-white rounded-xl border border-gray-200"><div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-50 to-slate-100 rounded-2xl flex items-center justify-center shadow-sm"><i class="lni lni-user-block-1 text-3xl text-gray-300"></i></div><p class="text-gray-500 font-medium">કોઈ વિદ્યાર્થી મળ્યો નથી</p><p class="text-gray-400 text-sm mt-1">ફિલ્ટર બદલીને ફરી પ્રયાસ કરો</p></div>';
                return;
            }
            var html = '';
            for (var i = 0; i < res.students.length; i++) {
                var s = res.students[i];
                var statusBadge = s.status === 'alumni'
                    ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600"><i class="lni lni-exit text-xs"></i> Alumni</span>'
                    : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i class="lni lni-check-circle-1 text-xs"></i> સક્રિય</span>';
                var lcBtn = s.status === 'alumni'
                    ? '<span class="text-xs text-slate-400">LC જારી — ' + (s.l_c_number || '—') + '</span>'
                    : '<button onclick="openLcModal(' + s.id + ')" class="px-3 py-1.5 text-xs font-medium text-white bg-slate-700 hover:bg-slate-800 rounded-lg transition"><i class="lni lni-checkmark-circle-1 text-xs"></i> LC આપો</button>';
                html += '<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4 hover:shadow-md transition">';
                html += '<div class="w-11 h-11 rounded-xl bg-gradient-to-br from-slate-100 to-gray-200 flex items-center justify-center flex-shrink-0 overflow-hidden">';
                if (s.photo) html += '<img src="{{ asset("storage") }}/' + s.photo + '" class="w-full h-full object-cover">';
                else html += '<span class="text-sm font-bold text-slate-500">' + (s.full_name_gu ? s.full_name_gu.charAt(0) : '?') + '</span>';
                html += '</div>';
                html += '<div class="flex-1 min-w-0"><p class="font-semibold text-gray-900 text-sm">' + (s.full_name_gu || s.full_name_en || '') + '</p>';
                html += '<p class="text-xs text-gray-500">GR: ' + s.gr_number + ' | ' + s.standard + ' - ' + s.class + ' | પ્રવેશ: ' + s.date_of_admission;
                if (s.father_name_gu) html += ' | પિતા: ' + s.father_name_gu;
                html += '</p></div>';
                html += '<div class="flex items-center gap-2 shrink-0">' + statusBadge + ' ' + lcBtn + '</div>';
                html += '</div>';
            }
            resultsContainer.innerHTML = html;
        })
        .catch(function(err) { resultsContainer.innerHTML = '<div class="text-center py-10 text-red-500">ભૂલ: ' + err.message + '</div>'; });
    }

    window.searchStudents = searchStudents;

    function openLcModal(studentId) {
        fetch('{{ url("students") }}/' + studentId, { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(s) {
            document.getElementById('lc-student-id').value = s.id;
            document.getElementById('lc-student-name').textContent = s.full_name_gu || s.full_name_en || '';
            document.getElementById('lc-student-info').textContent = 'GR: ' + s.gr_number + ' | ' + (s.current_standard?.name || '') + ' - ' + (s.current_class?.name || '');
            document.getElementById('lc_number').value = '';
            document.getElementById('leaving_date').value = '';
            document.getElementById('lc_issue_date').value = '';
            document.getElementById('leaving_standard_id').value = s.current_standard_id || '';
            document.getElementById('attendance_days').value = '';
            document.getElementById('leaving_reason_gu').value = '';
            document.getElementById('leaving_reason_en').value = '';
            document.getElementById('leaving_remarks').value = '';
            lcModal.classList.remove('hidden');
            requestAnimationFrame(function() { lcModal.style.opacity = '1'; });
        });
    }

    window.openLcModal = openLcModal;

    function closeModal() {
        lcModal.style.opacity = '0';
        setTimeout(function() { lcModal.classList.add('hidden'); }, 200);
    }

    window.closeModal = closeModal;

    lcModal.addEventListener('click', function(e) { if (e.target === lcModal) closeModal(); });

    lcForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('lc-student-id').value;
        if (!id) return;
        lcSubmitBtn.disabled = true;
        lcSubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાઈ રહ્યું છે...';
        fetch('{{ route("lc.store") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                student_id: parseInt(id),
                lc_number: document.getElementById('lc_number').value.trim(),
                leaving_date: document.getElementById('leaving_date').value.trim(),
                lc_issue_date: document.getElementById('lc_issue_date').value.trim() || null,
                leaving_standard_id: document.getElementById('leaving_standard_id').value || null,
                attendance_days: document.getElementById('attendance_days').value ? parseInt(document.getElementById('attendance_days').value) : null,
                leaving_reason_gu: document.getElementById('leaving_reason_gu').value.trim() || null,
                leaving_reason_en: document.getElementById('leaving_reason_en').value.trim() || null,
                leaving_remarks: document.getElementById('leaving_remarks').value.trim() || null,
            }),
        })
        .then(function(r) { if (!r.ok) return r.json().then(function(e) { throw e; }); return r.json(); })
        .then(function(res) {
            if (res.success) {
                NexSchool.alert.success(res.message);
                closeModal();
                searchStudents();
            } else {
                NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
            }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { lcSubmitBtn.disabled = false; lcSubmitBtn.innerHTML = '<i class="lni lni-checkmark-circle-1 text-sm"></i> LC જારી કરો'; });
    });

    document.getElementById('search-gr').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') searchStudents();
    });
})();
</script>
@endpush
