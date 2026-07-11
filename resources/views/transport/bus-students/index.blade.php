@extends('layouts.app')
@section('title', 'બસ વિદ્યાર્થીઓ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">બસ વિદ્યાર્થીઓ</h1>
                <p class="text-emerald-200 mt-1 text-sm">બીજી શાળાના વિદ્યાર્થીઓ કે જે ફક્ત બસ સેવા વાપરે છે</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2 backdrop-blur-sm">
                <i class="lni lni-plus text-base"></i> નવો વિદ્યાર્થી
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Filter + Print Buttons --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3 flex-wrap">
            <select id="filter-route" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white" onchange="loadStudents()">
                <option value="">બધા રૂટ</option>
                @foreach($routes as $r)
                <option value="{{ $r->id }}">{{ $r->route_name }}</option>
                @endforeach
            </select>
            <input type="text" id="filter-search" placeholder="નામ શોધો..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-48" onkeyup="if(event.key==='Enter')loadStudents()">
            <button onclick="loadStudents()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition"><i class="lni lni-search-1 text-xs"></i> શોધો</button>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transport.bus-students.due-list') }}" class="px-3 py-2 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> બાકી યાદી</a>
            <a href="{{ route('transport.bus-students.print-route-list') }}" target="_blank" class="px-3 py-2 bg-indigo-500 text-white text-xs font-medium rounded-lg hover:bg-indigo-600 transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> રૂટ યાદી</a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ક્રમ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">નામ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ધોરણ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ગામ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">મોબાઇલ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">રૂટ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ફી (Sem1+Sem2)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">સ્થિતિ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ક્રિયા</th>
                    </tr>
                </thead>
                <tbody id="student-tbody" class="divide-y divide-gray-100">
                    @forelse($students as $i => $s)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $s->full_name_gu }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $s->standard_label ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $s->gaam ?? '—' }}</td>
                        <td class="px-4 py-3 font-mono text-gray-600">{{ $s->mobile ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 bg-teal-50 rounded-md text-xs font-medium text-teal-700">{{ $s->route->route_name }}</span></td>
                        <td class="px-4 py-3 text-center font-mono text-gray-700">₹{{ number_format($s->fee_sem1, 0) }} + ₹{{ number_format($s->fee_sem2, 0) }}</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i class="lni lni-check-circle-1 text-xs"></i> સક્રિય</span></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="payFee({{ $s->id }}, '{{ $s->full_name_gu }}')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="ફી ભરો"><i class="lni lni-wallet-1"></i></button>
                                <button onclick="editStudent({{ $s->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1"></i></button>
                                <button onclick="deleteStudent({{ $s->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો"><i class="lni lni-trash-3"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-12 text-center text-gray-400">કોઈ બસ વિદ્યાર્થી નથી</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create/Edit Modal --}}
<div id="student-modal" class="fixed inset-0 z-[9998] flex items-start justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s;overflow-y:auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full my-8 p-6">
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 -mx-6 -mt-6 rounded-t-2xl mb-4">
            <h3 class="text-lg font-semibold text-white" id="modal-title">નવો બસ વિદ્યાર્થી</h3>
            <button type="button" onclick="closeModal()" class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition"><i class="lni lni-xmark text-xl"></i></button>
        </div>
        <form id="student-form">
            <input type="hidden" id="student-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">પૂરું નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name_gu" name="full_name_gu" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ</label>
                        <input type="text" id="standard_label" name="standard_label" placeholder="દા.ત. દશમું ધોરણ" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ગામ</label>
                        <input type="text" id="gaam" name="gaam" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">મોબાઇલ</label>
                    <input type="text" id="mobile" name="mobile" maxlength="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રૂટ <span class="text-red-500">*</span></label>
                    <select id="route_id" name="route_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">— પસંદ કરો —</option>
                        @foreach($routes as $r)
                        <option value="{{ $r->id }}">{{ $r->route_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ફી — સત્ર 1 (₹)</label>
                        <input type="number" id="fee_sem1" name="fee_sem1" step="0.01" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ફી — સત્ર 2 (₹)</label>
                        <input type="number" id="fee_sem2" name="fee_sem2" step="0.01" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition flex items-center gap-1"><i class="lni lni-save text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>

{{-- Fee Payment Modal --}}
<div id="fee-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">ફી ભરો — <span id="fee-student-name" class="text-emerald-600"></span></h3>
            <button type="button" onclick="closeFeeModal()" class="p-1 text-gray-400 hover:text-gray-600"><i class="lni lni-xmark text-xl"></i></button>
        </div>
        <form id="fee-form">
            <input type="hidden" id="fee_student_id" name="bus_only_student_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                    <select id="fee_semester" name="semester" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રકમ (₹) <span class="text-red-500">*</span></label>
                    <input type="number" id="fee_amount" name="amount" step="0.01" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">તારીખ</label>
                    <input type="text" id="fee_date" name="payment_date" required placeholder="dd/mm/yyyy" class="date-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ચુકવણી પદ્ધતિ</label>
                    <select id="fee_method" name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="cash">રોકડા</option>
                        <option value="bank">બેંક ટ્રાન્સફર</option>
                        <option value="cheque">ચેક</option>
                        <option value="online">ઓનલાઇન</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સંદર્ભ નંબર</label>
                    <input type="text" id="fee_ref" name="reference_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">નોંધ</label>
                    <textarea id="fee_notes" name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeFeeModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="fee-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition flex items-center gap-1"><i class="lni lni-wallet-1 text-sm"></i> ફી ભરો</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const routes = @json($routes->map(fn($r) => ['id' => $r->id, 'name' => $r->route_name]));

function closeModal() {
    const m = document.getElementById('student-modal');
    m.style.opacity = '0';
    setTimeout(() => m.classList.add('hidden'), 200);
}

function closeFeeModal() {
    const m = document.getElementById('fee-modal');
    m.style.opacity = '0';
    setTimeout(() => m.classList.add('hidden'), 200);
}

function openModal(data = null) {
    const m = document.getElementById('student-modal');
    m.classList.remove('hidden');
    requestAnimationFrame(() => m.style.opacity = '1');
    document.getElementById('modal-title').textContent = data ? 'બસ વિદ્યાર્થી સુધારો' : 'નવો બસ વિદ્યાર્થી';
    document.getElementById('submit-btn').textContent = data ? 'સુધારો' : 'સાચવો';
    if (!data) {
        document.getElementById('student-form').reset();
        document.getElementById('student-id').value = '';
    }
}

// Auto-format date
function autoFormatDate(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
    if (v.length > 5) v = v.slice(0,5) + '/' + v.slice(5);
    if (v.length > 10) v = v.slice(0,10);
    input.value = v;
}
document.querySelectorAll('.date-input').forEach(el => {
    el.addEventListener('input', function() { autoFormatDate(this); });
});

// Digit-only for mobile
document.getElementById('mobile').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'').slice(0,10);
});

// CRUD
document.getElementById('student-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'સાચવાઈ રહ્યું છે...';
    const id = document.getElementById('student-id').value;
    const url = id ? '/transport/bus-students/' + id : '/transport/bus-students';
    const method = id ? 'PUT' : 'POST';
    const data = new FormData(this);
    if (id) data.append('_method', 'PUT');
    fetch(url, {
        method: 'POST', body: data,
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
    .then(res => {
        if (res.success) { NexSchool.alert.success(res.message); closeModal(); setTimeout(() => loadStudents(), 400); }
        else { NexSchool.alert.danger(res.message || 'ભૂલ'); }
    })
    .catch(err => {
        if (err.errors) NexSchool.alert.danger(Object.values(err.errors).flat().join('<br>'));
        else NexSchool.alert.danger(err.message || 'સર્વર ભૂલ');
    })
    .finally(() => { btn.disabled = false; btn.textContent = id ? 'સુધારો' : 'સાચવો'; });
});

function editStudent(id) {
    fetch('/transport/bus-students/' + id, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(data => {
        openModal(data);
        document.getElementById('student-id').value = data.id;
        document.getElementById('full_name_gu').value = data.full_name_gu || '';
        document.getElementById('standard_label').value = data.standard_label || '';
        document.getElementById('gaam').value = data.gaam || '';
        document.getElementById('mobile').value = data.mobile || '';
        document.getElementById('route_id').value = data.route_id || '';
        document.getElementById('fee_sem1').value = data.fee_sem1 || 0;
        document.getElementById('fee_sem2').value = data.fee_sem2 || 0;
    });
}

function deleteStudent(id) {
    NexSchool.confirm.show('ખાતરી કરો', 'શું તમે આ બસ વિદ્યાર્થીને કાઢી નાખવા માંગો છો?', 'danger')
    .then(() => {
        fetch('/transport/bus-students/' + id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        })
        .then(res => res.json())
        .then(res => { if (res.success) { NexSchool.alert.success(res.message); setTimeout(() => loadStudents(), 400); } });
    }).catch(() => {});
}

function payFee(id, name) {
    document.getElementById('fee_student_id').value = id;
    document.getElementById('fee-student-name').textContent = name;
    document.getElementById('fee-form').reset();
    document.getElementById('fee_amount').value = '';
    const m = document.getElementById('fee-modal');
    m.classList.remove('hidden');
    requestAnimationFrame(() => m.style.opacity = '1');
}

document.getElementById('fee-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('fee-submit-btn');
    btn.disabled = true;
    btn.textContent = 'સેવ થાય છે...';
    const data = new FormData(this);
    fetch('/transport/bus-students/pay-fee', {
        method: 'POST', body: data,
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
    .then(res => {
        if (res.success) { NexSchool.alert.success(res.message); closeFeeModal(); setTimeout(() => loadStudents(), 400); }
        else { NexSchool.alert.danger(res.message || 'ભૂલ'); }
    })
    .catch(err => {
        if (err.errors) NexSchool.alert.danger(Object.values(err.errors).flat().join('<br>'));
        else NexSchool.alert.danger(err.message || 'સર્વર ભૂલ');
    })
    .finally(() => { btn.disabled = false; btn.textContent = 'ફી ભરો'; });
});

function loadStudents() {
    const routeId = document.getElementById('filter-route').value;
    const search = document.getElementById('filter-search').value.trim();
    let url = '{{ route("transport.bus-students.data") }}?';
    if (routeId) url += 'route_id=' + routeId + '&';
    if (search) url += 'search=' + encodeURIComponent(search);
    fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('student-tbody');
        if (!data.students || !data.students.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="px-4 py-12 text-center text-gray-400">કોઈ બસ વિદ્યાર્થી નથી</td></tr>';
            return;
        }
        tbody.innerHTML = data.students.map((s, i) => `<tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 text-gray-500">${i+1}</td>
            <td class="px-4 py-3 font-medium text-gray-800">${s.full_name_gu}</td>
            <td class="px-4 py-3 text-gray-600">${s.standard_label || '—'}</td>
            <td class="px-4 py-3 text-gray-600">${s.gaam || '—'}</td>
            <td class="px-4 py-3 font-mono text-gray-600">${s.mobile || '—'}</td>
            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 bg-teal-50 rounded-md text-xs font-medium text-teal-700">${s.route ? s.route.route_name : ''}</span></td>
            <td class="px-4 py-3 text-center font-mono text-gray-700">₹${Number(s.fee_sem1).toLocaleString()} + ₹${Number(s.fee_sem2).toLocaleString()}</td>
            <td class="px-4 py-3 text-center"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">સક્રિય</span></td>
            <td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-1">
                <button onclick="payFee(${s.id},'${s.full_name_gu.replace(/'/g,"\\'")}')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg" title="ફી ભરો"><i class="lni lni-wallet-1"></i></button>
                <button onclick="editStudent(${s.id})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg" title="સુધારો"><i class="lni lni-pencil-1"></i></button>
                <button onclick="deleteStudent(${s.id})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="કાઢી નાખો"><i class="lni lni-trash-3"></i></button>
            </div></td>
        </tr>`).join('');
    });
}
</script>
@endpush
@endsection
