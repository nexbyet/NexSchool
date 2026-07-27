<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>બસ ફી રસીદ</title>
<style>
    @page { margin: 6mm; size: A4 portrait; }
    body { font-family: Anek Gujarati, sans-serif; margin: 0; padding: 0; font-size: 12px; color: #111; }
    .rcpt-wrap { max-width: 190mm; margin: 0 auto; border: 1px solid #d1d5db; border-radius: 6px; overflow: hidden; }
    .rcpt-header { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-bottom: 2px solid #9ca3af; }
    .rcpt-header img { max-height: 44px; width: auto; }
    .rcpt-header .school-name { font-size: 14px; font-weight: 700; }
    .rcpt-header .school-sub { font-size: 10px; color: #6b7280; }
    .rcpt-body { padding: 10px 14px; }
    .rcpt-title { text-align: center; font-size: 13px; font-weight: 700; border-bottom: 1px solid #333; padding-bottom: 4px; margin-bottom: 8px; }
    .info-table { width: 100%; font-size: 11px; border-collapse: collapse; }
    .info-table td { padding: 2px 4px; vertical-align: top; }
    .info-table .lbl { color: #6b7280; width: 55px; }
    .fee-box { border: 1.5px solid #d1d5db; border-radius: 4px; padding: 6px 8px; margin: 6px 0; font-size: 11px; }
    .fee-box .head { font-size: 10px; font-weight: 700; color: #4b5563; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; margin-bottom: 4px; }
    .fee-box .row { display: flex; justify-content: space-between; padding: 1px 0; }
    .fee-box .row .lbl { color: #6b7280; }
    .fee-box .row .val { font-weight: 600; }
    .amount-words { text-align: center; font-size: 10px; color: #4b5563; border-top: 1px solid #e5e7eb; padding-top: 4px; margin-top: 6px; }
    .amount-words strong { color: #111; font-size: 11px; }
    .footer-note { text-align: center; font-size: 9px; color: #9ca3af; font-style: italic; margin-top: 4px; }
    .no-print { text-align: center; margin-bottom: 6px; }
    .no-print button { padding: 6px 16px; font-size: 12px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; background: #059669; color: #fff; }
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .rcpt-wrap { border: none; box-shadow: none; }
    }
</style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()">🖨️ પ્રિન્ટ કરો</button>
    <button onclick="window.close()" style="background:#6b7280;margin-left:6px;">બંધ કરો</button>
</div>

<div class="rcpt-wrap">
    <div class="rcpt-header">
        @if($school && $school->logo)
        <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo">
        @endif
        <div>
            <div class="school-name">{{ $school->school_name_gu ?? 'શાળા' }}</div>
            @if($school && $school->school_name_en && $school->school_name_en !== ($school->school_name_gu ?? ''))
            <div class="school-sub">{{ $school->school_name_en }}</div>
            @endif
            <div class="school-sub">{{ $school->address ?? '' }}</div>
        </div>
    </div>

    <div class="rcpt-body">
        <div class="rcpt-title">બસ ફી — રસીદ @if($payment->semester)(સત્ર {{ $payment->semester }})@endif</div>

        <table class="info-table">
            <tr><td class="lbl">નામ</td><td class="font-bold">{{ $student->full_name_gu }}</td></tr>
            @if($student->standard_label)
            <tr><td class="lbl">ધોરણ</td><td>{{ $student->standard_label }}</td></tr>
            @endif
            <tr><td class="lbl">રૂટ</td><td>{{ $student->route->route_name ?? '' }}</td></tr>
            @if($student->gaam)
            <tr><td class="lbl">ગામ</td><td>{{ $student->gaam }}</td></tr>
            @endif
            <tr><td class="lbl">તારીખ</td><td>{{ $payment->payment_date ? date('d/m/Y', strtotime($payment->payment_date)) : '' }}</td></tr>
        </table>

        <div class="fee-box">
            <div class="head">ફી વિગત</div>
            <div class="row"><span class="lbl">સત્ર {{ $payment->semester }} ફી</span><span class="val">₹{{ number_format($payment->amount, 2) }}</span></div>
        </div>

        <div class="fee-box">
            <div class="row"><span class="lbl">આજે ચૂકવેલ</span><span class="val" style="color:#059669;font-size:14px;">₹{{ number_format($payment->amount, 2) }}</span></div>
        </div>

        @php
            $allPaid = (float) \App\Models\BusOnlyFeePayment::where('bus_only_student_id', $student->id)->sum('amount');
            $totalFee = (float) $student->fee_sem1 + (float) $student->fee_sem2;
            $dueAmt = max(0, $totalFee - $allPaid);
        @endphp
        <div class="fee-box" style="background:#f9fafb;">
            <div class="row"><span class="lbl">કુલ ફી</span><span class="val">₹{{ number_format($totalFee, 2) }}</span></div>
            <div class="row"><span class="lbl">ચૂકવેલ (કુલ)</span><span class="val" style="color:#059669;">₹{{ number_format($allPaid, 2) }}</span></div>
            <hr style="border:none;border-top:1px solid #d1d5db;margin:3px 0;">
            <div class="row"><span class="lbl">બાકી</span><span class="val" style="color:#dc2626;font-weight:800;">₹{{ number_format($dueAmt, 2) }}</span></div>
        </div>

        @php
            $methodLabels = ['cash' => 'રોકડા', 'bank' => 'બેંક ટ્રાન્સફર', 'cheque' => 'ચેક', 'online' => 'ઓનલાઇન'];
        @endphp
        <table class="info-table" style="font-size:10px;color:#6b7280;">
            <tr><td class="lbl">ચુકવણી</td><td>{{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}</td></tr>
            @if($payment->reference_number)
            <tr><td class="lbl">સંદર્ભ</td><td>{{ $payment->reference_number }}</td></tr>
            @endif
            @if($payment->notes)
            <tr><td class="lbl">નોંધ</td><td>{{ $payment->notes }}</td></tr>
            @endif
        </table>

        <div class="amount-words">
            રકમ અક્ષરમાં: <strong>{{ $inWords($payment->amount) }}</strong>
        </div>
        <div class="footer-note">આ રસીદ કમ્પ્યુટર દ્વારા જનરેટ થયેલ છે. તેના પર સહી જરૂરી નથી.</div>
    </div>
</div>

</body>
</html>