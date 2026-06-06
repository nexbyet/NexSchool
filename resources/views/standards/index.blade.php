@extends('layouts.app')

@section('title', 'ધોરણ અને વર્ગ')

@section('content')
<div class="p-4 md:p-6">
    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ધોરણ અને વર્ગ વ્યવસ્થાપન</h1>
                <p class="text-purple-200 mt-1 text-sm">ધોરણો અને તેમના વર્ગોનું સંચાલન કરો — કાર્ડને ડ્રેગ કરીને ક્રમ ગોઠવો</p>
            </div>
            <button onclick="openStandardModal()" class="px-4 py-2 bg-white text-purple-700 text-sm font-medium rounded-lg hover:bg-purple-50 transition flex items-center gap-2 shadow-lg">
                <i class="lni lni-plus text-base"></i> નવું ધોરણ
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Stats --}}
    @php
        $totalStandards = $standards->count();
        $totalClasses = $standards->sum(fn($s) => $s->classes->count());
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                <i class="lni lni-layers-1 text-purple-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ ધોરણ</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalStandards }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="lni lni-buildings-1 text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ વર્ગ</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalClasses }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="lni lni-book-1 text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">સરેરાશ વર્ગ/ધોરણ</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalStandards > 0 ? round($totalClasses / $totalStandards, 1) : 0 }}</p>
            </div>
        </div>
    </div>

    <div id="standards-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 items-start">
        @forelse ($standards as $std)
            @include('standards._card', ['std' => $std])
        @empty
            <div id="empty-state" class="col-span-full text-center py-20 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-buildings-1 text-3xl text-purple-400"></i>
                </div>
                <p class="text-gray-500 font-medium">હજી સુધી કોઈ ધોરણ ઉમેરાયું નથી</p>
                <p class="text-gray-400 text-sm mt-1">પ્રથમ ધોરણ ઉમેરવા માટે બટન દબાવો</p>
                <button onclick="openStandardModal()" class="mt-4 px-5 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition shadow-sm">પ્રથમ ધોરણ ઉમેરો</button>
            </div>
        @endforelse
    </div>
</div>

{{-- Standard Modal (create/edit) --}}
<div id="standard-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="standard-modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવું ધોરણ</h3>
        <form id="standard-form">
            <input type="hidden" id="standard-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="standard-name-input" placeholder="દા.ત. પ્રથમ ધોરણ, Std 1" required autofocus
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeStandardModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="standard-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>

{{-- Class Modal (create/edit) --}}
<div id="class-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="class-modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવો વર્ગ</h3>
        <form id="class-form">
            <input type="hidden" id="class-id">
            <input type="hidden" id="class-standard-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="class-name-input" placeholder="દા.ત. વિભાગ A, Section A" required autofocus
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeClassModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="class-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===== Standard CRUD =====
    const standardModal = document.getElementById('standard-modal');
    const standardForm = document.getElementById('standard-form');
    const standardId = document.getElementById('standard-id');
    const standardNameInput = document.getElementById('standard-name-input');
    const standardSubmitBtn = document.getElementById('standard-submit-btn');
    const standardModalTitle = document.getElementById('standard-modal-title');

    function openStandardModal(data = null) {
        standardModal.classList.remove('hidden');
        requestAnimationFrame(() => { standardModal.style.opacity = '1'; standardNameInput.focus(); });
        if (data) {
            standardModalTitle.textContent = 'ધોરણ સુધારો';
            standardId.value = data.id;
            standardNameInput.value = data.name;
        } else {
            standardModalTitle.textContent = 'નવું ધોરણ';
            standardId.value = '';
            standardNameInput.value = '';
        }
    }

    function closeStandardModal() {
        standardModal.style.opacity = '0';
        setTimeout(() => standardModal.classList.add('hidden'), 200);
    }

    standardModal.addEventListener('click', (e) => { if (e.target === standardModal) closeStandardModal(); });

    standardForm.addEventListener('submit', function (e) {
        e.preventDefault();
        standardSubmitBtn.disabled = true;
        standardSubmitBtn.textContent = 'સાચવાઈ રહ્યું છે...';

        const isEdit = !!standardId.value;
        const url = isEdit ? '{{ url("standards") }}/' + standardId.value : '{{ route("standards.store") }}';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({
                _token: '{{ csrf_token() }}',
                _method: method,
                name: standardNameInput.value,
            }),
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
        })
        .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
        .then(data => {
            if (data.success) {
                NexSchool.alert.success(data.message);
                closeStandardModal();
                location.reload();
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ આવી.');
            }
        })
        .catch(err => { NexSchool.alert.danger(err.message || err.errors?.name?.[0] || 'સર્વર ભૂલ'); })
        .finally(() => { standardSubmitBtn.disabled = false; standardSubmitBtn.textContent = 'સાચવો'; });
    });

    function editStandard(id) {
        fetch('{{ url("standards") }}/' + id, {
            headers: { 'Accept': 'application/json' },
        })
        .then(res => res.json())
        .then(data => openStandardModal(data))
        .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
    }

    function deleteStandard(id, name) {
        NexSchool.confirm.show('ધોરણ કાઢી નાખો', 'શું તમે "' + name + '" કાઢી નાખવા માંગો છો?', 'danger')
        .then(() => {
            fetch('{{ url("standards") }}/' + id, {
                method: 'POST',
                body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
                headers: { 'Accept': 'application/json' },
            })
            .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
            .then(data => {
                if (data.success) {
                    NexSchool.alert.success(data.message);
                    document.getElementById('standard-card-' + id).remove();
                    checkEmpty();
                } else {
                    NexSchool.alert.danger(data.message);
                }
            })
            .catch(err => NexSchool.alert.danger(err.message || 'કાઢવામાં ભૂલ.'));
        })
        .catch(() => {});
    }

    function checkEmpty() {
        const container = document.getElementById('standards-container');
        if (container.children.length === 0) {
            container.innerHTML = '<div id="empty-state" class="col-span-full text-center py-20 bg-white rounded-xl border border-gray-200"><div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl flex items-center justify-center shadow-sm"><i class="lni lni-buildings-1 text-3xl text-purple-400"></i></div><p class="text-gray-500 font-medium">હજી સુધી કોઈ ધોરણ ઉમેરાયું નથી</p><p class="text-gray-400 text-sm mt-1">પ્રથમ ધોરણ ઉમેરવા માટે બટન દબાવો</p><button onclick="openStandardModal()" class="mt-4 px-5 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition shadow-sm">પ્રથમ ધોરણ ઉમેરો</button></div>';
        }
    }

    // ===== Class CRUD =====
    const classModal = document.getElementById('class-modal');
    const classForm = document.getElementById('class-form');
    const classId = document.getElementById('class-id');
    const classStandardId = document.getElementById('class-standard-id');
    const classNameInput = document.getElementById('class-name-input');
    const classSubmitBtn = document.getElementById('class-submit-btn');
    const classModalTitle = document.getElementById('class-modal-title');

    function openClassModal(standardIdVal, data = null) {
        classModal.classList.remove('hidden');
        requestAnimationFrame(() => { classModal.style.opacity = '1'; classNameInput.focus(); });
        classStandardId.value = standardIdVal;
        if (data) {
            classModalTitle.textContent = 'વર્ગ સુધારો';
            classId.value = data.id;
            classNameInput.value = data.name;
        } else {
            classModalTitle.textContent = 'નવો વર્ગ';
            classId.value = '';
            classNameInput.value = '';
        }
    }

    function closeClassModal() {
        classModal.style.opacity = '0';
        setTimeout(() => classModal.classList.add('hidden'), 200);
    }

    classModal.addEventListener('click', (e) => { if (e.target === classModal) closeClassModal(); });

    classForm.addEventListener('submit', function (e) {
        e.preventDefault();
        classSubmitBtn.disabled = true;
        classSubmitBtn.textContent = 'સાચવાઈ રહ્યું છે...';

        const stdId = classStandardId.value;
        const isEdit = !!classId.value;
        const url = isEdit
            ? '{{ url("standards") }}/class/' + classId.value
            : '{{ url("standards") }}/' + stdId + '/class';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({
                _token: '{{ csrf_token() }}',
                _method: method,
                name: classNameInput.value,
            }),
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
        })
        .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
        .then(data => {
            if (data.success) {
                NexSchool.alert.success(data.message);
                closeClassModal();
                location.reload();
            } else {
                NexSchool.alert.danger(data.message || 'ભૂલ આવી.');
            }
        })
        .catch(err => { NexSchool.alert.danger(err.message || err.errors?.name?.[0] || 'સર્વર ભૂલ'); })
        .finally(() => { classSubmitBtn.disabled = false; classSubmitBtn.textContent = 'સાચવો'; });
    });

    function editClass(id) {
        fetch('{{ url("standards") }}/class/' + id + '/edit', {
            headers: { 'Accept': 'application/json' },
        })
        .then(res => { if (!res.ok) throw new Error('ડેટા મેળવવામાં ભૂલ'); return res.json(); })
        .then(data => {
            const card = document.getElementById('class-item-' + id);
            const stdId = card ? card.dataset.standardId : '';
            openClassModal(stdId, data);
        })
        .catch(err => NexSchool.alert.danger(err.message));
    }

    function deleteClass(id, name) {
        NexSchool.confirm.show('વર્ગ કાઢી નાખો', 'શું તમે "' + name + '" કાઢી નાખવા માંગો છો?', 'danger')
        .then(() => {
            fetch('{{ url("standards") }}/class/' + id, {
                method: 'POST',
                body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
                headers: { 'Accept': 'application/json' },
            })
            .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
            .then(data => {
                if (data.success) {
                    NexSchool.alert.success(data.message);
                    document.getElementById('class-item-' + id).remove();
                } else {
                    NexSchool.alert.danger(data.message);
                }
            })
            .catch(err => NexSchool.alert.danger(err.message || 'કાઢવામાં ભૂલ.'));
        })
        .catch(() => {});
    }

    function addClass(standardId) {
        openClassModal(standardId);
    }

    // ===== Drag & Drop =====
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Sortable !== 'undefined') {
            // Standards container drag
            const standardsContainer = document.getElementById('standards-container');
            if (standardsContainer) {
                Sortable.create(standardsContainer, {
                    animation: 200,
                    handle: '.standard-drag-handle',
                    ghostClass: 'opacity-40',
                    onEnd: function () {
                        const items = standardsContainer.querySelectorAll('.standard-card');
                        const order = [];
                        items.forEach((el, i) => {
                            order.push({ id: parseInt(el.dataset.id), sort_order: i + 1 });
                        });
                        fetch('{{ route("standards.reorder") }}', {
                            method: 'POST',
                            body: JSON.stringify({ _token: '{{ csrf_token() }}', standards: order }),
                            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        })
                        .then(res => { if (!res.ok) throw new Error(); return res.json(); })
                        .then(data => {
                            if (data.success) NexSchool.alert.note('ક્રમ સચવાયો');
                        })
                        .catch(() => {});
                    },
                });
            }

            // Class list drag within each standard card
            document.querySelectorAll('.class-list').forEach(function (list) {
                Sortable.create(list, {
                    animation: 200,
                    handle: '.class-drag-handle',
                    ghostClass: 'opacity-40',
                    onEnd: function () {
                        const items = list.querySelectorAll('.class-item');
                        const order = [];
                        items.forEach((el, i) => {
                            order.push({ id: parseInt(el.dataset.id), sort_order: i + 1 });
                        });
                        fetch('{{ route("standards.classes.reorder") }}', {
                            method: 'POST',
                            body: JSON.stringify({ _token: '{{ csrf_token() }}', classes: order }),
                            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        })
                        .then(res => { if (!res.ok) throw new Error(); return res.json(); })
                        .then(data => {
                            if (data.success) NexSchool.alert.note('વર્ગોનો ક્રમ સચવાયો');
                        })
                        .catch(() => {});
                    },
                });
            });

            // Subject pills drag within each standard card footer
            document.querySelectorAll('.subject-list').forEach(function (list) {
                const pills = list.querySelector('.flex-wrap');
                if (!pills) return;
                Sortable.create(pills, {
                    animation: 200,
                    handle: '.subject-pill',
                    ghostClass: 'opacity-40',
                    onEnd: function () {
                        const items = pills.querySelectorAll('.subject-pill');
                        const order = [];
                        const stdId = list.dataset.standardId;
                        items.forEach((el, i) => {
                            order.push({ id: parseInt(el.dataset.id), sort_order: i + 1 });
                        });
                        fetch('{{ route("subjects.reorder") }}', {
                            method: 'POST',
                            body: JSON.stringify({
                                _token: '{{ csrf_token() }}',
                                standard_id: parseInt(stdId),
                                subjects: order,
                            }),
                            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        })
                        .then(res => { if (!res.ok) throw new Error(); return res.json(); })
                        .then(data => {
                            if (data.success) NexSchool.alert.note('વિષયોનો ક્રમ સચવાયો');
                        })
                        .catch(() => {});
                    },
                });
            });
        }
    });
</script>
@endpush
