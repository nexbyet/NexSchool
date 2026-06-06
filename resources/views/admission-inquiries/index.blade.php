@extends('layouts.app')
@section('title', 'પ્રવેશ અરજીઓ')
@section('content')
@php $statusColors = ['pending'=>'bg-amber-100 text-amber-800','approved'=>'bg-emerald-100 text-emerald-800','rejected'=>'bg-red-100 text-red-800']; $statusLabels = ['pending'=>'અપૂર્ણ','approved'=>'મંજૂર','rejected'=>'નામંજૂર']; $genderLabels = ['kumar'=>'કુમાર','kumari'=>'કુમારી']; @endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 to-cyan-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">પ્રવેશ અરજીઓ</h1>
                <p class="text-sky-200 mt-1 text-sm">ઓનલાઇન પ્રવેશ અરજીઓનું વ્યવસ્થાપન</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">વિદ્યાર્થી</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ધોરણ</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">પિતાનું નામ</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ફોન</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">સ્થિતિ</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">GR</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">તારીખ</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase">ક્રિયા</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($inquiries as $i)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $i->full_name_gu }}</div>
                            @if($i->full_name_en)<div class="text-xs text-gray-400">{{ $i->first_name_en }} {{ $i->father_name_en }} {{ $i->surname_en }}</div>@endif
                            <div class="text-xs text-gray-400 mt-0.5">{{ $genderLabels[$i->gender] ?? $i->gender }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $i->standard_applied_for }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $i->father_name }}</td>
                        <td class="px-4 py-3"><a href="tel:{{ $i->phone }}" class="text-blue-600 hover:underline">{{ $i->phone }}</a></td>
                        <td class="px-4 py-3 text-center"><span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$i->status] }}">{{ $statusLabels[$i->status] }}</span></td>
                        <td class="px-4 py-3 text-center text-xs font-mono text-gray-500">{{ $i->gr_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400">{{ $i->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="showInquiry({{ $i->id }})" class="px-3 py-1.5 text-xs font-medium text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition">વિગત</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">કોઈ અરજી નથી</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="inquiry-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">અરજી વિગત</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="lni lni-xmark-circle text-xl"></i></button>
        </div>
        <div id="inquiry-detail" class="space-y-4 text-sm"></div>
        <div id="inquiry-actions" class="flex flex-wrap gap-2 mt-6 pt-4 border-t border-gray-100"></div>
    </div>
</div>

<div id="approve-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">પ્રવેશ મંજૂર કરો</h3>
        <form id="approve-form">
            <input type="hidden" id="approve-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">GR નંબર</label><input type="text" id="approve-gr" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none" placeholder="GR-2026-001"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                    <select id="approve-year" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
                        <option value="">— પસંદ કરો —</option>
                        @foreach (\App\Models\AcademicYear::orderBy('start_date', 'desc')->get() as $y)
                        <option value="{{ $y->id }}">{{ $y->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">નોંધ</label><textarea id="approve-notes" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"></textarea></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition">મંજૂર કરો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var statusBadges = @json($statusColors);
    var statusLbls = @json($statusLabels);

    window.showInquiry = function(id){
        fetch('{{ url("admission-inquiries") }}/'+id, {headers:{'Accept':'application/json'}})
        .then(function(r){return r.json();})
        .then(function(d){
            var html = '<div class="grid grid-cols-2 gap-4">';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">નામ (ગુજરાતી)</p><p class="font-medium text-gray-900">'+(d.first_name_gu||'')+' '+(d.father_name_gu||'')+' '+(d.surname_gu||'')+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Name (English)</p><p class="font-medium text-gray-900">'+(d.first_name_en||'')+' '+(d.father_name_en||'')+' '+(d.surname_en||'')+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">ધોરણ</p><p class="font-medium text-gray-900">'+d.standard_applied_for+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">જન્મ તારીખ</p><p class="font-medium text-gray-900">'+d.date_of_birth+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">લિંગ</p><p class="font-medium text-gray-900">'+(d.gender==='kumar'?'કુમાર':'કુમારી')+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">પિતાનું નામ</p><p class="font-medium text-gray-900">'+d.father_name+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">માતાનું નામ</p><p class="font-medium text-gray-900">'+d.mother_name+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">ફોન</p><p class="font-medium text-gray-900">'+d.phone+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">ઇમેઇલ</p><p class="font-medium text-gray-900">'+(d.email||'—')+'</p></div>';
            if(d.address) html += '<div class="col-span-2 bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">સરનામું</p><p class="font-medium text-gray-900">'+d.address+'</p></div>';
            if(d.previous_school) html += '<div class="col-span-2 bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">અગાઉની શાળા</p><p class="font-medium text-gray-900">'+d.previous_school+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">GR નંબર</p><p class="font-medium text-gray-900">'+(d.gr_number||'—')+'</p></div>';
            html += '<div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">સ્થિતિ</p><p class="font-medium"><span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium '+(statusBadges[d.status]||'')+'">'+(statusLbls[d.status]||d.status)+'</span></p></div>';
            if(d.admin_notes) html += '<div class="col-span-2 bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">નોંધ</p><p class="text-gray-700">'+d.admin_notes+'</p></div>';
            html += '</div>';
            document.getElementById('inquiry-detail').innerHTML = html;

            var actions = document.getElementById('inquiry-actions');
            actions.innerHTML = '';
            if(d.status==='pending'){
                var appBtn = document.createElement('button');
                appBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition';
                appBtn.innerHTML = '<i class="lni lni-check-circle-1 text-sm"></i> મંજૂર કરો';
                appBtn.onclick = function(){ openApprove(d.id); };
                actions.appendChild(appBtn);
                var rejBtn = document.createElement('button');
                rejBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition';
                rejBtn.innerHTML = '<i class="lni lni-ban-2 text-sm"></i> નામંજૂર કરો';
                rejBtn.onclick = function(){ rejectInquiry(d.id); };
                actions.appendChild(rejBtn);
            }
            var delBtn = document.createElement('button');
            delBtn.className = 'px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition ml-auto';
            delBtn.innerHTML = '<i class="lni lni-trash-3 text-sm"></i> કાઢો';
            delBtn.onclick = function(){ deleteInquiry(d.id); };
            actions.appendChild(delBtn);

            var m=document.getElementById('inquiry-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
        });
    };

    window.closeModal = function(){ var el=document.getElementById('inquiry-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); };

    window.openApprove = function(id){
        document.getElementById('approve-id').value = id;
        var m=document.getElementById('approve-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
    };
    window.closeApproveModal = function(){ var el=document.getElementById('approve-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); };

    document.getElementById('approve-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('approve-id').value;
        fetch('{{ url("admission-inquiries") }}/'+id+'/approve', {
            method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
            body:JSON.stringify({
                gr_number: document.getElementById('approve-gr').value,
                academic_year_id: document.getElementById('approve-year').value,
                admin_notes: document.getElementById('approve-notes').value
            })
        })
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);window.closeApproveModal();window.closeModal();location.reload();}})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    });

    window.rejectInquiry = function(id){
        var notes = prompt('નામંજૂર કરવાનું કારણ (વૈકલ્પિક):');
        fetch('{{ url("admission-inquiries") }}/'+id+'/reject', {
            method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
            body:JSON.stringify({admin_notes: notes||''})
        })
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);window.closeModal();location.reload();}})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    };

    window.deleteInquiry = function(id){
        if(!confirm('ખાતરી કરો? આ અરજી કાઢી નાખશો?')) return;
        fetch('{{ url("admission-inquiries") }}/'+id, {method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);window.closeModal();location.reload();}})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    };
})();
</script>
@endpush
