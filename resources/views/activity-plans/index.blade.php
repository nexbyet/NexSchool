@extends('layouts.app')
@section('title', 'શૈક્ષણિક પ્રવૃત્તિઓનું આયોજન')
@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">પ્રવૃત્તિઓનું આયોજન</h1>
            <p class="text-gray-500 mt-1 text-sm">શૈક્ષણિક વર્ષ મુજબ પ્રવૃત્તિઓનું આયોજન કરો</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('activity-plans.print') }}" target="_blank" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition flex items-center gap-2">
                <i class="lni lni-printer text-base"></i> પ્રિન્ટ
            </a>
            <button onclick="openModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 transition flex items-center gap-2">
                <i class="lni lni-plus text-base"></i> નવી પ્રવૃત્તિ
            </button>
        </div>
    </div>

    <div class="flex items-center gap-3 mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <i class="lni lni-calendar-days text-emerald-500"></i>
        <span class="text-sm text-gray-600">શૈક્ષણિક વર્ષ:</span>
        <select id="year-filter" onchange="loadPlans()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            @foreach($years as $y)
                <option value="{{ $y->id }}" {{ $y->id === $activeYear?->id ? 'selected' : '' }}>{{ $y->year }}</option>
            @endforeach
        </select>
        <span id="plan-count" class="ml-auto text-xs text-gray-400 bg-gray-50 px-3 py-1.5 rounded-full font-medium">{{ $plans->count() }} પ્રવૃત્તિઓ</span>
    </div>

    <div id="plans-container" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">ક્રમ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">પ્રવૃત્તિનું નામ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">તારીખ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">વાર</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">રિમાર્ક</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-700">ક્રિયા</th>
                </tr>
            </thead>
            <tbody id="plans-tbody">
                @forelse($plans as $p)
                <tr id="plan-{{ $p->id }}" class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 font-bold text-emerald-700 text-xs">{{ $p->sort_order }}</span>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $p->activity_name }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ str_replace('બુધ્વાર', 'બુધવાર', \Carbon\Carbon::parse($p->date)->locale('gu')->dayName) }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-[200px] truncate">{{ $p->remarks ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="editPlan({{ $p->id }})" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો">
                            <i class="lni lni-pencil-1 text-sm"></i>
                        </button>
                        <button onclick="deletePlan({{ $p->id }}, '{{ $p->activity_name }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢો">
                            <i class="lni lni-trash-3 text-sm"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center">
                        <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="lni lni-book-1 text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">હજી સુધી કોઈ પ્રવૃત્તિ આયોજિત નથી</p>
                        <p class="text-gray-400 text-sm mt-1">ઉપર "નવી પ્રવૃત્તિ" બટનથી ઉમેરો</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="plan-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવી પ્રવૃત્તિ</h3>
        <form id="plan-form">
            <input type="hidden" id="plan-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <select id="modal-year" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        @foreach($years as $y)
                            <option value="{{ $y->id }}" {{ $y->id === $activeYear?->id ? 'selected' : '' }}>{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ક્રમ <span class="text-red-500">*</span></label>
                    <input type="number" id="plan-order" min="0" placeholder="દા.ત. 1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">પ્રવૃત્તિનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="plan-name" placeholder="દા.ત. વિજ્ઞાન પ્રદર્શન" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">તારીખ <span class="text-red-500">*</span></label>
                    <input type="date" id="plan-date" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રિમાર્ક</label>
                    <textarea id="plan-remarks" rows="2" placeholder="વૈકલ્પિક નોંધ" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="modal-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg focus:ring-4 focus:ring-emerald-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('plan-modal');
const form = document.getElementById('plan-form');
const modalTitle = document.getElementById('modal-title');
const planId = document.getElementById('plan-id');
const modalYear = document.getElementById('modal-year');
const orderInput = document.getElementById('plan-order');
const nameInput = document.getElementById('plan-name');
const dateInput = document.getElementById('plan-date');
const remarksInput = document.getElementById('plan-remarks');
const submitBtn = document.getElementById('modal-submit-btn');
const yearFilter = document.getElementById('year-filter');
const countEl = document.getElementById('plan-count');

function openModal(data = null) {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => modal.style.opacity = '1');
    if (data) {
        modalTitle.textContent = 'પ્રવૃત્તિ સુધારો';
        planId.value = data.id;
        modalYear.value = data.academic_year_id;
        orderInput.value = data.sort_order;
        nameInput.value = data.activity_name;
        dateInput.value = data.date;
        remarksInput.value = data.remarks || '';
    } else {
        modalTitle.textContent = 'નવી પ્રવૃત્તિ';
        planId.value = '';
        orderInput.value = '';
        nameInput.value = '';
        dateInput.value = '';
        remarksInput.value = '';
    }
}

function closeModal() {
    modal.style.opacity = '0';
    setTimeout(() => modal.classList.add('hidden'), 200);
}

modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

form.addEventListener('submit', function(e) {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.textContent = 'સાચવાઈ રહ્યું છે...';
    const isEdit = !!planId.value;
    const url = isEdit ? '{{ url('activity-plans') }}/' + planId.value : '{{ route('activity-plans.store') }}';
    const method = isEdit ? 'PUT' : 'POST';
    fetch(url, {
        method: 'POST',
        body: new URLSearchParams({
            _token: '{{ csrf_token() }}', _method: method,
            academic_year_id: modalYear.value,
            sort_order: orderInput.value,
            activity_name: nameInput.value,
            date: dateInput.value,
            remarks: remarksInput.value,
        }),
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { NexSchool.alert.success(data.message); closeModal(); loadPlans(); }
        else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
    })
    .catch(() => NexSchool.alert.danger('સર્વર ભૂલ — ફરી પ્રયાસ કરો'))
    .finally(() => { submitBtn.disabled = false; submitBtn.textContent = 'સાચવો'; });
});

function loadPlans() {
    const yearId = yearFilter.value;
    fetch('{{ url('activity-plans/by-year') }}?academic_year_id=' + yearId, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('plans-tbody');
        tbody.innerHTML = '';
        countEl.textContent = data.plans.length + ' પ્રવૃત્તિઓ';
        if (data.plans.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-16 text-center"><div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center"><i class="lni lni-book-1 text-2xl text-gray-400"></i></div><p class="text-gray-500 font-medium">આ વર્ષમાં કોઈ પ્રવૃત્તિ નથી</p></td></tr>';
            return;
        }
        data.plans.forEach(p => {
            const tr = document.createElement('tr');
            tr.id = 'plan-' + p.id;
            tr.className = 'border-b border-gray-100 hover:bg-gray-50 transition';
            let html = '<td class="px-4 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 font-bold text-emerald-700 text-xs">' + p.sort_order + '</span></td>' +
                '<td class="px-4 py-3 font-medium text-gray-900">' + p.activity_name + '</td>' +
                '<td class="px-4 py-3 text-gray-700">' + formatDate(p.date) + '</td>' +
                '<td class="px-4 py-3 text-gray-500">' + getDayName(p.date) + '</td>' +
                '<td class="px-4 py-3 text-gray-500 max-w-[200px] truncate">' + (p.remarks || '—') + '</td>' +
                '<td class="px-4 py-3 text-right"><button onclick="editPlan(' + p.id + ')" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1"></i></button><button onclick="deletePlan(' + p.id + ', \'' + p.activity_name.replace(/'/g, "\\'") + '\')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3"></i></button></td>';
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
    })
    .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
}

function getDayName(dateStr) {
    if (!dateStr) return '';
    const days = ['રવિવાર', 'સોમવાર', 'મંગળવાર', 'બુધવાર', 'ગુરુવાર', 'શુક્રવાર', 'શનિવાર'];
    return days[new Date(dateStr + 'T00:00:00').getDay()];
}

function formatDate(d) {
    if (!d) return '';
    const parts = d.split('-');
    return parts[2] + '/' + parts[1] + '/' + parts[0];
}

function editPlan(id) {
    fetch('{{ url('activity-plans') }}/' + id, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json()).then(data => openModal(data))
    .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
}

function deletePlan(id, name) {
    NexSchool.confirm.show('પ્રવૃત્તિ કાઢી નાખો', 'શું તમે "' + name + '" કાઢી નાખવા માંગો છો?', 'danger')
    .then(() => {
        fetch('{{ url('activity-plans') }}/' + id, {
            method: 'POST',
            body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
            headers: { 'Accept': 'application/json' },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); loadPlans(); }
            else { NexSchool.alert.danger(data.message); }
        })
        .catch(() => NexSchool.alert.danger('કાઢવામાં ભૂલ.'));
    }).catch(() => {});
}

document.getElementById('modal-year').value = '{{ $activeYear?->id }}';
</script>
@endpush
