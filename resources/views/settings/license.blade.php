@extends('layouts.app')
@section('title', 'લાઇસન્સ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-700 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">લાઇસન્સ વ્યવસ્થાપન</h1>
            <p class="text-violet-200 mt-1 text-sm">તમારા લાઇસન્સની સ્થિતિ તપાસો અને સક્રિય કરો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
            <div class="text-3xl mb-2">
                @if(!$setting || !$setting->license_key || $setting->license_status === 'unlicensed')
                    <i class="lni lni-shield-2 text-gray-300 text-5xl"></i>
                @elseif($setting->license_status === 'active')
                    <i class="lni lni-shield-2-check text-emerald-500 text-5xl"></i>
                @else
                    <i class="lni lni-shield-2 text-red-400 text-5xl"></i>
                @endif
            </div>
            <div class="text-lg font-bold">
                @if(!$setting || !$setting->license_key || $setting->license_status === 'unlicensed')
                    <span class="text-gray-400">અનલાઇસન્સ્ડ</span>
                @elseif($setting->license_status === 'active')
                    <span class="text-emerald-600">સક્રિય</span>
                @elseif($setting->license_status === 'expired')
                    <span class="text-red-600">મુદત સમાપ્ત</span>
                @elseif($setting->license_status === 'revoked')
                    <span class="text-red-600">રિવોક</span>
                @else
                    <span class="text-amber-600">{{ $setting->license_status }}</span>
                @endif
            </div>
            @if($setting && $setting->licensee_name)
            <div class="text-sm text-gray-500 mt-1">{{ $setting->licensee_name }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">ડોમેન</div>
            <div class="text-sm font-bold text-gray-900 font-mono">{{ request()->getHost() }}</div>
            @if($setting && $setting->licensed_until)
            <div class="text-sm font-medium text-gray-500 mt-3 mb-1">મુદત સુધી</div>
            <div class="text-sm font-bold text-gray-900">{{ $setting->licensed_until }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">છેલ્લો પિંગ</div>
            <div class="text-sm font-bold text-gray-900">{{ $setting->last_license_ping ?? '—' }}</div>
            <div class="text-sm font-medium text-gray-500 mt-3 mb-1">આવૃત્તિ</div>
            <div class="text-sm font-bold text-gray-900">{{ config('app.version', '1.0.0') }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">
            @if($setting && $setting->license_key) લાઇસન્સ નવીકરણ @else લાઇસન્સ સક્રિય કરો @endif
        </h3>
        <form id="license-form">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">લાઇસન્સ કી દાખલ કરો</label>
                <textarea name="license_key" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition" placeholder="લાઇસન્સ કી અહીં પેસ્ટ કરો...">{{ $setting->license_key ?? '' }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" id="activate-btn" class="px-6 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition flex items-center gap-2 shadow-sm">
                    <i class="lni lni-shield-2-check text-sm"></i> <span>સક્રિય કરો</span>
                </button>
                @if($setting && $setting->license_key)
                <button type="button" onclick="deactivateLicense()" class="px-4 py-2.5 text-sm font-medium text-red-600 hover:text-red-800 transition flex items-center gap-1">
                    <i class="lni lni-trash-1 text-sm"></i> દૂર કરો
                </button>
                @endif
            </div>
        </form>
        <div id="license-result" class="mt-3"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-2">માહિતી</h3>
        <ul class="text-sm text-gray-600 space-y-1.5">
            <li>• લાઇસન્સ સક્રિય કર્યા પછી તમામ એડમિન પેજીસ પર કામ કરશે.</li>
            <li>• દર ૨૪ કલાકે સિસ્ટમ આપમેળે લાઇસન્સ સર્વર સાથે ચકાસણી કરે છે.</li>
            <li>• જો લાઇસન્સ રિવોક થાય તો તમને આ પેજ પર રીડાઇરેક્ટ કરવામાં આવશે.</li>
            <li>• કોઈ સમસ્યા હોય તો અમારો સંપર્ક કરો.</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.getElementById('license-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('activate-btn');
        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સક્રિય કરી રહ્યા...';
        var result = document.getElementById('license-result');

        var formData = new FormData(this);

        fetch('{{ route("settings.license.activate") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) {
                result.innerHTML = '<div class="alert alert-danger mt-2">' + (data.message || 'ભૂલ') + '</div>';
                return;
            }
            result.innerHTML = '<div class="alert alert-success mt-2"><strong>✅ ' + data.message + '</strong></div>';
            setTimeout(function() { location.reload(); }, 1500);
        })
        .catch(function(err) { result.innerHTML = '<div class="alert alert-danger mt-2">' + (err.message || 'સર્વર ભૂલ') + '</div>'; })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });

    window.deactivateLicense = function() {
        NexSchool.confirm.show('ખાતરી કરો', 'લાઇસન્સ દૂર કરવાથી સિસ્ટમ અનલાઇસન્સ્ડ થઈ જશે. ચાલુ રાખવું?', 'danger', 'હા, દૂર કરો')
        .then(function(confirmed) {
            if (!confirmed) return;
            var result = document.getElementById('license-result');
            fetch('{{ route("settings.license.deactivate") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) { result.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>'; return; }
                setTimeout(function() { location.reload(); }, 1000);
            });
        });
    };
})();
</script>
@endpush
