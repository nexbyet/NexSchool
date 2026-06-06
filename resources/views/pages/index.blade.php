@extends('layouts.app')
@section('title', 'પેજ મેનેજમેન્ટ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-500 to-purple-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">પેજ મેનેજમેન્ટ</h1>
                <p class="text-violet-200 mt-1 text-sm">વેબસાઇટના પેજ બનાવો અને એડિટ કરો</p>
            </div>
            <button onclick="openPageModal()" class="px-4 py-2 bg-white text-violet-700 font-medium rounded-lg hover:bg-violet-50 transition flex items-center gap-2 text-sm"><i class="lni lni-plus"></i> નવું પેજ</button>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">શીર્ષક</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Slug</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">સ્થિતિ</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 text-xs uppercase">ક્રિયા</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($pages as $p)
                    <tr class="hover:bg-gray-50" data-id="{{ $p->id }}">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $p->title_gu }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $p->slug }}</td>
                        <td class="px-4 py-3 text-center">{!! $p->status === 'published' ? '<span class="text-xs px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full font-medium">પ્રકાશિત</span>' : '<span class="text-xs px-2 py-1 bg-gray-100 text-gray-500 rounded-full font-medium">ડ્રાફ્ટ</span>' !!}</td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="editPage({{ $p->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1"></i></button>
                            <button onclick="deletePage({{ $p->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="page-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="page-modal-title">નવું પેજ</h3>
        <form id="page-form">
            <input type="hidden" id="page-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (ગુજરાતી) <span class="text-red-500">*</span></label><input type="text" id="page-title-gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (English)</label><input type="text" id="page-title-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Slug</label><input type="text" id="page-slug" placeholder="ખાલી રાખો તો auto-generate થશે" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">કન્ટેન્ટ (ગુજરાતી) <span class="text-red-500">*</span></label><textarea id="page-content-gu" rows="6" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">કન્ટેન્ટ (English)</label><textarea id="page-content-en" rows="6" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Meta શીર્ષક</label><input type="text" id="page-meta-title" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Meta વર્ણન</label><textarea id="page-meta-desc" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Meta કીવર્ડ્સ</label><input type="text" id="page-meta-keywords" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label><select id="page-status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition"><option value="published">પ્રકાશિત</option><option value="draft">ડ્રાફ્ટ</option></select></div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closePageModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="page-submit" class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.openPageModal = function(id) {
        document.getElementById('page-id').value = '';
        document.getElementById('page-modal-title').textContent = 'નવું પેજ';
        document.getElementById('page-form').reset();
        document.getElementById('page-status').value = 'published';
        document.getElementById('page-modal').classList.remove('hidden');
        requestAnimationFrame(function(){ document.getElementById('page-modal').style.opacity = '1'; });
    };

    window.editPage = function(id) {
        fetch('{{ url("pages") }}/' + id, { headers: { 'Accept': 'application/json' }})
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('page-id').value = d.id;
            document.getElementById('page-modal-title').textContent = 'પેજ એડિટ કરો';
            document.getElementById('page-title-gu').value = d.title_gu || '';
            document.getElementById('page-title-en').value = d.title_en || '';
            document.getElementById('page-slug').value = d.slug || '';
            document.getElementById('page-content-gu').value = d.content_gu || '';
            document.getElementById('page-content-en').value = d.content_en || '';
            document.getElementById('page-meta-title').value = d.meta_title || '';
            document.getElementById('page-meta-desc').value = d.meta_description || '';
            document.getElementById('page-meta-keywords').value = d.meta_keywords || '';
            document.getElementById('page-status').value = d.status || 'draft';
            document.getElementById('page-modal').classList.remove('hidden');
            requestAnimationFrame(function(){ document.getElementById('page-modal').style.opacity = '1'; });
        });
    };

    window.closePageModal = function() {
        document.getElementById('page-modal').style.opacity = '0';
        setTimeout(function(){ document.getElementById('page-modal').classList.add('hidden'); }, 200);
    };

    document.getElementById('page-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('page-id').value;
        var url = id ? ('{{ url("pages") }}/' + id) : '{{ url("pages") }}';
        var method = id ? 'PUT' : 'POST';
        var btn = document.getElementById('page-submit');
        btn.disabled = true; btn.innerHTML = '<i class="lni lni-spinner-3 text-sm animate-spin"></i> સેવ થાય છે...';
        fetch(url, {
            method: method,
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                title_gu: document.getElementById('page-title-gu').value,
                title_en: document.getElementById('page-title-en').value,
                slug: document.getElementById('page-slug').value,
                content_gu: document.getElementById('page-content-gu').value,
                content_en: document.getElementById('page-content-en').value,
                meta_title: document.getElementById('page-meta-title').value,
                meta_description: document.getElementById('page-meta-desc').value,
                meta_keywords: document.getElementById('page-meta-keywords').value,
                status: document.getElementById('page-status').value,
            }),
        })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.success) { NexSchool.alert.success(d.message); closePageModal(); location.reload(); }
            else NexSchool.alert.danger(d.message || 'ભૂલ');
        })
        .catch(function(e){ NexSchool.alert.danger('સર્વર ભૂલ'); })
        .finally(function(){ btn.disabled = false; btn.innerHTML = '<i class="lni lni-check-circle-1 text-sm"></i> સાચવો'; });
    });

    window.deletePage = function(id) {
        NexSchool.confirm.show('ખાતરી કરો', 'આ પેજ કાઢી નાખશો?', 'danger').then(function(){
            fetch('{{ url("pages") }}/' + id, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            })
            .then(function(r){ return r.json(); })
            .then(function(d){ if(d.success){ NexSchool.alert.success(d.message); location.reload(); }})
            .catch(function(){ NexSchool.alert.danger('ભૂલ'); });
        });
    };
})();
</script>
@endpush
