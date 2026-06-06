@extends('layouts.app')
@section('title', 'ફ્રન્ટસાઇટ થીમ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-500 to-purple-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">ફ્રન્ટસાઇટ થીમ</h1>
            <p class="text-violet-200 mt-1 text-sm">વેબસાઇટના રંગો કસ્ટમાઇઝ કરો — ફેરફાર નીચે લાઇવ પ્રીવ્યુમાં જોઇ શકાય છે</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Color Form --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <form id="theme-form">
                @csrf
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">પ્રાથમિક રંગ</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary" value="{{ $theme['primary'] }}" class="theme-input w-10 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" class="theme-hex flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none" value="{{ $theme['primary'] }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">પ્રાથમિક (હોવર)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_hover" value="{{ $theme['primary_hover'] }}" class="theme-input w-10 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" class="theme-hex flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none" value="{{ $theme['primary_hover'] }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">હેડર ગ્રેડિઅન્ટ (થી)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="header_from" value="{{ $theme['header_from'] }}" class="theme-input w-10 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" class="theme-hex flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none" value="{{ $theme['header_from'] }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">હેડર ગ્રેડિઅન્ટ (સુધી)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="header_to" value="{{ $theme['header_to'] }}" class="theme-input w-10 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" class="theme-hex flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none" value="{{ $theme['header_to'] }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ફુટર બેકગ્રાઉન્ડ</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="footer_bg" value="{{ $theme['footer_bg'] }}" class="theme-input w-10 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" class="theme-hex flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none" value="{{ $theme['footer_bg'] }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">એક્સેન્ટ રંગ</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="accent" value="{{ $theme['accent'] }}" class="theme-input w-10 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" class="theme-hex flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none" value="{{ $theme['accent'] }}">
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" id="theme-submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition flex items-center gap-2 text-sm">
                        <i class="lni lni-save text-sm"></i> સાચવો
                    </button>
                </div>
            </form>
        </div>

        {{-- Live Preview --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">લાઇવ પ્રીવ્યુ</h3>
                <span class="text-[10px] text-gray-400">તમે રંગ બદલતા જ અહીં દેખાશે</span>
            </div>
            <div class="p-5">
                <div id="theme-preview" style="font-family: 'Anek Gujarati', sans-serif;">
                    {{-- Preview Header --}}
                    <div id="preview-header" class="rounded-t-lg px-4 py-3 text-white text-sm font-bold" style="background: linear-gradient(135deg, {{ $theme['header_from'] }}, {{ $theme['header_to'] }});">શાળાનું નામ</div>
                    {{-- Preview Body --}}
                    <div class="border-x border-gray-200 p-4 space-y-4" style="background: #fff;">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg" style="background: {{ $theme['primary'] }};"></div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">અમારા વિશે</p>
                                <div class="w-12 h-0.5 rounded-full mt-1" style="background: {{ $theme['primary'] }};"></div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-8 h-8 rounded-lg" style="background: rgba({{ implode(',', sscanf(ltrim($theme['primary'], '#'), '%2x%2x%2x') ?: [5,150,105]) }}, 0.12);"><div class="w-full h-full flex items-center justify-center text-xs" style="color: {{ $theme['primary'] }};"><i class="lni lni-star-fat text-xs"></i></div></div>
                            <div class="w-8 h-8 rounded-lg" style="background: rgba({{ implode(',', sscanf(ltrim($theme['primary'], '#'), '%2x%2x%2x') ?: [5,150,105]) }}, 0.12);"><div class="w-full h-full flex items-center justify-center text-xs" style="color: {{ $theme['primary'] }};"><i class="lni lni-star-fat text-xs"></i></div></div>
                            <div class="w-8 h-8 rounded-lg" style="background: rgba({{ implode(',', sscanf(ltrim($theme['primary'], '#'), '%2x%2x%2x') ?: [5,150,105]) }}, 0.12);"><div class="w-full h-full flex items-center justify-center text-xs" style="color: {{ $theme['primary'] }};"><i class="lni lni-star-fat text-xs"></i></div></div>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-xs px-3 py-1.5 rounded-lg text-white font-medium" style="background: {{ $theme['primary'] }};">બટન</span>
                            <span class="text-xs px-3 py-1.5 rounded-lg text-white font-medium" style="background: {{ $theme['accent'] }};">નોટિસ</span>
                        </div>
                    </div>
                    {{-- Preview Footer --}}
                    <div id="preview-footer" class="rounded-b-lg px-4 py-3 text-xs" style="background: {{ $theme['footer_bg'] }}; color: #9ca3af;">© શાળા — સર્વ અધિકાર સુરક્ષિત</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var inputs = document.querySelectorAll('.theme-input, .theme-hex');
    var previewHeader = document.getElementById('preview-header');
    var previewFooter = document.getElementById('preview-footer');

    function hexToRgb(hex) {
        hex = hex.replace('#','');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        var r = parseInt(hex.substring(0,2), 16);
        var g = parseInt(hex.substring(2,4), 16);
        var b = parseInt(hex.substring(4,6), 16);
        return r+','+g+','+b;
    }

    function hexColor(name) {
        var inp = document.querySelector('[name="'+name+'"]');
        if (inp) return inp.value;
        return '#000000';
    }

    function updatePreview() {
        var p = hexColor('primary');
        var hf = hexColor('header_from');
        var ht = hexColor('header_to');
        var fb = hexColor('footer_bg');
        var a = hexColor('accent');
        var pRgb = hexToRgb(p);
        if (previewHeader) previewHeader.style.background = 'linear-gradient(135deg, '+hf+', '+ht+')';
        if (previewFooter) previewFooter.style.background = fb;
        var els = document.querySelectorAll('#theme-preview [style*="'+p.replace('#','')+'"]');
        document.querySelectorAll('#theme-preview .preview-primary-bg').forEach(function(el){ el.style.background = p; });
        document.querySelectorAll('#theme-preview .preview-primary-bg').forEach(function(el){ el.style.background = p; });
    }

    document.querySelectorAll('.theme-hex').forEach(function(inp){
        inp.addEventListener('input', function(){
            var prev = this.previousElementSibling;
            if (prev && prev.type === 'color') prev.value = this.value;
            updatePreview();
        });
    });
    document.querySelectorAll('.theme-input').forEach(function(cp){
        cp.addEventListener('input', function(){
            var next = this.nextElementSibling;
            if (next && next.classList.contains('theme-hex')) next.value = this.value;
            updatePreview();
        });
    });

    document.getElementById('theme-form').addEventListener('submit', function(e){
        e.preventDefault();
        var btn = document.getElementById('theme-submit'); btn.disabled = true; btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સેવ થાય છે...';
        var fd = new FormData(this);
        fetch('{{ route("settings.theme.update") }}', { method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}, body:fd })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if(d.success){ NexSchool.alert.success(d.message); } else NexSchool.alert.danger(d.message || 'ભૂલ');
        }).catch(function(){ NexSchool.alert.danger('ભૂલ'); })
        .finally(function(){ btn.disabled=false; btn.innerHTML = '<i class="lni lni-save text-sm"></i> સાચવો'; });
    });
})();
</script>
@endpush
