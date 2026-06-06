@extends('layouts.app')
@section('title', 'શિક્ષક Import')
@section('content')
<div class="p-4 md:p-6">
    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">શિક્ષક Import</h1>
                <p class="text-emerald-200 mt-1 text-sm">Excel થી જથ્થાબંધ નોંધણી</p>
            </div>
            <a href="{{ route('teachers.index') }}" class="px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition backdrop-blur-sm flex items-center gap-2">
                <i class="lni lni-arrow-left text-sm"></i> શિક્ષક યાદી પર પાછા જાઓ
            </a>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Instructions --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6 shadow-sm">
        <h2 class="font-semibold text-amber-800 mb-3 flex items-center gap-2">
            <i class="lni lni-ban-2 text-lg"></i>
            Excel Import — મહત્વની સૂચનાઓ
        </h2>
        <ol class="list-decimal list-inside space-y-1.5 text-sm text-amber-900">
            <li><strong>Demo Excel ડાઉનલોડ કરો</strong> — નીચે આપેલ "Demo Excel ડાઉનલોડ કરો" બટન ઉપર ક્લિક કરીને ફોર્મેટ સમજો.</li>
            <li><strong>હેડર હરોળ (header row)</strong> નહીં બદલો — કૉલમના નામો (name, email, વગેરે) એકદમ એ જ રાખો.</li>
            <li><strong>ફરજિયાત કૉલમ:</strong> name, email. બાકીના વૈકલ્પિક છે. email unique હોવો જોઈએ.</li>
            <li><strong>તારીખ ફોર્મેટ:</strong> બધી તારીખો <code class="bg-amber-200 px-1 rounded">dd/mm/yyyy</code> ફોર્મેટમાં જ લખો (દા.ત. <code>15/08/1985</code>). <span class="text-red-600 font-semibold">તારીખમાં ડૅશ (-) અથવા ડોટ (.) ન વાપરો.</span></li>
            <li><strong>જાતિ (gender):</strong> ફક્ત <code class="bg-amber-200 px-1 rounded">male</code> અથવા <code class="bg-amber-200 px-1 rounded">female</code> લખો.</li>
            <li><strong>સ્થિતિ (status):</strong> <code class="bg-amber-200 px-1 rounded">active</code> અથવા <code class="bg-amber-200 px-1 rounded">inactive</code>. inactive હોય તો <code class="bg-amber-200 px-1 rounded">reason_inactive</code> અને <code class="bg-amber-200 px-1 rounded">date_inactive</code> પણ ભરવા.</li>
            <li><strong>teacher_id (ID):</strong> આપમેળે <code class="bg-amber-200 px-1 rounded">TEA001</code>, <code class="bg-amber-200 px-1 rounded">TEA002</code> ... જનરેટ થશે. Excel માં teacher_id કૉલમ નથી.</li>
            <li><strong>વપરાશકર્તા (User) આપમેળે બનશે:</strong> દરેક શિક્ષક માટે email = username અને <code class="bg-amber-200 px-1 rounded">Teacher@123</code> = password વડે teacher યુઝર બનશે.</li>
            <li><strong>પરિણામ:</strong> Import પૂર્ણ થયા પછી કેટલા શિક્ષક ઉમેરાયા અને કેટલા અવગણાયા તેનો રિપોર્ટ જોવા મળશે.</li>
        </ol>
    </div>

    {{-- Available Data Reference --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 mb-6 shadow-sm">
        <h2 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
            <i class="lni lni-database-2 text-lg"></i>
            Excel ભરતી વખતે આ મૂલ્યો જ વાપરો
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white/60 rounded-lg p-3">
                <h3 class="font-medium text-blue-700 mb-1.5 flex items-center gap-1"><i class="lni lni-user-4 text-xs"></i> જાતિ (Gender)</h3>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">male</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">female</span>
                </div>
            </div>
            <div class="bg-white/60 rounded-lg p-3">
                <h3 class="font-medium text-blue-700 mb-1.5 flex items-center gap-1"><i class="lni lni-check-circle-1 text-xs"></i> સ્થિતિ (Status)</h3>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">active</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">inactive</span>
                </div>
            </div>
            <div class="bg-white/60 rounded-lg p-3">
                <h3 class="font-medium text-blue-700 mb-1.5 flex items-center gap-1"><i class="lni lni-water-drop-1 text-xs"></i> બ્લડ ગ્રુપ (Blood Group)</h3>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">A+</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">A-</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">B+</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">B-</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">AB+</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">AB-</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">O+</span>
                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono">O-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Download Demo + Upload Form --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Download Demo --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                    <i class="lni lni-download-1 text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800">1. Demo Excel ડાઉનલોડ કરો</h2>
                    <p class="text-xs text-gray-500">નમૂના Excel શીટ ડાઉનલોડ કરો</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">નમૂના Excel શીટ ડાઉનલોડ કરો, તેમાં તમારો ડેટા ભરો, અને પછી Import કરો.</p>
            <a href="{{ route('teachers.import.demo') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all text-sm font-medium shadow-sm">
                <i class="lni lni-download-1 text-lg"></i>
                Demo Excel ડાઉનલોડ કરો
            </a>
        </div>

        {{-- Upload Form --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center">
                    <i class="lni lni-upload-1 text-indigo-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800">2. Excel ફાઇલ અપલોડ કરો</h2>
                    <p class="text-xs text-gray-500">ભરેલી Excel ફાઇલ પસંદ કરો</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">ભરેલી Excel ફાઇલ (.xlsx) પસંદ કરો અને Import શરૂ કરો.</p>
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Excel ફાઇલ પસંદ કરો</label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls" required
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                </div>
                <button type="submit" id="importBtn" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-lg hover:from-indigo-600 hover:to-blue-700 transition-all text-sm font-medium shadow-sm">
                    <i class="lni lni-upload-1 text-lg"></i>
                    Import શરૂ કરો
                </button>
            </form>
        </div>
    </div>

    {{-- Results --}}
    <div id="importResults" class="mt-6 hidden"></div>

    {{-- Loading Spinner --}}
    <div id="importLoading" class="hidden mt-6 text-center">
        <div class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-gray-200 rounded-xl shadow-lg">
            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-gray-700 font-medium">Import પ્રોસેસ થઈ રહ્યો છે... કૃપા કરી રાહ જુઓ...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('file');
    if (!fileInput.files.length) { NexSchool.alert.warning('કૃપા કરી Excel ફાઇલ પસંદ કરો.'); return; }
    const formData = new FormData(this);
    const btn = document.getElementById('importBtn');
    const loading = document.getElementById('importLoading');
    const results = document.getElementById('importResults');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Import થઈ રહ્યું છે...';
    loading.classList.remove('hidden');
    results.classList.add('hidden');
    fetch('{{ route("teachers.import") }}', {
        method: 'POST', body: formData, headers: { 'Accept': 'application/json' },
    })
    .then(res => res.json())
    .then(data => {
        loading.classList.add('hidden');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-upload-1 text-lg"></i> Import શરૂ કરો';
        results.classList.remove('hidden');
        if (data.success) {
            let html = `<div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                        <i class="lni lni-check-circle-1 text-emerald-600 text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-800">Import પૂર્ણ!</h3>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-700">${data.imported}</p>
                        <p class="text-sm text-emerald-600 flex items-center justify-center gap-1"><i class="lni lni-check-circle-1 text-xs"></i> શિક્ષક ઉમેરાયા</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-rose-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-red-700">${data.skipped}</p>
                        <p class="text-sm text-red-600 flex items-center justify-center gap-1"><i class="lni lni-ban-2 text-xs"></i> અવગણાયા</p>
                    </div>
                </div>`;
            if (data.errors && data.errors.length) {
                html += `<div class="mt-4">
                    <h4 class="font-medium text-gray-700 mb-2 flex items-center gap-1"><i class="lni lni-ban-2 text-amber-500"></i> ભૂલો / ચેતવણીઓ:</h4>
                    <div class="max-h-48 overflow-y-auto bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800 space-y-1">`;
                data.errors.forEach(err => { html += `<div class="flex items-start gap-2"><i class="lni lni-ban-2 text-red-500 mt-0.5"></i><span>${err}</span></div>`; });
                html += `</div></div>`;
            }
            html += `<div class="mt-4 flex gap-3">
                <a href="${'{{ route("teachers.index") }}'}" class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium"><i class="lni lni-arrow-left"></i> શિક્ષક યાદી જુઓ</a>
                <button onclick="location.reload()" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium"><i class="lni lni-refresh-circle-1-clockwise"></i> નવો Import કરો</button>
            </div></div>`;
            results.innerHTML = html;
            NexSchool.alert.success('Import પૂર્ણ! ' + data.imported + ' શિક્ષક ઉમેરાયા.');
        } else {
            results.innerHTML = `<div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-100 to-rose-100 flex items-center justify-center">
                        <i class="lni lni-ban-2 text-red-600 text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-lg text-red-800">Import નિષ્ફળ</h3>
                </div>
                <p class="text-red-700">${data.message || 'કૃપા કરી ફરી પ્રયાસ કરો.'}</p>
            </div>`;
            NexSchool.alert.danger('Import નિષ્ફળ: ' + (data.message || ''));
        }
    })
    .catch(err => {
        loading.classList.add('hidden');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-upload-1 text-lg"></i> Import શરૂ કરો';
        results.classList.remove('hidden');
        results.innerHTML = `<div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-100 to-rose-100 flex items-center justify-center">
                    <i class="lni lni-ban-2 text-red-600 text-lg"></i>
                </div>
                <h3 class="font-semibold text-lg text-red-800">સર્વર ભૂલ</h3>
            </div>
            <p class="text-red-700">સર્વર સાથે કનેક્શન સમસ્યા. કૃપા કરી ફરી પ્રયાસ કરો.</p>
        </div>`;
        NexSchool.alert.danger('સર્વર ભૂલ: ' + err.message);
    });
});
</script>
@endpush
