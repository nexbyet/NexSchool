@extends('layouts.app')
@section('title', 'હોમપેજ સેક્શન્સ')
@section('content')
@php
    $typeLabels = ['notice_ticker'=>'સૂચના ટિકર','slider'=>'સ્લાઇડર','about'=>'અમારા વિશે','features'=>'વિશેષતાઓ','stats'=>'આંકડા','gallery'=>'ગેલેરી','contact'=>'સંપર્ક'];
    $contentFields = [
        'about' => ['title_gu'=>'શીર્ષક','description_gu'=>'વર્ણન','image'=>'ફોટો'],
        'features' => ['title_gu'=>'શીર્ષક','subtitle_gu'=>'ઉપશીર્ષક','items'=>'આઇટમ્સ'],
        'stats' => ['title_gu'=>'શીર્ષક','stats'=>'આંકડા'],
        'contact' => ['title_gu'=>'શીર્ષક','subtitle_gu'=>'ઉપશીર્ષક','map_embed'=>'Map Embed'],
    ];
@endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">હોમપેજ સેક્શન્સ</h1>
                <p class="text-indigo-200 mt-1 text-sm">હોમપેજના વિભાગો ગોઠવો અને એડિટ કરો</p>
            </div>
            <button onclick="openSectionModal()" class="px-4 py-2 bg-white text-indigo-700 font-medium rounded-lg hover:bg-indigo-50 transition flex items-center gap-2 text-sm"><i class="lni lni-plus"></i> નવું સેક્શન</button>
        </div>
    </div>

    <div class="space-y-3" id="section-list">
        @foreach ($sections as $s)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center justify-between" data-id="{{ $s->id }}">
            <div class="flex items-center gap-3">
                <span class="section-drag-handle cursor-grab text-gray-300 hover:text-gray-500 hover:text-indigo-400 transition-colors"><i class="lni lni-arrow-all-direction"></i></span>
                <div>
                    <h3 class="font-medium text-gray-900">{{ $typeLabels[$s->type] ?? $s->type }}</h3>
                    <p class="text-xs text-gray-400">ક્રમ: {{ $s->sort_order }} @if(!$s->status)<span class="text-red-500 ml-2">નિષ્ક્રિય</span>@endif</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                @if(in_array($s->type, ['about','features','stats','contact']))
                <button onclick="editContent({{ $s->id }}, '{{ $s->type }}')" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">કન્ટેન્ટ</button>
                @endif
                <button onclick="toggleSection({{ $s->id }}, {{ $s->status ? '0' : '1' }})" class="p-1.5 rounded-lg transition {{ $s->status ? 'text-gray-400 hover:text-red-600' : 'text-red-400 bg-red-50 hover:text-emerald-600 hover:bg-transparent' }}"><i class="lni lni-eye"></i></button>
                <button onclick="deleteSection({{ $s->id }})" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg transition"><i class="lni lni-trash-3"></i></button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="section-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">નવું સેક્શન</h3>
        <form id="section-form">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">પ્રકાર <span class="text-red-500">*</span></label>
                    <select id="section-type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                        @foreach ($typeLabels as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeSectionModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> ઉમેરો</button>
            </div>
        </form>
    </div>
</div>

<div id="content-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="content-modal-title">કન્ટેન્ટ એડિટ કરો</h3>
        <form id="content-form">
            <input type="hidden" id="content-section-id">
            <div id="content-fields" class="space-y-4"></div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeContentModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var typeLabels = @json($typeLabels);
    var contentFields = @json($contentFields);

    window.openSectionModal = function(){
        document.getElementById('section-form').reset();
        var m=document.getElementById('section-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
    };
    window.closeSectionModal = function(){ var el=document.getElementById('section-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); };

    document.getElementById('section-form').addEventListener('submit', function(e){
        e.preventDefault();
        fetch('{{ route("homepage-sections.store") }}', {
            method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
            body:JSON.stringify({type:document.getElementById('section-type').value})
        })
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);window.closeSectionModal();location.reload();}})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    });

    function renderRow(key, data, idx){
        if(key==='items'){
            return '<div class="feature-row bg-gray-50 rounded-lg p-3 space-y-2 relative" data-index="'+idx+'">'+
                '<button type="button" onclick="removeRow(this)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500"><i class="lni lni-xmark-circle"></i></button>'+
                '<input type="hidden" name="'+key+'['+idx+'][_key]" value="'+idx+'">'+
                '<div class="grid grid-cols-3 gap-2"><div><label class="text-xs text-gray-500">આઇકન</label><input type="text" name="'+key+'['+idx+'][icon]" value="'+(data.icon||'')+'" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="lni lni-star-fat"></div>'+
                '<div><label class="text-xs text-gray-500">શીર્ષક</label><input type="text" name="'+key+'['+idx+'][title_gu]" value="'+(data.title_gu||'')+'" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></div>'+
                '<div><label class="text-xs text-gray-500">વર્ણન</label><input type="text" name="'+key+'['+idx+'][description_gu]" value="'+(data.description_gu||'')+'" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></div></div></div>';
        }
        if(key==='stats'){
            return '<div class="stat-row bg-gray-50 rounded-lg p-3 space-y-2 relative" data-index="'+idx+'">'+
                '<button type="button" onclick="removeRow(this)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500"><i class="lni lni-xmark-circle"></i></button>'+
                '<input type="hidden" name="'+key+'['+idx+'][_key]" value="'+idx+'">'+
                '<div class="grid grid-cols-2 gap-2"><div><label class="text-xs text-gray-500">આંકડો</label><input type="text" name="'+key+'['+idx+'][number]" value="'+(data.number||'')+'" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="500+"></div>'+
                '<div><label class="text-xs text-gray-500">લેબલ</label><input type="text" name="'+key+'['+idx+'][label_gu]" value="'+(data.label_gu||'')+'" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></div></div></div>';
        }
        return '';
    }

    window.editContent = function(id, type){
        document.getElementById('content-section-id').value = id;
        document.getElementById('content-modal-title').textContent = (typeLabels[type]||type) + ' — કન્ટેન્ટ એડિટ કરો';
        var fields = contentFields[type] || {};
        var html='';
        fetch('{{ url("homepage-sections") }}/'+id, {headers:{'Accept':'application/json'}})
        .then(function(r){return r.json();})
        .then(function(d){
            var c = d.content || {};
            for(var key in fields){
                var label = fields[key];
                if(key==='image'){
                    html += '<div><label class="block text-sm font-medium text-gray-700 mb-1">'+label+' (URL)</label><input type="text" id="cf-'+key+'" value="'+(c[key]||'')+'" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></div>';
                } else if(key==='map_embed'){
                    html += '<div><label class="block text-sm font-medium text-gray-700 mb-1">'+label+'</label><textarea id="cf-'+key+'" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">'+(c[key]||'')+'</textarea></div>';
                } else if(key==='items' || key==='stats'){
                    var arr = c[key] || [];
                    html += '<div class="space-y-2" id="container-'+key+'"><label class="block text-sm font-medium text-gray-700 mb-1">'+label+'</label>';
                    for(var ri=0;ri<arr.length;ri++){ html += renderRow(key, arr[ri], ri); }
                    var addLabel = (key==='items') ? 'આઇટમ ઉમેરો' : 'આંકડો ઉમેરો';
                    html += '<button type="button" onclick="addRow(\''+key+'\')" class="mt-2 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition flex items-center gap-1"><i class="lni lni-plus text-xs"></i> '+addLabel+'</button></div>';
                } else {
                    html += '<div><label class="block text-sm font-medium text-gray-700 mb-1">'+label+'</label><input type="text" id="cf-'+key+'" value="'+(c[key]||'')+'" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></div>';
                }
            }
            document.getElementById('content-fields').innerHTML = html;
            var m=document.getElementById('content-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
        });
    };

    window.addRow = function(key){
        var container = document.getElementById('container-'+key);
        if(!container) return;
        var rows = container.querySelectorAll(key==='items' ? '.feature-row' : '.stat-row');
        var idx = rows.length;
        var empty = key==='items' ? {icon:'',title_gu:'',description_gu:''} : {number:'',label_gu:''};
        var div = document.createElement('div');
        div.innerHTML = renderRow(key, empty, idx);
        container.insertBefore(div.firstElementChild, container.lastElementChild);
    };

    window.removeRow = function(btn){
        var row = btn.closest('.feature-row, .stat-row');
        if(row){ row.remove(); }
    };

    window.closeContentModal = function(){ var el=document.getElementById('content-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); };

    document.getElementById('content-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('content-section-id').value;
        var inputs = document.querySelectorAll('[id^="cf-"]');
        var content = {};
        for(var i=0;i<inputs.length;i++){
            var key = inputs[i].id.replace('cf-','');
            content[key] = inputs[i].value;
        }
        ['items','stats'].forEach(function(arrKey){
            var sel = arrKey==='items' ? '.feature-row' : '.stat-row';
            var rows = document.querySelectorAll('#container-'+arrKey+' '+sel);
            if(rows.length){
                var arr = [];
                rows.forEach(function(row){
                    var item = {};
                    var re = new RegExp('^'+arrKey+'\\[\\d+\\]\\[(.+)\\]$');
                    row.querySelectorAll('input[name^="'+arrKey+'["]').forEach(function(inp){
                        var m = inp.name.match(re);
                        if(m && m[1] !== '_key'){ item[m[1]] = inp.value; }
                    });
                    arr.push(item);
                });
                content[arrKey] = arr;
            }
        });
        fetch('{{ url("homepage-sections") }}/'+id+'/content', {
            method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
            body:JSON.stringify({content:content})
        })
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);window.closeContentModal();location.reload();}})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    });

    window.toggleSection = function(id, status){
        fetch('{{ url("homepage-sections") }}/'+id, {method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},body:JSON.stringify({status:status})})
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);location.reload();}})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    };

    window.deleteSection = function(id) {
        NexSchool.confirm.show('ખાતરી કરો','આ સેક્શન કાઢી નાખશો?','danger').then(function(){
            fetch('{{ url("homepage-sections") }}/'+id,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
            .then(function(r){return r.json();}).then(function(d){if(d.success){NexSchool.alert.success(d.message);location.reload();}})
            .catch(function(){NexSchool.alert.danger('ભૂલ');});
        });
    };

})();

document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('section-list');
    if (el && el.children.length > 1 && typeof Sortable !== 'undefined') {
        Sortable.create(el, {
            animation: 200,
            handle: '.section-drag-handle',
            ghostClass: 'opacity-50',
            dragClass: 'shadow-xl',
            onEnd: function() {
                var items = el.querySelectorAll('[data-id]');
                var order = [];
                for(var i=0;i<items.length;i++){
                    order.push({id: parseInt(items[i].dataset.id), sort_order: i+1});
                }
                fetch('{{ route("homepage-sections.reorder") }}', {
                    method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
                    body:JSON.stringify({items:order})
                }).then(function(r){return r.json();}).then(function(d){if(d.success){NexSchool.alert.success(d.message);}});
            }
        });
    }
});
</script>
@endpush
