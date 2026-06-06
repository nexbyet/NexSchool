@extends('layouts.app')
@section('title', 'મેનુ મેનેજમેન્ટ')
@section('content')
@php $locations = ['header' => 'હેડર', 'footer' => 'ફૂટર']; @endphp
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-orange-500 to-amber-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">મેનુ મેનેજમેન્ટ</h1>
                <p class="text-orange-200 mt-1 text-sm">વેબસાઇટના નેવિગેશન મેનુ બનાવો અને એડિટ કરો</p>
            </div>
            <button onclick="openMenuModal()" class="px-4 py-2 bg-white text-orange-700 font-medium rounded-lg hover:bg-orange-50 transition flex items-center gap-2 text-sm"><i class="lni lni-plus"></i> નવું મેનુ</button>
        </div>
    </div>

    <div class="space-y-6">
        @foreach ($menus as $menu)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $menu->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $locations[$menu->location] ?? $menu->location }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="addItem({{ $menu->id }})" class="px-3 py-1.5 text-xs font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition flex items-center gap-1"><i class="lni lni-plus text-xs"></i> આઇટમ</button>
                    <button onclick="editMenu({{ $menu->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"><i class="lni lni-pencil-1"></i></button>
                    <button onclick="deleteMenu({{ $menu->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"><i class="lni lni-trash-3"></i></button>
                </div>
            </div>
            <div class="p-4">
                @php $topItems = $menu->items->whereNull('parent_id')->sortBy('sort_order'); @endphp
                @if($topItems->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">હજી સુધી કોઈ આઇટમ નથી</p>
                @else
                <div class="space-y-1">
                    @foreach ($topItems as $item)
                    <div class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 border border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800">{{ $item->title_gu }}</span>
                            <span class="text-xs text-gray-400">{{ $item->page?->slug ? '/'.$item->page->slug : $item->url }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $item->target === '_blank' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">{{ $item->target === '_blank' ? 'blank' : 'self' }}</span>
                            @if(!$item->status)<span class="text-xs px-1.5 py-0.5 bg-red-50 text-red-500 rounded">નિષ્ક્રિય</span>@endif
                        </div>
                        <div class="flex items-center gap-1">
                            @php $children = $item->children->sortBy('sort_order'); @endphp
                            @if($children->isNotEmpty())
                            <div class="text-xs text-gray-400 mr-2">{{ $children->count() }} સબ</div>
                            @endif
                            <button onclick="editItem({{ $item->id }})" class="p-1 text-gray-400 hover:text-amber-600 rounded transition"><i class="lni lni-pencil-1 text-xs"></i></button>
                            <button onclick="deleteItem({{ $item->id }})" class="p-1 text-gray-400 hover:text-red-600 rounded transition"><i class="lni lni-trash-3 text-xs"></i></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="menu-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="menu-modal-title">નવું મેનુ</h3>
        <form id="menu-form">
            <input type="hidden" id="menu-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">નામ <span class="text-red-500">*</span></label><input type="text" id="menu-name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">સ્થાન <span class="text-red-500">*</span></label><select id="menu-location" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"><option value="header">હેડર</option><option value="footer">ફૂટર</option></select></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal('menu-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>

<div id="item-modal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="item-modal-title">આઇટમ ઉમેરો</h3>
        <form id="item-form">
            <input type="hidden" id="item-id">
            <input type="hidden" id="item-menu-id">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (ગુજરાતી) <span class="text-red-500">*</span></label><input type="text" id="item-title-gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">શીર્ષક (English)</label><input type="text" id="item-title-en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">જોડો (Page અથવા URL)</label>
                    <select id="item-page-id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                        <option value="">— પેજ પસંદ કરો —</option>
                        @foreach ($pages as $p)
                        <option value="{{ $p->id }}">{{ $p->title_gu }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">અથવા કસ્ટમ URL</label><input type="text" id="item-url" placeholder="https://..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">નવી ટેબમાં ખોલો?</label><select id="item-target" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"><option value="_self">ના</option><option value="_blank">હા</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label><select id="item-status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none"><option value="1">સક્રિય</option><option value="0">નિષ્ક્રિય</option></select></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal('item-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition flex items-center gap-2"><i class="lni lni-check-circle-1 text-sm"></i> સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    function closeModal(id){ var el=document.getElementById(id); el.style.opacity='0'; setTimeout(function(){el.classList.add('hidden');},200); }
    window.closeModal = closeModal;

    window.openMenuModal = function() {
        document.getElementById('menu-id').value = '';
        document.getElementById('menu-modal-title').textContent = 'નવું મેનુ';
        document.getElementById('menu-form').reset();
        closeModal('item-modal');
        var m=document.getElementById('menu-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
    };

    window.addItem = function(menuId) {
        document.getElementById('item-id').value = '';
        document.getElementById('item-menu-id').value = menuId;
        document.getElementById('item-modal-title').textContent = 'આઇટમ ઉમેરો';
        document.getElementById('item-form').reset();
        document.getElementById('item-status').value = '1';
        var m=document.getElementById('item-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
    };

    window.editItem = function(id) {
        fetch('{{ url("menu-items") }}/' + id, { headers: { 'Accept': 'application/json' } })
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('item-id').value = d.id;
            document.getElementById('item-menu-id').value = d.menu_id;
            document.getElementById('item-modal-title').textContent = 'આઇટમ એડિટ કરો';
            document.getElementById('item-title-gu').value = d.title_gu || '';
            document.getElementById('item-title-en').value = d.title_en || '';
            document.getElementById('item-page-id').value = d.page_id || '';
            document.getElementById('item-url').value = d.url || '';
            document.getElementById('item-target').value = d.target || '_self';
            document.getElementById('item-status').value = d.status ? '1' : '0';
            var m=document.getElementById('item-modal'); m.classList.remove('hidden'); requestAnimationFrame(function(){m.style.opacity='1';});
        });
    };

    window.deleteItem = function(id) {
        NexSchool.confirm.show('ખાતરી કરો', 'આ આઇટમ કાઢી નાખશો?', 'danger').then(function(){
            fetch('{{ url("menu-items") }}/' + id, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'} })
            .then(function(r){ return r.json(); })
            .then(function(d){ if(d.success){ NexSchool.alert.success(d.message); location.reload(); }})
            .catch(function(){ NexSchool.alert.danger('ભૂલ'); });
        });
    };

    document.getElementById('menu-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('menu-id').value;
        var url = id ? '{{ url("menus") }}/' + id : '{{ url("menus") }}';
        var method = id ? 'POST' : 'POST';
        var body = { name: document.getElementById('menu-name').value, location: document.getElementById('menu-location').value };
        fetch(url, { method: method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }, body: JSON.stringify(body) })
        .then(function(r){ return r.json(); })
        .then(function(d){ if(d.success){ NexSchool.alert.success(d.message); closeModal('menu-modal'); location.reload(); }})
        .catch(function(){ NexSchool.alert.danger('ભૂલ'); });
    });

    document.getElementById('item-form').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('item-id').value;
        var url = id ? '{{ url("menu-items") }}/' + id : '{{ url("menu-items") }}';
        var method = id ? 'POST' : 'POST';
        var body = {
            menu_id: parseInt(document.getElementById('item-menu-id').value),
            title_gu: document.getElementById('item-title-gu').value,
            title_en: document.getElementById('item-title-en').value,
            page_id: document.getElementById('item-page-id').value || null,
            url: document.getElementById('item-url').value || null,
            target: document.getElementById('item-target').value,
            status: parseInt(document.getElementById('item-status').value),
        };
        if (id) { body._method = 'PUT'; }
        fetch(url, { method: method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }, body: JSON.stringify(body) })
        .then(function(r){ return r.json(); })
        .then(function(d){ if(d.success){ NexSchool.alert.success(d.message); closeModal('item-modal'); location.reload(); }})
        .catch(function(){ NexSchool.alert.danger('ભૂલ'); });
    });
})();
</script>
@endpush
