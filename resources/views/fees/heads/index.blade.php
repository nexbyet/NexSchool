@extends('layouts.app')
@section('title', 'ફી હેડ્સ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 to-purple-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ફી હેડ્સ</h1>
                <p class="text-violet-200 mt-1 text-sm">ફી હેડ્સ ઉમેરો અને વ્યવસ્થાપિત કરો</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-white text-violet-700 text-sm font-medium rounded-lg hover:bg-violet-50 transition flex items-center gap-2 shadow-lg">
                <i class="lni lni-plus text-base"></i> નવું ફી હેડ
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    @php
        $totalHeads = $heads->count();
        $activeHeads = $heads->where('is_active', true)->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                <i class="lni lni-book-1 text-violet-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ ફી હેડ્સ</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalHeads }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="lni lni-check-circle-1 text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">સક્રિય</p>
                <p class="text-xl font-bold text-emerald-700">{{ $activeHeads }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                <i class="lni lni-ban-2 text-red-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">નિષ્ક્રિય</p>
                <p class="text-xl font-bold text-red-700">{{ $totalHeads - $activeHeads }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રમ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">નામ (ગુજરાતી)</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">નામ (English)</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">વર્ણન</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">સ્થિતિ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રિયા</th>
                    </tr>
                </thead>
                <tbody id="heads-tbody" class="divide-y divide-gray-100">
                    @forelse ($heads as $index => $head)
                    <tr id="head-row-{{ $head->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $head->name_gu }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $head->name_en }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $head->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($head->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <i class="lni lni-check-circle-1 text-xs"></i> સક્રિય
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="lni lni-ban-2 text-xs"></i> નિષ્ક્રિય
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editHead({{ $head->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો">
                                    <i class="lni lni-pencil-1 text-sm"></i>
                                </button>
                                <button onclick="deleteHead({{ $head->id }}, '{{ $head->name_gu }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો">
                                    <i class="lni lni-trash-3 text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl flex items-center justify-center shadow-sm">
                                <i class="lni lni-book-1 text-3xl text-violet-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">હજી સુધી કોઈ ફી હેડ ઉમેરાયું નથી</p>
                            <p class="text-gray-400 text-sm mt-1">પ્રથમ ફી હેડ ઉમેરવા માટે બટન દબાવો</p>
                            <button onclick="openModal()" class="mt-4 px-5 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition shadow-sm">નવું ફી હેડ ઉમેરો</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="head-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવું ફી હેડ</h3>
        <form id="head-form">
            <input type="hidden" id="head-id">
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                        <input type="text" id="name-gu-input" placeholder="દા.ત. શિક્ષણ ફી" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">નામ (English) <span class="text-red-500">*</span></label>
                        <input type="text" id="name-en-input" placeholder="e.g. Tuition Fee" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વર્ણન</label>
                    <textarea id="description-input" rows="2" placeholder="વૈકલ્પિક વર્ણન" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition resize-none"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ક્રમ (Sort Order)</label>
                        <input type="number" id="sort-order-input" placeholder="આપોઆપ" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label>
                        <select id="is-active-input" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                            <option value="1">સક્રિય</option>
                            <option value="0">નિષ્ક્રિય</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg focus:ring-4 focus:ring-violet-200 transition flex items-center gap-2">
                    <i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો
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
    var modal = document.getElementById('head-modal');
    var form = document.getElementById('head-form');
    var headId = document.getElementById('head-id');
    var nameGu = document.getElementById('name-gu-input');
    var nameEn = document.getElementById('name-en-input');
    var description = document.getElementById('description-input');
    var sortOrder = document.getElementById('sort-order-input');
    var isActive = document.getElementById('is-active-input');
    var submitBtn = document.getElementById('submit-btn');
    var modalTitle = document.getElementById('modal-title');

    function openModal(data) {
        modal.classList.remove('hidden');
        requestAnimationFrame(function() { modal.style.opacity = '1'; nameGu.focus(); });
        if (data) {
            modalTitle.textContent = 'ફી હેડ એડિટ કરો';
            headId.value = data.id;
            nameGu.value = data.name_gu || '';
            nameEn.value = data.name_en || '';
            description.value = data.description || '';
            sortOrder.value = data.sort_order || '';
            isActive.value = data.is_active ? '1' : '0';
        } else {
            modalTitle.textContent = 'નવું ફી હેડ';
            headId.value = '';
            nameGu.value = '';
            nameEn.value = '';
            description.value = '';
            sortOrder.value = '';
            isActive.value = '1';
        }
    }

    function closeModal() {
        modal.style.opacity = '0';
        setTimeout(function() { modal.classList.add('hidden'); }, 200);
    }

    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાઈ રહ્યું છે...';
        var isEdit = !!headId.value;
        var url = isEdit ? '{{ url("fees/heads") }}/' + headId.value : '{{ route("fees.heads.store") }}';
        var method = isEdit ? 'PUT' : 'POST';
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                _method: method,
                name_gu: nameGu.value,
                name_en: nameEn.value,
                description: description.value,
                sort_order: sortOrder.value ? parseInt(sortOrder.value) : null,
                is_active: isActive.value === '1',
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) { NexSchool.alert.success(data.message); closeModal(); location.reload(); }
            else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || err.errors?.name_gu?.[0] || 'સર્વર ભૂલ'); })
        .finally(function() { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો'; });
    });

    window.editHead = function(id) {
        fetch('{{ url("fees/heads") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { if (!res.ok) throw new Error('ડેટા મેળવવામાં ભૂલ'); return res.json(); })
        .then(function(data) { openModal(data); })
        .catch(function(err) { NexSchool.alert.danger(err.message); });
    };

    window.deleteHead = function(id, name) {
        NexSchool.confirm.show('ફી હેડ કાઢી નાખો', 'શું તમે "' + name + '" કાઢી નાખવા માંગો છો?', 'danger')
        .then(function(confirmed) {
            if (!confirmed) return;
            fetch('{{ url("fees/heads") }}/' + id, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({ _method: 'DELETE' }),
            })
            .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
            .then(function(data) {
                if (data.success) { NexSchool.alert.success(data.message); document.getElementById('head-row-' + id).remove(); }
                else { NexSchool.alert.danger(data.message); }
            })
            .catch(function(err) { NexSchool.alert.danger(err.message || 'કાઢવામાં ભૂલ.'); });
        });
    };

    window.openModal = openModal;
    window.closeModal = closeModal;
})();
</script>
@endpush
