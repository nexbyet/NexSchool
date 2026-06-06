@extends('layouts.app')
@section('title', 'સ્લાઇડર')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">સ્લાઇડર</h1>
                <p class="text-sky-200 mt-1 text-sm">હોમપેજ સ્લાઇડર મેનેજ કરો</p>
            </div>
            <button onclick="openSlideModal()" class="px-4 py-2 bg-white text-sky-700 font-medium rounded-lg hover:bg-sky-50 transition flex items-center gap-2 text-sm"><i class="lni lni-plus"></i> નવી સ્લાઇડ</button>
        </div>
    </div>

    <div class="space-y-4" id="slider-list">
        @foreach ($slides as $s)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex items-stretch">
            <div class="w-48 flex-shrink-0 bg-gray-100 flex items-center justify-center">
                @if($s->image)<img src="{{ asset('storage/'.$s->image) }}" class="w-full h-32 object-cover">@else<div class="text-gray-400 text-sm">કોઈ ફોટો નથી</div>@endif
            </div>
            <div class="p-4 flex-1 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $s->title_gu }}</h3>
                    @if($s->subtitle_gu)<p class="text-sm text-gray-500">{{ $s->subtitle_gu }}</p>@endif
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                        <span>ક્રમ: {{ $s->sort_order }}</span>
                        {!! $s->status ? '<span class="text-emerald-600">સક્રિય</span>' : '<span class="text-gray-400">નિષ્ક્રિય</span>' !!}
                        @if($s->link_url)<span>🔗 {{ $s->link_url }}</span>@endif
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="editSlide({{ $s->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1"></i></button>
                    <button onclick="deleteSlide({{ $s->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3"></i></button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="slide-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="slide-modal-title">નવી સ્લાઇડ</h3>
        <form id="slide-form">
            <input type="hidden" id="slide-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (ગુજરાતી) <span class="text-red-500">*</span></label><input type="text" id="slide-title-gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (English)</label><input type="text" id="slide-title-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">ઉપશીર્ષક (ગુજરાતી)</label><input type="text" id="slide-sub-gu" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">ઉપશીર્ષક (English)</label><input type="text" id="slide-sub-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">ફોટો (1920x600)</label><input type="file" id="slide-image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:border-0 file:rounded-lg file:bg-sky-50 file:text-sky-700 file:font-medium hover:file:bg-sky-100"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">લિંક URL</label><input type="text" id="slide-link" placeholder="https://..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label><select id="slide-status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none"><option value="1">સક્રિય</option><option value="0">નિષ્ક્રિય</option></select></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="slide-submit" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    function closeModal(){ var el=document.getElementById('slide-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); }
    window.closeModal = closeModal;
    window.openSlideModal = function(){ document.getElementById('slide-id').value=''; document.getElementById('slide-modal-title').textContent='નવી સ્લાઇડ'; document.getElementById('slide-form').reset(); var m=document.getElementById('slide-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';}); };
    window.editSlide = function(id) {
        fetch('{{ url("sliders") }}/' + id, { headers:{'Accept':'application/json'} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('slide-id').value=d.id; document.getElementById('slide-modal-title').textContent='સ્લાઇડ એડિટ કરો';
            document.getElementById('slide-title-gu').value=d.title_gu||''; document.getElementById('slide-title-en').value=d.title_en||'';
            document.getElementById('slide-sub-gu').value=d.subtitle_gu||''; document.getElementById('slide-sub-en').value=d.subtitle_en||'';
            document.getElementById('slide-link').value=d.link_url||''; document.getElementById('slide-status').value=d.status?'1':'0';
            var m=document.getElementById('slide-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
        });
    };
    document.getElementById('slide-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id=document.getElementById('slide-id').value;
        var url=id?'{{ url("sliders") }}/'+id:'{{ url("sliders") }}';
        var fd=new FormData(); fd.append('title_gu',document.getElementById('slide-title-gu').value);
        fd.append('title_en',document.getElementById('slide-title-en').value);
        fd.append('subtitle_gu',document.getElementById('slide-sub-gu').value);
        fd.append('subtitle_en',document.getElementById('slide-sub-en').value);
        fd.append('link_url',document.getElementById('slide-link').value);
        fd.append('status',parseInt(document.getElementById('slide-status').value));
        var fi=document.getElementById('slide-image'); if(fi.files.length>0) fd.append('image',fi.files[0]);
        if(id) fd.append('_method','PUT');
        var btn=document.getElementById('slide-submit'); btn.disabled=true; btn.innerHTML='<i class="lni lni-spinner-3 text-sm animate-spin"></i> સેવ થાય છે...';
        fetch(url,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf},body:fd})
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);closeModal();location.reload();}else NexSchool.alert.danger(d.message||'ભૂલ');})
        .catch(function(){NexSchool.alert.danger('ભૂલ');})
        .finally(function(){btn.disabled=false;btn.innerHTML='<i class="lni lni-check-circle-1 text-sm"></i> સાચવો';});
    });
    window.deleteSlide = function(id) {
        NexSchool.confirm.show('ખાતરી કરો','આ સ્લાઇડ કાઢી નાખશો?','danger').then(function(){
            fetch('{{ url("sliders") }}/'+id,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
            .then(function(r){return r.json();}).then(function(d){if(d.success){NexSchool.alert.success(d.message);location.reload();}})
            .catch(function(){NexSchool.alert.danger('ભૂલ');});
        });
    };
})();
</script>
@endpush
