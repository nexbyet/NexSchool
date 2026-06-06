@extends('layouts.app')
@section('title', 'સાઇટ સેટિંગ્સ')
@section('content')
<style>
    .favicon-preview { width: 32px; height: 32px; object-fit: contain; border-radius: 4px; border: 1px solid #e5e7eb; }
    .favicon-preview-lg { width: 64px; height: 64px; object-fit: contain; border-radius: 6px; border: 1px solid #e5e7eb; }
</style>
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-700 to-gray-900 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">સાઇટ સેટિંગ્સ</h1>
            <p class="text-gray-300 mt-1 text-sm">ફેવિકોન, ફૂટર ક્રેડિટ્સ અને કોપીરાઇટ સેટિંગ્સ</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Frontend Favicon --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-1">ફ્રન્ટએન્ડ ફેવિકોન</h3>
            <p class="text-xs text-gray-500 mb-4">વેબસાઇટ ફેવિકોન (32×32 px, ICO/PNG)</p>
            <div class="flex items-center gap-4">
                <div id="frontend-preview" class="flex-shrink-0">
                    @if($setting->favicon)
                    <img src="{{ asset('storage/'.$setting->favicon) }}" class="favicon-preview-lg">
                    @else
                    <div class="favicon-preview-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">—</div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg hover:border-gray-400 cursor-pointer transition text-center">
                        <i class="lni lni-upload text-sm mr-1"></i> ફેવિકોન અપલોડ કરો
                        <input type="file" accept="image/png,image/jpeg,image/gif,image/vnd.microsoft.icon,image/svg+xml" class="hidden" onchange="uploadFavicon(this, 'frontend')">
                    </label>
                    @if($setting->favicon)
                    <button onclick="deleteFavicon('frontend')" class="mt-2 text-xs text-red-500 hover:text-red-700 flex items-center gap-1"><i class="lni lni-trash-1 text-xs"></i> દૂર કરો</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Backend Favicon --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-1">બેકએન્ડ ફેવિકોન</h3>
            <p class="text-xs text-gray-500 mb-4">એડમિન પેનલ ફેવિકોન (32×32 px, ICO/PNG)</p>
            <div class="flex items-center gap-4">
                <div id="backend-preview" class="flex-shrink-0">
                    @if($setting->backend_favicon)
                    <img src="{{ asset('storage/'.$setting->backend_favicon) }}" class="favicon-preview-lg">
                    @else
                    <div class="favicon-preview-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">—</div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg hover:border-gray-400 cursor-pointer transition text-center">
                        <i class="lni lni-upload text-sm mr-1"></i> ફેવિકોન અપલોડ કરો
                        <input type="file" accept="image/png,image/jpeg,image/gif,image/vnd.microsoft.icon,image/svg+xml" class="hidden" onchange="uploadFavicon(this, 'backend')">
                    </label>
                    @if($setting->backend_favicon)
                    <button onclick="deleteFavicon('backend')" class="mt-2 text-xs text-red-500 hover:text-red-700 flex items-center gap-1"><i class="lni lni-trash-1 text-xs"></i> દૂર કરો</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Footer & Copyright --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">ફૂટર અને કોપીરાઇટ</h3>
        <form id="site-settings-form">
            @csrf
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ફૂટર ક્રેડિટ્સ / નોંધ</label>
                    <textarea name="footer_credits" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500 outline-none transition" placeholder="દા.ત. Powered by NexSchool | ડિઝાઇન અને ડેવલપમેન્ટ: XYZ">{{ $setting->footer_credits }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">ફ્રન્ટએન્ડ અને બેકએન્ડ બંનેમાં ફૂટરમાં દેખાશે.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">કોપીરાઇટ ટેક્સ્ટ</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="copyright_text" value="{{ $setting->copyright_text }}" id="copyright-input" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500 outline-none transition" placeholder="દા.ત. © {{ date('Y') }} NexSchool. All rights reserved." maxlength="500">
                        <button type="button" onclick="insertCopyrightSymbol()" class="px-3 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition" title="© સિમ્બોલ ઉમેરો">©</button>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="submit" id="save-btn" class="px-6 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition flex items-center gap-2 shadow-sm">
                    <i class="lni lni-save text-sm"></i> <span>સાચવો</span>
                </button>
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition">રદ કરો</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.uploadFavicon = function(input, type) {
        var file = input.files[0];
        if (!file) return;
        var formData = new FormData();
        formData.append('favicon', file);
        formData.append('type', type);
        formData.append('_token', csrfToken);

        fetch('{{ route("settings.site.favicon") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { NexSchool.alert.danger(data.message || 'ભૂલ'); return; }
            var preview = document.getElementById(type + '-preview');
            preview.innerHTML = '<img src="' + data.url + '?t=' + Date.now() + '" class="favicon-preview-lg">';
            preview.nextElementSibling.querySelector('button')?.remove();
            var btnWrap = preview.nextElementSibling;
            if (!btnWrap.querySelector('button')) {
                var delBtn = document.createElement('button');
                delBtn.className = 'mt-2 text-xs text-red-500 hover:text-red-700 flex items-center gap-1';
                delBtn.innerHTML = '<i class="lni lni-trash-1 text-xs"></i> દૂર કરો';
                delBtn.onclick = function() { deleteFavicon(type); };
                btnWrap.appendChild(delBtn);
            }
            NexSchool.alert.success(data.message);
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'અપલોડ નિષ્ફળ'); });
        input.value = '';
    };

    window.deleteFavicon = function(type) {
        NexSchool.confirm.show('ખાતરી કરો', 'ફેવિકોન દૂર કરવા માંગો છો?', 'danger', 'હા, દૂર કરો')
        .then(function(confirmed) {
            if (!confirmed) return;
            fetch('{{ route("settings.site.favicon.delete") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: type }),
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) { NexSchool.alert.danger(data.message); return; }
                var preview = document.getElementById(type + '-preview');
                preview.innerHTML = '<div class="favicon-preview-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">—</div>';
                var btnWrap = preview.nextElementSibling;
                var delBtn = btnWrap.querySelector('button');
                if (delBtn) delBtn.remove();
                NexSchool.alert.success(data.message);
            })
            .catch(function() { NexSchool.alert.danger('કંઈક ભૂલ થઈ'); });
        });
    };

    window.insertCopyrightSymbol = function() {
        var input = document.getElementById('copyright-input');
        var val = input.value;
        var sym = '\u00A9';
        if (val.includes(sym)) { NexSchool.alert.note('\u00A9 સિમ્બોલ પહેલેથી જ છે.'); return; }
        input.value = val ? sym + ' ' + val : sym + ' ' + new Date().getFullYear();
        input.focus();
    };

    document.getElementById('site-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var btn = document.getElementById('save-btn');
        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવી રહ્યા...';

        var formData = new FormData(form);

        fetch('{{ route("settings.site.update") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (!data.success) { NexSchool.alert.danger(data.message || 'ભૂલ'); return; }
            NexSchool.alert.success(data.message);
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સાચવવામાં ભૂલ'); })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });
})();
</script>
@endpush
