@extends('layouts.app')
@section('title', 'જાહેર રજાઓ')
@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">જાહેર રજાઓ</h1>
            <p class="text-gray-500 mt-1 text-sm">શૈક્ષણિક વર્ષ મુજબ રજાઓનું સંચાલન કરો</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('public-holidays.print') }}" target="_blank" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition flex items-center gap-2">
                <i class="lni lni-printer text-base"></i> પ્રિન્ટ
            </a>
            <button onclick="openModal()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition flex items-center gap-2">
                <i class="lni lni-plus text-base"></i> નવી રજા
            </button>
        </div>
    </div>

    <div class="flex items-center gap-3 mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <i class="lni lni-calendar-days text-indigo-500"></i>
        <span class="text-sm text-gray-600">શૈક્ષણિક વર્ષ:</span>
        <select id="year-filter" onchange="loadHolidays()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            @foreach($years as $y)
                <option value="{{ $y->id }}" {{ $y->id === $activeYear?->id ? 'selected' : '' }}>{{ $y->year }}</option>
            @endforeach
        </select>
        <span id="holiday-count" class="ml-auto text-xs text-gray-400 bg-gray-50 px-3 py-1.5 rounded-full font-medium">{{ $holidays->count() }} રજાઓ</span>
    </div>

    <div id="holidays-container" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">ક્રમ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">રજાનું નામ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">પ્રકાર</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">તારીખ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-700">વાર</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-700">ક્રિયા</th>
                </tr>
            </thead>
            <tbody id="holidays-tbody">
                @forelse($holidays as $i => $h)
                <tr id="holiday-{{ $h->id }}" class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $h->name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $h->type === 'jaher' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $h->type === 'jaher' ? 'જાહેર રજા' : 'સ્થાનિક રજા' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($h->date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ str_replace('બુધ્વાર', 'બુધવાર', \Carbon\Carbon::parse($h->date)->locale('gu')->dayName) }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="editHoliday({{ $h->id }})" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો">
                            <i class="lni lni-pencil-1 text-sm"></i>
                        </button>
                        <button onclick="deleteHoliday({{ $h->id }}, '{{ $h->name }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢો">
                            <i class="lni lni-trash-3 text-sm"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center">
                        <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="lni lni-calendar-days text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">હજી સુધી કોઈ રજા ઉમેરાઈ નથી</p>
                        <p class="text-gray-400 text-sm mt-1">ઉપર "નવી રજા" બટનથી ઉમેરો</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="holiday-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવી રજા</h3>
        <form id="holiday-form">
            <input type="hidden" id="holiday-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <select id="modal-year" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        @foreach($years as $y)
                            <option value="{{ $y->id }}" {{ $y->id === $activeYear?->id ? 'selected' : '' }}>{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રજાનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="holiday-name" placeholder="દા.ત. સ્વતંત્રતા દિવસ" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રજાનો પ્રકાર <span class="text-red-500">*</span></label>
                    <select id="holiday-type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="jaher">જાહેર રજા</option>
                        <option value="sthanik">સ્થાનિક રજા</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">તારીખ <span class="text-red-500">*</span></label>
                    <input type="date" id="holiday-date" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="modal-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('holiday-modal');
const form = document.getElementById('holiday-form');
const modalTitle = document.getElementById('modal-title');
const holidayId = document.getElementById('holiday-id');
const modalYear = document.getElementById('modal-year');
const nameInput = document.getElementById('holiday-name');
const typeInput = document.getElementById('holiday-type');
const dateInput = document.getElementById('holiday-date');
const submitBtn = document.getElementById('modal-submit-btn');
const yearFilter = document.getElementById('year-filter');
const countEl = document.getElementById('holiday-count');

function openModal(data = null) {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => modal.style.opacity = '1');
    if (data) {
        modalTitle.textContent = 'રજા સુધારો';
        holidayId.value = data.id;
        modalYear.value = data.academic_year_id;
        nameInput.value = data.name;
        typeInput.value = data.type;
        dateInput.value = data.date;
    } else {
        modalTitle.textContent = 'નવી રજા';
        holidayId.value = '';
        nameInput.value = '';
        typeInput.value = 'jaher';
        dateInput.value = '';
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
    const isEdit = !!holidayId.value;
    const url = isEdit ? '{{ url('public-holidays') }}/' + holidayId.value : '{{ route('public-holidays.store') }}';
    const method = isEdit ? 'PUT' : 'POST';
    fetch(url, {
        method: 'POST',
        body: new URLSearchParams({
            _token: '{{ csrf_token() }}', _method: method,
            academic_year_id: modalYear.value,
            name: nameInput.value,
            type: typeInput.value,
            date: dateInput.value,
        }),
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { NexSchool.alert.success(data.message); closeModal(); loadHolidays(); }
        else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
    })
    .catch(() => NexSchool.alert.danger('સર્વર ભૂલ — ફરી પ્રયાસ કરો'))
    .finally(() => { submitBtn.disabled = false; submitBtn.textContent = 'સાચવો'; });
});

function loadHolidays() {
    const yearId = yearFilter.value;
    fetch('{{ url('public-holidays/by-year') }}?academic_year_id=' + yearId, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('holidays-tbody');
        tbody.innerHTML = '';
        countEl.textContent = data.holidays.length + ' રજાઓ';
        if (data.holidays.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-16 text-center"><div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center"><i class="lni lni-calendar-days text-2xl text-gray-400"></i></div><p class="text-gray-500 font-medium">આ વર્ષમાં કોઈ રજા નથી</p></td></tr>';
            return;
        }
        data.holidays.forEach((h, i) => {
            const tr = document.createElement('tr');
            tr.id = 'holiday-' + h.id;
            tr.className = 'border-b border-gray-100 hover:bg-gray-50 transition';
            tr.innerHTML = '<td class="px-4 py-3 text-gray-500">' + (i + 1) + '</td>' +
                '<td class="px-4 py-3 font-medium text-gray-900">' + h.name + '</td>' +
                '<td class="px-4 py-3"><span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium ' + (h.type === 'jaher' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') + '">' + (h.type === 'jaher' ? 'જાહેર રજા' : 'સ્થાનિક રજા') + '</span></td>' +
                '<td class="px-4 py-3 text-gray-700">' + formatDate(h.date) + '</td>' +
                '<td class="px-4 py-3 text-gray-500">' + getDayName(h.date) + '</td>' +
                '<td class="px-4 py-3 text-right"><button onclick="editHoliday(' + h.id + ')" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></button><button onclick="deleteHoliday(' + h.id + ', \'' + h.name.replace(/'/g, "\\'") + '\')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button></td>';
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

function editHoliday(id) {
    fetch('{{ url('public-holidays') }}/' + id, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json()).then(data => openModal(data))
    .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
}

function deleteHoliday(id, name) {
    NexSchool.confirm.show('રજા કાઢી નાખો', 'શું તમે "' + name + '" કાઢી નાખવા માંગો છો?', 'danger')
    .then(() => {
        fetch('{{ url('public-holidays') }}/' + id, {
            method: 'POST',
            body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
            headers: { 'Accept': 'application/json' },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); loadHolidays(); }
            else { NexSchool.alert.danger(data.message); }
        })
        .catch(() => NexSchool.alert.danger('કાઢવામાં ભૂલ.'));
    }).catch(() => {});
}

document.getElementById('modal-year').value = '{{ $activeYear?->id }}';
</script>
@endpush
