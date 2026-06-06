@extends('layouts.app')
@section('title', 'રૂટ વ્યવસ્થાપન')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-600 to-cyan-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">રૂટ વ્યવસ્થાપન</h1>
                <p class="text-sky-200 mt-1 text-sm">બસ રૂટ ઉમેરો અને વ્યવસ્થાપિત કરો</p>
            </div>
            <button onclick="openRouteModal()" class="px-4 py-2 bg-white text-sky-700 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition flex items-center gap-2">
                <i class="lni lni-plus text-base"></i> નવો રૂટ
            </button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="space-y-4">
        @forelse($routes as $route)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between bg-gray-50 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600"><i class="lni lni-map-marker-1 text-sm"></i></span>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $route->route_name }}</h3>
                        <p class="text-xs text-gray-500">{{ $route->vehicle?->vehicle_no ?? 'કોઈ વાહન નથી' }} • {{ $route->stops->count() }} સ્ટોપ</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $route->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $route->is_active ? 'સક્રિય' : 'નિષ્ક્રિય' }}</span>
                    <button onclick="editRoute({{ $route->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1 text-sm"></i></button>
                    <button onclick="deleteRoute({{ $route->id }}, '{{ $route->route_name }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3 text-sm"></i></button>
                </div>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">સ્ટોપ્સ</h4>
                    <button onclick="openStopModal({{ $route->id }})" class="text-xs font-medium text-sky-600 hover:text-sky-800 flex items-center gap-1"><i class="lni lni-plus text-xs"></i> સ્ટોપ ઉમેરો</button>
                </div>
                @if($route->stops->isNotEmpty())
                <table class="w-full text-xs">
                    <thead><tr class="text-gray-500 border-b border-gray-100">
                        <th class="py-1.5 text-left w-8">ક્રમ</th>
                        <th class="py-1.5 text-left">સ્ટોપ</th>
                        <th class="py-1.5 text-center w-16">પિકઅપ</th>
                        <th class="py-1.5 text-center w-16">ડ્રોપ</th>
                        <th class="py-1.5 text-center w-16">ક્રિયા</th>
                    </tr></thead>
                    <tbody>
                        @foreach($route->stops as $stop)
                        <tr id="stop-row-{{ $stop->id }}" class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-1.5 text-gray-400">{{ $stop->stop_order }}</td>
                            <td class="py-1.5 font-medium">{{ $stop->stop_name }}</td>
                            <td class="py-1.5 text-center">{{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : '—' }}</td>
                            <td class="py-1.5 text-center">{{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : '—' }}</td>
                            <td class="py-1.5 text-center">
                                <button onclick="editStop({{ $stop->id }}, {{ $route->id }})" class="p-1 text-gray-400 hover:text-amber-600" title="સુધારો"><i class="lni lni-pencil-1 text-xs"></i></button>
                                <button onclick="deleteStop({{ $stop->id }}, '{{ $stop->stop_name }}')" class="p-1 text-gray-400 hover:text-red-600" title="કાઢો"><i class="lni lni-trash-3 text-xs"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-xs text-gray-400 text-center py-3">કોઈ સ્ટોપ ઉમેરાયા નથી. "સ્ટોપ ઉમેરો" પર ક્લિક કરો.</p>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
            <i class="lni lni-map-marker-1 text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500 font-medium">હજી સુધી કોઈ રૂટ ઉમેરાયો નથી</p>
            <button onclick="openRouteModal()" class="mt-3 px-4 py-2 bg-sky-600 text-white rounded-xl text-sm font-semibold hover:bg-sky-700 transition flex items-center gap-2 mx-auto"><i class="lni lni-plus text-sm"></i> નવો રૂટ ઉમેરો</button>
        </div>
        @endforelse
    </div>
</div>

{{-- Route Modal --}}
<div id="route-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
        <h3 id="route-modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવો રૂટ</h3>
        <form id="route-form">
            <input type="hidden" id="route-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">રૂટનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="route-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વાહન</label>
                    <select id="route-vehicle" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">— કોઈ વાહન નથી —</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->vehicle_no }} ({{ $v->vehicle_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વર્ણન</label>
                    <textarea id="route-desc" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500" rows="2"></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="route-active" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500" checked>
                    <span class="text-sm font-medium text-gray-700">સક્રિય</span>
                </label>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeRouteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="route-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg focus:ring-4 focus:ring-sky-200 transition flex items-center gap-2"><i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>

{{-- Stop Modal --}}
<div id="stop-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="stop-modal-title" class="text-lg font-semibold text-gray-900 mb-4">સ્ટોપ</h3>
        <form id="stop-form">
            <input type="hidden" id="stop-route-id">
            <input type="hidden" id="stop-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સ્ટોપનું નામ <span class="text-red-500">*</span></label>
                    <input type="text" id="stop-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ક્રમ</label>
                        <input type="number" id="stop-order" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">પિકઅપ સમય</label>
                        <input type="time" id="pickup-time" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ડ્રોપ સમય</label>
                        <input type="time" id="drop-time" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeStopModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="stop-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg focus:ring-4 focus:ring-sky-200 transition flex items-center gap-2"><i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ---- Route CRUD ----
    window.openRouteModal = function(data) {
        document.getElementById('route-modal-title').textContent = data ? 'રૂટ સુધારો' : 'નવો રૂટ';
        document.getElementById('route-id').value = data ? data.id : '';
        document.getElementById('route-name').value = data ? data.route_name : '';
        document.getElementById('route-vehicle').value = data ? (data.vehicle_id || '') : '';
        document.getElementById('route-desc').value = data ? (data.description || '') : '';
        document.getElementById('route-active').checked = data ? data.is_active : true;
        const m = document.getElementById('route-modal');
        m.classList.remove('hidden');
        setTimeout(() => m.style.opacity = '1', 10);
    };
    window.closeRouteModal = function() {
        const m = document.getElementById('route-modal');
        m.style.opacity = '0';
        setTimeout(() => m.classList.add('hidden'), 200);
    };
    document.getElementById('route-modal').addEventListener('click', function(e) { if (e.target === this) closeRouteModal(); });
    document.getElementById('route-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('route-id').value;
        const url = id ? '/transport/routes/' + id : '/transport/routes';
        const method = id ? 'PUT' : 'POST';
        fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: JSON.stringify({
            route_name: document.getElementById('route-name').value,
            vehicle_id: document.getElementById('route-vehicle').value || null,
            description: document.getElementById('route-desc').value,
            is_active: document.getElementById('route-active').checked,
        }) }).then(r => r.json()).then(d => {
            if (d.success) { NexSchool.alert.success(d.message); location.reload(); }
        }).catch(() => NexSchool.alert.danger('ભૂલ'));
    });

    window.editRoute = function(id) {
        fetch('/transport/routes/' + id, { headers: { Accept: 'application/json' } })
        .then(r => r.json()).then(d => openRouteModal(d));
    };
    window.deleteRoute = function(id, name) {
        NexSchool.confirm.show('ખાતરી કરો', name + ' કાઢી નાખવું?', 'danger').then(c => {
            if (!c) return;
            fetch('/transport/routes/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } })
            .then(r => r.json()).then(d => { if (d.success) { NexSchool.alert.success(d.message); location.reload(); } });
        });
    };

    // ---- Stop CRUD ----
    window.openStopModal = function(routeId, data) {
        document.getElementById('stop-modal-title').textContent = data ? 'સ્ટોપ સુધારો' : 'નવો સ્ટોપ';
        document.getElementById('stop-route-id').value = routeId;
        document.getElementById('stop-id').value = data ? data.id : '';
        document.getElementById('stop-name').value = data ? data.stop_name : '';
        document.getElementById('stop-order').value = data ? data.stop_order : '';
        document.getElementById('pickup-time').value = data ? data.pickup_time || '' : '';
        document.getElementById('drop-time').value = data ? data.drop_time || '' : '';
        const m = document.getElementById('stop-modal');
        m.classList.remove('hidden');
        setTimeout(() => m.style.opacity = '1', 10);
    };
    window.closeStopModal = function() {
        const m = document.getElementById('stop-modal');
        m.style.opacity = '0';
        setTimeout(() => m.classList.add('hidden'), 200);
    };
    document.getElementById('stop-modal').addEventListener('click', function(e) { if (e.target === this) closeStopModal(); });
    document.getElementById('stop-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('stop-id').value;
        const routeId = document.getElementById('stop-route-id').value;
        const url = id ? '/transport/stops/' + id : '/transport/routes/' + routeId + '/stops';
        const method = id ? 'PUT' : 'POST';
        fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: JSON.stringify({
            stop_name: document.getElementById('stop-name').value,
            stop_order: document.getElementById('stop-order').value || null,
            pickup_time: document.getElementById('pickup-time').value || null,
            drop_time: document.getElementById('drop-time').value || null,
        }) }).then(r => r.json()).then(d => {
            if (d.success) { NexSchool.alert.success(d.message); location.reload(); }
        }).catch(() => NexSchool.alert.danger('ભૂલ'));
    });

    window.editStop = function(id, routeId) {
        // fetch stop data from the route's stops list
        fetch('/transport/routes/' + routeId, { headers: { Accept: 'application/json' } })
        .then(r => r.json()).then(d => {
            const stop = d.stops.find(s => s.id === id);
            if (stop) openStopModal(routeId, stop);
        });
    };
    window.deleteStop = function(id, name) {
        NexSchool.confirm.show('ખાતરી કરો', name + ' કાઢી નાખવું?', 'danger').then(c => {
            if (!c) return;
            fetch('/transport/stops/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } })
            .then(r => r.json()).then(d => { if (d.success) { NexSchool.alert.success(d.message); location.reload(); } });
        });
    };
})();
</script>
@endpush
@endsection
