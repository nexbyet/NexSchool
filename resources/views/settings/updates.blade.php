@extends('layouts.app')
@section('title', 'અપડેટ્સ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">સિસ્ટમ અપડેટ્સ</h1>
            <p class="text-emerald-200 mt-1 text-sm">GitHub રિલીઝ દ્વારા નવા વર્ઝન ચેક કરો અને અપડેટ કરો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <div class="text-5xl mb-2 text-emerald-500"><i class="lni lni-code-alt"></i></div>
            <div class="text-sm text-gray-500">વર્તમાન વર્ઝન</div>
            <div class="text-2xl font-bold text-gray-800 font-mono">v{{ $currentVersion }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center" id="latest-version-card">
            <div class="text-5xl mb-2 text-gray-300"><i class="lni lni-cloud"></i></div>
            <div class="text-sm text-gray-500">નવું વર્ઝન</div>
            <div class="text-2xl font-bold text-gray-400 font-mono" id="latest-version-text">—</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col items-center justify-center">
            <button onclick="checkUpdate()" id="check-btn" class="px-6 py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <i class="lni lni-refresh text-sm"></i> અપડેટ ચેક કરો
            </button>
            <button onclick="runUpdate()" id="update-btn" class="px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 shadow-sm mt-3 hidden">
                <i class="lni lni-download text-sm"></i> અપડેટ કરો
            </button>
        </div>
    </div>

    <div id="update-result" class="hidden"></div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">છેલ્લા અપડેટ્સ</h3>
        <div class="text-sm text-gray-500">હજી સુધી કોઈ અપડેટ લોગ નથી.</div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-2">અપડેટ સિસ્ટમ કેવી રીતે કામ કરે છે</h3>
        <div class="text-sm text-gray-600 space-y-2">
            <p><strong>nexbyet/NexSchool</strong> — પબ્લિક રીપો, કોઈ Token કે સેટિંગની જરૂર નથી.</p>
            <ul class="list-disc list-inside space-y-1.5">
                <li>GitHub Release ચેક કરે છે → નવું વર્ઝન મળે → ZIP ડાઉનલોડ કરે છે → એક્સટ્રેક્ટ કરે છે → ફાઇલો બદલે છે → માઇગ્રેશન ચલાવે છે → કેશ ક્લિયર કરે છે → વર્ઝન અપડેટ કરે છે</li>
                <li>દરેક વર્ઝન માટે <strong>GitHub Release</strong> બનાવો (tag: <code class="px-1.5 py-0.5 bg-gray-100 rounded text-xs font-mono">v1.1.0</code>)</li>
                <li>માત્ર tag પૂરતો છે — GitHub auto-generated ZIP નો ઉપયોગ થાય છે</li>
                <li>મોટા અપડેટ માટે (નવી માઇગ્રેશન + ફાઇલ ડિલીટ), ZIP સાથે <code class="px-1.5 py-0.5 bg-gray-100 rounded text-xs font-mono">update.json</code> ઉમેરો:</li>
            </ul>
            <pre class="bg-gray-900 text-gray-100 p-3 rounded-lg text-xs mt-2 overflow-x-auto">{
  "version": "1.1.0",
  "requires": "1.0.0",
  "migrations": ["2026_07_01_xxx.php"],
  "delete_files": ["old/unused.php"]
}</pre>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkUpdate() {
    var btn = document.getElementById('check-btn');
    var result = document.getElementById('update-result');
    btn.disabled = true; btn.innerHTML = '<i class="lni lni-spinner-2 text-sm animate-spin"></i> ચેક કરી રહ્યા...';
    result.className = 'hidden';

    fetch('{{ route("settings.updates.check") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json' },
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (!d.success) {
            result.className = 'bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 mb-6';
            result.innerHTML = '<i class="lni lni-warning mr-1"></i> ' + d.message;
            btn.disabled = false; btn.innerHTML = '<i class="lni lni-refresh text-sm"></i> અપડેટ ચેક કરો';
            return;
        }

        document.getElementById('latest-version-card').querySelector('.text-5xl').className = 'text-5xl mb-2 ' + (d.update_available ? 'text-amber-500' : 'text-emerald-500');
        document.getElementById('latest-version-card').querySelector('.text-5xl').innerHTML = d.update_available ? '<i class="lni lni-download"></i>' : '<i class="lni lni-checkmark-circle"></i>';
        document.getElementById('latest-version-text').textContent = 'v' + d.latest_version;
        document.getElementById('latest-version-text').className = 'text-2xl font-bold font-mono ' + (d.update_available ? 'text-amber-600' : 'text-emerald-600');

        if (d.update_available) {
            result.className = 'bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 mb-6';
            result.innerHTML = '<strong class="block mb-2">📦 v' + d.latest_version + ' ઉપલબ્ધ છે!</strong>' + (d.changelog ? '<div class="whitespace-pre-line">' + d.changelog + '</div>' : '');
            document.getElementById('update-btn').classList.remove('hidden');
        } else {
            result.className = 'bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700 mb-6';
            result.innerHTML = '<i class="lni lni-checkmark-circle mr-1"></i> તમે નવીનતમ વર્ઝન પર છો!';
            document.getElementById('update-btn').classList.add('hidden');
        }

        btn.disabled = false; btn.innerHTML = '<i class="lni lni-refresh text-sm"></i> ફરી ચેક કરો';
    })
    .catch(function() {
        result.className = 'bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 mb-6';
        result.innerHTML = '<i class="lni lni-warning mr-1"></i> સર્વર ભૂલ. ફરી પ્રયાસ કરો.';
        btn.disabled = false; btn.innerHTML = '<i class="lni lni-refresh text-sm"></i> અપડેટ ચેક કરો';
    });
}

function runUpdate() {
    if (!confirm('અપડેટ ઇન્સ્ટોલ કરવાથી સિસ્ટમ થોડી સેકંડ માટે બંધ રહેશે. ચાલુ રાખવું?')) return;
    var btn = document.getElementById('update-btn');
    btn.disabled = true; btn.innerHTML = '<i class="lni lni-spinner-2 text-sm animate-spin"></i> અપડેટ કરી રહ્યા...';

    fetch('{{ route("settings.updates.run") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json' },
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) { location.reload(); }
        else { alert(d.message || 'અપડેટ નિષ્ફળ'); btn.disabled = false; btn.innerHTML = '<i class="lni lni-download text-sm"></i> અપડેટ કરો'; }
    });
}
</script>
@endpush
@endsection
