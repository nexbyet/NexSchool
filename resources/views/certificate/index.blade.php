@extends('layouts.app')
@section('title', 'બોનાફાઈડ પ્રમાણપત્ર')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-600 to-emerald-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">બોનાફાઈડ પ્રમાણપત્ર</h1>
            <p class="text-teal-200 mt-1 text-sm">Bonafied Certificate — વિદ્યાર્થી શોધો અને પ્રિન્ટ કરો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Search Panel --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Search by GR Number --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="lni lni-search-1 text-teal-600"></i>
                        GR નંબર શોધો
                    </h3>
                </div>
                <div class="p-4">
                    <input type="text" id="grSearch" placeholder="GR નંબર લખો..." autocomplete="off" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none transition">
                    <div id="grResults" class="mt-2 space-y-1 max-h-64 overflow-y-auto"></div>
                </div>
            </div>

            {{-- OR Divider --}}
            <div class="flex items-center gap-3">
                <hr class="flex-1 border-gray-200">
                <span class="text-xs font-medium text-gray-400">અથવા</span>
                <hr class="flex-1 border-gray-200">
            </div>

            {{-- Filter by Standard + Class --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="lni lni-funnel text-teal-600"></i>
                        ધોરણ અને વર્ગ પસંદ કરો
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <select id="f_standard_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        <option value="">— પસંદ કરો —</option>
                        @foreach($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                    <select id="f_class_id" disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 focus:ring-2 focus:ring-teal-500 outline-none">
                        <option value="">— પહેલા ધોરણ પસંદ કરો —</option>
                    </select>
                    <button id="classSearchBtn" disabled class="w-full px-4 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="lni lni-search-1"></i> વિદ્યાર્થીઓ બતાવો
                    </button>
                </div>
            </div>
        </div>

        {{-- Results Panel --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900" id="resultsTitle">વિદ્યાર્થીઓ</h3>
                    <div id="langToggle" class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5 hidden">
                        <button data-lang="gu" class="px-3 py-1 text-xs font-medium rounded-md bg-white text-gray-800 shadow-sm transition">ગુજરાતી</button>
                        <button data-lang="en" class="px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-800 transition">English</button>
                    </div>
                </div>
                <div id="resultsContent" class="p-5">
                    <div class="text-center py-12">
                        <i class="lni lni-search-1 text-4xl text-gray-300"></i>
                        <p class="text-gray-500 mt-2 text-sm">ઉપરથી GR નંબર દાખલ કરો અથવા ધોરણ-વર્ગ પસંદ કરો</p>
                    </div>
                </div>
            </div>

            {{-- Certificate Preview --}}
            <div id="previewSection" class="hidden mt-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">પ્રિવ્યુ &amp; પ્રિન્ટ</h3>
                        <button id="printBtn" class="px-4 py-1.5 text-xs font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                            <i class="lni lni-printer"></i> પ્રિન્ટ કરો
                        </button>
                    </div>
                    <div id="previewContent" class="p-5"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var studentId = null, currentLang = 'gu';
    var $ = function(id) { return document.getElementById(id); };
    var grInput = $('grSearch'), grResults = $('grResults');
    var stdSelect = $('f_standard_id'), clsSelect = $('f_class_id'), clsBtn = $('classSearchBtn');
    var resultsContent = $('resultsContent'), resultsTitle = $('resultsTitle');
    var langToggle = $('langToggle'), previewSection = $('previewSection');
    var previewContent = $('previewContent'), printBtn = $('printBtn');

    if (!grInput || !stdSelect) return;

    var grTimer;
    grInput.addEventListener('input', function() {
        clearTimeout(grTimer);
        var val = this.value.trim();
        if (val.length < 1) { grResults.innerHTML = ''; return; }
        grTimer = setTimeout(function() {
            fetch('{{ route("certificates.search-by-gr") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ gr_number: val })
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.students || !res.students.length) {
                    grResults.innerHTML = '<p class="text-xs text-gray-400 text-center py-3">કોઈ વિદ્યાર્થી મળ્યો નહીં</p>';
                    return;
                }
                var html = '';
                for (var i = 0; i < res.students.length; i++) {
                    var s = res.students[i];
                    var initial = (s.gr_number && s.gr_number.length > 0) ? s.gr_number.charAt(0) : '?';
                    var img = s.photo
                        ? '<img src="' + s.photo + '" class="w-8 h-8 rounded-full object-cover">'
                        : '<div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">' + initial + '</div>';
                    html += '<div class="gr-item px-3 py-2 rounded-lg cursor-pointer hover:bg-teal-50 border border-gray-100 transition" data-id="' + s.id + '">'
                        + '<div class="flex items-center gap-3">' + img
                        + '<div><p class="text-sm font-medium text-gray-800">' + (s.name_gu || '') + '</p>'
                        + '<p class="text-xs text-gray-500">GR: ' + (s.gr_number || '—') + ' | ' + (s.standard || '') + ' - ' + (s.class || '') + '</p></div></div></div>';
                }
                grResults.innerHTML = html;
                var items = grResults.querySelectorAll('.gr-item');
                for (var i = 0; i < items.length; i++) {
                    items[i].addEventListener('click', function() {
                        selectStudent(parseInt(this.getAttribute('data-id')));
                        grInput.value = '';
                        grResults.innerHTML = '';
                    });
                }
            })
            .catch(function() { grResults.innerHTML = '<p class="text-xs text-red-400 text-center py-3">શોધમાં ભૂલ</p>'; });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#grSearch') && !e.target.closest('#grResults')) grResults.innerHTML = '';
    });

    stdSelect.addEventListener('change', function() {
        var id = this.value;
        clsSelect.innerHTML = '<option value="">— પસંદ કરો —</option>';
        clsSelect.disabled = true;
        clsBtn.disabled = true;
        if (!id) return;
        fetch('{{ url("attendance/register/classes") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].id; opt.textContent = data[i].name;
                clsSelect.appendChild(opt);
            }
            clsSelect.disabled = false;
            clsSelect.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white';
        })
        .catch(function() {});
    });

    clsSelect.addEventListener('change', function() { clsBtn.disabled = !this.value; });

    clsBtn.addEventListener('click', function() {
        var stdId = stdSelect.value, clsId = clsSelect.value;
        if (!stdId || !clsId) return;
        langToggle.className = langToggle.className.replace('hidden', '').trim();
        resultsTitle.textContent = 'વિદ્યાર્થીઓ — પસંદ કરો';
        resultsContent.innerHTML = '<div class="text-center py-8"><i class="lni lni-spinner-3 text-2xl text-teal-600 animate-spin inline-block"></i></div>';
        fetch('{{ route("certificates.search-by-class") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ standard_id: stdId, class_id: clsId })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            resultsContent.innerHTML = res.html || '<p class="text-center text-gray-500 py-8">કોઈ વિદ્યાર્થી મળ્યો નહીં</p>';
            var btns = resultsContent.querySelectorAll('.student-select-btn');
            for (var i = 0; i < btns.length; i++) {
                btns[i].addEventListener('click', function() {
                    selectStudent(parseInt(this.getAttribute('data-id')));
                });
            }
        })
        .catch(function() { resultsContent.innerHTML = '<p class="text-center text-red-500 py-8">શોધમાં ભૂલ</p>'; });
    });

    langToggle.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-lang]');
        if (!btn) return;
        currentLang = btn.getAttribute('data-lang');
        var btns = this.querySelectorAll('button');
        for (var i = 0; i < btns.length; i++) {
            btns[i].className = btns[i].getAttribute('data-lang') === currentLang
                ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-gray-800 shadow-sm transition'
                : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-800 transition';
        }
        if (studentId) loadPreview(studentId, currentLang);
    });

    function selectStudent(id) {
        studentId = id;
        langToggle.className = langToggle.className.replace('hidden', '').trim();
        resultsTitle.textContent = 'વિદ્યાર્થી પસંદ થયો';
        previewSection.classList.remove('hidden');
        previewContent.innerHTML = '<div class="text-center py-8"><i class="lni lni-spinner-3 text-2xl text-teal-600 animate-spin inline-block"></i></div>';
        loadPreview(id, currentLang);
    }

    function loadPreview(id, lang) {
        printBtn.disabled = true;
        fetch('{{ url("certificates/bonafied/preview") }}/' + id + '/' + lang, { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.html) { previewContent.innerHTML = res.html; printBtn.disabled = false; }
        })
        .catch(function() { previewContent.innerHTML = '<p class="text-center text-red-500 py-4">પ્રિવ્યુ લાવવામાં ભૂલ</p>'; });
    }

    printBtn.addEventListener('click', function() {
        if (studentId) window.open('{{ url("certificates/bonafied/print") }}/' + studentId + '/' + currentLang, '_blank');
    });
})();
</script>
@endpush

