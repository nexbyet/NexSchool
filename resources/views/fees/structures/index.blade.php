@extends('layouts.app')
@section('title', 'ફી માળખું')
@section('content')
@php
    use App\Models\FeeHead;
    $allFeeHeads = FeeHead::orderBy('sort_order')->get();
    $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
    $activeYearId = $activeYear ? $activeYear->id : 0;
    $freqLabels = ['monthly' => 'માસિક', 'semesterly' => 'સત્ર', 'yearly' => 'વાર્ષિક'];
    $lateFeeLabels = ['none' => 'નથી', 'fixed' => 'નિશ્ચિત', 'per_month' => 'દર મહિને'];
    $freqColors = ['monthly' => 'bg-blue-100 text-blue-700', 'semesterly' => 'bg-amber-100 text-amber-700', 'yearly' => 'bg-purple-100 text-purple-700'];
    $typeLabels = ['tuition' => 'શાળા ફી', 'transport' => 'બસ ફી', 'other' => 'અન્ય'];
    $typeColors = ['tuition' => 'bg-indigo-100 text-indigo-700', 'transport' => 'bg-cyan-100 text-cyan-700', 'other' => 'bg-gray-100 text-gray-700'];
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ફી માળખું</h1>
                <p class="text-amber-200 mt-1 text-sm">ધોરણ પ્રમાણે ફીનું માળખું સેટ કરો</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openCopyModal()" class="px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition flex items-center gap-2">
                    <i class="lni lni-copy text-base"></i> ગત વર્ષથી કૉપી કરો
                </button>
                <button onclick="openModal()" class="px-4 py-2 bg-white text-amber-700 text-sm font-medium rounded-lg hover:bg-amber-50 transition flex items-center gap-2 shadow-lg">
                    <i class="lni lni-plus text-base"></i> નવું ફી માળખું
                </button>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <i class="lni lni-funnel text-gray-400 text-sm"></i>
                <label class="text-sm font-medium text-gray-700">શૈક્ષણિક વર્ષ:</label>
            </div>
            <select id="year-selector" onchange="switchYear(parseInt(this.value))" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                @foreach ($academicYears as $y)
                <option value="{{ $y->id }}" @if($y->id === $activeYearId) selected @endif>{{ $y->year }} @if($y->is_active)(ચાલુ)@endif</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="structures-container" class="space-y-4">
        @php $filteredStructures = $structures->where('academic_year_id', $activeYearId); @endphp
        @forelse ($filteredStructures as $structure)
        <div id="structure-card-{{ $structure->id }}" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                        <i class="lni lni-buildings-1 text-amber-600 text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">@foreach($structure->standards as $std)<span class="mr-1">{{ $std->name }}</span>@endforeach</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$structure->type] ?? 'bg-gray-100 text-gray-700' }}">{{ $typeLabels[$structure->type] ?? $structure->type }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $freqColors[$structure->frequency] ?? 'bg-gray-100 text-gray-700' }}">{{ $freqLabels[$structure->frequency] ?? $structure->frequency }}</span>
                            @if($structure->late_fee_type !== 'none')
                            <span class="text-xs text-gray-500">લેટ ફી: {{ $lateFeeLabels[$structure->late_fee_type] ?? $structure->late_fee_type }} @if($structure->late_fee_amount > 0)(₹{{ number_format($structure->late_fee_amount, 2) }})@endif @if($structure->late_fee_after_days > 0)- {{ $structure->late_fee_after_days }} દિવસ પછી@endif</span>
                            @endif
                            @if($structure->semester)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">સત્ર {{ $structure->semester }}</span>
                            @endif
                            @if($structure->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i class="lni lni-check-circle-1 text-xs"></i> સક્રિય</span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><i class="lni lni-ban-2 text-xs"></i> નિષ્ક્રિય</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="editStructure({{ $structure->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></button>
                    <button onclick="deleteStructure({{ $structure->id }}, '{{ $structure->standards->pluck('name')->implode(', ') }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>
                </div>
            </div>
            <div class="p-5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="pb-2 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">ફી હેડ</th>
                            <th class="pb-2 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">રકમ (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($structure->details as $detail)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2 text-gray-800">{{ $detail->feeHead->name_gu ?? $detail->feeHead->name_en ?? '—' }}</td>
                            <td class="py-2 text-right font-medium text-gray-900">{{ number_format($detail->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200">
                            <td class="pt-2 font-bold text-gray-800">કુલ</td>
                            <td class="pt-2 text-right font-bold text-amber-700">₹ {{ number_format($structure->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="lni lni-buildings-1 text-3xl text-amber-400"></i>
            </div>
            <p class="text-gray-500 font-medium">આ વર્ષ માટે હજી સુધી કોઈ ફી માળખું ઉમેરાયું નથી</p>
            <p class="text-gray-400 text-sm mt-1">પ્રથમ ફી માળખું ઉમેરવા માટે બટન દબાવો</p>
            <button onclick="openModal()" class="mt-4 px-5 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition shadow-sm">નવું ફી માળખું ઉમેરો</button>
        </div>
        @endforelse
    </div>
</div>

{{-- Create/Edit Modal --}}
<div id="structure-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 id="structure-modal-title" class="text-lg font-semibold text-gray-900 mb-4">નવું ફી માળખું</h3>
        <form id="structure-form">
            <input type="hidden" id="structure-id">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ધોરણો <span class="text-red-500">*</span></label>
                    <div id="structure-standards-container" class="flex flex-wrap gap-2 p-3 border border-gray-200 rounded-lg bg-gray-50 max-h-28 overflow-y-auto">
                        @foreach ($standards as $std)
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer hover:text-amber-700">
                            <input type="checkbox" name="standard_ids" value="{{ $std->id }}" class="standard-checkbox rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            {{ $std->name }}
                        </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">એકથી વધુ ધોરણ પસંદ કરી શકો છો</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ફી પ્રકાર <span class="text-red-500">*</span></label>
                    <select id="structure-type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="tuition">શાળા ફી</option>
                        <option value="transport">બસ ફી</option>
                        <option value="other">અન્ય</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">આવર્તન (Frequency) <span class="text-red-500">*</span></label>
                    <select id="structure-frequency" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="monthly">માસિક</option>
                        <option value="semesterly">સત્ર</option>
                        <option value="yearly">વાર્ષિક</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                    <select id="structure-semester" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">— વાર્ષિક —</option>
                        <option value="1">સત્ર 1</option>
                        <option value="2">સત્ર 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">લેટ ફી પ્રકાર</label>
                    <select id="structure-late-type" onchange="toggleLateFields()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="none">નથી</option>
                        <option value="fixed">નિશ્ચિત</option>
                        <option value="per_month">દર મહિને</option>
                    </select>
                </div>
                <div id="late-amount-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">લેટ ફી રકમ (₹)</label>
                    <input type="number" id="structure-late-amount" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                </div>
                <div id="late-days-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">લેટ ફી પછી (દિવસ)</label>
                    <input type="number" id="structure-late-days" min="0" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700">ફી હેડ્સ</label>
                    <button type="button" onclick="addHeadRow()" class="text-xs font-medium text-amber-600 hover:text-amber-700 hover:bg-amber-50 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                        <i class="lni lni-plus text-xs"></i> હેડ ઉમેરો
                    </button>
                </div>
                <div id="heads-container">
                    <div class="head-row grid grid-cols-[1fr_120px_36px] gap-2 mb-2">
                        <select class="head-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                            <option value="">ફી હેડ પસંદ કરો</option>
                            @foreach ($allFeeHeads as $fh)
                            <option value="{{ $fh->id }}">{{ $fh->name_gu }} ({{ $fh->name_en }})</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" min="0" placeholder="રકમ" class="head-amount w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <button type="button" onclick="removeHeadRow(this)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો">
                            <i class="lni lni-trash-3 text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="structure-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg focus:ring-4 focus:ring-amber-200 transition flex items-center gap-2">
                    <i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Copy Fee Structures Modal --}}
<div id="copy-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">ફી માળખું કૉપી કરો</h3>
        <form id="copy-form">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">કૉપી કરો (આ વર્ષ માટે)</label>
                    <input type="text" id="copy-to-year" readonly class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સ્રોત વર્ષ <span class="text-red-500">*</span></label>
                    <select id="copy-from-year" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">વર્ષ પસંદ કરો</option>
                        @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}">{{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">સ્રોત ધોરણ (ખાલી = બધા)</label>
                    <select id="copy-from-standard" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">બધા ધોરણો</option>
                        @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">લક્ષ્ય ધોરણ (ખાલી = સ્રોત જેવું જ)</label>
                    <select id="copy-to-standard" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <option value="">સ્રોત જેવું જ ધોરણ</option>
                        @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeCopyModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="copy-submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg focus:ring-4 focus:ring-amber-200 transition flex items-center gap-2">
                    <i class="lni lni-copy text-sm"></i> કૉપી કરો
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var yearSelector = document.getElementById('year-selector');
    var structuresContainer = document.getElementById('structures-container');
    var modal = document.getElementById('structure-modal');
    var structureForm = document.getElementById('structure-form');
    var structureId = document.getElementById('structure-id');
    var structureFrequency = document.getElementById('structure-frequency');
    var structureLateType = document.getElementById('structure-late-type');
    var structureLateAmount = document.getElementById('structure-late-amount');
    var structureLateDays = document.getElementById('structure-late-days');
    var structureType = document.getElementById('structure-type');
    var structureSemester = document.getElementById('structure-semester');
    var lateAmountGroup = document.getElementById('late-amount-group');
    var lateDaysGroup = document.getElementById('late-days-group');
    var structureSubmitBtn = document.getElementById('structure-submit-btn');
    var structureModalTitle = document.getElementById('structure-modal-title');
    var headsContainer = document.getElementById('heads-container');
    var copyModal = document.getElementById('copy-modal');
    var copyForm = document.getElementById('copy-form');
    var copyFromYear = document.getElementById('copy-from-year');
    var copyFromStandard = document.getElementById('copy-from-standard');
    var copyToStandard = document.getElementById('copy-to-standard');
    var copySubmitBtn = document.getElementById('copy-submit-btn');
    var copyToYear = document.getElementById('copy-to-year');

    var freqLabels = {'monthly': 'માસિક', 'semesterly': 'સત્ર', 'yearly': 'વાર્ષિક'};
    var freqColors = {'monthly': 'bg-blue-100 text-blue-700', 'semesterly': 'bg-amber-100 text-amber-700', 'yearly': 'bg-purple-100 text-purple-700'};
    var lateFeeLabels = {'none': 'નથી', 'fixed': 'નિશ્ચિત', 'per_month': 'દર મહિને'};
    var selectedYearId = parseInt(yearSelector.value);

    var copyToYearText = '';
    for (var i = 0; i < yearSelector.options.length; i++) {
        if (parseInt(yearSelector.options[i].value) === selectedYearId) {
            copyToYearText = yearSelector.options[i].text;
            break;
        }
    }
    if (copyToYear) copyToYear.value = copyToYearText;

    function toggleLateFields() {
        var val = structureLateType.value;
        if (val === 'none') {
            lateAmountGroup.classList.add('hidden');
            lateDaysGroup.classList.add('hidden');
        } else if (val === 'fixed') {
            lateAmountGroup.classList.remove('hidden');
            lateDaysGroup.classList.add('hidden');
        } else {
            lateAmountGroup.classList.remove('hidden');
            lateDaysGroup.classList.remove('hidden');
        }
    }

    function addHeadRow(data) {
        var row = document.createElement('div');
        row.className = 'head-row grid grid-cols-[1fr_120px_36px] gap-2 mb-2';
        var headOpts = '';
        headOpts += '<option value="">ફી હેડ પસંદ કરો</option>';
        @foreach ($allFeeHeads as $fh)
        headOpts += '<option value="{{ $fh->id }}">{{ $fh->name_gu }} ({{ $fh->name_en }})</option>';
        @endforeach
        row.innerHTML = '<select class="head-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">' + headOpts + '</select><input type="number" step="0.01" min="0" placeholder="રકમ" class="head-amount w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition"><button type="button" onclick="removeHeadRow(this)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button>';
        if (data) {
            var selects = row.querySelectorAll('.head-select');
            var amounts = row.querySelectorAll('.head-amount');
            for (var j = 0; j < selects.length; j++) {
                selects[j].value = data.fee_head_id || '';
            }
            for (var k = 0; k < amounts.length; k++) {
                amounts[k].value = data.amount || '';
            }
        }
        headsContainer.appendChild(row);
    }

    function removeHeadRow(btn) {
        var row = btn.closest('.head-row');
        if (headsContainer.querySelectorAll('.head-row').length > 1) {
            row.remove();
        } else {
            NexSchool.alert.danger('ઓછામાં ઓછું એક ફી હેડ હોવું જરૂરી છે.');
        }
    }

    function collectHeads() {
        var rows = headsContainer.querySelectorAll('.head-row');
        var heads = [];
        for (var i = 0; i < rows.length; i++) {
            var sel = rows[i].querySelector('.head-select');
            var amt = rows[i].querySelector('.head-amount');
            if (sel && amt && sel.value) {
                heads.push({ fee_head_id: parseInt(sel.value), amount: parseFloat(amt.value) || 0 });
            }
        }
        return heads;
    }

    function resetHeadRows(data) {
        headsContainer.innerHTML = '';
        if (data && data.details && data.details.length > 0) {
            for (var i = 0; i < data.details.length; i++) {
                addHeadRow(data.details[i]);
            }
        } else {
            addHeadRow(null);
        }
    }

    function getStandardCheckboxes() {
        return document.querySelectorAll('.standard-checkbox');
    }

    function openModal(data) {
        modal.classList.remove('hidden');
        requestAnimationFrame(function() { modal.style.opacity = '1'; });
        var chk = getStandardCheckboxes();
        if (data) {
            structureModalTitle.textContent = 'ફી માળખું એડિટ કરો';
            structureId.value = data.id;
            structureType.value = data.type || 'tuition';
            structureSemester.value = data.semester || '';
            structureFrequency.value = data.frequency || 'monthly';
            structureLateType.value = data.late_fee_type || 'none';
            structureLateAmount.value = data.late_fee_amount || '';
            structureLateDays.value = data.late_fee_after_days || '';
            resetHeadRows(data);
            var stdIds = (data.standards || []).map(function(s) { return typeof s === 'object' ? s.id : s; });
            for (var i = 0; i < chk.length; i++) {
                chk[i].checked = stdIds.indexOf(parseInt(chk[i].value)) !== -1;
            }
        } else {
            structureModalTitle.textContent = 'નવું ફી માળખું';
            structureId.value = '';
            structureType.value = 'tuition';
            structureSemester.value = '';
            structureFrequency.value = 'monthly';
            structureLateType.value = 'none';
            structureLateAmount.value = '';
            structureLateDays.value = '';
            resetHeadRows(null);
            for (var i = 0; i < chk.length; i++) chk[i].checked = false;
        }
        toggleLateFields();
    }

    function closeModal() {
        modal.style.opacity = '0';
        setTimeout(function() { modal.classList.add('hidden'); }, 200);
    }

    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

    structureForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var heads = collectHeads();
        if (heads.length === 0) {
            NexSchool.alert.danger('ઓછામાં ઓછું એક ફી હેડ ઉમેરો.');
            return;
        }
        var chk = getStandardCheckboxes();
        var selectedStds = [];
        for (var i = 0; i < chk.length; i++) {
            if (chk[i].checked) selectedStds.push(parseInt(chk[i].value));
        }
        if (selectedStds.length === 0) {
            NexSchool.alert.danger('ઓછામાં ઓછું એક ધોરણ પસંદ કરો.');
            return;
        }
        structureSubmitBtn.disabled = true;
        structureSubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સાચવાઈ રહ્યું છે...';
        var isEdit = !!structureId.value;
        var url = isEdit ? '{{ url("fees/structures/update") }}/' + structureId.value : '{{ route("fees.structures.store") }}';
        var bodyData = {
            academic_year_id: selectedYearId,
            standard_ids: selectedStds,
            semester: structureSemester.value || null,
            type: structureType.value,
            frequency: structureFrequency.value,
            late_fee_type: structureLateType.value,
            late_fee_amount: structureLateAmount.value ? parseFloat(structureLateAmount.value) : 0,
            late_fee_after_days: structureLateDays.value ? parseInt(structureLateDays.value) : 0,
            heads: heads,
        };
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify(bodyData),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) { NexSchool.alert.success(data.message); closeModal(); switchYear(selectedYearId); }
            else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'સર્વર ભૂલ'); })
        .finally(function() { structureSubmitBtn.disabled = false; structureSubmitBtn.innerHTML = '<i class="lni lni-floppy-disk-1 text-sm"></i> સાચવો'; });
    });

    function renderStructures(structures) {
        structuresContainer.innerHTML = '';
        if (!structures || structures.length === 0) {
            structuresContainer.innerHTML = '<div class="text-center py-16 bg-white rounded-xl border border-gray-200"><div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl flex items-center justify-center shadow-sm"><i class="lni lni-buildings-1 text-3xl text-amber-400"></i></div><p class="text-gray-500 font-medium">આ વર્ષ માટે હજી સુધી કોઈ ફી માળખું ઉમેરાયું નથી</p><p class="text-gray-400 text-sm mt-1">પ્રથમ ફી માળખું ઉમેરવા માટે બટન દબાવો</p><button onclick="openModal()" class="mt-4 px-5 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition shadow-sm">નવું ફી માળખું ઉમેરો</button></div>';
            return;
        }
        for (var i = 0; i < structures.length; i++) {
            var s = structures[i];
            var freqLabel = freqLabels[s.frequency] || s.frequency;
            var freqColor = freqColors[s.frequency] || 'bg-gray-100 text-gray-700';
            var lateHtml = '';
            if (s.late_fee_type && s.late_fee_type !== 'none') {
                lateHtml = '<span class="text-xs text-gray-500">લેટ ફી: ' + (lateFeeLabels[s.late_fee_type] || s.late_fee_type);
                if (parseFloat(s.late_fee_amount) > 0) lateHtml += ' (₹' + parseFloat(s.late_fee_amount).toFixed(2) + ')';
                if (parseInt(s.late_fee_after_days) > 0) lateHtml += ' - ' + s.late_fee_after_days + ' દિવસ પછી';
                lateHtml += '</span>';
            }
            var typeLabels = {'tuition': 'શાળા ફી', 'transport': 'બસ ફી', 'other': 'અન્ય'};
            var typeColors = {'tuition': 'bg-indigo-100 text-indigo-700', 'transport': 'bg-cyan-100 text-cyan-700', 'other': 'bg-gray-100 text-gray-700'};
            var typeHtml = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + (typeColors[s.type] || 'bg-gray-100 text-gray-700') + '">' + (typeLabels[s.type] || s.type) + '</span>';
            var semHtml = s.semester ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">સત્ર ' + s.semester + '</span>' : '';
            var statusHtml = s.is_active
                ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i class="lni lni-check-circle-1 text-xs"></i> સક્રિય</span>'
                : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><i class="lni lni-ban-2 text-xs"></i> નિષ્ક્રિય</span>';
            var stdNames = (s.standards || []).map(function(st) { return st.name || st; }).join(', ') || 'ધોરણ';
            var detailsHtml = '';
            var totalAmount = 0;
            if (s.details && s.details.length > 0) {
                for (var j = 0; j < s.details.length; j++) {
                    var d = s.details[j];
                    var headName = (d.fee_head && d.fee_head.name_gu) ? d.fee_head.name_gu : (d.fee_head ? d.fee_head.name_en : '—');
                    var amt = parseFloat(d.amount) || 0;
                    totalAmount += amt;
                    detailsHtml += '<tr class="hover:bg-gray-50/50"><td class="py-2 text-gray-800">' + headName + '</td><td class="py-2 text-right font-medium text-gray-900">' + amt.toFixed(2) + '</td></tr>';
                }
            }
            var card = document.createElement('div');
            card.className = 'bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden';
            card.id = 'structure-card-' + s.id;
            card.innerHTML = '<div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-200"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center"><i class="lni lni-buildings-1 text-amber-600 text-base"></i></div><div><h3 class="font-semibold text-gray-900 text-sm">' + stdNames + '</h3><div class="flex items-center gap-2 mt-0.5">' + typeHtml + '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + freqColor + '">' + freqLabel + '</span>' + semHtml + lateHtml + statusHtml + '</div></div></div><div class="flex items-center gap-1"><button onclick="editStructure(' + s.id + ')" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1 text-sm"></i></button><button onclick="deleteStructure(' + s.id + ', \'' + stdNames.replace(/'/g, "\\'") + '\')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો"><i class="lni lni-trash-3 text-sm"></i></button></div></div><div class="p-5"><table class="w-full text-sm"><thead><tr class="border-b border-gray-100"><th class="pb-2 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">ફી હેડ</th><th class="pb-2 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">રકમ (₹)</th></tr></thead><tbody class="divide-y divide-gray-50">' + detailsHtml + '</tbody><tfoot><tr class="border-t-2 border-gray-200"><td class="pt-2 font-bold text-gray-800">કુલ</td><td class="pt-2 text-right font-bold text-amber-700">₹ ' + totalAmount.toFixed(2) + '</td></tr></tfoot></table></div>';
            structuresContainer.appendChild(card);
        }
    }

    function switchYear(yearId) {
        selectedYearId = yearId;
        var txt = '';
        for (var i = 0; i < yearSelector.options.length; i++) {
            if (parseInt(yearSelector.options[i].value) === yearId) {
                txt = yearSelector.options[i].text;
                break;
            }
        }
        if (copyToYear) copyToYear.value = txt;
        fetch('{{ url("fees/structures/by-year") }}/' + yearId, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { if (!res.ok) throw new Error('ડેટા મેળવવામાં ભૂલ'); return res.json(); })
        .then(function(data) { renderStructures(data); })
        .catch(function(err) { NexSchool.alert.danger(err.message); });
    }

    window.editStructure = function(id) {
        fetch('{{ url("fees/structures") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { if (!res.ok) throw new Error('ડેટા મેળવવામાં ભૂલ'); return res.json(); })
        .then(function(data) { openModal(data); })
        .catch(function(err) { NexSchool.alert.danger(err.message); });
    };

    window.deleteStructure = function(id, name) {
        NexSchool.confirm.show('ફી માળખું કાઢી નાખો', 'શું તમે "' + name + '"નું ફી માળખું કાઢી નાખવા માંગો છો?', 'danger')
        .then(function(confirmed) {
            if (!confirmed) return;
            fetch('{{ url("fees/structures/delete") }}/' + id, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            })
            .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
            .then(function(data) {
                if (data.success) { NexSchool.alert.success(data.message); switchYear(selectedYearId); }
                else { NexSchool.alert.danger(data.message); }
            })
            .catch(function(err) { NexSchool.alert.danger(err.message || 'કાઢવામાં ભૂલ.'); });
        });
    };

    window.openCopyModal = function() {
        copyModal.classList.remove('hidden');
        requestAnimationFrame(function() { copyModal.style.opacity = '1'; });
        copyFromYear.value = '';
        copyFromStandard.value = '';
        copyToStandard.value = '';
    };

    function closeCopyModal() {
        copyModal.style.opacity = '0';
        setTimeout(function() { copyModal.classList.add('hidden'); }, 200);
    }

    copyModal.addEventListener('click', function(e) { if (e.target === copyModal) closeCopyModal(); });

    copyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!copyFromYear.value) {
            NexSchool.alert.danger('કૃપા કરીને "આ વર્ષથી કૉપી કરો" પસંદ કરો.');
            return;
        }
        if (parseInt(copyFromYear.value) === selectedYearId) {
            NexSchool.alert.danger('સ્રોત અને લક્ષ્ય વર્ષ સમાન ન હોઈ શકે.');
            return;
        }
        copySubmitBtn.disabled = true;
        copySubmitBtn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> કૉપી થાય છે...';
        fetch('{{ route("fees.structures.copy") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                to_academic_year_id: selectedYearId,
                from_academic_year_id: parseInt(copyFromYear.value),
                standard_id: copyFromStandard.value ? parseInt(copyFromStandard.value) : null,
                to_standard_id: copyToStandard.value ? parseInt(copyToStandard.value) : null,
            }),
        })
        .then(function(res) { if (!res.ok) return res.json().then(function(e) { throw e; }); return res.json(); })
        .then(function(data) {
            if (data.success) { NexSchool.alert.success(data.message); closeCopyModal(); switchYear(selectedYearId); }
            else { NexSchool.alert.danger(data.message || 'ભૂલ આવી.'); }
        })
        .catch(function(err) { NexSchool.alert.danger(err.message || 'કૉપી કરવામાં ભૂલ.'); })
        .finally(function() { copySubmitBtn.disabled = false; copySubmitBtn.innerHTML = '<i class="lni lni-copy text-sm"></i> કૉપી કરો'; });
    });

    window.openModal = openModal;
    window.closeModal = closeModal;
    window.closeCopyModal = closeCopyModal;
    window.addHeadRow = addHeadRow;
    window.removeHeadRow = removeHeadRow;
    window.toggleLateFields = toggleLateFields;
    window.switchYear = switchYear;
})();
</script>
@endpush
