{{-- NexSchool - Academic Years Management --}}
@extends('layouts.app')
@section('title', 'શૈક્ષણિક વર્ષો')
@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">શૈક્ષણિક વર્ષો</h1>
            <p class="text-gray-500 mt-1 text-sm">શાળાના શૈક્ષણિક વર્ષોનું સંચાલન કરો</p>
        </div>
        <button onclick="openModal()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition flex items-center gap-2">
            <i class="lni lni-plus text-base"></i> નવું વર્ષ
        </button>
    </div>

    {{-- Stats --}}
    @php
        $totalYears = $years->count();
        $activeYear = $years->firstWhere('is_active', true);
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="lni lni-calendar-days text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ વર્ષો</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalYears }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="lni lni-check-circle-1 text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">ચાલુ વર્ષ</p>
                <p class="text-xl font-bold text-emerald-700">{{ $activeYear ? $activeYear->year : '—' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="lni lni-calendar-days text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">અન્ય વર્ષો</p>
                <p class="text-xl font-bold text-amber-700">{{ $totalYears - ($activeYear ? 1 : 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Year Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($years as $y)
            <div id="year-card-{{ $y->id }}" class="rounded-xl border-2 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300
                @if($y->is_active) border-emerald-400 bg-emerald-50/30 @else border-gray-200 bg-white @endif">
                {{-- Card header --}}
                <div class="px-5 py-4 flex items-center justify-between @if($y->is_active) bg-emerald-50 border-b border-emerald-200 @else bg-gray-50 border-b border-gray-200 @endif">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 @if($y->is_active) bg-emerald-100 @else bg-indigo-100 @endif rounded-lg flex items-center justify-center">
                            <i class="lni lni-calendar-days text-sm @if($y->is_active) text-emerald-600 @else text-indigo-600 @endif"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ $y->year }}</h3>
                    </div>
                    @if($y->is_active)
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 flex items-center gap-1">
                            <i class="lni lni-check-circle-1 text-xs"></i> ચાલુ
                        </span>
                    @endif
                </div>
                {{-- Card body --}}
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">શરૂ તારીખ</span>
                        <span class="font-medium text-gray-800">{{ $y->start_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">સમાપ્તિ તારીખ</span>
                        <span class="font-medium text-gray-800">{{ $y->end_date->format('d/m/Y') }}</span>
                    </div>
                    @if(!$y->is_active)
                        <button onclick="setActive({{ $y->id }}, '{{ $y->year }}')" class="w-full mt-2 py-2 border-2 border-dashed border-indigo-200 rounded-lg text-xs font-medium text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 transition">
                            <i class="lni lni-check-circle-1 text-sm mr-1"></i> આ વર્ષ સક્રિય કરો
                        </button>
                    @endif
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button onclick="editYear({{ $y->id }})" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-sm" title="સુધારો">
                            <i class="lni lni-pencil-1"></i>
                        </button>
                        <button onclick="deleteYear({{ $y->id }}, '{{ $y->year }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition text-sm" title="કાઢો">
                            <i class="lni lni-trash-3"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="lni lni-calendar-days text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">હજી સુધી કોઈ શૈક્ષણિક વર્ષ ઉમેરાયું નથી</p>
                <button onclick="openModal()" class="mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">નવું વર્ષ ઉમેરો</button>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal (unchanged) --}}
<div id="year-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવું શૈક્ષણિક વર્ષ</h3>
        <form id="year-form">
            <input type="hidden" id="year-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <input type="text" id="year-input" placeholder="દા.ત. 2025-26" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શરૂ તારીખ <span class="text-red-500">*</span></label>
                    <input type="date" id="start-date-input" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સમાપ્તિ તારીખ <span class="text-red-500">*</span></label>
                    <input type="date" id="end-date-input" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">વેકેશન પૂરી થઈને શાળા ખૂલવાની તારીખ</label>
                    <input type="date" id="session-start-input" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    <p class="text-xs text-gray-500 mt-1">આ તારીખથી હાજરી ગણતરી શરૂ થશે. ખાલી રાખો તો <code>start_date</code> વપરાશે.</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="modal-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('year-modal');
    const form = document.getElementById('year-form');
    const modalTitle = document.getElementById('modal-title');
    const yearId = document.getElementById('year-id');
    const yearInput = document.getElementById('year-input');
    const startInput = document.getElementById('start-date-input');
    const endInput = document.getElementById('end-date-input');
    const sessionInput = document.getElementById('session-start-input');
    const submitBtn = document.getElementById('modal-submit-btn');

    function openModal(data = null) {
        modal.classList.remove('hidden');
        requestAnimationFrame(() => modal.style.opacity = '1');
        if (data) {
            modalTitle.textContent = 'વર્ષ સુધારો';
            yearId.value = data.id;
            yearInput.value = data.year;
            startInput.value = data.start_date;
            endInput.value = data.end_date;
            sessionInput.value = data.session_start_date;
        } else {
            modalTitle.textContent = 'નવું શૈક્ષણિક વર્ષ';
            yearId.value = '';
            yearInput.value = '';
            startInput.value = '';
            endInput.value = '';
            sessionInput.value = '';
        }
    }

    function closeModal() {
        modal.style.opacity = '0';
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = 'સાચવાઈ રહ્યું છે...';
        const isEdit = !!yearId.value;
        const url = isEdit ? '{{ url('academic-years') }}/' + yearId.value : '{{ route('academic-years.store') }}';
        const method = isEdit ? 'PUT' : 'POST';
        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({
                _token: '{{ csrf_token() }}', _method: method,
                year: yearInput.value, start_date: startInput.value, end_date: endInput.value, session_start_date: sessionInput.value,
            }),
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); closeModal(); location.reload(); }
            else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
        })
        .catch(() => NexSchool.alert.danger('સર્વર ભૂલ — ફરી પ્રયાસ કરો'))
        .finally(() => { submitBtn.disabled = false; submitBtn.textContent = 'સાચવો'; });
    });

    function editYear(id) {
        fetch('{{ url('academic-years') }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(res => res.json()).then(data => openModal(data))
        .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
    }

    function deleteYear(id, year) {
        NexSchool.confirm.show('વર્ષ કાઢી નાખો', 'શું તમે ' + year + ' કાઢી નાખવા માંગો છો?', 'danger')
        .then(() => {
            fetch('{{ url('academic-years') }}/' + id, {
                method: 'POST',
                body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
                headers: { 'Accept': 'application/json' },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) { NexSchool.alert.success(data.message); document.getElementById('year-card-' + id).remove(); }
                else { NexSchool.alert.danger(data.message); }
            })
            .catch(() => NexSchool.alert.danger('કાઢવામાં ભૂલ.'));
        }).catch(() => {});
    }

    function setActive(id, year) {
        fetch('{{ url('academic-years') }}/' + id + '/active', {
            method: 'POST',
            body: new URLSearchParams({ _token: '{{ csrf_token() }}' }),
            headers: { 'Accept': 'application/json' },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); location.reload(); }
            else { NexSchool.alert.danger(data.message); }
        })
        .catch(() => NexSchool.alert.danger('ભૂલ આવી.'));
    }
</script>
@endpush
