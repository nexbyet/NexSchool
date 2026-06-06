@extends('layouts.app')
@section('title', 'વાહનો')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">વાહનો</h1>
                <p class="text-emerald-200 mt-1 text-sm">વાહન ઉમેરો અને વ્યવસ્થાપિત કરો</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-white text-emerald-700 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition flex items-center gap-2">
                <i class="lni lni-plus text-base"></i> નવું વાહન
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રમ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">વાહન નંબર</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">પ્રકાર</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્ષમતા</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">ડ્રાઇવર</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">મોબાઇલ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">સ્થિતિ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">ક્રિયા</th>
                    </tr>
                </thead>
                <tbody id="vehicles-tbody" class="divide-y divide-gray-100">
                    @forelse($vehicles as $index => $v)
                    <tr id="vehicle-row-{{ $v->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium">{{ $v->vehicle_no }}</td>
                        <td class="px-4 py-3">{{ $v->vehicle_type }}</td>
                        <td class="px-4 py-3 text-center">{{ $v->capacity }}</td>
                        <td class="px-4 py-3">{{ $v->driver_name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $v->driver_mobile ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $v->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $v->is_active ? 'સક્રિય' : 'નિષ્ક્રિય' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editVehicle({{ $v->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></button>
                                <button onclick="deleteVehicle({{ $v->id }}, '{{ $v->vehicle_no }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-16 text-center"><div class="flex flex-col items-center gap-3"><i class="lni lni-truck-1 text-5xl text-gray-300"></i><p class="text-gray-500 font-medium">હજી સુધી કોઈ વાહન ઉમેરાયું નથી</p><button onclick="openModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition flex items-center gap-2"><i class="lni lni-plus text-sm"></i> નવું વાહન ઉમેરો</button></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="vehicle-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવું વાહન</h3>
        <form id="vehicle-form">
            <input type="hidden" id="vehicle-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વાહન નંબર <span class="text-red-500">*</span></label>
                    <input type="text" id="vehicle-no" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">પ્રકાર</label>
                    <select id="vehicle-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="bus">બસ</option>
                        <option value="van">વાન</option>
                        <option value="auto">ઓટો</option>
                        <option value="other">અન્ય</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ક્ષમતા</label>
                    <input type="number" id="capacity" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ડ્રાઇવરનું નામ</label>
                    <input type="text" id="driver-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ડ્રાઇવર મોબાઇલ</label>
                    <input type="text" id="driver-mobile" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="is-active" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" checked>
                        <span class="text-sm font-medium text-gray-700">સક્રિય</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg focus:ring-4 focus:ring-emerald-200 transition flex items-center gap-2"><i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const modal = document.getElementById('vehicle-modal');
    const form = document.getElementById('vehicle-form');

    window.openModal = function(data) {
        document.getElementById('modal-title').textContent = data ? 'વાહન સુધારો' : 'નવું વાહન';
        document.getElementById('vehicle-id').value = data ? data.id : '';
        document.getElementById('vehicle-no').value = data ? data.vehicle_no : '';
        document.getElementById('vehicle-type').value = data ? data.vehicle_type : 'bus';
        document.getElementById('capacity').value = data ? data.capacity : '';
        document.getElementById('driver-name').value = data ? (data.driver_name || '') : '';
        document.getElementById('driver-mobile').value = data ? (data.driver_mobile || '') : '';
        document.getElementById('is-active').checked = data ? data.is_active : true;
        modal.classList.remove('hidden');
        setTimeout(() => modal.style.opacity = '1', 10);
    };

    window.closeModal = function() {
        modal.style.opacity = '0';
        setTimeout(() => modal.classList.add('hidden'), 200);
    };

    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('vehicle-id').value;
        const url = id ? '/transport/vehicles/' + id : '/transport/vehicles';
        const method = id ? 'PUT' : 'POST';
        const body = {
            vehicle_no: document.getElementById('vehicle-no').value,
            vehicle_type: document.getElementById('vehicle-type').value,
            capacity: document.getElementById('capacity').value || 0,
            driver_name: document.getElementById('driver-name').value,
            driver_mobile: document.getElementById('driver-mobile').value,
            is_active: document.getElementById('is-active').checked,
        };
        fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: JSON.stringify(body) })
        .then(r => r.json()).then(d => {
            if (d.success) { NexSchool.alert.success(d.message); location.reload(); }
            else { NexSchool.alert.danger('ભૂલ'); }
        }).catch(() => NexSchool.alert.danger('ભૂલ'));
    });

    window.editVehicle = function(id) {
        fetch('/transport/vehicles/' + id, { headers: { Accept: 'application/json' } })
        .then(r => r.json()).then(d => openModal(d));
    };

    window.deleteVehicle = function(id, name) {
        NexSchool.confirm.show('ખાતરી કરો', name + ' કાઢી નાખવું?', 'danger').then(confirmed => {
            if (!confirmed) return;
            fetch('/transport/vehicles/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } })
            .then(r => r.json()).then(d => {
                if (d.success) { NexSchool.alert.success(d.message); location.reload(); }
            });
        });
    };
})();
</script>
@endpush
@endsection
