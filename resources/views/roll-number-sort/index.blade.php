@extends('layouts.app')
@section('title', 'રોલ નંબર ગોઠવણી')
@section('content')
<div class="p-4 md:p-6">

    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-rose-600 to-pink-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">રોલ નંબર ગોઠવણી</h1>
            <p class="text-rose-200 mt-1 text-sm">વિદ્યાર્થીઓના ડિફૉલ્ટ સૉર્ટિંગ ક્રમમાં ફેરફાર કરો — વિકલ્પોને ખેંચીને ગોઠવો</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Stats --}}
    @php
        $totalSorted = count($sortFields);
        $totalAvailable = count($availableFields) - $totalSorted;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                <i class="lni lni-layers-1 text-rose-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">સૉર્ટ વિકલ્પ</p>
                <p id="sort-count" class="text-xl font-bold text-gray-900">{{ $totalSorted }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center">
                <i class="lni lni-plus text-pink-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">બાકી વિકલ્પ</p>
                <p id="avail-count" class="text-xl font-bold text-gray-900">{{ $totalAvailable }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-fuchsia-100 flex items-center justify-center">
                <i class="lni lni-check-circle-1 text-fuchsia-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ</p>
                <p class="text-xl font-bold text-gray-900">{{ count($availableFields) }}</p>
            </div>
        </div>
    </div>

    {{-- Instruction box --}}
    <div class="bg-gradient-to-r from-rose-50 to-pink-50 border border-rose-100 rounded-xl p-4 mb-5">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="lni lni-bulb-2 text-rose-600 text-sm"></i>
            </div>
            <div class="text-sm text-rose-800 leading-relaxed">
                <p class="font-medium mb-1">સૂચના:</p>
                <p>નીચે <strong>સૉર્ટિંગ ક્રમ</strong> વિભાગમાં વિકલ્પોને ખેંચીને ગોઠવો. ઉપરના વિકલ્પને પ્રાથમિકતા આપવામાં આવશે. બાકીના વિકલ્પોને <strong>ઉપલબ્ધ વિકલ્પો</strong> માંથી ખેંચીને ઉમેરી શકાય છે.</p>
                <ul class="mt-2 space-y-0.5 list-disc list-inside text-rose-600">
                    <li>પ્રથમ વિકલ્પ = મુખ્ય સૉર્ટ</li>
                    <li>બીજો વિકલ્પ = ગૌણ સૉર્ટ (જો પ્રથમ સરખું હોય તો)</li>
                    <li>ત્રીજો વિકલ્પ = વધુ ગૌણ સૉર્ટ</li>
                </ul>
                <p class="mt-2 text-rose-500 text-xs">ફેરફાર કર્યા પછી "સાચવો" બટન દબાવો.</p>
            </div>
        </div>
    </div>

    {{-- Sort list --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 text-sm flex items-center gap-2">
                <i class="lni lni-layers-1 text-rose-500"></i> સૉર્ટિંગ ક્રમ
            </h2>
            <span id="sort-badge" class="text-xs text-gray-400 bg-gray-50 px-2.5 py-1 rounded-full font-medium">{{ $totalSorted }} વિકલ્પ</span>
        </div>
        <div id="sort-list" class="p-4 space-y-2 min-h-[80px]">
            @forelse($sortFields as $field)
                @php $f = collect($availableFields)->firstWhere('key', $field); @endphp
                <div data-key="{{ $field }}" class="sort-item flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-rose-300 hover:shadow transition-all duration-150">
                    <span class="drag-handle flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex-shrink-0 cursor-grab">
                        <i class="lni lni-menu-cheesburger text-sm"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 text-sm">{{ $f['label_gu'] ?? $field }}</p>
                        <p class="text-xs text-gray-400">{{ $f['label_en'] ?? '' }}</p>
                    </div>
                    <span class="text-xs text-gray-300 bg-gray-50 px-2 py-1 rounded-full font-medium">{{ $loop->index + 1 }}</span>
                </div>
            @empty
                <div id="empty-sort" class="text-center py-8">
                    <div class="w-12 h-12 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="lni lni-layers-1 text-gray-300 text-lg"></i>
                    </div>
                    <p class="text-sm text-gray-400">હજી સુધી કોઈ વિકલ્પ પસંદ થયો નથી</p>
                    <p class="text-xs text-gray-300 mt-1">નીચેથી વિકલ્પો ખેંચીને અહીં લાવો</p>
                </div>
            @endforelse
        </div>
        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <button onclick="resetSort()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">ડિફૉલ્ટ પર રીસેટ કરો</button>
            <button onclick="saveSort()" class="px-5 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg focus:ring-4 focus:ring-rose-200 transition flex items-center gap-2">
                <i class="lni lni-check text-base"></i> સાચવો
            </button>
        </div>
    </div>

    {{-- Available options --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 text-sm flex items-center gap-2">
                <i class="lni lni-plus text-gray-400"></i> ઉપલબ્ધ વિકલ્પો
            </h2>
        </div>
        <div id="available-list" class="p-4 space-y-2 min-h-[60px]">
            @php $inSort = $sortFields; @endphp
            @foreach($availableFields as $f)
                @if(!in_array($f['key'], $inSort))
                <div data-key="{{ $f['key'] }}" class="available-item flex items-center gap-3 px-4 py-3 bg-gray-50 border border-dashed border-gray-200 rounded-xl hover:border-rose-300 hover:bg-rose-50/50 transition-all duration-150">
                    <span class="drag-handle flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-400 flex-shrink-0 cursor-grab">
                        <i class="lni lni-plus text-sm"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-500 text-sm">{{ $f['label_gu'] }}</p>
                        <p class="text-xs text-gray-400">{{ $f['label_en'] }}</p>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
#sort-list .sort-item, #available-list .available-item {
    -webkit-user-select: none; user-select: none; -webkit-touch-callout: none;
}
#sort-list .sort-item *, #available-list .available-item * {
    pointer-events: none;
}
#sort-list .sort-item .drag-handle, #available-list .available-item .drag-handle {
    pointer-events: auto;
}
.sortable-fallback {
    opacity: 0.8 !important;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15) !important;
    transform: scale(1.02) !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sortList = document.getElementById('sort-list');
    const availList = document.getElementById('available-list');
    const sortCount = document.getElementById('sort-count');
    const availCount = document.getElementById('avail-count');
    const sortBadge = document.getElementById('sort-badge');
    const emptySort = document.getElementById('empty-sort');

    function toSortItem(el) {
        el.className = 'sort-item flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-rose-300 hover:shadow transition-all duration-150';
        var dh = el.querySelector('.drag-handle');
        if (dh) { dh.className = 'drag-handle flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex-shrink-0 cursor-grab'; }
        var ic = dh ? dh.querySelector('i') : null;
        if (ic) { ic.className = 'lni lni-menu-cheesburger text-sm'; }
        var lb = el.querySelector('.font-medium');
        if (lb) { lb.className = 'font-medium text-gray-900 text-sm'; }
        var sortNum = el.querySelector('.rounded-full');
        if (!sortNum) {
            var d = document.createElement('span');
            d.className = 'text-xs text-gray-300 bg-gray-50 px-2 py-1 rounded-full font-medium';
            d.textContent = '0';
            el.appendChild(d);
        }
    }

    function toAvailItem(el) {
        el.className = 'available-item flex items-center gap-3 px-4 py-3 bg-gray-50 border border-dashed border-gray-200 rounded-xl hover:border-rose-300 hover:bg-rose-50/50 transition-all duration-150';
        var dh = el.querySelector('.drag-handle');
        if (dh) { dh.className = 'drag-handle flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-400 flex-shrink-0 cursor-grab'; }
        var ic = dh ? dh.querySelector('i') : null;
        if (ic) { ic.className = 'lni lni-plus text-sm'; }
        var lb = el.querySelector('.font-medium');
        if (lb) { lb.className = 'font-medium text-gray-500 text-sm'; }
        var sortNum = el.querySelector('.rounded-full');
        if (sortNum) { sortNum.remove(); }
    }

    function updateUI() {
        var items = sortList.querySelectorAll('.sort-item');
        items.forEach(function(el, i) {
            var b = el.querySelector('.rounded-full');
            if (b) b.textContent = i + 1;
        });
        var sortLen = items.length;
        var availLen = availList.querySelectorAll('.available-item').length;
        if (sortCount) sortCount.textContent = sortLen;
        if (availCount) availCount.textContent = availLen;
        if (sortBadge) sortBadge.textContent = sortLen + ' વિકલ્પ';

        if (sortLen === 0) {
            if (!sortList.querySelector('#empty-sort')) {
                var es = document.createElement('div');
                es.id = 'empty-sort';
                es.className = 'text-center py-8';
                es.innerHTML = '<div class="w-12 h-12 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center"><i class="lni lni-layers-1 text-gray-300 text-lg"></i></div><p class="text-sm text-gray-400">હજી સુધી કોઈ વિકલ્પ પસંદ થયો નથી</p><p class="text-xs text-gray-300 mt-1">નીચેથી વિકલ્પો ખેંચીને અહીં લાવો</p>';
                sortList.appendChild(es);
            }
        } else {
            var es = sortList.querySelector('#empty-sort');
            if (es) es.remove();
        }
    }

    if (typeof Sortable !== 'undefined' && sortList && availList) {
        Sortable.create(sortList, {
            handle: '.drag-handle',
            group: { name: 'sort', pull: true, put: true },
            animation: 200,
            ghostClass: 'opacity-30',
            dragClass: 'shadow-xl scale-105',
            forceFallback: true,
            fallbackClass: 'sortable-fallback',
            fallbackOnBody: false,
            setData: function(dt) { dt.setData('Text', ''); },
            onAdd: function(e) { toSortItem(e.item); updateUI(); },
            onRemove: function(e) { toAvailItem(e.item); },
            onEnd: function() { updateUI(); }
        });

        Sortable.create(availList, {
            handle: '.drag-handle',
            group: { name: 'sort', pull: true, put: true },
            animation: 200,
            ghostClass: 'opacity-30',
            dragClass: 'shadow-xl scale-105',
            forceFallback: true,
            fallbackClass: 'sortable-fallback',
            fallbackOnBody: false,
            setData: function(dt) { dt.setData('Text', ''); },
            onAdd: function(e) { toAvailItem(e.item); },
            onRemove: function(e) { toSortItem(e.item); }
        });
    }

    window.saveSort = function() {
        var items = sortList.querySelectorAll('.sort-item');
        var fields = [];
        items.forEach(function(el) { fields.push(el.dataset.key); });
        if (fields.length === 0) { NexSchool.alert.danger('ઓછામાં ઓછો એક વિકલ્પ રાખો.'); return; }
        var btn = document.querySelector('button[onclick="saveSort()"]');
        btn.disabled = true; btn.innerHTML = '<i class="lni lni-spinner-3 text-base animate-spin"></i> સાચવાઈ રહ્યું છે...';
        fetch('{{ route('roll-number-sort.update') }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ fields: fields }),
        })
        .then(function(r) { if (!r.ok) return r.json().then(function(e) { throw e; }); return r.json(); })
        .then(function(d) {
            if (d.success) { NexSchool.alert.success(d.message); } else { NexSchool.alert.danger(d.message || 'ભૂલ'); }
        })
        .catch(function(e) { NexSchool.alert.danger(e.message || e.errors?.fields?.[0] || 'સર્વર ભૂલ'); })
        .finally(function() { btn.disabled = false; btn.innerHTML = '<i class="lni lni-check text-base"></i> સાચવો'; });
    };

    window.resetSort = function() {
        NexSchool.confirm.show('રીસેટ કરો', 'શું તમે ડિફૉલ્ટ સૉર્ટિંગ પર પાછા જવા માંગો છો?', 'warning')
        .then(function() {
            var btn = document.querySelector('button[onclick="resetSort()"]');
            btn.disabled = true; btn.textContent = 'રીસેટ થાય છે...';
            fetch('{{ route('roll-number-sort.update') }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ fields: ['name_en', 'father_name_en', 'surname_en'] }),
            })
            .then(function(r) { if (!r.ok) return r.json().then(function(e) { throw e; }); return r.json(); })
            .then(function(d) {
                if (d.success) { NexSchool.alert.success(d.message); setTimeout(function() { location.reload(); }, 400); }
                else NexSchool.alert.danger(d.message || 'ભૂલ');
            })
            .catch(function(e) { NexSchool.alert.danger(e.message || e.errors?.fields?.[0] || 'સર્વર ભૂલ'); })
            .finally(function() { btn.disabled = false; btn.textContent = 'ડિફૉલ્ટ પર રીસેટ કરો'; });
        })
        .catch(function() {});
    };
});
</script>
@endpush
@endsection