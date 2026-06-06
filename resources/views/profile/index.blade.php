@extends('layouts.app')
@section('title', 'પ્રોફાઇલ')
@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    editModal: false,
    passwordModal: false,
    loading: false,
    form: { name: '{{ $user->name }}', email: '{{ $user->email }}', phone: '{{ $profile?->phone ?? $profile?->mobile ?? '' }}', address: '{{ $profile?->address ?? '' }}' },
    passForm: { current_password: '', password: '', password_confirmation: '' }
}">
    {{-- ==================== HERO SECTION ==================== --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-cyan-500 p-6 sm:p-8 mb-6 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-end gap-5">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg border-2 border-white/30 shrink-0">
                @if(($profile?->photo ?? null) && $user->role === 'student')
                    <img src="{{ asset('storage/' . $profile->photo) }}" class="w-full h-full rounded-2xl object-cover">
                @else
                    <span class="text-3xl sm:text-4xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                @endif
            </div>
            <div class="text-center sm:text-left flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-white truncate">{{ $user->name }}</h1>
                <p class="text-sm text-indigo-100 mt-0.5">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm capitalize">
                        @switch($user->role)
                            @case('admin') <i class="lni lni-shield-2 text-[10px]"></i> એડમિન @break
                            @case('teacher') <i class="lni lni-user-4 text-[10px]"></i> શિક્ષક @break
                            @case('student') <i class="lni lni-graduation-cap-1 text-[10px]"></i> વિદ્યાર્થી @break
                            @default {{ $user->role }}
                        @endswitch
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/15 text-white/90 backdrop-blur-sm">
                        <i class="lni lni-calendar-days text-[10px]"></i> {{ $user->created_at->format('d/m/Y') }} થી
                    </span>
                </div>
            </div>
            <button @click="editModal = true" class="shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-medium transition backdrop-blur-sm flex items-center gap-1.5">
                <i class="lni lni-pencil-1 text-xs"></i> એડિટ
            </button>
        </div>
    </div>

    {{-- Birthday wish banner --}}
    @if(isset($isBirthday) && $isBirthday)
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400 p-5 mb-5 shadow-md">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 bg-white/25 rounded-2xl flex items-center justify-center backdrop-blur-sm shrink-0 animate-bounce">
                <i class="lni lni-cake-1 text-2xl text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">🎉 જન્મદિવસની હાર્દિક શુભકામનાઓ!</h3>
                <p class="text-sm text-white/80">પ્રભુ તમને લાંબુ આયુષ્ય આપે અને તમારા બધા સપના પૂરા કરે. આજનો દિવસ તમારા માટે ખાસ છે!</p>
            </div>
        </div>
    </div>
    @endif

    <div class="space-y-5">
        {{-- ==================== ADMIN / BASE INFO ==================== --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="lni lni-user-4 text-indigo-500"></i> મૂળભૂત માહિતી
                </h2>
                <button @click="editModal = true" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                    <i class="lni lni-pencil-1 text-[10px]"></i> એડિટ
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">નામ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">ઈમેલ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $user->email }}</p>
                </div>
                @if($user->username)
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">યુઝરનેમ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $user->username }}</p>
                </div>
                @endif
                @if($profile)
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">ફોન</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->phone ?? $profile->mobile ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">સરનામું</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->address ?? '—' }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ==================== TEACHER INFO ==================== --}}
        @if($user->role === 'teacher' && $profile)
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="lni lni-book-1 text-emerald-500"></i>
                <h2 class="text-base font-semibold text-gray-900">શિક્ષક માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">શિક્ષક ક્રમાંક</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->teacher_id ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">જન્મ તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">લિંગ</label>
                    <p class="text-sm font-medium text-gray-800 capitalize">{{ $profile->gender ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">લોહીનો પ્રકાર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->blood_group ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">જોડાવાની તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->joining_date ? \Carbon\Carbon::parse($profile->joining_date)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">અનુભવ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->experience_in_years ? $profile->experience_in_years . ' વર્ષ' : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">લાયકાત</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->qualification ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">વિશેષતા</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->specialization ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">સ્થિતિ</label>
                    @if($profile->status === 'active')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">સક્રિય</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600">નિષ્ક્રિય</span>
                    @endif
                </div>
            </div>
            @if($teacherSubjects->count())
            <div class="mt-5 pt-5 border-t border-gray-100">
                <label class="block text-xs font-medium text-gray-400 mb-2">વિષયો</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($teacherSubjects as $subj)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $subj->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            @if($teacherClasses->count())
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">વર્ગશિક્ષક</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($teacherClasses as $cls)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                            <i class="lni lni-buildings-1 text-[9px]"></i> {{ $cls->standard?->name ?? '' }} - {{ $cls->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ==================== STUDENT INFO ==================== --}}
        @if(in_array($user->role, ['student', 'parent', 'staff']) && $profile)
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="lni lni-graduation-cap-1 text-cyan-500"></i>
                <h2 class="text-base font-semibold text-gray-900">વિદ્યાર્થી માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">GR નંબર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->gr_number ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">નામ (ગુજરાતી)</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->student_name_gu ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">નામ (અંગ્રેજી)</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->student_name_en ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">પિતાનું નામ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->father_name_gu ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">માતાનું નામ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->mother_name_gu ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">જન્મ તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">ધોરણ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $studentStandard?->name ?? ($profile->current_standard_id ?? '—') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">વર્ગ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $studentClass?->name ?? ($profile->current_class_id ?? '—') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">મોબાઇલ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->mobile ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">ધોરણ પ્રવેશ તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->date_of_admission ? \Carbon\Carbon::parse($profile->date_of_admission)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">આધાર નંબર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->aadhar_no ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">શ્રેણી</label>
                    <p class="text-sm font-medium text-gray-800">{{ $profile->category ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================== PASSWORD CHANGE ==================== --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="lni lni-locked-2 text-rose-500"></i>
                    <h2 class="text-base font-semibold text-gray-900">પાસવર્ડ</h2>
                </div>
                <button @click="passwordModal = true" class="text-xs font-medium text-rose-600 hover:text-rose-800 transition flex items-center gap-1">
                    <i class="lni lni-pencil-1 text-[10px]"></i> બદલો
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">સુરક્ષા માટે સમયાંતરે પાસવર્ડ બદલતા રહો.</p>
        </div>
    </div>

    {{-- ==================== EDIT PROFILE MODAL ==================== --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="editModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="lni lni-pencil-1 text-indigo-500"></i> પ્રોફાઇલ એડિટ કરો
                </h2>
                <button @click="editModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 transition text-gray-400 hover:text-gray-600">
                    <i class="lni lni-xmark text-lg"></i>
                </button>
            </div>
            <form @submit.prevent="
                loading = true;
                fetch('/profile/update', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') },
                    body: JSON.stringify(form)
                })
                .then(r => r.json()).then(res => {
                    if (res.success) {
                        NexSchool.alert.success(res.message);
                        editModal = false;
                        window.location.reload();
                    } else {
                        NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
                    }
                }).catch(err => {
                    NexSchool.alert.danger('સર્વર ભૂલ: ' + err.message);
                }).finally(() => { loading = false; });
            " class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">નામ</label>
                    <input type="text" x-model="form.name" required class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ઈમેલ</label>
                    <input type="email" x-model="form.email" required class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                @if($profile)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ફોન</label>
                    <input type="text" x-model="form.phone" class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">સરનામું</label>
                    <textarea x-model="form.address" rows="2" class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                </div>
                @endif
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition">રદ કરો</button>
                    <button type="submit" :disabled="loading" class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl text-sm font-medium hover:from-indigo-600 hover:to-indigo-700 transition shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i x-show="!loading" class="lni lni-floppy-disk-1 text-sm"></i>
                        <i x-show="loading" class="lni lni-spinner-3 text-sm animate-spin"></i>
                        <span x-text="loading ? 'સેવ થઈ રહ્યું...' : 'સેવ કરો'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== PASSWORD MODAL ==================== --}}
    <div x-show="passwordModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="passwordModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="lni lni-locked-2 text-rose-500"></i> પાસવર્ડ બદલો
                </h2>
                <button @click="passwordModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 transition text-gray-400 hover:text-gray-600">
                    <i class="lni lni-xmark text-lg"></i>
                </button>
            </div>
            <form @submit.prevent="
                loading = true;
                fetch('/profile/password', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') },
                    body: JSON.stringify(passForm)
                })
                .then(r => r.json()).then(res => {
                    if (res.success) {
                        NexSchool.alert.success(res.message);
                        passwordModal = false;
                        passForm = { current_password: '', password: '', password_confirmation: '' };
                    } else {
                        NexSchool.alert.danger(Object.values(res.errors || { message: res.message || 'ભૂલ આવી.' }).flat().join(', '));
                    }
                }).catch(err => {
                    NexSchool.alert.danger('સર્વર ભૂલ: ' + err.message);
                }).finally(() => { loading = false; });
            " class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">હાલનો પાસવર્ડ</label>
                    <input type="password" x-model="passForm.current_password" required class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">નવો પાસવર્ડ</label>
                    <input type="password" x-model="passForm.password" required minlength="6" class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">નવો પાસવર્ડ (ફરીથી)</label>
                    <input type="password" x-model="passForm.password_confirmation" required class="w-full text-sm border-gray-300 rounded-xl px-4 py-2.5 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="passwordModal = false" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition">રદ કરો</button>
                    <button type="submit" :disabled="loading" class="px-6 py-2.5 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl text-sm font-medium hover:from-rose-600 hover:to-rose-700 transition shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i x-show="!loading" class="lni lni-locked-2 text-sm"></i>
                        <i x-show="loading" class="lni lni-spinner-3 text-sm animate-spin"></i>
                        <span x-text="loading ? 'બદલાઈ રહ્યું...' : 'બદલો'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
