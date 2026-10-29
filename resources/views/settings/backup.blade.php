@extends('layouts.app')
@section('title', 'ડેટાબેઝ બેકઅપ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">ડેટાબેઝ બેકઅપ</h1>
            <p class="text-blue-200 mt-1 text-sm">આખો ડેટાબેઝ SQL ફાઇલમાં ડાઉનલોડ કરો — સર્વર બંધ થાય તો પણ ડેટા સુરક્ષિત રહે</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <div class="text-5xl mb-2 text-blue-500"><i class="lni lni-database-2"></i></div>
            <div class="text-sm text-gray-500">ડેટાબેઝ</div>
            <div class="text-lg font-bold text-gray-800 font-mono break-all">{{ $dbName ?: '—' }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <div class="text-5xl mb-2 text-indigo-500"><i class="lni lni-code-1"></i></div>
            <div class="text-sm text-gray-500">ડ્રાઇવર</div>
            <div class="text-lg font-bold text-gray-800 font-mono uppercase">{{ $driver ?: '—' }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <div class="text-5xl mb-2 text-emerald-500"><i class="lni lni-file-multiple"></i></div>
            <div class="text-sm text-gray-500">ફાઇલ પ્રકાર</div>
            <div class="text-lg font-bold text-gray-800">SQL (.sql)</div>
        </div>
    </div>

    @if(!$isMysql)
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 mb-6">
        <i class="lni lni-ban-2 mr-1"></i> આ સુવિધા ફક્ત MySQL / MariaDB ડેટાબેઝ માટે ઉપલબ્ધ છે. હાલનો ડ્રાઇવર: <strong>{{ $driver }}</strong>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">SQL બેકઅપ ડાઉનલોડ કરો</h2>
                <p class="text-sm text-gray-500 mt-1">બધા ટેબલ, સ્ટ્રક્ચર અને ડેટા એક જ <code class="px-1.5 py-0.5 bg-gray-100 rounded text-xs">.sql</code> ફાઇલમાં મળશે.</p>
            </div>
            <button id="backup-btn" onclick="downloadBackup()" @if(!$isMysql) disabled @endif
                class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="lni lni-download-1 text-sm"></i> બેકઅપ ડાઉનલોડ કરો
            </button>
        </div>

        <div id="backup-status" class="hidden mt-4 rounded-xl px-4 py-3 text-sm"></div>

        <p class="text-[11px] text-gray-400 mt-3">
            મોટા ડેટાબેઝમાં થોડી સેકંડ લાગી શકે — ડાઉનલોડ પૂરું ન થાય ત્યાં સુધી પેજ બંધ ન કરો.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2"><i class="lni lni-shield-2 text-amber-500"></i> સલામતી સૂચના</h3>
            <ul class="text-sm text-gray-600 space-y-2 list-disc pl-5">
                <li>બેકઅપ ફાઇલમાં શાળાનો <strong>આખો ડેટા</strong> હોય છે — તેને સુરક્ષિત જગ્યાએ (Google Drive, pen drive કે બીજા કમ્પ્યુટરમાં) સાચવો.</li>
                <li>ફાઇલ કોઈને શેર ન કરો; તેમાં વિદ્યાર્થી/શાળાની માહિતી હોય છે.</li>
                <li>નિયમિતપણે (અઠવાડિયે કે મહિને) બેકઅપ લેતા રહો.</li>
                <li>આ ફાઇલ ફક્ત ડેટાબેઝની છે — લોગો/ફોટા જેવી અપલોડ ફાઇલો અલગથી સાચવવી પડશે.</li>
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2"><i class="lni lni-refresh-circle-1-clockwise text-blue-500"></i> રીસ્ટોર કેવી રીતે કરવું</h3>
            <ol class="text-sm text-gray-600 space-y-2 list-decimal pl-5">
                <li><strong>phpMyAdmin</strong> ખોલો → ડેટાબેઝ પસંદ કરો → <strong>Import</strong> ટેબ.</li>
                <li>ડાઉનલોડ કરેલી <code class="px-1.5 py-0.5 bg-gray-100 rounded text-xs">.sql</code> ફાઇલ પસંદ કરો → <strong>Go</strong> દબાવો.</li>
                <li>અથવા કમાન્ડ લાઇન: <code class="px-1.5 py-0.5 bg-gray-100 rounded text-xs break-all">mysql -u USER -p DB_NAME &lt; backup.sql</code></li>
            </ol>
            <p class="text-[11px] text-red-500 mt-3">⚠️ રીસ્ટોર કરતાં પહેલાં વર્તમાન ડેટાનો પણ બેકઅપ લઈ લો — import કરવાથી જૂનું ડેટા બદલાઈ જશે.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function downloadBackup() {
    var btn = document.getElementById('backup-btn');
    var box = document.getElementById('backup-status');

    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> બેકઅપ બની રહ્યું છે...';
    box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-blue-50 border border-blue-200 text-blue-700';
    box.classList.remove('hidden');
    box.innerHTML = '<i class="lni lni-spinner-3 mr-1 animate-spin"></i> ડેટાબેઝ વાંચીને SQL ફાઇલ બનાવી રહ્યા છીએ...';

    fetch('{{ route("settings.backup.download") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function(res) {
        var type = res.headers.get('content-type') || '';

        // JSON = server-side error; application/sql = actual dump.
        // Anything else (HTML login/419 page) = session expired / error.
        if (!res.ok || type.indexOf('application/json') !== -1) {
            return res.json().then(function(d) {
                throw new Error(d.message || 'બેકઅપ બનાવી શકાયું નહીં.');
            }).catch(function(err) {
                if (err instanceof Error) throw err;
                throw new Error('બેકઅપ બનાવી શકાયું નહીં.');
            });
        }

        if (type.indexOf('application/sql') === -1) {
            throw new Error('બેકઅપ ડાઉનલોડ નિષ્ફળ — સેશન સમાપ્ત થયું હોઈ શકે. ફરી લોગિન કરો.');
        }

        var disposition = res.headers.get('content-disposition') || '';
        var match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
        var filename = match ? decodeURIComponent(match[1]) : 'nexschool-backup.sql';

        return res.blob().then(function(blob) {
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function() { URL.revokeObjectURL(url); }, 1000);

            box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-emerald-50 border border-emerald-200 text-emerald-700';
            box.innerHTML = '<i class="lni lni-check-circle-1 mr-1"></i> બેકઅપ ડાઉનલોડ થયું: <strong>' + filename + '</strong> — ફાઇલ સુરક્ષિત જગ્યાએ સાચવો.';
        });
    })
    .catch(function(err) {
        box.className = 'mt-4 rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-700';
        box.innerHTML = '<i class="lni lni-ban-2 mr-1"></i> ' + (err.message || 'બેકઅપ નિષ્ફળ ગયું.');
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-download-1 text-sm"></i> બેકઅપ ડાઉનલોડ કરો';
    });
}
</script>
@endpush
