<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
<meta charset="UTF-8">
<title>ફી રજિસ્ટર - {{ $standard->name }} - {{ $class->name }} - {{ $academicYear->year }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    @page { size: Legal landscape; margin: 6mm 5mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Anek Gujarati', 'DejaVu Sans', sans-serif;
        font-size: 10pt;
        color: #000;
        line-height: 1.3;
    }
    .header { text-align: center; margin-bottom: 3px; }
    .header-top { display: flex; align-items: center; justify-content: center; gap: 12px; }
    .logo { width: 55px; height: 55px; object-fit: contain; }
    .school-name { font-size: 18pt; font-weight: 800; }
    .school-addr { font-size: 8pt; color: #555; }
    .header-line { border-top: 3px solid #000; margin: 3px 0; }
    .title { font-size: 14pt; font-weight: 800; text-align: center; margin: 3px 0; }
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 4px 8px; background: #f0f4f8; border: 1.5px solid #94a3b8;
        font-size: 9pt; font-weight: 600; margin-bottom: 4px;
    }

    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 3px 3px; text-align: center; vertical-align: middle; }
    th { background: #d1d5db; font-weight: 700; font-size: 8pt; }
    thead tr:first-child th { border-bottom: 3px solid #000; }

    .col-sr { width: 7mm; font-size: 7pt; }
    .col-name { text-align: left !important; padding-left: 4px !important; min-width: 36mm; max-width: 44mm; font-size: 9pt; }
    .col-addr { text-align: left !important; padding-left: 4px !important; min-width: 26mm; max-width: 32mm; font-size: 7.5pt; }
    .col-mobile { width: 16mm; font-size: 8pt; }
    .col-amount { width: 13mm; font-size: 8pt; text-align: center !important; font-weight: 600; }
    .col-pay { width: 11mm; font-size: 7pt; }
    .col-balance { width: 14mm; font-size: 10pt; text-align: center !important; font-weight: 800; }
    .pay-receipt { font-size: 5.5pt; color: #444; display: block; }
    .pay-amount { font-size: 7.5pt; font-weight: 700; display: block; }
    .waived-badge { display: inline-block; background: #059669; color: #fff; font-weight: 700; font-size: 6.5pt; padding: 1px 6px; border-radius: 2px; }
    .trans-bg { background: #fffbeb; }

    .footer { margin-top: 8px; display: flex; justify-content: space-between; font-size: 8pt; }
    .sign-line { border-top: 1.5px solid #333; display: inline-block; min-width: 45mm; padding-top: 2px; }

    .no-print { text-align: center; margin-bottom: 8px; }
    .no-print .btn { padding: 8px 24px; font-size: 11pt; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: #e11d48; color: #fff; }

    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { display: none !important; }
        table { page-break-after: auto; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
    }
</style>
</head>
<body>

<div class="no-print">
    <button class="btn" onclick="window.print()">🖨️ {{ $lang === 'gu' ? 'પ્રિન્ટ કરો' : 'Print' }}</button>
</div>

<div class="header">
    <div class="header-top">
        @if($school && $school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="logo">
        @endif
        <div>
            <div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en ?? '' }}</div>
            <div class="school-addr">{{ $school->address ?? '' }}</div>
        </div>
    </div>
    <div class="header-line"></div>
    <div class="title">{{ $lang === 'gu' ? 'ફી રજિસ્ટર' : 'FEE REGISTER' }}</div>
</div>

<div class="info-row">
    <span>{{ $lang === 'gu' ? 'ધોરણ' : 'Std' }}: <strong>{{ $standard->name }}</strong></span>
    <span>{{ $lang === 'gu' ? 'વર્ગ' : 'Class' }}: <strong>{{ $class->name }}</strong></span>
    <span>{{ $lang === 'gu' ? 'શૈક્ષણિક વર્ષ' : 'Acad Year' }}: <strong>{{ $academicYear->year }}</strong></span>
    @if($semester)
    <span>{{ $lang === 'gu' ? 'સત્ર' : 'Semester' }}: <strong>{{ $semester }}</strong></span>
    @endif
    <span>{{ $lang === 'gu' ? 'વિદ્યાર્થીઓ' : 'Students' }}: <strong>{{ count($registerData) }}</strong></span>
    @php
        $totalPayableSum = collect($registerData)->sum('total_payable');
        $totalPaidSum = collect($registerData)->sum('total_paid');
        $totalBalanceSum = collect($registerData)->sum('balance');
    @endphp
    <span>{{ $lang === 'gu' ? 'કુલ પાત્ર' : 'Payable' }}: <strong>₹{{ number_format($totalPayableSum, 2) }}</strong></span>
    <span>{{ $lang === 'gu' ? 'ભરેલ' : 'Paid' }}: <strong>₹{{ number_format($totalPaidSum, 2) }}</strong></span>
    <span>{{ $lang === 'gu' ? 'બાકી' : 'Due' }}: <strong>₹{{ number_format($totalBalanceSum, 2) }}</strong></span>
</div>

    <table>
        <thead>
            <tr>
                <th class="col-sr" rowspan="2">ક્રમ</th>
                <th class="col-name" rowspan="2">{{ $lang === 'gu' ? 'વિદ્યાર્થીનું નામ' : 'Name' }}</th>
                <th class="col-addr" rowspan="2">{{ $lang === 'gu' ? 'સરનામું' : 'Address' }}</th>
                <th class="col-mobile" rowspan="2">{{ $lang === 'gu' ? 'મોબાઇલ' : 'Mobile' }}</th>
                <th class="col-pay" rowspan="2" style="width:10mm;font-size:7pt">{{ $lang === 'gu' ? 'નોંધ' : 'Note' }}</th>
                <th class="col-amount" rowspan="2">{{ $lang === 'gu' ? 'આગલી બાકી' : 'Prev Due' }}</th>
                @if($semester == 2)
                <th class="col-amount" rowspan="2">{{ $lang === 'gu' ? 'સત્ર 1 બાકી' : 'Sem 1 Due' }}</th>
                @endif
                <th class="col-amount" rowspan="2">{{ $lang === 'gu' ? ($semester == 1 ? 'સત્ર 1 ફી' : ($semester == 2 ? 'સત્ર 2 ફી' : 'ચાલુ સત્ર')) : ($semester == 1 ? 'Sem 1 Fee' : ($semester == 2 ? 'Sem 2 Fee' : 'Current')) }}</th>
                <th class="col-amount" rowspan="2">{{ $lang === 'gu' ? 'કુલ પાત્ર' : 'Total' }}</th>
                <th colspan="{{ $schoolFeeCols }}">{{ $lang === 'gu' ? 'શાળા ફી ચુકવણી' : 'School Fee Payments' }}</th>
                <th colspan="{{ $busFeeCols }}">{{ $lang === 'gu' ? 'બસ ફી ચુકવણી' : 'Bus Fee Payments' }}</th>
                <th class="col-balance" rowspan="2">{{ $lang === 'gu' ? 'બાકી' : 'Balance' }}</th>
            </tr>
            <tr>
                @for($i = 1; $i <= $schoolFeeCols; $i++)
                <th class="col-pay">{{ $i }}</th>
                @endfor
                @for($i = 1; $i <= $busFeeCols; $i++)
                <th class="col-pay trans-bg">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($registerData as $idx => $rd)
            @php
                $student = $rd['student'];
                $addr = $student->native_place_gu ?: ($student->native_place_en ?: ($student->birth_place_gu ?: ($student->birth_place_en ?? '')));
                $mobile = $student->mobile ?? ($student->whatsapp ?? '');
                $waived = $rd['is_waived'];
            @endphp
            <tr>
                <td class="col-sr">{{ $idx + 1 }}</td>
                <td class="col-name">{{ $student->full_name_gu ?: $student->full_name_en }}</td>
                <td class="col-addr">{{ $addr ?: '—' }}</td>
                <td class="col-mobile">{{ $mobile ?: '—' }}</td>
                <td class="col-pay" style="font-size:7pt;font-weight:700">{{ $rd['note'] ?: '' }}</td>
                <td class="col-amount">{{ $rd['prev_dues'] > 0 ? '₹' . number_format($rd['prev_dues'], 2) : '—' }}</td>
                @if($semester == 2)
                <td class="col-amount">{{ $rd['sem1_balance'] > 0 ? '₹' . number_format($rd['sem1_balance'], 2) : '—' }}</td>
                @endif
                <td class="col-amount">
                    @if($waived)
                        <span class="waived-badge">{{ $lang === 'gu' ? 'માફી' : 'WAIVED' }}</span>
                    @else
                        {{ $rd['current_fee'] > 0 ? '₹' . number_format($rd['current_fee'], 2) : '—' }}
                    @endif
                </td>
                <td class="col-amount" style="font-weight:800">{{ '₹' . number_format($rd['total_payable'], 2) }}</td>
                @for($i = 0; $i < $schoolFeeCols; $i++)
                <td class="col-pay">
                    @if(isset($rd['school_payments'][$i]))
                        <span class="pay-receipt">{{ $rd['school_payments'][$i]['receipt'] }}</span>
                        <span class="pay-amount">₹{{ number_format($rd['school_payments'][$i]['amount'], 2) }}</span>
                    @endif
                </td>
                @endfor
                @for($i = 0; $i < $busFeeCols; $i++)
                <td class="col-pay trans-bg">
                    @if(isset($rd['bus_payments'][$i]))
                        <span class="pay-receipt">{{ $rd['bus_payments'][$i]['receipt'] }}</span>
                        <span class="pay-amount">₹{{ number_format($rd['bus_payments'][$i]['amount'], 2) }}</span>
                    @endif
                </td>
                @endfor
                <td class="col-balance">{{ $rd['balance'] > 0 ? '₹' . number_format($rd['balance'], 2) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

<div class="footer">
    <span>{{ $lang === 'gu' ? 'P = શાળા ફી ચુકવણી, B = બસ ફી ચુકવણી' : 'S = School Fee, B = Bus Fee' }}</span>
    <span>{{ $lang === 'gu' ? 'તારીખ :' : 'Date:' }} ___________</span>
    <span>{{ $lang === 'gu' ? 'સહી :' : 'Sign:' }} <span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
</div>

</body>
</html>
