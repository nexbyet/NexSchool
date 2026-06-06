@extends('layouts.app')
@section('title', 'શાળા માહિતી')
@section('content')
<div class="p-4 md:p-6">
    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-700 to-gray-900 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">શાળા માહિતી</h1>
                <p class="text-gray-300 mt-1 text-sm">તમારી શાળાની વિગતો અહીં સંચાલિત કરો</p>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Logo Section --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 bg-gradient-to-br from-amber-100 to-amber-200 rounded-lg flex items-center justify-center">
                <i class="lni lni-gallery text-amber-600 text-sm"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">શાળાનો લોગો</h2>
        </div>
        <div class="flex items-center gap-6">
            <div id="logo-preview" class="w-24 h-24 bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300">
                @if ($setting->logo)
                    <img src="{{ asset('storage/' . $setting->logo) }}" class="w-full h-full object-contain">
                @else
                    <i class="lni lni-gallery text-4xl text-gray-300"></i>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">લોગો બદલો</label>
                <input type="file" id="logo-input" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="text-xs text-gray-400 mt-1">ફાઇલ પસંદ કરતાં જ આપમેળે અપલોડ થશે</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.school-info.update') }}" class="space-y-5" id="settings-form">
        @csrf
        @method('POST')

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg flex items-center justify-center">
                    <i class="lni lni-buildings-1 text-indigo-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">શાળાનું નામ</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શાળાનું નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                    <input type="text" name="school_name_gu" value="{{ old('school_name_gu', $setting->school_name_gu) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    @error('school_name_gu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Name (English) <span class="text-red-500">*</span></label>
                    <input type="text" name="school_name_en" value="{{ old('school_name_en', $setting->school_name_en) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    @error('school_name_en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center">
                    <i class="lni lni-buildings-1 text-purple-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">સંચાલન મંડળ</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">નામ (ગુજરાતી)</label>
                    <input type="text" name="management_name_gu" value="{{ old('management_name_gu', $setting->management_name_gu) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (English)</label>
                    <input type="text" name="management_name_en" value="{{ old('management_name_en', $setting->management_name_en) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-gradient-to-br from-amber-100 to-amber-200 rounded-lg flex items-center justify-center">
                    <i class="lni lni-map-marker-1-1 text-amber-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">સરનામું</h2>
            </div>
            <textarea name="address" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">{{ old('address', $setting->address) }}</textarea>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-lg flex items-center justify-center">
                    <i class="lni lni-file-pencil text-emerald-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">મંજૂરી & UID</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">મંજૂરી નંબર</label>
                    <input type="text" name="grant_number" value="{{ old('grant_number', $setting->grant_number) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">મંજૂરી તારીખ</label>
                    <input type="date" name="grant_date" value="{{ old('grant_date', $setting->grant_date) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">શાળા UID (11 અંક)</label>
                    <input type="text" name="uid_number" value="{{ old('uid_number', $setting->uid_number) }}" maxlength="11" pattern="\d{11}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    @error('uid_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                    <i class="lni lni-telephone-1 text-blue-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">સંપર્ક માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ઇમેઇલ</label>
                    <input type="email" name="email" value="{{ old('email', $setting->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">મોબાઇલ નંબર</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $setting->mobile) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp નંબર</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $setting->whatsapp) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center">
                    <i class="lni lni-share-1 text-sky-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">સોશિયલ મીડિયા</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5"><i class="lni lni-facebook text-blue-600 text-sm"></i> Facebook</label>
                    <input type="url" name="facebook" value="{{ old('facebook', $setting->facebook) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5"><i class="lni lni-instagram text-pink-600 text-sm"></i> Instagram</label>
                    <input type="url" name="instagram" value="{{ old('instagram', $setting->instagram) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5"><i class="lni lni-youtube text-red-600 text-sm"></i> YouTube</label>
                    <input type="url" name="youtube" value="{{ old('youtube', $setting->youtube) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5"><i class="lni lni-globe-1 text-indigo-600 text-sm"></i> Website</label>
                    <input type="url" name="website" value="{{ old('website', $setting->website) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</a>
            <button type="submit" id="save-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition flex items-center gap-2 shadow-sm">
                <i class="lni lni-check-circle-1 text-base"></i> સાચવો
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('logo-input').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const preview = document.getElementById('logo-preview');
        const formData = new FormData();
        formData.append('logo', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        NexSchool.alert.info('લોગો અપલોડ થઈ રહ્યું છે...');
        fetch('{{ route('settings.school-info.logo') }}', {
            method: 'POST', body: formData, headers: { 'Accept': 'application/json' },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                preview.innerHTML = '<img src="' + data.logo_url + '?t=' + Date.now() + '" class="w-full h-full object-contain">';
                NexSchool.alert.success(data.message);
            } else { NexSchool.alert.danger('અપલોડ નિષ્ફળ'); }
        })
        .catch(() => { NexSchool.alert.danger('અપલોડ નિષ્ફળ — ફરી પ્રયાસ કરો'); });
    });

    document.getElementById('settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('save-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> સાચવાઈ રહ્યું છે...';
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST', body: formData, headers: { 'Accept': 'application/json' },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { NexSchool.alert.success(data.message); location.reload(); }
            else { NexSchool.alert.danger('સાચવવામાં ભૂલ: ' + (data.message || '')); }
        })
        .catch(() => { NexSchool.alert.danger('સર્વર ભૂલ — ફરી પ્રયાસ કરો'); })
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="lni lni-check-circle-1 text-base"></i> સાચવો'; });
    });
</script>
@endpush

