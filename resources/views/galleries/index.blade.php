@extends('layouts.app')
@section('title', 'ગેલેરી')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-pink-500 to-rose-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">ફોટો ગેલેરી</h1>
                <p class="text-pink-200 mt-1 text-sm">ફોટો ગેલેરી મેનેજ કરો</p>
            </div>
            <button onclick="openGalleryModal()" class="px-4 py-2 bg-white text-pink-700 font-medium rounded-lg hover:bg-pink-50 transition flex items-center gap-2 text-sm"><i class="lni lni-plus"></i> નવી ગેલેરી</button>
        </div>
    </div>

    <div class="space-y-6">
        @foreach ($galleries as $g)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $g->name_gu }}</h3>
                    <p class="text-xs text-gray-500">{{ $g->images->count() }} ફોટા</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="addImage({{ $g->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition flex items-center gap-1"><i class="lni lni-plus text-xs"></i> ફોટો</button>
                    <button onclick="editGallery({{ $g->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1"></i></button>
                    <button onclick="deleteGallery({{ $g->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3"></i></button>
                </div>
            </div>
            @if($g->images->isNotEmpty())
            <div class="flex gap-3 p-4 overflow-x-auto">
                @foreach ($g->images as $img)
                <div class="flex-shrink-0 relative group">
                    <img src="{{ asset('storage/'.$img->image) }}" class="w-32 h-24 object-cover rounded-lg border border-gray-200">
                    <button onclick="deleteImage({{ $img->id }})" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition"><i class="lni lni-xmark"></i></button>
                    @if($img->caption_gu)<p class="text-[10px] text-gray-500 mt-0.5 text-center truncate w-32">{{ $img->caption_gu }}</p>@endif
                </div>
                @endforeach
            </div>
            @else
            <div class="p-4 text-center text-sm text-gray-400">કોઈ ફોટા નથી</div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div id="gallery-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="gallery-modal-title">નવી ગેલેરી</h3>
        <form id="gallery-form">
            <input type="hidden" id="gallery-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">નામ (ગુજરાતી) <span class="text-red-500">*</span></label><input type="text" id="gallery-name-gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">નામ (English)</label><input type="text" id="gallery-name-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">વર્ણન (ગુજરાતી)</label><textarea id="gallery-desc-gu" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">વર્ણન (English)</label><textarea id="gallery-desc-en" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label><select id="gallery-status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"><option value="1">સક્રિય</option><option value="0">નિષ્ક્રિય</option></select></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeGalModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>

<div id="image-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">ફોટો ઉમેરો</h3>
        <form id="image-form">
            <input type="hidden" id="image-gallery-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">ફોટો <span class="text-red-500">*</span></label><input type="file" id="image-file" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:border-0 file:rounded-lg file:bg-pink-50 file:text-pink-700 file:font-medium hover:file:bg-pink-100"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">કૅપ્શન (ગુજરાતી)</label><input type="text" id="image-cap-gu" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">કૅપ્શન (English)</label><input type="text" id="image-cap-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none"></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeImgModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="img-submit" class="px-4 py-2 text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> ઉમેરો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function closeGalModal(){ var el=document.getElementById('gallery-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); }
    function closeImgModal(){ var el=document.getElementById('image-modal'); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); }
    window.closeGalModal=closeGalModal; window.closeImgModal=closeImgModal;

    window.openGalleryModal = function(){
        document.getElementById('gallery-id').value=''; document.getElementById('gallery-modal-title').textContent='નવી ગેલેરી';
        document.getElementById('gallery-form').reset(); var m=document.getElementById('gallery-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
    };

    window.editGallery = function(id) {
        fetch('{{ url("galleries") }}/'+id, { headers:{'Accept':'application/json'} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('gallery-id').value=d.id; document.getElementById('gallery-modal-title').textContent='ગેલેરી એડિટ કરો';
            document.getElementById('gallery-name-gu').value=d.name_gu||''; document.getElementById('gallery-name-en').value=d.name_en||'';
            document.getElementById('gallery-desc-gu').value=d.description_gu||''; document.getElementById('gallery-desc-en').value=d.description_en||'';
            document.getElementById('gallery-status').value=d.status?'1':'0';
            var m=document.getElementById('gallery-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
        });
    };

    document.getElementById('gallery-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id=document.getElementById('gallery-id').value;
        var url=id?'{{ url("galleries") }}/'+id:'{{ url("galleries") }}';
        var body={name_gu:document.getElementById('gallery-name-gu').value,name_en:document.getElementById('gallery-name-en').value,description_gu:document.getElementById('gallery-desc-gu').value,description_en:document.getElementById('gallery-desc-en').value,status:parseInt(document.getElementById('gallery-status').value)};
        if(id) body._method='PUT';
        fetch(url,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},body:JSON.stringify(body)})
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);closeGalModal();location.reload();}else NexSchool.alert.danger(d.message||'ભૂલ');})
        .catch(function(){NexSchool.alert.danger('ભૂલ');});
    });

    window.addImage = function(gid){ document.getElementById('image-gallery-id').value=gid; document.getElementById('image-form').reset(); var m=document.getElementById('image-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';}); };

    document.getElementById('image-form').addEventListener('submit', function(e){
        e.preventDefault();
        var gid=document.getElementById('image-gallery-id').value;
        var fd=new FormData(); fd.append('image',document.getElementById('image-file').files[0]);
        fd.append('caption_gu',document.getElementById('image-cap-gu').value);
        fd.append('caption_en',document.getElementById('image-cap-en').value);
        var btn=document.getElementById('img-submit'); btn.disabled=true; btn.innerHTML='<i class="lni lni-spinner-3 text-sm animate-spin"></i> ઉમેરાય છે...';
        fetch('{{ url("galleries") }}/'+gid+'/images',{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf},body:fd})
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){NexSchool.alert.success(d.message);closeImgModal();location.reload();}else NexSchool.alert.danger(d.message||'ભૂલ');})
        .catch(function(){NexSchool.alert.danger('ભૂલ');})
        .finally(function(){btn.disabled=false;btn.innerHTML='<i class="lni lni-check-circle-1 text-sm"></i> ઉમેરો';});
    });

    window.deleteGallery = function(id) {
        NexSchool.confirm.show('ખાતરી કરો','આ ગેલેરી અને તેના બધા ફોટા કાઢી નાખશો?','danger').then(function(){
            fetch('{{ url("galleries") }}/'+id,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
            .then(function(r){return r.json();}).then(function(d){if(d.success){NexSchool.alert.success(d.message);location.reload();}})
            .catch(function(){NexSchool.alert.danger('ભૂલ');});
        });
    };

    window.deleteImage = function(id) {
        NexSchool.confirm.show('ખાતરી કરો','આ ફોટો કાઢી નાખશો?','danger').then(function(){
            fetch('{{ url("gallery-images") }}/'+id,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
            .then(function(r){return r.json();}).then(function(d){if(d.success){NexSchool.alert.success(d.message);location.reload();}})
            .catch(function(){NexSchool.alert.danger('ભૂલ');});
        });
    };
})();
</script>
@endpush
