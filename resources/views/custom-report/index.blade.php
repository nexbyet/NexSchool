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
.column-slot .width-input {
    width: 40px;
    padding: 0 2px;
    font-size: 0.6875rem;
    text-align: center;
    border: 1px solid #d1d5db;
    border-radius: 3px;
    outline: none;
}
.column-slot .width-input:focus { border-color: #6366f1; }
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
            <div class="mb-4 flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="selection_mode" value="filter" checked onchange="toggleSelectionMode()" class="text-violet-600 focus:ring-violet-500">
                    <span class="text-sm font-medium text-gray-700">ધોરણ/વર્ગ ફિલ્ટર</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="selection_mode" value="manual" onchange="toggleSelectionMode()" class="text-violet-600 focus:ring-violet-500">
                    <span class="text-sm font-medium text-gray-700">વિદ્યાર્થી પસંદ કરો</span>
                </label>
            </div>
            <div id="filter-mode-fields" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">હરોળ ઊંચાઈ (mm)</label>
                    <input type="number" name="row_height" id="row_height" value="7" min="4" max="50" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <input type="checkbox" id="include_unregistered" name="include_unregistered" value="1" class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <label for="include_unregistered" class="text-sm text-gray-600 cursor-pointer">અનબોર્ડ (નોંધાયેલ ન હોય તેવા) વિદ્યાર્થીઓ પણ સામેલ કરો</label>
            </div>
            <div id="manual-mode-fields" class="hidden">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-600">વિદ્યાર્થીઓ પસંદ કરો</label>
                    <span id="student-select-count" class="text-xs text-gray-400">0 પસંદ</span>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <button type="button" onclick="selectAllStudents()" class="px-3 py-1 text-xs font-medium text-violet-700 bg-violet-50 border border-violet-200 rounded-lg hover:bg-violet-100 transition">બધા પસંદ કરો</button>
                    <button type="button" onclick="deselectAllStudents()" class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition">બધા અનપસંદ કરો</button>
                </div>
                <div id="student-list-container" class="border border-gray-200 rounded-lg max-h-60 overflow-y-auto bg-white">
                    <div class="text-center py-6 text-sm text-gray-400">પહેલા ધોરણ અને વર્ગ પસંદ કરો</div>
                </div>
                <input type="hidden" name="student_ids" id="student_ids" value="">
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
                                'fee' => ['title_gu' => 'ફી માહિતી', 'color' => 'rose'],
                                'computed' => ['title_gu' => 'ગણતરી કરેલ', 'color' => 'amber'],
                            ];
                            $grouped = [];
                            foreach ($fields as $f) {
                                $g = $f['type'] === 'system' ? 'system' : ($f['type'] === 'student' ? 'student' : ($f['type'] === 'relation' ? 'relation' : ($f['type'] === 'fee' ? 'fee' : 'computed')));
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
                                             data-width="{{ $f['width'] }}"
                                             draggable="true">
                                            <i class="lni lni-arrow-all-direction text-gray-300 text-xs"></i>
                                            <span>{{ $f['label_gu'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="openCustomColumnModal()" class="mt-3 w-full px-3 py-2 text-xs font-medium text-violet-700 bg-violet-50 border border-dashed border-violet-200 rounded-lg hover:bg-violet-100 transition flex items-center justify-center gap-1.5">
                        <i class="lni lni-plus text-sm"></i> કસ્ટમ બ્લેન્ક કૉલમ ઉમેરો
                    </button>
                </div>

                {{-- Drop Zone --}}
                <div class="lg:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                        <i class="lni lni-layers-1 text-violet-500"></i> પસંદ કરેલ કૉલમ
                        <span class="text-xs text-gray-400 font-normal">(ક્રમ બદલવા ખેંચો, નીચે પહોળાઈ ગોઠવો)</span>
                    </h3>
                    <div class="drop-zone" id="dropZone">
                        <div class="empty-msg">
                            <i class="lni lni-arrow-both-direction-horizontal-1 text-2xl text-gray-300 block mb-2"></i>
                            ઉપરથી કૉલમ અહીં ખેંચી લાવો
                        </div>
                    </div>

                    <input type="hidden" name="columns" id="columnsInput" value="">
                    <input type="hidden" name="column_widths" id="columnWidthsInput" value="">
                    <input type="hidden" name="custom_columns" id="customColumnsInput" value="">

                    <div class="flex flex-wrap gap-2 mt-3">
                        <button type="button" onclick="clearColumns()" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center gap-1">
                            <i class="lni lni-xmark text-xs"></i> બધા દૂર કરો
                        </button>
                    </div>

                    {{-- Sort by column --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                            <i class="lni lni-sort-alpha-asc text-violet-500"></i> કૉલમ મુજબ સૉર્ટ કરો
                        </h4>
                        <div class="flex items-center gap-3">
                            <select id="sort_column" name="sort_column" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                                <option value="">— સૉર્ટ ન કરો —</option>
                            </select>
                            <select id="sort_direction" name="sort_direction" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                                <option value="asc">ઉતરતા (A-Z)</option>
                                <option value="desc">ચડતા (Z-A)</option>
                            </select>
                        </div>
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

    {{-- Custom Column Modal --}}
    <div id="custom-col-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">કસ્ટમ બ્લેન્ક કૉલમ</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">હેડર (ગુજરાતી)</label>
                    <input type="text" id="custom-header-gu" placeholder="દા.ત. વજન" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">હેડર (English)</label>
                    <input type="text" id="custom-header-en" placeholder="e.g. Weight" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">પહોળાઈ (px, 10-500)</label>
                    <input type="number" id="custom-width" value="100" min="10" max="500" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeCustomColumnModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="button" onclick="addCustomColumn()" class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition">ઉમેરો</button>
            </div>
        </div>
    </div>

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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let selectedColumns = [];
let customColumns = {};  // key => { header_gu, header_en, width }
let customColCounter = 0;
let selectedStudentIds = [];

// Field data for label lookup
window.fieldData = @json($fields);

function toggleSelectionMode() {
    const mode = document.querySelector('input[name="selection_mode"]:checked').value;
    document.getElementById('filter-mode-fields').classList.toggle('hidden', mode === 'manual');
    document.getElementById('manual-mode-fields').classList.toggle('hidden', mode !== 'manual');
    if (mode === 'manual' && typeof loadStudentList === 'function') {
        loadStudentList();
    }
}

function populateSortColumns() {
    const sel = document.getElementById('sort_column');
    const currentVal = sel.value;
    sel.innerHTML = '<option value="">— સૉર્ટ ન કરો —</option>';
    selectedColumns.forEach(function (col) {
        if (col.key === 'sr_no') return;
        const opt = document.createElement('option');
        opt.value = col.key;
        opt.textContent = col.label_gu;
        sel.appendChild(opt);
    });
    if (currentVal) sel.value = currentVal;
}

document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const availableFields = document.getElementById('availableFields');

    // Drag start on available fields
    availableFields.addEventListener('dragstart', function (e) {
        const el = e.target.closest('.drag-field');
        if (!el) return;
        e.dataTransfer.setData('text/plain', JSON.stringify({
            key: el.dataset.key,
            label_gu: el.dataset.labelGu,
            label_en: el.dataset.labelEn,
            width: el.dataset.width || 100,
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

    // SortableJS on drop zone (for reordering)
    new Sortable(dropZone, {
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function () {
            updateInputs();
        },
    });

    // Standard -> Class cascade
    document.getElementById('standard_id').addEventListener('change', function () {
        const stdId = this.value;
        const clsSelect = document.getElementById('class_id');
        clsSelect.innerHTML = '<option value="">બધા વર્ગ</option>';
        if (!stdId) return;
        fetch('{{ url("reports/custom/classes") }}/' + stdId)
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

    // Standard/Class change -> load student list in manual mode
    function loadStudentList() {
        var mode = document.querySelector('input[name="selection_mode"]:checked').value;
        if (mode !== 'manual') return;
        var stdId = document.getElementById('standard_id').value;
        var clsId = document.getElementById('class_id').value;
        var container = document.getElementById('student-list-container');
        if (!stdId && !clsId) {
            container.innerHTML = '<div class="text-center py-6 text-sm text-gray-400">પહેલા ધોરણ અને વર્ગ પસંદ કરો</div>';
            return;
        }
        container.innerHTML = '<div class="text-center py-6 text-sm text-gray-400"><i class="lni lni-spinner-3 animate-spin"></i> લોડ થાય છે...</div>';
        fetch('{{ route("custom-report.students-by-filter") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ standard_id: stdId || null, class_id: clsId || null }),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success || !data.students || data.students.length === 0) {
                container.innerHTML = '<div class="text-center py-6 text-sm text-gray-400">કોઈ વિદ્યાર્થી મળ્યો નથી</div>';
                return;
            }
            var html = '';
            for (var si = 0; si < data.students.length; si++) {
                var st = data.students[si];
                var checked = selectedStudentIds.indexOf(st.id) !== -1;
                html += '<label class="student-check-item flex items-center gap-3 px-3 py-2 hover:bg-violet-50 cursor-pointer border-b border-gray-100 last:border-0">'
                      + '<input type="checkbox" class="student-checkbox text-violet-600 focus:ring-violet-500 rounded" data-id="' + st.id + '" ' + (checked ? 'checked' : '') + ' onchange="toggleStudent(' + st.id + ')">'
                      + '<span class="text-sm font-medium text-gray-700">' + (st.full_name_gu || st.full_name_en || '') + '</span>'
                      + '<span class="text-xs text-gray-400 font-mono ml-auto">' + (st.gr_number || '') + '</span>'
                      + '</label>';
            }
            container.innerHTML = html;
            updateStudentCount();
        })
        .catch(function () {
            container.innerHTML = '<div class="text-center py-6 text-sm text-red-500">વિદ્યાર્થીઓ લોડ કરવામાં ભૂલ</div>';
        });
    }

    document.getElementById('standard_id').addEventListener('change', function () {
        loadStudentList();
    });
    document.getElementById('class_id').addEventListener('change', function () {
        loadStudentList();
    });
});

function addColumn(key, labelGu, labelEn) {
    if (selectedColumns.some(c => c.key === key)) {
        NexSchool.alert.warning('આ કૉલમ પહેલેથી પસંદ કરેલ છે');
        return;
    }
    const field = window.fieldData ? window.fieldData.find(f => f.key === key) : null;
    const w = field ? field.width : 100;
    selectedColumns.push({ key, label_gu: labelGu, label_en: labelEn, width: w });
    renderColumns();
    populateSortColumns();
}

function removeColumn(key) {
    selectedColumns = selectedColumns.filter(c => c.key !== key);
    if (customColumns[key]) {
        delete customColumns[key];
    }
    renderColumns();
    populateSortColumns();
}

function clearColumns() {
    if (selectedColumns.length === 0) return;
    NexSchool.confirm.show('બધી કૉલમ દૂર કરો?', 'શું તમે બધી પસંદ કરેલ કૉલમ દૂર કરવા માંગો છો?', function () {
        selectedColumns = [];
        customColumns = {};
        renderColumns();
        populateSortColumns();
    });
}

function updateStudentCount() {
    var count = selectedStudentIds.length;
    var el = document.getElementById('student-select-count');
    if (el) el.textContent = count + ' પસંદ';
    var input = document.getElementById('student_ids');
    if (input) input.value = JSON.stringify(selectedStudentIds);
}

function toggleStudent(id) {
    var idx = selectedStudentIds.indexOf(id);
    if (idx === -1) {
        selectedStudentIds.push(id);
    } else {
        selectedStudentIds.splice(idx, 1);
    }
    updateStudentCount();
}

function selectAllStudents() {
    var checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(function (cb) {
        var id = parseInt(cb.dataset.id);
        if (selectedStudentIds.indexOf(id) === -1) {
            selectedStudentIds.push(id);
        }
        cb.checked = true;
    });
    updateStudentCount();
}

function deselectAllStudents() {
    var checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(function (cb) {
        cb.checked = false;
    });
    selectedStudentIds = [];
    updateStudentCount();
}

function openCustomColumnModal() {
    document.getElementById('custom-header-gu').value = '';
    document.getElementById('custom-header-en').value = '';
    document.getElementById('custom-width').value = 100;
    const modal = document.getElementById('custom-col-modal');
    modal.classList.remove('hidden');
    setTimeout(function () { modal.style.opacity = '1'; }, 10);
}

function closeCustomColumnModal() {
    const modal = document.getElementById('custom-col-modal');
    modal.style.opacity = '0';
    setTimeout(function () { modal.classList.add('hidden'); }, 200);
}

function addCustomColumn() {
    const headerGu = document.getElementById('custom-header-gu').value.trim();
    const headerEn = document.getElementById('custom-header-en').value.trim();
    const width = parseInt(document.getElementById('custom-width').value) || 100;
    if (!headerGu && !headerEn) {
        NexSchool.alert.warning('કૃપા કરીને ઓછામાં ઓછું ગુજરાતી હેડર આપો');
        return;
    }
    customColCounter++;
    const key = 'custom_' + customColCounter;
    const labelGu = headerGu || headerEn;
    const labelEn = headerEn || headerGu;
    customColumns[key] = { header_gu: headerGu, header_en: headerEn, width: width };
    addColumn(key, labelGu, labelEn);
    closeCustomColumnModal();
}

function renderColumns() {
    const dropZone = document.getElementById('dropZone');
    if (selectedColumns.length === 0) {
        dropZone.innerHTML = '<div class="empty-msg"><i class="lni lni-arrow-both-direction-horizontal-1 text-2xl text-gray-300 block mb-2"></i>ઉપરથી કૉલમ અહીં ખેંચી લાવો</div>';
        document.getElementById('columnsInput').value = '';
        document.getElementById('columnWidthsInput').value = '';
        document.getElementById('customColumnsInput').value = '';
        return;
    }
    // Auto-add sr_no
    if (selectedColumns[0] && selectedColumns[0].key !== 'sr_no') {
        selectedColumns.unshift({ key: 'sr_no', label_gu: 'ક્રમ', label_en: 'Sr No', width: 30 });
    }
    // Deduplicate sr_no
    const seen = new Set();
    selectedColumns = selectedColumns.filter(function (c) {
        if (seen.has(c.key)) return false;
        seen.add(c.key);
        return true;
    });
    let html = '';
    selectedColumns.forEach(function (col, idx) {
        const isCustom = col.key.startsWith('custom_');
        const cc = customColumns[col.key];
        const w = col.width || (cc ? cc.width : 100);
        html += '<div class="column-slot" data-key="' + col.key + '" data-idx="' + idx + '">'
              + '<i class="lni lni-arrow-all-direction text-gray-300 text-xs"></i>'
              + '<span class="text-xs font-semibold">' + col.label_gu + '</span>'
              + '<span class="text-[10px] text-gray-400">| ' + col.label_en + '</span>'
              + '<input type="number" class="width-input" value="' + w + '" min="10" max="500" title="પહોળાઈ px" onchange="updateColumnWidth(' + idx + ', this.value)" onclick="event.stopPropagation()">'
              + '<span class="text-[9px] text-gray-400">px</span>'
              + (col.key !== 'sr_no' ? '<span class="remove-btn" onclick="event.stopPropagation();removeColumn(\'' + col.key + '\')">&times;</span>' : '')
              + '</div>';
    });
    dropZone.innerHTML = html;
    updateInputs();
}

function updateColumnWidth(idx, val) {
    if (idx >= 0 && idx < selectedColumns.length) {
        selectedColumns[idx].width = parseInt(val) || 100;
        updateInputs();
    }
}

function updateInputs() {
    const slots = document.querySelectorAll('#dropZone .column-slot');
    const cols = [];
    const widths = {};
    slots.forEach(function (slot) {
        const key = slot.dataset.key;
        if (key) cols.push(key);
        const wi = slot.querySelector('.width-input');
        if (wi && key) {
            widths[key] = parseInt(wi.value) || 100;
        }
    });
    // Update selectedColumns from DOM order
    const newCols = [];
    slots.forEach(function (slot) {
        const key = slot.dataset.key;
        if (key) {
            const existing = selectedColumns.find(c => c.key === key);
            if (existing) {
                const wi = slot.querySelector('.width-input');
                existing.width = wi ? parseInt(wi.value) || 100 : existing.width;
                newCols.push(existing);
            }
        }
    });
    if (newCols.length > 0) selectedColumns = newCols;

    document.getElementById('columnsInput').value = JSON.stringify(cols);
    document.getElementById('columnWidthsInput').value = JSON.stringify(widths);
    document.getElementById('customColumnsInput').value = JSON.stringify(customColumns);
}

function generatePreview() {
    updateInputs();

    var mode = document.querySelector('input[name="selection_mode"]:checked').value;
    if (mode === 'manual' && selectedStudentIds.length === 0) {
        NexSchool.alert.error('કૃપા કરીને ઓછામાં ઓછો એક વિદ્યાર્થી પસંદ કરો');
        return;
    }

    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    formData.delete('columns');
    formData.delete('column_widths');
    formData.delete('custom_columns');

    try {
        const cols = JSON.parse(document.getElementById('columnsInput').value || '[]');
        cols.forEach(function (col) { formData.append('columns[]', col); });
        const widths = JSON.parse(document.getElementById('columnWidthsInput').value || '{}');
        Object.keys(widths).forEach(function (k) { formData.append('column_widths[' + k + ']', widths[k]); });
        const custom = JSON.parse(document.getElementById('customColumnsInput').value || '{}');
        Object.keys(custom).forEach(function (k) {
            formData.append('custom_columns[' + k + '][header_gu]', custom[k].header_gu || '');
            formData.append('custom_columns[' + k + '][header_en]', custom[k].header_en || '');
            formData.append('custom_columns[' + k + '][width]', custom[k].width || 100);
        });
    } catch (e) {
        formData.append('columns[]', '');
    }

    // Add sort
    var sortCol = document.getElementById('sort_column').value;
    var sortDir = document.getElementById('sort_direction').value;
    if (sortCol) {
        formData.append('sort_column', sortCol);
        formData.append('sort_direction', sortDir);
    }

    // Add student IDs for manual mode
    if (mode === 'manual') {
        selectedStudentIds.forEach(function (sid) { formData.append('student_ids[]', sid); });
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
