@extends('layouts.app')
@section('title', 'વિદ્યાર્થી રૂટ સોંપણી')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-600 to-orange-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">વિદ્યાર્થી રૂટ સોંપણી</h1>
            <p class="text-amber-200 mt-1 text-sm">વિદ્યાર્થીઓને બસ રૂટ સોંપો અને વ્યવસ્થાપિત કરો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Filter + Bulk Assign --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="font-semibold text-gray-800 mb-4">નવા વિદ્યાર્થીઓને રૂટ સોંપો</h3>
        <form id="filter-form" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ધોરણ</label>
                <select name="standard_id" id="filter-standard" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="">— બધા —</option>
                    @foreach($standards as $s)
                    <option value="{{ $s->id }}" {{ request('standard_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">વર્ગ</label>
                <select name="class_id" id="filter-class" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="">— બધા —</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">રૂટ</label>
                <select name="route_id" id="filter-route" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="">— બધા —</option>
                    @foreach($routes as $r)
                    <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>{{ $r->route_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('transport.student-route.index') }}" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રીસેટ</a>
            </div>
        </form>

        @if($students->isNotEmpty())
        <form id="bulk-assign-form" class="border-t border-gray-100 pt-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">રૂટ <span class="text-red-500">*</span></label>
                    <select id="bulk-route" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                        <option value="">— પસંદ કરો —</option>
                        @foreach($routes as $r)
                        <option value="{{ $r->id }}">{{ $r->route_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">સ્ટોપ</label>
                    <select id="bulk-stop" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">— પસંદ કરો —</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700 transition flex items-center gap-2"><i class="lni lni-checkmark-circle text-sm"></i> પસંદ કરેલ વિદ્યાર્થીઓને સોંપો</button>
                </div>
            </div>
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 w-10"><input type="checkbox" id="select-all" class="rounded border-gray-300 text-amber-600"></th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 text-xs uppercase">GR નંબર</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 text-xs uppercase">નામ</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-600 text-xs uppercase">વર્ગ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-center"><input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="student-checkbox rounded border-gray-300 text-amber-600"></td>
                            <td class="px-3 py-2 font-medium">{{ $s->gr_number }}</td>
                            <td class="px-3 py-2">{{ $s->full_name_gu ?: $s->full_name_en }}</td>
                            <td class="px-3 py-2 text-center text-xs text-gray-500">{{ $s->currentStandard?->name }} - {{ $s->currentClass?->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        @elseif(request('standard_id') && request('class_id'))
        <div class="text-center py-6 text-gray-400 text-sm">આ વર્ગના બધા વિદ્યાર્થીઓને પહેલેથી રૂટ સોંપાયેલ છે.</div>
        @endif
    </div>

    {{-- Assigned Students List --}}
    @if($assignments->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">રૂટ સોંપણી યાદી</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">રૂટ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">GR નંબર</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">નામ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ધોરણ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">વર્ગ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">સ્ટોપ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ક્રિયા</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($assignments as $routeName => $items)
                    @foreach($items as $a)
                    <tr id="assign-row-{{ $a->id }}" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ $routeName }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $a->student?->gr_number ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->student?->full_name_gu ?? $a->student?->full_name_en ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $a->student?->currentStandard?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $a->student?->currentClass?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $a->stop?->stop_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="deleteAssign({{ $a->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const filterForm = document.getElementById('filter-form');

    // Auto-submit on filter change
    document.querySelectorAll('#filter-standard, #filter-class, #filter-route').forEach(el => {
        el.addEventListener('change', () => filterForm.submit());
    });

    // Bulk route change → load stops
    document.getElementById('bulk-route').addEventListener('change', function() {
        const routeId = this.value;
        const stopSelect = document.getElementById('bulk-stop');
        stopSelect.innerHTML = '<option value="">— પસંદ કરો —</option>';
        if (!routeId) return;
        fetch('/transport/stops/' + routeId, { headers: { Accept: 'application/json' } })
        .then(r => r.json()).then(data => {
            data.forEach(s => {
                const o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.stop_name;
                stopSelect.appendChild(o);
            });
        });
    });

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // Bulk assign
    document.getElementById('bulk-assign-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const checked = document.querySelectorAll('.student-checkbox:checked');
        if (checked.length === 0) { NexSchool.alert.warning('કૃપા કરીને વિદ્યાર્થીઓ પસંદ કરો'); return; }
        const routeId = document.getElementById('bulk-route').value;
        if (!routeId) { NexSchool.alert.warning('રૂટ પસંદ કરો'); return; }
        const studentIds = Array.from(checked).map(cb => cb.value);
        NexSchool.confirm.show('ખાતરી કરો', checked.length + ' વિદ્યાર્થીઓને રૂટ સોંપવા?', 'warning').then(c => {
            if (!c) return;
            fetch('/transport/assignments/bulk', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: JSON.stringify({
                student_ids: studentIds,
                route_id: routeId,
                stop_id: document.getElementById('bulk-stop').value || null,
            }) }).then(r => r.json()).then(d => {
                if (d.success) { NexSchool.alert.success(d.message); location.reload(); }
            }).catch(() => NexSchool.alert.danger('ભૂલ'));
        });
    });

    window.deleteAssign = function(id) {
        NexSchool.confirm.show('ખાતરી કરો', 'રૂટ સોંપણી કાઢી નાખવી?', 'danger').then(c => {
            if (!c) return;
            fetch('/transport/assignments/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } })
            .then(r => r.json()).then(d => {
                if (d.success) { NexSchool.alert.success(d.message); document.getElementById('assign-row-' + id).remove(); }
            });
        });
    };
})();
</script>
@endpush
@endsection
