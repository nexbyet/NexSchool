@extends('frontsite.layouts.master')
@section('title', 'પ્રવેશ અરજી')
@section('meta_description', 'ઓનલાઇન પ્રવેશ અરજી ફોર્મ')
@section('content')

<section class="bg-gradient-to-r from-sky-600 to-cyan-700 text-white py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-4xl font-bold">ઓનલાઇન પ્રવેશ અરજી</h1>
        <p class="text-sky-200 text-sm mt-1">નવા પ્રવેશ માટે અરજી ફોર્મ ભરો</p>
    </div>
</section>

<section class="py-12 md:py-16 bg-white">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center"><i class="lni lni-file-pencil text-sky-600"></i></div>
                <div><h2 class="text-lg font-bold text-gray-900">અરજી ફોર્મ</h2><p class="text-xs text-gray-400">નીચેની માહિતી ભરો</p></div>
            </div>
            <form id="admission-form">
                <div class="grid md:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name_gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="નામ">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">પિતાનું નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                        <input type="text" name="father_name_gu" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="પિતાનું નામ">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">અટક (ગુજરાતી)</label>
                        <input type="text" name="surname_gu" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="અટક">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name (English) </label>
                        <input type="text" name="first_name_en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="First Name">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Father Name (English)</label>
                        <input type="text" name="father_name_en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="Father Name">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Surname (English)</label>
                        <input type="text" name="surname_en" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="Surname">
                    </div>
                </div>

                <div class="border-t border-gray-100 my-4"></div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">લિંગ <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                            <option value="">— પસંદ કરો —</option>
                            @foreach ($genders as $g)
                            <option value="{{ $g['value'] }}">{{ $g['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">જન્મ તારીખ <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">ધોરણ (પ્રવેશ માટે) <span class="text-red-500">*</span></label>
                        <select name="standard_applied_for" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                            <option value="">— પસંદ કરો —</option>
                            @foreach ($standards as $std)
                            <option value="{{ $std->name }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">અગાઉની શાળા</label>
                        <input type="text" name="previous_school" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="જો હોય તો">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">પિતાનું નામ <span class="text-red-500">*</span></label>
                        <input type="text" name="father_name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">માતાનું નામ <span class="text-red-500">*</span></label>
                        <input type="text" name="mother_name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">ફોન નંબર <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="98765 43210">
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">ઇમેઇલ</label>
                        <input type="email" name="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="email@example.com">
                    </div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">સરનામું</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" placeholder="સંપૂર્ણ સરનામું"></textarea>
                    </div>
                </div>
                <button type="submit" id="submit-btn" class="mt-6 w-full px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-sky-200">
                    <i class="lni lni-check-circle-1"></i> અરજી સબમિટ કરો
                </button>
            </form>
            <div id="success-msg" class="hidden mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                <i class="lni lni-check-circle-1 text-3xl text-emerald-500 mb-2"></i>
                <p class="text-emerald-800 font-medium" id="success-text">તમારી અરજી સફળતાપૂર્વક સબમિટ થઈ ગઈ છે!</p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('admission-form').addEventListener('submit', function(e){
    e.preventDefault();
    var btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 animate-spin"></i> સબમિટ થાય છે...';
    var form = this;
    var fd = new FormData(form);
    var data = {};
    fd.forEach(function(v,k){ data[k] = v; });
    fetch('{{ route("frontsite.admission.submit") }}', {
        method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
        body:JSON.stringify(data)
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(d.success){
            form.classList.add('hidden');
            document.getElementById('success-msg').classList.remove('hidden');
            document.getElementById('success-text').textContent = d.message;
        } else {
            NexSchool.alert.danger(d.message || 'ભૂલ');
            btn.disabled = false;
            btn.innerHTML = '<i class="lni lni-check-circle-1"></i> અરજી સબમિટ કરો';
        }
    })
    .catch(function(){
        NexSchool.alert.danger('સર્વર ભૂલ. ફરી પ્રયાસ કરો.');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-check-circle-1"></i> અરજી સબમિટ કરો';
    });
});
</script>
@endsection
