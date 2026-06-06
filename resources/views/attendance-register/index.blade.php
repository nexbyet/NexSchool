@extends('layouts.app')
@section('title', 'હાજરી પત્રક')
@section('content')
<div class="p-4 md:p-6">

    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-rose-600 to-pink-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">હાજરી પત્રક</h1>
            <p class="text-rose-200 mt-1 text-sm">ધોરણ, વર્ગ અને મહિના પ્રમાણે હાજરી પત્રક જનરેટ કરો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Form card --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">હાજરી પત્રક વિગતો</h2>
                <p class="text-sm text-gray-500 mt-0.5">પ્રિન્ટ કરવા માટે નીચેની માહિતી ભરો</p>
            </div>
            <form id="attendance-form" method="POST" action="{{ route('attendance-register.print') }}" target="_blank" class="p-6 space-y-5">
                @csrf

                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">શૈક્ષણિક વર્ષ <span class="text-red-500">*</span></label>
                    <select name="academic_year_id" id="academic_year_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition text-sm bg-white">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" @selected($ay->id == ($activeYear?->id))>{{ $ay->year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Standard --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ધોરણ <span class="text-red-500">*</span></label>
                    <select name="standard_id" id="standard_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition text-sm bg-white">
                        <option value="">-- ધોરણ પસંદ કરો --</option>
                        @foreach($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">વર્ગ <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" required disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition text-sm bg-gray-100">
                        <option value="">-- પહેલા ધોરણ પસંદ કરો --</option>
                    </select>
                </div>

                {{-- Month & Year --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">મહિનો <span class="text-red-500">*</span></label>
                        <select name="month" id="month" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition text-sm bg-white">
                            <option value="">-- મહિનો --</option>
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" @selected($num == now()->month)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">વર્ષ <span class="text-red-500">*</span></label>
                        <select name="year" id="year" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition text-sm bg-white">
                            <option value="">-- વર્ષ --</option>
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" @selected($y == now()->year)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Language --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ભાષા</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="lang" value="gu" checked class="text-rose-600 focus:ring-rose-500">
                            <span class="text-sm text-gray-700">ગુજરાતી</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="lang" value="en" class="text-rose-600 focus:ring-rose-500">
                            <span class="text-sm text-gray-700">English</span>
                        </label>
                    </div>
                </div>

                {{-- Register Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">પત્રક પ્રકાર <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="blank" checked class="text-rose-600 focus:ring-rose-500">
                            <span class="text-sm text-gray-700">ખાલી પત્રક</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="filled" class="text-rose-600 focus:ring-rose-500">
                            <span class="text-sm text-gray-700">ભરેલું પત્રક</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">ખાલી = હાથ વડે ભરવા માટે &nbsp;·&nbsp; ભરેલું = પોર્ટલ પર ભરેલ હાજરી સાથે</p>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" id="generateBtn" class="px-6 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg focus:ring-4 focus:ring-rose-200 transition flex items-center gap-2 shadow-sm">
                        <i class="lni lni-printer text-base"></i> જનરેટ કરો
                    </button>
                    <button type="reset" class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">સાફ કરો</button>
                </div>
            </form>
        </div>

        {{-- Instructions --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="lni lni-bulb-2 text-rose-600 text-lg"></i>
                <h3 class="font-semibold text-gray-900">સૂચનાઓ</h3>
            </div>
            <div class="px-6 py-4 text-sm text-gray-600 space-y-2">
                <p>📌 ધોરણ, વર્ગ, મહિનો અને વર્ષ પસંદ કરી "જનરેટ કરો" બટન દબાવો.</p>
                <p>📌 પ્રિન્ટ વ્યૂ નવી ટેબમાં ખુલશે. ત્યાં "પ્રિન્ટ કરો" બટન દબાવો.</p>
                <p>📌 ફક્ત સક્રિય (Active) વિદ્યાર્થીઓ જ પત્રકમાં દેખાશે.</p>
                <p>📌 "ખાલી પત્રક" = હાથ વડે ભરવા માટે &nbsp;·&nbsp; "ભરેલું પત્રક" = પોર્ટલ પર ભરેલ હાજરી સાથે.</p>
                <p>📌 જે વિદ્યાર્થીઓએ પસંદ કરેલ મહિનાની ૧ તારીખ પહેલા શાળા છોડી દીધી હોય તેઓ પત્રકમાં નહીં દેખાય.</p>
                <p>📌 રવિવાર અને જાહેર રજાના દિવસો આપોઆપ "રજા" તરીકે ચિહ્નિત થશે.</p>
                <p>📌 પ્રિન્ટ પેજ સાઇઝ: લીગલ (Legal) — લેન્ડસ્કેપ ઓરિયન્ટેશન.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('standard_id').addEventListener('change', function() {
    var stdId = this.value;
    var classSelect = document.getElementById('class_id');
    classSelect.innerHTML = '<option value="">-- વર્ગ પસંદ કરો --</option>';
    classSelect.disabled = true;
    if (!stdId) return;
    fetch('{{ url("attendance/register/classes") }}/' + stdId, {
        headers: { 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.length) {
            data.forEach(function(cls) {
                var opt = document.createElement('option');
                opt.value = cls.id;
                opt.textContent = cls.name;
                classSelect.appendChild(opt);
            });
            classSelect.disabled = false;
            classSelect.className = 'w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition text-sm bg-white';
        }
    })
    .catch(function() { NexSchool.alert.danger('વર્ગો લાવવામાં ભૂલ.'); });
});

document.getElementById('attendance-form').addEventListener('submit', function(e) {
    var btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 text-base animate-spin"></i> જનરેટ થાય છે...';
    // Allow default form submission (opens in new tab)
    setTimeout(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-printer text-base"></i> જનરેટ કરો';
    }, 2000);
});
</script>
@endpush
