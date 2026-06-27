@extends('layouts.app')

@section('title', 'કસ્ટમ રિપોર્ટ')

@push('styles')
<style>
.drag-field {
    cursor: grab;
    user-select: none;
    transition: all 0.15s ease;
}
.drag-field:active { cursor: grabbing; }
.drag-field:hover { border-color: #6366f1; background-color: #eef2ff; }
.drag-field.dragging { opacity: 0.5; }
.drop-zone {
    min-height: 120px;
    border: 2px dashed #d1d5db;
    border-radius: 0.75rem;
    padding: 0.5rem;
    transition: all 0.2s ease;
}
.drop-zone.drag-over { border-color: #6366f1; background-color: #eef2ff; }
.drop-zone .empty-msg { color: #9ca3af; text-align: center; padding: 2rem 1rem; }
.column-slot {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin: 0.25rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    cursor: move;
    transition: all 0.15s ease;
}
.column-slot:hover { border-color: #6366f1; background-color: #eef2ff; }
.column-slot .remove-btn {
    cursor: pointer;
    color: #ef4444;
    font-size: 1rem;
    line-height: 1;
    opacity: 0.6;
    transition: opacity 0.15s;
}
.column-slot .remove-btn:hover { opacity: 1; }
.sortable-chosen { border-color: #6366f1; background: #eef2ff; box-shadow: 0 4px 12px rgba(99,102,241,0.15); }
.sortable-ghost { border: 2px dashed #6366f1; background: #eef2ff; opacity: 0.6; }
.field-group-toggle { cursor: pointer; }
.field-group-toggle:hover { color: #4f46e5; }
#printPreview { display: none; }
#printPreview.show { display: block; }
#printFrame { width: 100%; height: 0; border: none; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 to-violet-700 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">કસ્ટમ રિપોર્ટ જનરેટર</h1>
            <p class="text-violet-200 mt-1 text-sm">ડ્રેગ એન્ડ ડ્રોપ દ્વારા તમારો રિપોર્ટ ડિઝાઇન કરો</p>
        </div>
    </div>

    <form id="reportForm" class="space-y-6">
        @csrf

        {{-- Step 1: Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-bold">1</span>
                વિદ્યાર્થીઓ પસંદ કરો
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">ધોરણ</label>
                    <select name="standard_id" id="standard_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                        <option value="">બધા ધોરણ</option>
                        @foreach($standards as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">વર્ગ</label>
                    <select name="class_id" id="class_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                        <option value="">બધા વર્ગ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">રિપોર્ટ પ્રકાર</label>
                    <select name="report_type" id="report_type" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                        <option value="filled">વિદ્યાર્થી ડેટા સાથે</option>
                        <option value="blank">ખાલી (બ્લેન્ક) રિપોર્ટ</option>
                    </select>
                </div>
                <div id="blankRowsWrap">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">ખાલી હરોળ</label>
                    <input type="number" name="blank_rows" id="blank_rows" value="20" min="1" max="200" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Step 2: Columns --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-bold">2</span>
                કૉલમ ડિઝાઇન કરો
                <span class="text-xs text-gray-400 font-normal ml-2">ડ્રેગ એન્ડ ડ્રોપ</span>
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Available Fields --}}
                <div class="lg:col-span-1">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                        <i class="lni lni-layers-1 text-violet-500"></i> ઉપલબ્ધ કૉલમ
                        <span class="text-xs text-gray-400 font-normal">(ખેંચો)</span>
                    </h3>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 space-y-2 max-h-[500px] overflow-y-auto" id="availableFields">
                        @php
                            $fieldGroups = [
                                'system' => ['title_gu' => 'સિસ્ટમ', 'color' => 'gray'],
                                'student' => ['title_gu' => 'વિદ્યાર્થી માહિતી', 'color' => 'indigo'],
                                'relation' => ['title_gu' => 'સંબંધિત', 'color' => 'emerald'],
                                'computed' => ['title_gu' => 'ગણતરી કરેલ', 'color' => 'amber'],
                            ];
                            $grouped = [];
                            foreach ($fields as $f) {
                                $g = $f['type'] === 'system' ? 'system' : ($f['type'] === 'student' ? 'student' : ($f['type'] === 'relation' ? 'relation' : 'computed'));
                                $grouped[$g][] = $f;
                            }
                        @endphp
                        @foreach ($grouped as $gKey => $gFields)
                            <div x-data="{ open: true }">
                                <button @click="open = !open" type="button" class="field-group-toggle flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-700 w-full text-left">
                                    <i class="lni lni-chevron-down text-xs transition-transform" :class="open ? 'rotate-0' : '-rotate-90'"></i>
                                    {{ $fieldGroups[$gKey]['title_gu'] }}
                                    <span class="text-gray-400 font-normal normal-case">({{ count($gFields) }})</span>
                                </button>
                                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-1 flex flex-wrap gap-1.5">
                                    @foreach ($gFields as $f)
                                        <div class="drag-field px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 flex items-center gap-1.5"
                                             data-key="{{ $f['key'] }}"
                                             data-label-gu="{{ $f['label_gu'] }}"
                                             data-label-en="{{ $f['label_en'] }}"
                                             draggable="true">
                                            <i class="lni lni-arrow-all-direction text-gray-300 text-xs"></i>
                                            <span>{{ $f['label_gu'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Drop Zone --}}
                <div class="lg:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                        <i class="lni lni-layers-1 text-violet-500"></i> પસંદ કરેલ કૉલમ
                        <span class="text-xs text-gray-400 font-normal">(ક્રમ બદલવા ખેંચો)</span>
                    </h3>
                    <div class="drop-zone" id="dropZone">
                        <div class="empty-msg">
                            <i class="lni lni-arrow-both-direction-horizontal-1 text-2xl text-gray-300 block mb-2"></i>
                            ઉપરથી કૉલમ અહીં ખેંચી લાવો
                        </div>
                    </div>

                    {{-- Hidden input for selected columns --}}
                    <input type="hidden" name="columns" id="columnsInput" value="">

                    {{-- Quick action buttons --}}
                    <div class="flex flex-wrap gap-2 mt-3">
                        <button type="button" onclick="clearColumns()" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center gap-1">
                            <i class="lni lni-xmark text-xs"></i> બધા દૂર કરો
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Title --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-bold">3</span>
                રિપોર્ટ ટાઇટલ
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">શીર્ષક (ગુજરાતી)</label>
                    <input type="text" name="title_gu" id="title_gu" placeholder="દા.ત. શૈક્ષણિક વર્ષ ૨૦૨૫-૨૬ ની વિદ્યાર્થી યાદી" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">શીર્ષક (English)</label>
                    <input type="text" name="title_en" id="title_en" placeholder="e.g. Student List 2025-26" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Generate Button --}}
        <div class="flex items-center gap-3">
            <button type="submit" id="generateBtn" class="px-6 py-3 bg-violet-600 text-white font-medium rounded-xl hover:bg-violet-700 transition flex items-center gap-2 shadow-lg shadow-violet-200">
                <i class="lni lni-search-1 text-lg"></i> પ્રીવ્યૂ જુઓ
            </button>
            <button type="button" id="printBtn" onclick="printReport()" class="px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg shadow-emerald-200" style="display:none">
                <i class="lni lni-printer text-lg"></i> પ્રિન્ટ કરો
            </button>
        </div>
    </form>

    {{-- Preview --}}
    <div id="printPreview" class="mt-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="lni lni-eye text-violet-500"></i> પ્રીવ્યૂ
                </h3>
                <button onclick="printReport()" class="px-4 py-1.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center gap-1.5">
                    <i class="lni lni-printer"></i> પ્રિન્ટ
                </button>
            </div>
            <div class="p-4 overflow-x-auto">
                <iframe id="printFrame" srcdoc="" class="w-full border-0"></iframe>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedColumns = [];

document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const columnsInput = document.getElementById('columnsInput');
    const availableFields = document.getElementById('availableFields');

    // Drag start on available fields
    availableFields.addEventListener('dragstart', function (e) {
        const el = e.target.closest('.drag-field');
        if (!el) return;
        e.dataTransfer.setData('text/plain', JSON.stringify({
            key: el.dataset.key,
            label_gu: el.dataset.labelGu,
            label_en: el.dataset.labelEn,
        }));
        el.classList.add('dragging');
    });
    availableFields.addEventListener('dragend', function (e) {
        const el = e.target.closest('.drag-field');
        if (el) el.classList.remove('dragging');
    });

    // Drag over drop zone
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', function (e) {
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('drag-over');
        }
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        try {
            const data = JSON.parse(e.dataTransfer.getData('text/plain'));
            addColumn(data.key, data.label_gu, data.label_en);
        } catch (err) {}
    });

    // SortableJS on drop zone
    new Sortable(dropZone, {
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function () {
            updateColumnsInput();
        },
    });

    // Standard -> Class cascade
    document.getElementById('standard_id').addEventListener('change', function () {
        const stdId = this.value;
        const clsSelect = document.getElementById('class_id');
        clsSelect.innerHTML = '<option value="">બધા વર્ગ</option>';
        if (!stdId) return;
        fetch('{{ route("custom-report.classes", "") }}/' + stdId)
            .then(r => r.json())
            .then(data => {
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    clsSelect.appendChild(opt);
                });
            });
    });

    // Report type toggle
    document.getElementById('report_type').addEventListener('change', function () {
        document.getElementById('blankRowsWrap').style.display = this.value === 'blank' ? 'block' : 'none';
    });

    // Form submit
    document.getElementById('reportForm').addEventListener('submit', function (e) {
        e.preventDefault();
        if (selectedColumns.length === 0) {
            NexSchool.alert.error('કૃપા કરીને ઓછામાં ઓછી એક કૉલમ પસંદ કરો');
            return;
        }
        generatePreview();
    });

    // Auto-close blank rows for filled type
    document.getElementById('blankRowsWrap').style.display = 'none';
});

function addColumn(key, labelGu, labelEn) {
    if (selectedColumns.some(c => c.key === key)) {
        NexSchool.alert.warning('આ કૉલમ પહેલેથી પસંદ કરેલ છે');
        return;
    }
    selectedColumns.push({ key, label_gu: labelGu, label_en: labelEn });
    renderColumns();
}

function removeColumn(key) {
    selectedColumns = selectedColumns.filter(c => c.key !== key);
    renderColumns();
}

function clearColumns() {
    if (selectedColumns.length === 0) return;
    NexSchool.confirm.show('બધી કૉલમ દૂર કરો?', 'શું તમે બધી પસંદ કરેલ કૉલમ દૂર કરવા માંગો છો?', function () {
        selectedColumns = [];
        renderColumns();
    });
}

function renderColumns() {
    const dropZone = document.getElementById('dropZone');
    if (selectedColumns.length === 0) {
        dropZone.innerHTML = '<div class="empty-msg"><i class="lni lni-arrow-both-direction-horizontal-1 text-2xl text-gray-300 block mb-2"></i>ઉપરથી કૉલમ અહીં ખેંચી લાવો</div>';
        document.getElementById('columnsInput').value = '';
        return;
    }
    let html = '';
    selectedColumns.forEach(function (col) {
        html += '<div class="column-slot" data-key="' + col.key + '">'
              + '<i class="lni lni-arrow-all-direction text-gray-300 text-xs"></i>'
              + '<span>' + col.label_gu + '</span>'
              + '<span class="text-[10px] text-gray-400">| ' + col.label_en + '</span>'
              + '<span class="remove-btn" onclick="removeColumn(\'' + col.key + '\')">&times;</span>'
              + '</div>';
    });
    dropZone.innerHTML = html;
    updateColumnsInput();
}

function updateColumnsInput() {
    const slots = document.querySelectorAll('#dropZone .column-slot');
    const cols = [];
    slots.forEach(function (slot) {
        const key = slot.dataset.key;
        if (key) cols.push(key);
    });
    // Also add sr_no if first column is not sr_no
    if (cols.length > 0 && cols[0] !== 'sr_no') {
        cols.unshift('sr_no');
    }
    selectedColumns = cols.map(function (k) {
        // Find label from original field data
        const field = window.fieldData ? window.fieldData.find(f => f.key === k) : null;
        return { key: k, label_gu: field ? field.label_gu : k, label_en: field ? field.label_en : k };
    });
    document.getElementById('columnsInput').value = JSON.stringify(cols);
    // Re-render to show sr_no
    renderColumnsSilent();
}

function renderColumnsSilent() {
    // Re-render without duplicate sr_no
    const dropZone = document.getElementById('dropZone');
    if (selectedColumns.length === 0) {
        dropZone.innerHTML = '<div class="empty-msg"><i class="lni lni-arrow-both-direction-horizontal-1 text-2xl text-gray-300 block mb-2"></i>ઉપરથી કૉલમ અહીં ખેંચી લાવો</div>';
        return;
    }
    // Remove duplicate sr_no
    const seen = new Set();
    selectedColumns = selectedColumns.filter(function (c) {
        if (seen.has(c.key) && c.key === 'sr_no') return false;
        if (c.key === 'sr_no') seen.add(c.key);
        return true;
    });
    let html = '';
    selectedColumns.forEach(function (col) {
        html += '<div class="column-slot" data-key="' + col.key + '">'
              + '<i class="lni lni-arrow-all-direction text-gray-300 text-xs"></i>'
              + '<span>' + col.label_gu + '</span>'
              + '<span class="text-[10px] text-gray-400">| ' + col.label_en + '</span>'
              + (col.key !== 'sr_no' ? '<span class="remove-btn" onclick="removeColumn(\'' + col.key + '\')">&times;</span>' : '')
              + '</div>';
    });
    dropZone.innerHTML = html;
}

// Field data for label lookup
window.fieldData = @json($fields);

function generatePreview() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    // Send columns as separate array items
    formData.delete('columns');
    try {
        const cols = JSON.parse(document.getElementById('columnsInput').value || '[]');
        cols.forEach(function (col) { formData.append('columns[]', col); });
    } catch (e) {
        formData.append('columns[]', '');
    }

    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 spin text-lg"></i> જનરેટ થાય છે...';

    fetch('{{ route("custom-report.preview") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData,
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (!data.success) {
            NexSchool.alert.error(data.message || 'ભૂલ આવી');
            btn.disabled = false;
            btn.innerHTML = '<i class="lni lni-search-1 text-lg"></i> પ્રીવ્યૂ જુઓ';
            return;
        }
        const preview = document.getElementById('printPreview');
        const frame = document.getElementById('printFrame');
        preview.classList.add('show');
        frame.srcdoc = data.html;
        frame.onload = function () {
            frame.style.height = frame.contentWindow.document.documentElement.scrollHeight + 50 + 'px';
            btn.disabled = false;
            btn.innerHTML = '<i class="lni lni-search-1 text-lg"></i> પ્રીવ્યૂ જુઓ';
            document.getElementById('printBtn').style.display = 'inline-flex';
        };
    })
    .catch(function () {
        NexSchool.alert.error('સર્વર ભૂલ. ફરી પ્રયાસ કરો.');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-search-1 text-lg"></i> પ્રીવ્યૂ જુઓ';
    });
}

function printReport() {
    const frame = document.getElementById('printFrame');
    if (frame.contentWindow) {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    }
}
</script>
@endpush
