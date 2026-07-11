@extends('layouts.app')
@section('title', 'બસ હાજરી')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-rose-600 to-pink-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">બસ હાજરી</h1>
            <p class="text-rose-200 mt-1 text-sm">વિદ્યાર્થીઓની બસ હાજરી નોંધો (આવક અને જાવક)</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <form method="GET" action="{{ route('transport.bus-attendance.index') }}" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">રૂટ</label>
                <select name="route_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="">— પસંદ કરો —</option>
                    @foreach($routes as $r)
                    <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>{{ $r->route_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">તારીખ</label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">પત્રક પ્રકાર</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="blank" {{ request('type') === 'blank' ? 'selected' : '' }}>ખાલી પત્રક</option>
                    <option value="filled" {{ request('type') === 'filled' ? 'selected' : '' }}>ભરેલું પત્રક</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('transport.bus-attendance.index') }}" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રીસેટ</a>
            </div>
        </form>
    </div>

    {{-- Attendance Table --}}
    @if($students->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                {{ $routeName ?? '' }} —
                {{ request('date') ? \Carbon\Carbon::parse(request('date'))->format('d/m/Y') : '' }}
            </h3>
            @if(request('route_id') && request('date'))
            @php
                $printDate = \Carbon\Carbon::parse(request('date'));
                $printParams = array_merge(request()->except(['date', 'page']), [
                    'month' => $printDate->month,
                    'year' => $printDate->year,
                ]);
            @endphp
            <a href="{{ route('transport.bus-attendance.print', $printParams) }}" target="_blank" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-semibold hover:bg-rose-700 transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> માસિક પ્રિન્ટ</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ક્રમ</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase">GR</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-600 text-xs uppercase">નામ</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase" style="min-width:40px">પ્રકાર</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase" style="min-width:60px">આવક (Morning)</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase" style="min-width:60px">જાવક (Evening)</th>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs uppercase">નોંધ</th>
                    </tr>
                </thead>
                <tbody id="attendance-tbody" class="divide-y divide-gray-100">
                    @foreach($students as $index => $s)
                    @php
                        $att = $attendances->get($s['display_id']);
                        $typeBadge = match($s['type']) {
                            'regular' => '<span class="inline-flex px-1.5 py-0.5 bg-blue-50 rounded text-[10px] font-medium text-blue-700">શાળા</span>',
                            'unregistered' => '<span class="inline-flex px-1.5 py-0.5 bg-amber-50 rounded text-[10px] font-medium text-amber-700">અનબોર્ડ</span>',
                            'bus_only' => '<span class="inline-flex px-1.5 py-0.5 bg-teal-50 rounded text-[10px] font-medium text-teal-700">બસ</span>',
                            default => '',
                        };
                    @endphp
                    <tr id="att-row-{{ $s['id'] }}" class="hover:bg-gray-50 transition">
                        <td class="px-3 py-2.5 text-gray-500 text-xs">{{ $index + 1 }}</td>
                        <td class="px-3 py-2.5 font-medium text-xs">{{ $s['gr_number'] }}</td>
                        <td class="px-3 py-2.5 flex items-center gap-1">{{ $s['name'] }}</td>
                        <td class="px-3 py-2.5 text-center">{!! $typeBadge !!}</td>
                        <td class="px-3 py-2.5 text-center">
                            <select class="morning-select w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs" data-student-id="{{ $s['id'] }}" data-student-type="{{ $s['model'] }}" data-shift="morning">
                                <option value="">—</option>
                                <option value="present" {{ $att && $att->morning_status === 'present' ? 'selected' : '' }}>✅ હાજર</option>
                                <option value="absent" {{ $att && $att->morning_status === 'absent' ? 'selected' : '' }}>❌ ગેરહાજર</option>
                                <option value="leave" {{ $att && $att->morning_status === 'leave' ? 'selected' : '' }}>📋 રજા</option>
                            </select>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <select class="evening-select w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs" data-student-id="{{ $s['id'] }}" data-student-type="{{ $s['model'] }}" data-shift="evening">
                                <option value="">—</option>
                                <option value="present" {{ $att && $att->evening_status === 'present' ? 'selected' : '' }}>✅ હાજર</option>
                                <option value="absent" {{ $att && $att->evening_status === 'absent' ? 'selected' : '' }}>❌ ગેરહાજર</option>
                                <option value="leave" {{ $att && $att->evening_status === 'leave' ? 'selected' : '' }}>📋 રજા</option>
                            </select>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <input type="text" class="notes-input w-20 px-2 py-1.5 border border-gray-300 rounded-lg text-xs" data-student-id="{{ $s['id'] }}" data-student-type="{{ $s['model'] }}" value="{{ $att->notes ?? '' }}" placeholder="નોંધ">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(request('route_id'))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <i class="lni lni-users-2 text-5xl text-gray-300 mb-3 block"></i>
        <p class="text-gray-500 font-medium">આ રૂટ પર કોઈ વિદ્યાર્થી સોંપાયેલ નથી</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const routeId = "{{ request('route_id') }}";
    const date = "{{ request('date', date('Y-m-d')) }}";

    function markAttendance(el) {
        if (!routeId || !date) return;
        const studentId = el.dataset.studentId;
        const studentType = el.dataset.studentType;
        const shift = el.dataset.shift;
        const field = shift === 'morning' ? 'morning_status' : 'evening_status';
        const body = {
            student_type: studentType,
            route_id: routeId,
            date: date,
        };
        if (studentType === 'bus_only') {
            body.bus_only_student_id = studentId;
        } else {
            body.student_id = studentId;
        }
        body[field] = el.value || null;

        fetch('/transport/attendance/mark', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            body: JSON.stringify(body)
        }).then(r => r.json()).then(d => {
            if (!d.success) NexSchool.alert.danger('ભૂલ');
        }).catch(() => {});
    }

    document.querySelectorAll('.morning-select, .evening-select').forEach(el => {
        el.addEventListener('change', function() { markAttendance(this); });
    });

    document.querySelectorAll('.notes-input').forEach(el => {
        el.addEventListener('blur', function() { markAttendance(this); });
    });
})();
</script>
@endpush
@endsection
