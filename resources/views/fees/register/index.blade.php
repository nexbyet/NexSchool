@extends('layouts.app')
@section('title', 'ફી રજિસ્ટર')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-600 to-orange-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">ફી રજિસ્ટર</h1>
            <p class="text-amber-200 mt-1 text-sm">પ્રિન્ટેબલ ફી રજિસ્ટર — સર્વર બંધ હોય ત્યારે મેન્યુઅલી ફી ભરવા માટે</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="GET" action="{{ route('fees.register.print') }}" target="_blank" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <select name="academic_year_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">— પસંદ કરો —</option>
                        @foreach($academicYears as $y)
                        <option value="{{ $y->id }}" @if($y->is_active) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ધોરણ <span class="text-red-500">*</span></label>
                    <select name="standard_id" id="standard-select" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">— પસંદ કરો —</option>
                        @foreach($standards as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">વર્ગ <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class-select" required disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition bg-gray-100">
                        <option value="">— પહેલા ધોરણ પસંદ કરો —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">સત્ર</label>
                    <select name="semester" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">— બંને સત્ર —</option>
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ભાષા</label>
                    <select name="lang" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="gu">ગુજરાતી</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg focus:ring-4 focus:ring-amber-200 transition flex items-center gap-2 shadow-sm">
                    <i class="lni lni-printer text-base"></i> રજિસ્ટર જનરેટ કરો
                </button>
                <button type="reset" class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">સાફ કરો</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('standard-select').addEventListener('change', function() {
    const stdId = this.value;
    const classSelect = document.getElementById('class-select');
    if (!stdId) {
        classSelect.innerHTML = '<option value="">— પહેલા ધોરણ પસંદ કરો —</option>';
        classSelect.disabled = true;
        classSelect.classList.add('bg-gray-100');
        return;
    }
    classSelect.innerHTML = '<option value="">— લોડ થાય છે... —</option>';
    classSelect.disabled = true;
    classSelect.classList.remove('bg-gray-100');
    fetch('/attendance/register/classes/' + stdId)
        .then(r => r.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">— પસંદ કરો —</option>';
            data.forEach(c => {
                classSelect.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>';
            });
            classSelect.disabled = false;
        })
        .catch(() => {
            classSelect.innerHTML = '<option value="">— ભૂલ —</option>';
            classSelect.disabled = false;
        });
});
</script>
@endpush
@endsection
