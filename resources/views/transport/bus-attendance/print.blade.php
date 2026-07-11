<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
<meta charset="UTF-8">
<title>બસ હાજરી પત્રક - {{ $route->route_name }} - {{ $monthName }} {{ $data['year'] }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: Legal landscape; margin: 8mm 10mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Anek Gujarati', 'DejaVu Sans', sans-serif; font-size: 9pt; color: #000; line-height: 1.3; }
    .header { text-align: center; margin-bottom: 4px; }
    .header-top { display: flex; align-items: center; justify-content: center; gap: 12px; }
    .logo { width: 50px; height: 50px; object-fit: contain; }
    .school-name { font-size: 14pt; font-weight: 800; }
    .school-addr { font-size: 6.5pt; color: #555; }
    .header-line { border-top: 2px solid #000; margin: 3px 0; }
    .title { font-size: 11pt; font-weight: 700; text-align: center; margin-top: 2px; }
    .subtitle { font-size: 8pt; text-align: center; color: #444; margin-bottom: 3px; }

    .info-bar { display: flex; justify-content: space-between; padding: 2px 6px; background: #f0f4f8; border: 1px solid #cbd5e1; font-size: 7pt; font-weight: 500; margin-bottom: 3px; }

    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 2px 2px; text-align: center; vertical-align: middle; }
    th { background: #e2e8f0; font-weight: 600; font-size: 7pt; }
    thead tr:first-child th { border-bottom: 2px solid #000; }

    .col-sr { width: 6mm; font-size: 7pt; }
    .col-mobile { font-size: 6.5pt; text-align: center !important; }
    .col-name { text-align: left !important; padding-left: 4px !important; min-width: 32mm; font-size: 8pt; }
    .col-type { width: 8mm; font-size: 6.5pt; font-weight: 600; }
    .col-shift { width: 10mm; font-size: 6.5pt; font-weight: 600; }
    .col-date { width: 8mm; font-size: 7pt; }
    .morning { background: #f0f7ff; }
    .evening { background: #fef2f2; }
    .present { font-weight: 700; color: #059669; font-size: 8pt; }
    .absent { font-weight: 700; color: #dc2626; font-size: 8pt; }
    .leave { font-weight: 600; color: #d97706; font-size: 8pt; }
    .shift-label-m { color: #1e40af; }
    .shift-label-e { color: #be123c; }
    .type-regular { color: #1e40af; }
    .type-unregistered { color: #d97706; }
    .type-bus-only { color: #0d9488; }

    .blank td { height: 16px; }

    .footer { margin-top: 6px; display: flex; justify-content: space-between; font-size: 7pt; }
    .sign-line { border-top: 1px solid #333; display: inline-block; min-width: 50mm; padding-top: 1px; }

    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { display: none !important; }
    }
    .no-print { text-align: center; margin-bottom: 8px; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
    .no-print .btn {
        padding: 7px 20px; font-size: 10pt; font-weight: 600;
        border: none; border-radius: 6px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .no-print .btn-primary { background: #e11d48; color: #fff; }
    .no-print .btn-primary:hover { background: #be123c; }
    .no-print .btn-secondary { background: #6b7280; color: #fff; }
    .no-print .btn-secondary:hover { background: #4b5563; }
</style>
</head>
<body>

<div class="no-print">
    <button class="btn btn-primary" onclick="window.print()">🖨️ {{ $lang === 'gu' ? 'પ્રિન્ટ કરો' : 'Print' }}</button>
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
    <div class="title">{{ $lang === 'gu' ? 'બસ હાજરી પત્રક' : 'BUS ATTENDANCE REGISTER' }}</div>
    <div class="subtitle">{{ $route->route_name }} ({{ $route->vehicle?->vehicle_no ?? '' }}) — {{ $monthName }} {{ $data['year'] }} — {{ $type === 'blank' ? ($lang === 'gu' ? 'ખાલી પત્રક' : 'Blank') : ($lang === 'gu' ? 'ભરેલું પત્રક' : 'Filled') }}</div>
</div>

<div class="info-bar">
    <span>{{ $lang === 'gu' ? 'રૂટ' : 'Route' }}: <strong>{{ $route->route_name }}</strong></span>
    <span>{{ $lang === 'gu' ? 'વાહન' : 'Vehicle' }}: <strong>{{ $route->vehicle?->vehicle_no ?? '—' }}</strong></span>
    <span>{{ $lang === 'gu' ? 'ડ્રાઇવર' : 'Driver' }}: <strong>{{ $route->vehicle?->driver_name ?? '—' }}</strong></span>
    <span>{{ $lang === 'gu' ? 'કુલ વિદ્યાર્થી' : 'Students' }}: <strong>{{ $students->count() }}</strong></span>
</div>

<table>
    <thead>
        <tr>
            <th class="col-sr" rowspan="2">ક્રમ</th>
            <th rowspan="2" style="min-width:10mm">GR</th>
            <th rowspan="2" class="col-name">{{ $lang === 'gu' ? 'નામ' : 'Name' }}</th>
            <th rowspan="2" class="col-mobile">{{ $lang === 'gu' ? 'મોબાઇલ' : 'Mobile' }}</th>
            <th rowspan="2" class="col-type">{{ $lang === 'gu' ? 'પ્રકાર' : 'Type' }}</th>
            <th rowspan="2" class="col-shift">{{ $lang === 'gu' ? 'પાળી' : 'Shift' }}</th>
            <th colspan="{{ $workingDays }}" style="font-size:6.5pt">{{ $monthName }} {{ $data['year'] }}</th>
        </tr>
        <tr>
            @foreach($workingDates as $d)
            <th class="col-date">{{ $d['day'] }}<br><span style="font-weight:400;font-size:5.5pt">{{ $d['dayName'] }}</span></th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $student)
        @php
            $typeClass = match($student['type']) {
                'regular' => 'type-regular',
                'unregistered' => 'type-unregistered',
                'bus_only' => 'type-bus-only',
                default => '',
            };
            $typeLabel = $student['type_label'] ?? $student['type'];
        @endphp
        {{-- Morning row --}}
        <tr class="morning">
            <td class="col-sr" rowspan="2" style="vertical-align:middle">{{ $index + 1 }}</td>
            <td rowspan="2" style="vertical-align:middle;font-size:7pt">{{ $student['gr_number'] }}</td>
            <td class="col-name" rowspan="2" style="vertical-align:middle">{{ $student['name'] }}</td>
            <td class="col-mobile" rowspan="2" style="vertical-align:middle">{{ $student['mobile'] }}</td>
            <td rowspan="2" style="vertical-align:middle" class="{{ $typeClass }}">{{ $typeLabel }}</td>
            <td class="col-shift shift-label-m">{{ $lang === 'gu' ? 'આવક' : 'AM' }}</td>
            @foreach($workingDates as $d)
                @php
                    $key = $student['display_id'] . '-' . $d['dateKey'];
                    $att = $attendances->get($key);
                    $val = $att ? $att->morning_status : null;
                @endphp
                <td class="col-date">
                    @if($val === 'present')
                    <span class="present">P</span>
                    @elseif($val === 'absent')
                    <span class="absent">A</span>
                    @elseif($val === 'leave')
                    <span class="leave">L</span>
                    @endif
                </td>
            @endforeach
        </tr>
        {{-- Evening row --}}
        <tr class="evening">
            <td class="col-shift shift-label-e">{{ $lang === 'gu' ? 'જાવક' : 'PM' }}</td>
            @foreach($workingDates as $d)
                @php
                    $key = $student['display_id'] . '-' . $d['dateKey'];
                    $att = $attendances->get($key);
                    $val = $att ? $att->evening_status : null;
                @endphp
                <td class="col-date">
                    @if($val === 'present')
                    <span class="present">P</span>
                    @elseif($val === 'absent')
                    <span class="absent">A</span>
                    @elseif($val === 'leave')
                    <span class="leave">L</span>
                    @endif
                </td>
            @endforeach
        </tr>
        @endforeach

        {{-- Blank rows (2 per blank student) --}}
        @for($b = 0; $b < $blankRows; $b++)
        <tr class="blank">
            <td class="col-sr" rowspan="2">&nbsp;</td>
            <td rowspan="2"></td>
            <td class="col-name" rowspan="2"></td>
            <td class="col-mobile" rowspan="2"></td>
            <td rowspan="2"></td>
            <td class="col-shift shift-label-m">{{ $lang === 'gu' ? 'આવક' : 'AM' }}</td>
            @foreach($workingDates as $d)
            <td class="col-date"></td>
            @endforeach
        </tr>
        <tr class="blank">
            <td class="col-shift shift-label-e">{{ $lang === 'gu' ? 'જાવક' : 'PM' }}</td>
            @foreach($workingDates as $d)
            <td class="col-date"></td>
            @endforeach
        </tr>
        @endfor
    </tbody>
</table>

<div class="footer" style="margin-top:10px">
    <span style="font-size:8pt">{{ $lang === 'gu' ? 'P = હાજર, A = ગેરહાજર, L = રજા' : 'P = Present, A = Absent, L = Leave' }}</span>
    <span style="font-size:8pt">{{ $lang === 'gu' ? 'આવક = સવારની પાળી, જાવક = સાંજની પાળી' : 'AM = Morning Shift, PM = Evening Shift' }}</span>
    <span style="font-size:8pt">{{ $lang === 'gu' ? 'તારીખ :' : 'Date:' }} ___________</span>
    <span style="font-size:8pt">{{ $lang === 'gu' ? 'ડ્રાઇવર સહી :' : 'Driver Sig:' }} <span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
</div>

</body>
</html>
