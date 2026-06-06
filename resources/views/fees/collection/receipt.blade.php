@extends('layouts.app')
@section('title', 'ફી રસીદ')
@php
    use App\Models\SchoolSetting;
    $school = SchoolSetting::find(1);
    $methodLabels = ['cash' => 'રોકડા', 'bank' => 'બેંક ટ્રાન્સફર', 'cheque' => 'ચેક', 'online' => 'ઓનલાઇન'];

    $guDigits = ['શૂન્ય','એક','બે','ત્રણ','ચાર','પાંચ','છ','સાત','આઠ','નવ'];
    $guTeens = ['દસ','અગિયાર','બાર','તેર','ચૌદ','પંદર','સોળ','સત્તર','અઢાર','ઓગણીસ'];
    $guTens = ['','','વીસ','ત્રીસ','ચાલીસ','પચાસ','સાઠ','સિત્તેર','એંસી','નેવું'];
    $inWords = function($num) use ($guDigits, $guTeens, $guTens) {
        $w = function($n) use ($guDigits, $guTeens, $guTens) {
            $r = '';
            if ($n >= 100) { $r .= $guDigits[floor($n/100)] . ' સો '; $n %= 100; }
            if ($n >= 20) { $r .= $guTens[floor($n/10)] . ' '; $n %= 10; }
            elseif ($n >= 10) { $r .= $guTeens[$n-10] . ' '; $n = 0; }
            if ($n > 0) $r .= $guDigits[$n] . ' ';
            return $r;
        };
        $whole = floor($num); $frac = round(($num - $whole) * 100);
        $words = '';
        if ($whole >= 10000000) { $words .= $guDigits[floor($whole/10000000)] . ' કરોડ '; $whole %= 10000000; }
        if ($whole >= 100000) { $words .= $guDigits[floor($whole/100000)] . ' લાખ '; $whole %= 100000; }
        if ($whole >= 1000) { $words .= $guDigits[floor($whole/1000)] . ' હજાર '; $whole %= 1000; }
        $words .= $w($whole);
        $words = trim($words) . ' રૂપિયા';
        if ($frac > 0) $words .= ' અને ' . $w($frac) . ' પૈસા';
        return $words . ' માત્ર';
    };
@endphp
<style>
    @page { margin: 5mm; size: A4 portrait; }
    @media print {
        body * { visibility: hidden; }
        #receipt-area, #receipt-area * { visibility: visible; }
        #receipt-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .rcpt-section { page-break-inside: avoid; break-inside: avoid; }
    }
</style>
@section('content')
<div class="p-3 md:p-4">
    <div class="no-print mb-3 flex items-center gap-2">
        <a href="{{ route('fees.collection.index') }}" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1"><i class="lni lni-arrow-left text-xs"></i> પાછા જાઓ</a>
        <button onclick="window.print()" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition flex items-center gap-1.5 ml-auto"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ</button>
    </div>

    <div id="receipt-area" class="mx-auto bg-white border border-gray-200 shadow-lg max-w-[210mm]">
        @foreach ($typeData as $type => $td)
        @php $firstPay = $td['payments']->first(); $showBorder = !$loop->first; @endphp
        <div class="rcpt-section p-5 {{ $showBorder ? 'border-t-2 border-gray-300 pt-5 mt-3' : '' }}">
            <div class="flex items-start gap-4 pb-3 mb-2 border-b border-gray-400">
                @if($school && $school->logo)
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="h-16 w-auto">
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-bold text-gray-900 uppercase tracking-wide">{{ $school->school_name_gu ?? 'શાળા' }}</h1>
                    @if($school && $school->school_name_en && $school->school_name_en !== ($school->school_name_gu ?? ''))
                    <p class="text-[11px] text-gray-500 uppercase tracking-wider">{{ $school->school_name_en }}</p>
                    @endif
                    <p class="text-[11px] text-gray-600 mt-0.5">{{ $school->address ?? '' }}</p>
                    @if($school && ($school->phone || $school->email))
                    <p class="text-[10px] text-gray-500 mt-0.5">
                        @if($school->phone)ફોન: {{ $school->phone }}@endif
                        @if($school->phone && $school->email) | @endif
                        @if($school->email)ઇમેઇલ: {{ $school->email }}@endif
                    </p>
                    @endif
                </div>
            </div>

            <div class="text-center mb-2 border-b-2 border-gray-900 pb-1">
                <h2 class="text-sm font-bold text-gray-800">{{ $td['label'] }} — રસીદ @if($td['semester'])<span class="font-bold text-gray-800 ml-1">(સત્ર {{ $td['semester'] }})</span>@endif</h2>
            </div>

            <div class="flex justify-between text-sm mb-3">
                <div><span class="text-gray-500">રસીદ નં:</span> <span class="font-bold text-gray-900 font-mono">{{ $td['payments']->pluck('receipt_number')->implode(', ') }}</span></div>
                <div><span class="text-gray-500">તા:</span> <span class="font-semibold text-gray-900">{{ $firstPay?->payment_date ? date('d/m/Y', strtotime($firstPay->payment_date)) : '' }}</span></div>
            </div>

            <div class="text-sm mb-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
                <table class="w-full">
                    <tr><td class="text-gray-500 w-16 py-1 align-top">નામ</td><td class="font-bold text-gray-900 py-1">{{ $student->full_name_gu ?? $student->full_name_en ?? '' }}</td></tr>
                    <tr><td class="text-gray-500 w-16 py-1">GR</td><td class="font-bold text-gray-900 py-1">{{ $student->gr_number }}</td><td class="text-gray-500 w-16 text-right py-1">ધોરણ</td><td class="font-bold text-gray-900 py-1">{{ $student->currentStandard->name ?? '' }}-{{ $student->currentClass->name ?? '' }}</td></tr>
                </table>
            </div>

            <div class="border-2 border-gray-300 rounded-lg p-3 mb-3">
                <p class="text-sm font-bold text-gray-600 mb-2 border-b border-gray-200 pb-1">ફી હેડ વિગત</p>
                @foreach ($td['heads'] as $d)
                <div class="flex justify-between text-sm py-1"><span class="text-gray-700">{{ $d->feeHead?->name_gu ?? $d->feeHead?->name_en ?? 'હેડ' }}</span><span class="font-medium text-gray-900">₹{{ number_format($d->amount, 2) }}</span></div>
                @endforeach
                <hr class="border-t border-gray-300 my-1">
                <div class="flex justify-between text-sm font-bold"><span>કુલ ચુકવવાપાત્ર રકમ</span><span>₹{{ number_format($td['net_amount'], 2) }}</span></div>
            </div>

            <div class="border-2 border-gray-300 rounded-lg p-3 mb-3">
                <div class="flex justify-between text-sm py-1"><span class="text-gray-600">આજે ચૂકવેલ રકમ</span><span class="font-bold text-emerald-700 text-base">₹{{ number_format($td['paid_now'], 2) }}</span></div>
                <div class="flex justify-between text-sm py-1"><span class="text-gray-600">આગઉ ચૂકવેલ રકમ</span><span class="font-medium text-gray-700">₹{{ number_format($td['prev_paid'], 2) }}</span></div>
                @if($td['waived'] > 0)
                <div class="flex justify-between text-sm py-1"><span class="text-gray-600">ફી માફી</span><span class="font-medium text-amber-700">₹{{ number_format($td['waived'], 2) }}</span></div>
                @endif
                <hr class="border-t border-gray-300 my-1">
                <div class="flex justify-between text-sm font-bold py-1"><span class="text-gray-700">હવે બાકી રકમ</span><span class="text-base {{ $td['due'] > 0 ? 'text-red-700' : 'text-emerald-700' }}">₹{{ number_format($td['due'], 2) }}</span></div>
            </div>

            <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                <span>ચુકવણી: <span class="font-medium text-gray-700">{{ $methodLabels[$firstPay->payment_method] ?? $firstPay->payment_method }}</span></span>
                @if($firstPay->reference_number)<span>સંદર્ભ: <span class="font-medium text-gray-700">{{ $firstPay->reference_number }}</span></span>@endif
            </div>

            <div class="text-center text-xs text-gray-500 border-t-2 border-gray-200 pt-2">
                <p>રકમ અક્ષરમાં: <span class="font-bold text-gray-800 text-sm">{{ $inWords($td['paid_now']) }}</span></p>
                <p class="italic text-[10px] text-gray-400 mt-1">આ રસીદ કમ્પ્યુટર દ્વારા જનરેટ થયેલ છે. તેના પર સહી જરૂરી નથી.</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
