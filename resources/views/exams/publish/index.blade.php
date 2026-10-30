@extends('layouts.app')
@section('title', 'પરિણામ પ્રકાશન')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">પરિણામ પ્રકાશન</h1>
            <p class="text-amber-100 mt-1 text-sm">એડમિન: પરિણામ પ્રકાશિત/અપ્રકાશિત કરો - વિદ્યાર્થીઓ frontsite પર GR + DOB થી જોઈ શકે છે.</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                <select id="p-year" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg">
                    @foreach (\App\Models\AcademicYear::orderBy('year','desc')->get() as $y)
                        <option value="{{ $y->id }}">{{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ (Ctrl+click = multiple)</label>
                <select id="p-standard" multiple class="w-full px-3 py-2.5 border border-gray-300 rounded-lg h-32">
                    <option value="all">બધા ધોરણ</option>
                    @foreach (\App\Models\Standard::orderBy('sort_order')->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">વર્ગ (Ctrl+click = multiple)</label>
                <select id="p-class" multiple class="w-full px-3 py-2.5 border border-gray-300 rounded-lg h-32">
                    <option value="all">બધા વર્ગ</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                <select id="p-semester" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg">
                    <option value="both">બંને (વાર્ષિક)</option>
                    <option value="1">સત્ર 1</option>
                    <option value="2">સત્ર 2</option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-4">
            <button onclick="doPublish(true)" style="background-color:#059669;color:#ffffff;font-weight:500;padding:10px 20px;border-radius:8px;border:none;cursor:pointer;" class="text-sm">પ્રકાશિત</button>
            <button onclick="doPublish(false)" class="px-5 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">અપ્રકાશિત</button>
        </div>
        <div id="p-status" class="hidden mt-4 rounded-xl px-4 py-3 text-sm"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-base font-bold text-gray-900 mb-3">પ્રકાશિત પરિણામો</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-amber-50 to-orange-50">
                    <tr>
                        <th class="px-3 py-2.5 text-left font-semibold text-gray-700">વર્ષ</th>
                        <th class="px-3 py-2.5 text-left font-semibold text-gray-700">ધોરણ</th>
                        <th class="px-3 py-2.5 text-left font-semibold text-gray-700">વર્ગ</th>
                        <th class="px-3 py-2.5 text-left font-semibold text-gray-700">સત્ર</th>
                        <th class="px-3 py-2.5 text-left font-semibold text-gray-700">પ્રકાશન તારીખ</th>
                        <th class="px-3 py-2.5 text-center font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($publications as $pub)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2.5">{{ $pub->academicYear->year ?? '' }}</td>
                            <td class="px-3 py-2.5">{{ $pub->standard->name ?? '—' }}</td>
                            <td class="px-3 py-2.5">{{ $pub->schoolClass->name ?? '—' }}</td>
                            <td class="px-3 py-2.5">{{ $pub->semester === 'both' ? 'બંને' : 'સત્ર '.$pub->semester }}</td>
                            <td class="px-3 py-2.5 text-xs text-gray-500">{{ $pub->published_at }}</td>
                            <td class="px-3 py-2.5 text-center">
                                <button onclick="unpublishOne({{ $pub->academic_year_id }}, {{ $pub->standard_id }}, {{ $pub->school_class_id }}, '{{ $pub->semester }}')"
                                    class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition">
                                    અપ્રકાશિત
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">કોઈ પ્રકાશિત પરિણામ નથી.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var stdSelect = document.getElementById('p-standard');
    var clsSelect = document.getElementById('p-class');

    function selectedValues(sel) {
        return Array.from(sel.selectedOptions).map(function (o) { return o.value; });
    }

    function loadClasses() {
        var vals = selectedValues(stdSelect);
        clsSelect.innerHTML = '<option value="all">બધા વર્ગ</option>';
        if (vals.indexOf('all') !== -1 || vals.length === 0) return;
        // fetch classes for each selected standard
        vals.forEach(function (sid) {
            fetch('{{ url('exams/publish/classes') }}/' + sid, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    data.forEach(function (c) {
                        var opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name + ' (' + (c.standard_name || sid) + ')';
                        clsSelect.appendChild(opt);
                    });
                });
        });
    }

    stdSelect.addEventListener('change', loadClasses);

    function unpublishOne(yearId, stdId, classId, semester) {
        if (!confirm('આ result અપ્રકાશિત karvu chhe?')) return;
        var box = document.getElementById('p-status');
        box.classList.remove('hidden');
        box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-blue-50 border border-blue-200 text-blue-700';
        box.innerHTML = '<i class="lni lni-spinner-3 mr-1 animate-spin"></i> પ્રોસેસિંગ...';

        var payload = {
            academic_year_id: yearId,
            standard_id: stdId,
            school_class_id: classId,
            semester: semester,
        };

        fetch('{{ route("exams.publish.unpublish") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (d.success) {
                box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-emerald-50 border border-emerald-200 text-emerald-700';
                box.innerHTML = '<i class="lni lni-check-circle-1 mr-1"></i> ' + d.message;
                setTimeout(function () { location.reload(); }, 1000);
            } else {
                box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-700';
                box.innerHTML = '<i class="lni lni-ban-2 mr-1"></i> ' + (d.message || 'Error');
            }
        })
        .catch(function (err) {
            box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-700';
            box.innerHTML = '<i class="lni lni-ban-2 mr-1"></i> ' + (err.message || 'Error');
        });
    }

    function doPublish(isPublish) {
        var box = document.getElementById('p-status');
        box.classList.remove('hidden');
        box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-blue-50 border border-blue-200 text-blue-700';
        box.innerHTML = '<i class="lni lni-spinner-3 mr-1 animate-spin"></i> પ્રોસેસિંગ...';

        var stdVals = selectedValues(stdSelect);
        var clsVals = selectedValues(clsSelect);
        var payload = {
            academic_year_id: document.getElementById('p-year').value,
            standard_id: stdVals.indexOf('all') !== -1 ? 'all' : stdVals.join(','),
            school_class_id: clsVals.indexOf('all') !== -1 || clsVals.length === 0 ? 'all' : clsVals.join(','),
            semester: document.getElementById('p-semester').value,
        };

        fetch(isPublish ? '{{ route("exams.publish.publish") }}' : '{{ route("exams.publish.unpublish") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (d.success) {
                box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-emerald-50 border border-emerald-200 text-emerald-700';
                box.innerHTML = '<i class="lni lni-check-circle-1 mr-1"></i> ' + d.message;
                setTimeout(function () { location.reload(); }, 1200);
            } else {
                box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-700';
                box.innerHTML = '<i class="lni lni-ban-2 mr-1"></i> ' + (d.message || 'Error');
            }
        })
        .catch(function (err) {
            box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-700';
            box.innerHTML = '<i class="lni lni-ban-2 mr-1"></i> ' + (err.message || 'Error');
        });
    }
</script>
@endpush