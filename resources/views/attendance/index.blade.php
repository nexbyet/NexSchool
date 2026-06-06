@extends('layouts.app')
@section('title', 'હાજરી')
@section('content')
<div class="p-4 md:p-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-600 to-cyan-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">હાજરી</h1>
            <p class="text-teal-200 mt-1 text-sm">વિદ્યાર્થીઓની દૈનિક હાજરી નોંધો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="max-w-4xl mx-auto">

        {{-- Filter Form --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">તારીખ પસંદ કરો</h2>
            </div>
            <form id="attendance-form" class="p-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ધોરણ</label>
                        <select name="standard_id" id="f_standard_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="">— પસંદ કરો —</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">વર્ગ</label>
                        <select name="class_id" id="f_class_id" required disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="">— પહેલા ધોરણ પસંદ કરો —</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">તારીખ</label>
                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" id="loadBtn" class="px-5 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition flex items-center gap-2 shadow-sm">
                        <i class="lni lni-search-1 text-base"></i> વિદ્યાર્થીઓ બતાવો
                    </button>
                </div>
            </form>
        </div>

        {{-- Student List Container --}}
        <div id="studentsContainer"></div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('f_standard_id').addEventListener('change', function() {
    var stdId = this.value;
    var cls = document.getElementById('f_class_id');
    cls.innerHTML = '<option value="">— વર્ગ પસંદ કરો —</option>';
    cls.disabled = true;
    if (!stdId) return;
    fetch('{{ url("attendance/register/classes") }}/' + stdId, {
        headers: { 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.length) {
            data.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                cls.appendChild(opt);
            });
            cls.disabled = false;
            cls.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white';
        }
    })
    .catch(function() { NexSchool.alert.danger('વર્ગો લાવવામાં ભૂલ.'); });
});

document.getElementById('attendance-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('loadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 text-base animate-spin"></i> લોડ થાય છે...';
    document.getElementById('studentsContainer').innerHTML = '';

    var formData = new FormData(this);

    fetch('{{ route("attendance.students") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData,
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.error) {
            document.getElementById('studentsContainer').innerHTML = '<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center"><p class="text-amber-600 font-medium">' + res.error + '</p></div>';
        } else {
            document.getElementById('studentsContainer').innerHTML = res.students_html;
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-search-1 text-base"></i> વિદ્યાર્થીઓ બતાવો';
    })
    .catch(function() {
        NexSchool.alert.danger('વિદ્યાર્થીઓ લાવવામાં ભૂલ.');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-search-1 text-base"></i> વિદ્યાર્થીઓ બતાવો';
    });
});

// Status button styling (event delegation)
document.getElementById('studentsContainer').addEventListener('click', function(e) {
    var btn = e.target.closest('.status-btn');
    if (!btn) return;
    var row = btn.closest('.attendance-row');
    row.querySelectorAll('.status-btn').forEach(function(b) {
        b.className = 'status-btn px-1.5 py-1 rounded text-[10px] font-medium border transition-all duration-150 cursor-pointer text-gray-400 border-gray-200 hover:border-gray-300 hover:text-gray-600';
    });
    var st = btn.dataset.status;
    var colors = {
        present: 'text-emerald-700 bg-emerald-50 border-emerald-300 ring-1 ring-offset-1 ring-emerald-300',
        absent: 'text-red-700 bg-red-50 border-red-300 ring-1 ring-offset-1 ring-red-300',
        absent_with_leave: 'text-amber-700 bg-amber-50 border-amber-300 ring-1 ring-offset-1 ring-amber-300',
        medical_leave: 'text-blue-700 bg-blue-50 border-blue-300 ring-1 ring-offset-1 ring-blue-300',
    };
    btn.className = 'status-btn px-1.5 py-1 rounded text-[10px] font-medium border transition-all duration-150 cursor-pointer ' + (colors[st] || '');
});

// Save attendance
document.getElementById('studentsContainer').addEventListener('click', function(e) {
    if (!e.target.closest('#saveAttendance')) return;
    var btn = document.getElementById('saveAttendance');
    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 text-base animate-spin"></i> સેવ થાય છે...';

    var form = document.getElementById('attendance-form');
    var rows = document.querySelectorAll('.attendance-row');
    var attendances = [];

    rows.forEach(function(row) {
        var active = row.querySelector('.status-btn.ring-1');
        if (active) {
            attendances.push({
                student_id: row.dataset.studentId,
                status: active.dataset.status,
            });
        }
    });

    if (attendances.length === 0) {
        NexSchool.alert.warning('કોઈ હાજરી બદલાઈ નથી.');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-floppy-disk-1 text-base"></i> સેવ કરો';
        return;
    }

    fetch('{{ route("attendance.save") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            standard_id: form.querySelector('[name=standard_id]').value,
            class_id: form.querySelector('[name=class_id]').value,
            date: form.querySelector('[name=date]').value,
            attendances: attendances,
        }),
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.success) {
            NexSchool.alert.success(res.message);
        } else {
            NexSchool.alert.danger('સેવ કરવામાં ભૂલ.');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-floppy-disk-1 text-base"></i> સેવ કરો';
    })
    .catch(function() {
        NexSchool.alert.danger('સેવ કરવામાં ભૂલ.');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-floppy-disk-1 text-base"></i> સેવ કરો';
    });
});
</script>
@endpush

