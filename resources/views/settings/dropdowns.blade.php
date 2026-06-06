@extends('layouts.app')
@section('title', 'ડ્રોપડાઉન વિકલ્પો')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">ડ્રોપડાઉન વિકલ્પો</h1>
            <p class="text-teal-200 mt-1 text-sm">ફોર્મમાં દેખાતા ડ્રોપડાઉન વિકલ્પો મેનેજ કરો</p>
        </div>
    </div>

    <div class="space-y-6">
        @foreach ($settings as $key => $setting)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $setting['label'] }}</h3>
            <form class="dropdown-form" data-key="{{ $key }}">
                <div class="space-y-2" id="options-{{ $key }}">
                    @forelse ($setting['value'] as $opt)
                    <div class="option-row flex items-center gap-2">
                        <span class="cursor-grab text-gray-300"><i class="lni lni-arrow-all-direction text-xs"></i></span>
                        <input type="text" name="options[][label]" value="{{ $opt['label'] }}" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none" placeholder="લેબલ">
                        <input type="text" name="options[][value]" value="{{ $opt['value'] }}" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none" placeholder="મૂલ્ય">
                        <button type="button" onclick="this.closest('.option-row').remove()" class="p-1.5 text-gray-400 hover:text-red-500"><i class="lni lni-xmark-circle"></i></button>
                    </div>
                    @empty
                    <div class="option-row flex items-center gap-2">
                        <span class="cursor-grab text-gray-300"><i class="lni lni-arrow-all-direction text-xs"></i></span>
                        <input type="text" name="options[][label]" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none" placeholder="લેબલ">
                        <input type="text" name="options[][value]" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none" placeholder="મૂલ્ય">
                        <button type="button" onclick="this.closest('.option-row').remove()" class="p-1.5 text-gray-400 hover:text-red-500"><i class="lni lni-xmark-circle"></i></button>
                    </div>
                    @endforelse
                </div>
                <button type="button" onclick="addOption('{{ $key }}')" class="mt-2 px-3 py-1.5 text-xs font-medium text-teal-600 bg-teal-50 hover:bg-teal-100 rounded-lg transition flex items-center gap-1"><i class="lni lni-plus text-xs"></i> વિકલ્પ ઉમેરો</button>
                <button type="submit" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition">સાચવો</button>
            </form>
        </div>
        @endforeach
    </div>
</div>

<script>
(function(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.addOption = function(key){
        var container = document.getElementById('options-'+key);
        var div = document.createElement('div');
        div.className = 'option-row flex items-center gap-2';
        div.innerHTML = '<span class="cursor-grab text-gray-300"><i class="lni lni-arrow-all-direction text-xs"></i></span><input type="text" name="options[][label]" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none" placeholder="લેબલ"><input type="text" name="options[][value]" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none" placeholder="મૂલ્ય"><button type="button" onclick="this.closest(\'.option-row\').remove()" class="p-1.5 text-gray-400 hover:text-red-500"><i class="lni lni-xmark-circle"></i></button>';
        container.appendChild(div);
    };

    document.querySelectorAll('.dropdown-form').forEach(function(form){
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var key = this.dataset.key;
            var rows = this.querySelectorAll('.option-row');
            var options = [];
            rows.forEach(function(row){
                var inputs = row.querySelectorAll('input');
                if(inputs[0] && inputs[1] && inputs[0].value && inputs[1].value){
                    options.push({label: inputs[0].value, value: inputs[1].value});
                }
            });
            var btn = this.querySelector('button[type="submit"]');
            btn.disabled = true; btn.textContent = 'સાચવાય છે...';
            fetch('{{ route("settings.dropdowns.update") }}', {
                method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
                body:JSON.stringify({key: key, options: options})
            })
            .then(function(r){return r.json();})
            .then(function(d){if(d.success){NexSchool.alert.success(d.message);}})
            .catch(function(){NexSchool.alert.danger('ભૂલ');})
            .finally(function(){btn.disabled = false; btn.textContent = 'સાચવો';});
        });
    });
})();
</script>
@endsection
