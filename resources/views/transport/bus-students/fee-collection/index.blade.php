@extends('layouts.app')
@section('title', 'બસ ફી વસૂલાત')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">બસ ફી વસૂલાત</h1>
                <p class="text-emerald-200 mt-1 text-sm">બીજી શાળાના બસ વિદ્યાર્થીઓ પાસેથી ફી વસૂલ કરો</p>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">રૂટ</label>
                <select id="route-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">બધા રૂટ</option>
                    @foreach($routes as $r)
                    <option value="{{ $r->id }}">{{ $r->route_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadStudents()" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <i class="lni lni-search-1 text-sm"></i> વિદ્યાર્થીઓ શોધો
                </button>
            </div>
        </div>
    </div>

    <div id="results-section" class="space-y-4"></div>

    <div id="empty-section" class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="lni lni-wallet-1 text-3xl text-emerald-400"></i>
        </div>
        <p class="text-gray-500 font-medium">વિદ્યાર્થીઓ શોધવા માટે રૂટ પસંદ કરો</p>
        <p class="text-gray-400 text-sm mt-1">રૂટ પસંદ કરીને શોધો બટન દબાવો</p>
    </div>
</div>

{{-- Payment Modal --}}
<div id="payment-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900" id="pay-student-name"></h3>
                <p class="text-xs text-gray-500" id="pay-student-info"></p>
            </div>
            <button type="button" onclick="closePayModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"><i class="lni lni-xmark text-xl"></i></button>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-4 p-3 bg-gray-50 rounded-xl text-center text-xs">
            <div><span class="block text-gray-500">કુલ ફી</span><span class="block font-bold text-gray-800 text-sm" id="pay-total">₹0</span></div>
            <div><span class="block text-gray-500">ચૂકવેલ</span><span class="block font-bold text-emerald-700 text-sm" id="pay-paid">₹0</span></div>
            <div><span class="block text-gray-500">બાકી</span><span class="block font-bold text-red-700 text-sm" id="pay-due">₹0</span></div>
        </div>
        <form id="payment-form">
            <input type="hidden" id="pay-student-id" name="bus_only_student_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર <span class="text-red-500">*</span></label>
                    <select id="pay-semester" name="semester" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રકમ (₹) <span class="text-red-500">*</span></label>
                    <input type="number" id="pay-amount" name="amount" step="0.01" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">તારીખ</label>
                    <input type="text" id="pay-date" name="payment_date" required placeholder="dd/mm/yyyy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ચુકવણી પદ્ધતિ <span class="text-red-500">*</span></label>
                    <select id="pay-method" name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="cash">રોકડા</option>
                        <option value="bank">બેંક ટ્રાન્સફર</option>
                        <option value="cheque">ચેક</option>
                        <option value="online">ઓનલાઇન</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સંદર્ભ નંબર</label>
                    <input type="text" id="pay-ref" name="reference_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">નોંધ</label>
                    <textarea id="pay-notes" name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closePayModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="pay-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition flex items-center gap-1"><i class="lni lni-wallet-1 text-sm"></i> ફી ભરો</button>
            </div>
        </form>
    </div>
</div>

{{-- History Modal --}}
<div id="history-modal" class="fixed inset-0 z-[9998] flex items-start justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s;overflow-y:auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full my-8">
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-t-2xl">
            <h3 class="text-lg font-semibold text-white">ફી હિસ્ટ્રી — <span id="hist-name" class="font-bold"></span></h3>
            <button type="button" onclick="closeHistoryModal()" class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition"><i class="lni lni-xmark text-xl"></i></button>
        </div>
        <div class="p-6">
            <div id="hist-summary" class="grid grid-cols-3 gap-3 mb-4 text-center text-sm"></div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">તારીખ</th>
                            <th class="px-3 py-2 text-left">સત્ર</th>
                            <th class="px-3 py-2 text-right">રકમ</th>
                            <th class="px-3 py-2 text-left">પદ્ધતિ</th>
                            <th class="px-3 py-2 text-left">સંદર્ભ</th>
                            <th class="px-3 py-2 text-left">નોંધ</th>
                        </tr>
                    </thead>
                    <tbody id="hist-tbody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
            <div class="mt-4 text-center">
                <button onclick="closeHistoryModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">બંધ કરો</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function autoFormatDate(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
    if (v.length > 5) v = v.slice(0,5) + '/' + v.slice(5);
    if (v.length > 10) v = v.slice(0,10);
    input.value = v;
}
document.getElementById('pay-date').addEventListener('input', function() { autoFormatDate(this); });

function loadStudents() {
    const routeId = document.getElementById('route-select').value;
    let url = '{{ route("transport.bus-students.fee-collection.data") }}?';
    if (routeId) url += 'route_id=' + routeId;
    fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(res => {
        const section = document.getElementById('results-section');
        const empty = document.getElementById('empty-section');
        if (!res.success || !res.students.length) {
            section.innerHTML = '';
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');
        section.innerHTML = renderStudentCards(res.students);
    });
}

function renderStudentCards(students) {
    return `<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ક્રમ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">નામ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ધોરણ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ગામ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">મોબાઇલ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">કુલ ફી</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ચૂકવેલ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">બાકી</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ક્રિયા</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                ${students.map((s, i) => `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">${i+1}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">${s.name}</td>
                        <td class="px-4 py-3 text-gray-600">${s.standard || '—'}</td>
                        <td class="px-4 py-3 text-gray-600">${s.gaam || '—'}</td>
                        <td class="px-4 py-3 font-mono text-gray-600">${s.mobile || '—'}</td>
                        <td class="px-4 py-3 text-center font-mono text-gray-700 font-semibold">₹${Number(s.total).toLocaleString()}</td>
                        <td class="px-4 py-3 text-center font-mono ${s.paid > 0 ? 'text-emerald-700 font-semibold' : 'text-gray-400'}">₹${Number(s.paid).toLocaleString()}</td>
                        <td class="px-4 py-3 text-center font-mono ${s.due > 0 ? 'text-red-700 font-bold' : 'text-emerald-600 font-semibold'}">${s.due > 0 ? '₹' + Number(s.due).toLocaleString() : '—'}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openPay(${s.id},'${s.name.replace(/'/g,"\\'")}','${s.standard||''}','${s.gaam||''}',${s.total},${s.paid},${s.due})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="ફી ભરો"><i class="lni lni-wallet-1"></i></button>
                                <button onclick="showHistory(${s.id},'${s.name.replace(/'/g,"\\'")}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="હિસ્ટ્રી"><i class="lni lni-history"></i></button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
                </tbody>
            </table>
        </div>
    </div>`;
}

function openPay(id, name, standard, gaam, total, paid, due) {
    document.getElementById('pay-student-id').value = id;
    document.getElementById('pay-student-name').textContent = name;
    document.getElementById('pay-student-info').textContent = (standard ? standard + ' | ' : '') + (gaam || '');
    document.getElementById('pay-total').textContent = '₹' + Number(total).toLocaleString();
    document.getElementById('pay-paid').textContent = '₹' + Number(paid).toLocaleString();
    document.getElementById('pay-due').textContent = '₹' + Number(due).toLocaleString();
    document.getElementById('payment-form').reset();
    document.getElementById('pay-amount').value = '';
    const m = document.getElementById('payment-modal');
    m.classList.remove('hidden');
    requestAnimationFrame(() => m.style.opacity = '1');
}

function closePayModal() {
    const m = document.getElementById('payment-modal');
    m.style.opacity = '0';
    setTimeout(() => m.classList.add('hidden'), 200);
}

document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('pay-submit-btn');
    btn.disabled = true;
    btn.innerHTML = 'સેવ થાય છે...';
    const data = new FormData(this);
    fetch('{{ route("transport.bus-students.fee-collection.pay") }}', {
        method: 'POST', body: data,
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
    .then(res => {
        if (res.success) {
            NexSchool.alert.success(res.message);
            const sid = document.getElementById('pay-student-id').value;
            window.open('{{ route("transport.bus-students.fee-collection.receipt") }}?payment_id=' + res.payment_id + '&student_id=' + sid, '_blank');
            closePayModal();
            setTimeout(() => loadStudents(), 400);
        } else {
            NexSchool.alert.danger(res.message || 'ભૂલ');
        }
    })
    .catch(err => {
        if (err.errors) NexSchool.alert.danger(Object.values(err.errors).flat().join('<br>'));
        else NexSchool.alert.danger(err.message || 'સર્વર ભૂલ');
    })
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="lni lni-wallet-1 text-sm"></i> ફી ભરો'; });
});

function showHistory(id, name) {
    document.getElementById('hist-name').textContent = name;
    document.getElementById('hist-tbody').innerHTML = '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">લોડ થાય છે...</td></tr>';
    const m = document.getElementById('history-modal');
    m.classList.remove('hidden');
    requestAnimationFrame(() => m.style.opacity = '1');
    fetch('{{ route("transport.bus-students.fee-collection.history") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ student_id: id }),
    })
    .then(res => res.json())
    .then(res => {
        if (!res.success) return;
        const s = res.student;
        document.getElementById('hist-summary').innerHTML = `
            <div class="bg-gray-50 rounded-lg p-2"><span class="block text-gray-500 text-xs">કુલ ફી</span><span class="block font-bold text-gray-800">₹${Number(s.total).toLocaleString()}</span></div>
            <div class="bg-emerald-50 rounded-lg p-2"><span class="block text-gray-500 text-xs">ચૂકવેલ</span><span class="block font-bold text-emerald-700">₹${Number(s.paid).toLocaleString()}</span></div>
            <div class="bg-red-50 rounded-lg p-2"><span class="block text-gray-500 text-xs">બાકી</span><span class="block font-bold text-red-700">₹${Number(s.due).toLocaleString()}</span></div>
        `;
        document.getElementById('hist-tbody').innerHTML = res.payments.length
            ? res.payments.map(p => `<tr>
                <td class="px-3 py-2.5 whitespace-nowrap">${p.payment_date}</td>
                <td class="px-3 py-2.5">સત્ર ${p.semester}</td>
                <td class="px-3 py-2.5 text-right font-mono font-semibold text-emerald-700">₹${Number(p.amount).toLocaleString()}</td>
                <td class="px-3 py-2.5">${p.payment_method}</td>
                <td class="px-3 py-2.5 text-gray-500">${p.reference_number || '—'}</td>
                <td class="px-3 py-2.5 text-gray-500 max-w-[120px] truncate">${p.notes || '—'}</td>
            </tr>`).join('')
            : '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">કોઈ ચુકવણી નથી</td></tr>';
    });
}

function closeHistoryModal() {
    const m = document.getElementById('history-modal');
    m.style.opacity = '0';
    setTimeout(() => m.classList.add('hidden'), 200);
}
</script>
@endpush
@endsection