@extends('layouts.app')
@section('title', 'સૂચના બોર્ડ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-500 to-rose-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">સૂચના બોર્ડ</h1>
                <p class="text-red-200 mt-1 text-sm">સૂચનાઓ અને પરિપત્રો મેનેજ કરો</p>
            </div>
            <button onclick="openNoticeModal()" class="px-4 py-2 bg-white text-red-700 font-medium rounded-lg hover:bg-red-50 transition flex items-center gap-2 text-sm"><i class="lni lni-plus"></i> નવી સૂચના</button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">શીર્ષક</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">પ્રકાર</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">તારીખ</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ફાઇલ</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">સ્થિતિ</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase">ક્રિયા</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($notices as $n)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $n->title_gu }}</td>
                        <td class="px-4 py-3 text-center">{!! $n->is_circular ? '<span class="text-xs px-2 py-1 bg-purple-50 text-purple-700 rounded-full font-medium">પરિપત્ર</span>' : '<span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full font-medium">સૂચના</span>' !!}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $n->date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">@if($n->file_path)<a href="{{ asset('storage/'.$n->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline"><i class="lni lni-download-1 text-xs"></i></a>@else<span class="text-xs text-gray-400">—</span>@endif</td>
                        <td class="px-4 py-3 text-center">{!! $n->status ? '<span class="text-xs px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full font-medium">સક્રિય</span>' : '<span class="text-xs px-2 py-1 bg-gray-100 text-gray-500 rounded-full font-medium">નિષ્ક્રિય</span>' !!}</td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="editNotice({{ $n->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1"></i></button>
                            <button onclick="deleteNotice({{ $n->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="notice-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="notice-modal-title">નવી સૂચના</h3>
        <form id="notice-form">
            <input type="hidden" id="notice-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (ગુજરાતી) <span class="text-red-500">*</span></label><input type="text" id="notice-title-gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (English)</label><input type="text" id="notice-title-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">વિગત (ગુજરાતી)</label><textarea id="notice-content-gu" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">વિગત (English)</label><textarea id="notice-content-en" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">તારીખ <span class="text-red-500">*</span></label><input type="date" id="notice-date" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">પ્રકાર</label><select id="notice-circular" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"><option value="0">સૂચના</option><option value="1">પરિપત્ર</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">ફાઇલ (PDF/ઇમેજ)</label><input type="file" id="notice-file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:border-0 file:rounded-lg file:bg-red-50 file:text-red-700 file:font-medium hover:file:bg-red-100"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label><select id="notice-status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"><option value="1">સક્રિય</option><option value="0">નિષ્ક્રિય</option></select></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="notice-submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    function closeModal(){ var el=document.getElementById('notice-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); }
    window.closeModal = closeModal;

    window.openNoticeModal = function() {
        document.getElementById('notice-id').value = ''; document.getElementById('notice-modal-title').textContent = 'નવી સૂચના';
        document.getElementById('notice-form').reset();
        document.getElementById('notice-date').value = new Date().toISOString().split('T')[0];
        var m=document.getElementById('notice-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
    };

    window.editNotice = function(id) {
        fetch('{{ url("notice-board") }}/' + id, { headers:{'Accept':'application/json'} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('notice-id').value = d.id; document.getElementById('notice-modal-title').textContent = 'સૂચના એડિટ કરો';
            document.getElementById('notice-title-gu').value = d.title_gu || ''; document.getElementById('notice-title-en').value = d.title_en || '';
            document.getElementById('notice-content-gu').value = d.content_gu || ''; document.getElementById('notice-content-en').value = d.content_en || '';
            document.getElementById('notice-date').value = d.date ? d.date.substring(0,10) : '';
            document.getElementById('notice-circular').value = d.is_circular ? '1' : '0';
            document.getElementById('notice-status').value = d.status ? '1' : '0';
            var m=document.getElementById('notice-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
        });
    };

    document.getElementById('notice-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('notice-id').value;
        var url = id ? '{{ url("notice-board") }}/' + id : '{{ url("notice-board") }}';
        var fd = new FormData();
        fd.append('title_gu', document.getElementById('notice-title-gu').value);
        fd.append('title_en', document.getElementById('notice-title-en').value);
        fd.append('content_gu', document.getElementById('notice-content-gu').value);
        fd.append('content_en', document.getElementById('notice-content-en').value);
        fd.append('date', document.getElementById('notice-date').value);
        fd.append('is_circular', parseInt(document.getElementById('notice-circular').value));
        fd.append('status', parseInt(document.getElementById('notice-status').value));
        var fileInput = document.getElementById('notice-file');
        if (fileInput.files.length > 0) fd.append('file_path', fileInput.files[0]);
        if (id) fd.append('_method', 'PUT');
        var btn = document.getElementById('notice-submit'); btn.disabled = true; btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સેવ થાય છે...';
        fetch(url, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: fd })
        .then(function(r){ return r.json(); })
        .then(function(d){ if(d.success){ NexSchool.alert.success(d.message); closeModal(); location.reload(); } else NexSchool.alert.danger(d.message || 'ભૂલ'); })
        .catch(function(){ NexSchool.alert.danger('ભૂલ'); })
        .finally(function(){ btn.disabled = false; btn.innerHTML = '<i class="lni lni-check-circle-1 text-sm"></i> સાચવો'; });
    });

    window.deleteNotice = function(id) {
        NexSchool.confirm.show('ખાતરી કરો', 'આ સૂચના કાઢી નાખશો?', 'danger').then(function(){
            fetch('{{ url("notice-board") }}/' + id, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf} })
            .then(function(r){ return r.json(); })
            .then(function(d){ if(d.success){ NexSchool.alert.success(d.message); location.reload(); }})
            .catch(function(){ NexSchool.alert.danger('ભૂલ'); });
        });
    };
})();
</script>
@endpush

