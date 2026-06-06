@extends('layouts.app')
@section('title', 'વિષયો')
@section('content')
<div class="p-4 md:p-6">
    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-600 to-orange-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">વિષય વ્યવસ્થાપન</h1>
                <p class="text-amber-200 mt-1 text-sm">વૈશ્વિક વિષયો ઉમેરો અને તેઓ કયા ધોરણમાં આવે છે તે નક્કી કરો</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-white text-amber-700 text-sm font-medium rounded-lg hover:bg-amber-50 transition flex items-center gap-2 shadow-lg">
                <i class="lni lni-plus text-base"></i> નવો વિષય
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Stats --}}
    @php
        $totalSubjects = $subjects->count();
        $assignedCount = $subjects->filter(fn($s) => $s->standards->isNotEmpty())->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="lni lni-book-1 text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ વિષયો</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalSubjects }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="lni lni-link-2-angular-right text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">ધોરણ સોંપાયેલ</p>
                <p class="text-xl font-bold text-emerald-700">{{ $assignedCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="lni lni-book-1 text-gray-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">બાકી</p>
                <p class="text-xl font-bold text-gray-600">{{ $totalSubjects - $assignedCount }}</p>
            </div>
        </div>
    </div>

    {{-- Subject Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($subjects as $s)
        <div id="subject-card-{{ $s->id }}" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300">
            {{-- Card header --}}
            <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                        <i class="lni lni-book-1 text-amber-600 text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $s->name }}</h3>
                        @if($s->code)
                            <span class="text-xs text-gray-400 font-mono">{{ $s->code }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="editSubject({{ $s->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></button>
                    <button onclick="deleteSubject({{ $s->id }}, '{{ $s->name }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>
                </div>
            </div>
            {{-- Card body: assigned standards --}}
            <div class="p-5">
                @if($s->standards->isNotEmpty())
                    <p class="text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">સંલગ્ન ધોરણો</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($s->standards as $std)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 border border-amber-200">{{ $std->name }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="w-10 h-10 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="lni lni-link-2-angular-right text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-400 mb-3">કોઈ ધોરણ સોંપાયું નથી</p>
                    </div>
                @endif
            </div>
            {{-- Card footer --}}
            <div class="px-5 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ $s->standards->count() }} ધોરણ{{ $s->standards->count() !== 1 ? 'ો' : '' }}</span>
                <button onclick="assignStandards({{ $s->id }})" class="text-xs font-medium text-amber-600 hover:text-amber-700 hover:bg-amber-50 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                    <i class="lni lni-link-2-angular-right text-xs"></i> ધોરણો સોંપો
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white rounded-xl border border-gray-200">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="lni lni-book-1 text-3xl text-amber-400"></i>
            </div>
            <p class="text-gray-500 font-medium">હજી સુધી કોઈ વિષય ઉમેરાયો નથી</p>
            <p class="text-gray-400 text-sm mt-1">પ્રથમ વિષય ઉમેરવા માટે બટન દબાવો</p>
            <button onclick="openModal()" class="mt-4 px-5 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition shadow-sm">નવો વિષય ઉમેરો</button>
        </div>
        @endforelse
    </div>
</div>

{{-- Subject Create/Edit Modal --}}
<div id="subject-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવો વિષય</h3>
        <form id="subject-form">
            <input type="hidden" id="subject-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વિષયનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="name-input" placeholder="દા.ત. ગણિત, Maths" required autofocus class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">કોડ (વૈકલ્પિક)</label>
                    <input type="text" id="code-input" placeholder="દા.ત. MTH01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg focus:ring-4 focus:ring-amber-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>

{{-- Assign Standards Modal --}}
<div id="assign-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">ધોરણો સોંપો: <span id="assign-subject-name" class="text-amber-600"></span></h3>
        <form id="assign-form">
            <input type="hidden" id="assign-subject-id">
            <div class="space-y-3 max-h-64 overflow-y-auto">
                <label class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 cursor-pointer border border-gray-200">
                    <input type="checkbox" id="select-all-standards" class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-gray-700">બધા પસંદ કરો / નાબૂદ કરો</span>
                </label>
                @foreach ($standards as $std)
                <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="standard_ids[]" value="{{ $std->id }}" class="standard-checkbox w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm font-medium text-gray-700">{{ $std->name }}</span>
                </label>
                @endforeach
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="assign-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg focus:ring-4 focus:ring-amber-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('subject-modal');
    const form = document.getElementById('subject-form');
    const subjectId = document.getElementById('subject-id');
    const nameInput = document.getElementById('name-input');
    const codeInput = document.getElementById('code-input');
    const submitBtn = document.getElementById('submit-btn');
    const modalTitle = document.getElementById('modal-title');

    function openModal(data = null) {
        modal.classList.remove('hidden');
        requestAnimationFrame(() => { modal.style.opacity = '1'; nameInput.focus(); });
        if (data) {
            modalTitle.textContent = 'વિષય સુધારો';
            subjectId.value = data.id;
            nameInput.value = data.name;
            codeInput.value = data.code || '';
        } else {
            modalTitle.textContent = 'નવો વિષય';
            subjectId.value = '';
            nameInput.value = '';
            codeInput.value = '';
        }
    }

    function closeModal() {
        modal.style.opacity = '0';
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = 'સાચવાઈ રહ્યું છે...';
        const isEdit = !!subjectId.value;
        const url = isEdit ? '{{ url("subjects") }}/' + subjectId.value : '{{ route("subjects.store") }}';
        const method = isEdit ? 'PUT' : 'POST';
        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: method, name: nameInput.value, code: codeInput.value }),
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
        })
        .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); closeModal(); location.reload(); }
            else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
        })
        .catch(err => { NexSchool.alert.danger(err.message || err.errors?.name?.[0] || 'સર્વર ભૂલ'); })
        .finally(() => { submitBtn.disabled = false; submitBtn.textContent = 'સાચવો'; });
    });

    function editSubject(id) {
        fetch('{{ url("subjects") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(res => { if (!res.ok) throw new Error('ડેટા મેળવવામાં ભૂલ'); return res.json(); })
        .then(data => openModal(data))
        .catch(err => NexSchool.alert.danger(err.message));
    }

    function deleteSubject(id, name) {
        NexSchool.confirm.show('વિષય કાઢી નાખો', 'શું તમે "' + name + '" કાઢી નાખવા માંગો છો?', 'danger')
        .then(() => {
            fetch('{{ url("subjects") }}/' + id, {
                method: 'POST',
                body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
                headers: { 'Accept': 'application/json' },
            })
            .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
            .then(data => {
                if (data.success) { NexSchool.alert.success(data.message); document.getElementById('subject-card-' + id).remove(); }
                else { NexSchool.alert.danger(data.message); }
            })
            .catch(err => NexSchool.alert.danger(err.message || 'કાઢવામાં ભૂલ.'));
        }).catch(() => {});
    }

    const assignModal = document.getElementById('assign-modal');
    const assignForm = document.getElementById('assign-form');
    const assignSubjectId = document.getElementById('assign-subject-id');
    const assignSubjectName = document.getElementById('assign-subject-name');
    const assignSubmitBtn = document.getElementById('assign-submit-btn');

    function assignStandards(id) {
        fetch('{{ url("subjects") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            assignSubjectId.value = data.id;
            assignSubjectName.textContent = data.name;
            const checkboxes = assignForm.querySelectorAll('input[type="checkbox"]');
            const assignedIds = (data.standards || []).map(s => s.id);
            checkboxes.forEach(cb => { cb.checked = assignedIds.includes(parseInt(cb.value)); });
            assignModal.classList.remove('hidden');
            requestAnimationFrame(() => assignModal.style.opacity = '1');
        })
        .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
    }

    function closeAssignModal() {
        assignModal.style.opacity = '0';
        setTimeout(() => assignModal.classList.add('hidden'), 200);
    }

    document.getElementById('select-all-standards').addEventListener('change', function () {
        assignForm.querySelectorAll('.standard-checkbox').forEach(cb => cb.checked = this.checked);
    });
    assignForm.addEventListener('change', function (e) {
        if (e.target.classList.contains('standard-checkbox')) {
            const allCbs = assignForm.querySelectorAll('.standard-checkbox');
            const checkedCbs = assignForm.querySelectorAll('.standard-checkbox:checked');
            document.getElementById('select-all-standards').checked = allCbs.length === checkedCbs.length;
        }
    });
    assignModal.addEventListener('click', (e) => { if (e.target === assignModal) closeAssignModal(); });
    assignForm.addEventListener('submit', function (e) {
        e.preventDefault();
        assignSubmitBtn.disabled = true;
        assignSubmitBtn.textContent = 'સાચવાઈ રહ્યું છે...';
        const checked = [];
        assignForm.querySelectorAll('.standard-checkbox:checked').forEach(cb => checked.push(cb.value));
        fetch('{{ url("subjects") }}/' + assignSubjectId.value + '/standards', {
            method: 'POST',
            body: JSON.stringify({ _token: '{{ csrf_token() }}', standard_ids: checked.map(Number) }),
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        })
        .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); closeAssignModal(); location.reload(); }
            else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
        })
        .catch(err => { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(() => { assignSubmitBtn.disabled = false; assignSubmitBtn.textContent = 'સાચવો'; });
    });
</script>
@endpush
