@extends('layouts.app')
@section('title', $student->student_name_gu . ' - પ્રોફાઇલ')
@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-600 via-cyan-500 to-blue-500 p-6 sm:p-8 mb-6 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-end gap-5">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg border-2 border-white/30 shrink-0 overflow-hidden">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover">
                @else
                    <span class="text-3xl sm:text-4xl font-bold text-white">{{ substr($student->student_name_gu ?? 'S', 0, 1) }}</span>
                @endif
            </div>
            <div class="text-center sm:text-left flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-white truncate">{{ $student->student_name_gu }}</h1>
                <p class="text-sm text-cyan-100 mt-0.5">GR: {{ $student->gr_number }}</p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                        <i class="lni lni-graduation-cap-1 text-[10px]"></i> વિદ્યાર્થી
                    </span>
                    @if($student->currentStandard)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/15 text-white/90 backdrop-blur-sm">
                        <i class="lni lni-buildings-1 text-[10px]"></i> {{ $student->currentStandard->name }}
                    </span>
                    @endif
                    @if($student->currentClass)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/15 text-white/90 backdrop-blur-sm">
                        {{ $student->currentClass->name }}
                    </span>
                    @endif
                    @if($student->status === 'active')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/30 text-white backdrop-blur-sm">સક્રિય</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-400/30 text-white backdrop-blur-sm">{{ $student->status }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('students.index') }}" class="shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-medium transition backdrop-blur-sm flex items-center gap-1.5">
                <i class="lni lni-arrow-left text-xs"></i> પાછા
            </a>
        </div>
    </div>

    {{-- Birthday wish banner --}}
    @if(isset($isBirthday) && $isBirthday)
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400 p-5 mb-6 shadow-md">
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
        {{-- Personal & Guardian --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <i class="lni lni-user-4 text-cyan-500"></i>
                    <h2 class="text-base font-semibold text-gray-900">વ્યક્તિગત માહિતી</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">પૂરું નામ (ગુજરાતી)</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->full_name_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">પૂરું નામ (અંગ્રેજી)</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->full_name_en ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">જન્મ તારીખ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y') : '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">જન્મ સ્થળ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->birth_place_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">વતન</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->native_place_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">શરીરક જાતિ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->sharirik_jaati === 'kumar' ? 'કુમાર' : ($student->sharirik_jaati === 'kumari' ? 'કુમારી' : '—') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <i class="lni lni-user-4 text-amber-500"></i>
                    <h2 class="text-base font-semibold text-gray-900">વાલી માહિતી</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">પિતાનું નામ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->father_name_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">માતાનું નામ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->mother_name_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">ધર્મ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->religion_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">જ્ઞાતિ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->cast_gu ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">શ્રેણી</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->category_gu ?? '—' }}</p>
                    </div>
                    @if($student->is_minority)
                    <div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700">લઘુમતી</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Academic Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="lni lni-book-1 text-indigo-500"></i>
                <h2 class="text-base font-semibold text-gray-900">શૈક્ષણિક માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">GR નંબર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->gr_number }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">હાલનું ધોરણ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->currentStandard?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">હાલનો વર્ગ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->currentClass?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">પ્રવેશ ધોરણ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->admissionStandard?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">પ્રવેશ વર્ગ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->admissionClass?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">પ્રવેશ તારીખ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->date_of_admission ? \Carbon\Carbon::parse($student->date_of_admission)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">છેલ્લી શાળા</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->last_school_gu ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">RTE હેઠળ પ્રવેશ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->admission_under_rte ? 'હા' : 'ના' }}</p>
                </div>
            </div>
        </div>

        {{-- Contact & Documents --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <i class="lni lni-envelope-1 text-rose-500"></i>
                <h2 class="text-base font-semibold text-gray-900">સંપર્ક અને દસ્તાવેજ</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">મોબાઇલ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->mobile ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">વોટ્સએપ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->whatsapp ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">આધાર નંબર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->aadhar_no ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">આધાર પર નામ</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->name_as_per_aadhar ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">APAAR ID</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->apaar_id ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">UID</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->uid_no ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">PEN નંબર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->pen_no ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">રેશનકાર્ડ નંબર</label>
                    <p class="text-sm font-medium text-gray-800">{{ $student->ration_card_no ?? '—' }}</p>
                </div>
            </div>

            @if($student->bank_name || $student->bank_account_no)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <label class="block text-xs font-medium text-gray-400 mb-2.5">બેંક વિગત</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">બેંક નામ</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->bank_name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">શાખા</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->bank_branch ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">IFSC</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->bank_ifsc ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">ખાતા નંબર</label>
                        <p class="text-sm font-medium text-gray-800">{{ $student->bank_account_no ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if($student->status !== 'active' && ($student->leaving_reason_gu || $student->leaving_date))
        <div class="bg-rose-50 rounded-2xl border border-rose-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <i class="lni lni-ban-2 text-rose-500"></i>
                <h2 class="text-base font-semibold text-rose-900">શાળા ત્યાગ માહિતી</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($student->leaving_date)
                <div>
                    <label class="block text-xs font-medium text-rose-400 mb-1">તારીખ</label>
                    <p class="text-sm font-medium text-rose-800">{{ \Carbon\Carbon::parse($student->leaving_date)->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($student->lc_number)
                <div>
                    <label class="block text-xs font-medium text-rose-400 mb-1">LC નંબર</label>
                    <p class="text-sm font-medium text-rose-800">{{ $student->lc_number }}</p>
                </div>
                @endif
                @if($student->leaving_reason_gu)
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-rose-400 mb-1">કારણ</label>
                    <p class="text-sm font-medium text-rose-800">{{ $student->leaving_reason_gu }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
