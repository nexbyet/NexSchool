@extends('layouts.app')
@section('title', 'વિદ્યાર્થી ડેશબોર્ડ')
@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Welcome heading --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">વિદ્યાર્થી ડેશબોર્ડ</h1>
                <p class="text-gray-500 mt-1">{{ $schoolSetting->school_name_gu ?? 'NexSchool' }}, {{ $student->student_name_gu }}</p>
            </div>
            @if($activeYear)
                <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl px-5 py-3 text-right shadow-sm">
                    <span class="text-xs font-medium text-cyan-200 uppercase tracking-wide">ચાલુ વર્ષ</span>
                    <p class="text-base font-bold text-white">{{ $activeYear->year }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Birthday wish --}}
    @if($isBirthday)
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400 p-5 mb-5 shadow-md">
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 bg-white/25 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="lni lni-cake-1 text-2xl text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">જન્મદિવસની હાર્દિક શુભકામનાઓ!</h3>
                <p class="text-sm text-white/80">પ્રભુ તમને લાંબુ આયુષ્ય આપે અને તમારા બધા સપના પૂરા કરે.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Student Info Summary --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="lni lni-graduation-cap-1 text-cyan-500"></i>
            <h2 class="text-base font-semibold text-gray-900">મારી માહિતી</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">GR નંબર</label>
                <p class="text-sm font-medium text-gray-800">{{ $student->gr_number ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">નામ</label>
                <p class="text-sm font-medium text-gray-800">{{ $student->student_name_gu ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">ધોરણ</label>
                <p class="text-sm font-medium text-gray-800">{{ $student->currentStandard?->name ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">વર્ગ</label>
                <p class="text-sm font-medium text-gray-800">{{ $student->currentClass?->name ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">મોબાઇલ</label>
                <p class="text-sm font-medium text-gray-800">{{ $student->mobile ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">ઈમેલ</label>
                <p class="text-sm font-medium text-gray-800">{{ $user->email ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Fee Summary (View Only) --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="lni lni-wallet-1 text-amber-500"></i>
            <h2 class="text-base font-semibold text-gray-900">ફી વિગત</h2>
        </div>

        {{-- Fee Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                <p class="text-xs text-amber-600 font-medium">કુલ ફી</p>
                <p class="text-xl font-bold text-amber-800">₹{{ number_format($totalDue) }}</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <p class="text-xs text-emerald-600 font-medium">ચૂકવેલ</p>
                <p class="text-xl font-bold text-emerald-800">₹{{ number_format($totalPaid) }}</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                <p class="text-xs text-red-600 font-medium">બાકી</p>
                <p class="text-xl font-bold text-red-800">₹{{ number_format(max(0, $totalDue - $totalPaid)) }}</p>
            </div>
        </div>

        {{-- Fee Structure Details --}}
        @if($feeAssignments->count())
        <h3 class="text-sm font-semibold text-gray-700 mb-3">ફી માળખું</h3>
        <div class="overflow-x-auto mb-5">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-2 px-3 font-semibold text-gray-600">ફી હેડ</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600">રકમ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($feeAssignments as $assignment)
                    <tr>
                        <td class="py-2 px-3 text-gray-800">{{ optional(optional($assignment->feeStructure)->details->first())->feeHead?->name_gu ?? '—' }}</td>
                        <td class="py-2 px-3 text-right text-gray-800">₹{{ number_format($assignment->feeStructure?->total_amount ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td class="py-2 px-3 text-gray-900">કુલ</td>
                        <td class="py-2 px-3 text-right text-gray-900">₹{{ number_format($totalDue) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        {{-- Payment History --}}
        @if($feePayments->count())
        <h3 class="text-sm font-semibold text-gray-700 mb-3">ચૂકવણીનો ઇતિહાસ</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-2 px-3 font-semibold text-gray-600">તારીખ</th>
                        <th class="text-left py-2 px-3 font-semibold text-gray-600">રસીદ #</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600">રકમ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($feePayments as $payment)
                    <tr>
                        <td class="py-2 px-3 text-gray-800">{{ ($payment->payment_date ?? $payment->created_at)?->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 text-gray-800">{{ $payment->receipt_number ?? '—' }}</td>
                        <td class="py-2 px-3 text-right text-gray-800">₹{{ number_format($payment->amount_paid ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if(!$feeAssignments->count() && !$feePayments->count())
        <p class="text-sm text-gray-400 text-center py-4">કોઈ ફી માહિતી ઉપલબ્ધ નથી.</p>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <i class="lni lni-bulb-2 text-amber-500 text-lg"></i>
            <h2 class="text-lg font-semibold text-gray-900">ઝડપી ક્રિયાઓ</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('profile.index') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl hover:from-cyan-100 hover:to-blue-100 transition group border border-cyan-100">
                <div class="w-11 h-11 bg-gradient-to-br from-cyan-400 to-cyan-500 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-user-4 text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-cyan-700">પ્રોફાઇલ</p>
                    <p class="text-xs text-gray-500">તમારી માહિતી જુઓ અને એડિટ કરો</p>
                </div>
            </a>
            <a href="{{ route('profile.index') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl hover:from-amber-100 hover:to-yellow-100 transition group border border-amber-100">
                <div class="w-11 h-11 bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="lni lni-wallet-1 text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-amber-700">ફી વિગત</p>
                    <p class="text-xs text-gray-500">તમારી ફી ની માહિતી જુઓ</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
