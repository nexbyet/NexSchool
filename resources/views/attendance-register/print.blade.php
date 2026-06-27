<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
<meta charset="UTF-8">
<title>હાજરી પત્રક - {{ $standard->name }} - {{ $monthName }} {{ $data['year'] }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page {
        size: 356mm 216mm landscape;
        margin: 12mm 15mm 12mm 15mm;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Anek Gujarati', 'DejaVu Sans', sans-serif;
        font-size: 9pt;
        color: #000;
        line-height: 1.3;
    }
    .header { text-align: center; margin-bottom: 5px; }
    .header-top { display: flex; align-items: center; justify-content: center; gap: 12px; }
    .logo { width: 55px; height: 55px; object-fit: contain; }
    .school-name { font-size: 15pt; font-weight: 700; letter-spacing: 0.5px; }
    .school-addr { font-size: 7pt; color: #555; }
    .title-row {
        display: flex; justify-content: space-between; align-items: center;
        margin: 5px 0 3px; padding: 3px 6px;
        background: #eef2f7; border: 1px solid #d0d5dd; border-radius: 2px;
        font-weight: 600; font-size: 8pt;
    }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 2px 1.5px; text-align: center; vertical-align: middle; }
    th { background: #e8e8e8; font-weight: 600; font-size: 7.5pt; }
    thead tr:first-child th { border-bottom: 2px solid #000; }
    td { font-size: 7.5pt; }
    td.name-cell { text-align: left; padding-left: 4px; font-size: 9pt; width: 36mm; }
    td.sr-cell { font-size: 7.5pt; width: 5mm; }
    td.gr-cell { font-size: 7pt; width: 9mm; }
    td.dob-cell { font-size: 7pt; width: 9mm; }
    td.cat-cell { font-size: 7pt; width: 7mm; }
    td.date-cell { font-size: 7pt; min-width: 4mm; max-width: 4.2mm; height: 15px; }
    td.sum-cell { font-size: 7pt; width: 9mm; }
    .student-row td { padding: 2px 1.5px; }
    .student-row:nth-child(even) { background: #f7f8fa; }
    .name-kumari { color: #c00; }
    .blank td { height: 16px; }
    .summary-row td { font-size: 8pt; text-align: left; padding: 2.5px 5px; font-weight: 500; }
    .summary-row .sr-cell { text-align: center; font-weight: 700; }
    .summary-row td.sum-cell { text-align: center; font-weight: 600; }
    .section-label { font-size: 8.5pt; text-align: left !important; padding-left: 6px !important; }
    .summary-divider { border-top: 2px solid #000; }
    .summary-subtotal { border-top: 1px solid #000; border-bottom: 1px solid #000; background: #f0f3f7; }
    .summary-total { border-top: 2px solid #000; background: #e8ecf2; }
    .holiday-red { color: #c00; font-weight: 700; font-size: 5.5pt; }
    .th-sun .date-label { color: #c00; }
    .th-hol .date-label { color: #c00; }

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
    .no-print .btn-outline { background: #fff; color: #374151; border: 1px solid #d1d5db; }
    .no-print .btn-outline:hover { background: #f9fafb; }
    .header-line { border-top: 2px solid #333; margin: 3px 0; }

    .sign-line { border-top: 1px solid #333; padding-top: 1px; display: inline-block; }
    .keep-together { page-break-inside: avoid; }
</style>
</head>
<body>

<div class="no-print">
    <button class="btn btn-primary" onclick="window.print()">🖨️ {{ $lang === 'gu' ? 'પ્રિન્ટ કરો' : 'Print' }}</button>
    <button class="btn btn-secondary" onclick="window.open('{{ route('attendance-register.summary') }}?standard_id={{ $data['standard_id'] }}&class_id={{ $data['class_id'] }}&month={{ $data['month'] }}&year={{ $data['year'] }}&academic_year_id={{ $data['academic_year_id'] }}&lang={{ $lang }}&type={{ $type }}&page=front', '_blank')">📊 {{ $lang === 'gu' ? 'પ્રિન્ટ સમરી' : 'Print Summary' }}</button>
    <button class="btn btn-outline" onclick="window.open('{{ route('attendance-register.summary') }}?standard_id={{ $data['standard_id'] }}&class_id={{ $data['class_id'] }}&month={{ $data['month'] }}&year={{ $data['year'] }}&academic_year_id={{ $data['academic_year_id'] }}&lang={{ $lang }}&type={{ $type }}&page=back', '_blank')">📄 {{ $lang === 'gu' ? 'બેક પેજ પ્રિન્ટ' : 'Back Page Print' }}</button>
</div>

{{-- Header --}}
<div class="header">
    <div class="header-top">
        @if($school && $school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="logo">
        @endif
        <div>
            <div class="school-name">
                {{ $lang === 'gu' ? ($school->school_name_gu ?? '') : ($school->school_name_en ?? '') }}
            </div>
            <div class="school-addr">{{ $school->address ?? '' }}</div>
        </div>
    </div>
    <div class="header-line"></div>
    <div style="font-size:10.5pt;font-weight:700;margin-top:2px">
        {{ $lang === 'gu' ? 'હાજરી પત્રક' : 'ATTENDANCE REGISTER' }} — {{ $monthName }} {{ $data['year'] }}
    </div>
</div>

{{-- Info row --}}
<div class="title-row">
    <span>📚 {{ $lang === 'gu' ? 'ધોરણ' : 'Standard' }}: <strong>{{ $standard->name }}</strong></span>
    <span>🏫 {{ $lang === 'gu' ? 'વર્ગ' : 'Class' }}: <strong>{{ $class->name }}</strong></span>
    <span>👤 {{ $lang === 'gu' ? 'વર્ગ શિક્ષક' : 'Class Teacher' }}: <strong>{{ $classTeacher?->teacher?->name ?? '______________' }}</strong></span>
    <span>👥 {{ $lang === 'gu' ? 'કુલ' : 'Total' }}: <strong>{{ $studentCount }}</strong></span>
</div>

{{-- Table --}}
<table>
    <thead>
        <tr>
            <th class="sr-cell" rowspan="2">{{ $lang === 'gu' ? 'ક્રમ' : 'Sr' }}</th>
            <th class="gr-cell" rowspan="2">{{ $lang === 'gu' ? 'GR નં' : 'GR No' }}</th>
            <th style="width:44mm" rowspan="2">{{ $lang === 'gu' ? 'વિદ્યાર્થીનું નામ' : 'Student Name' }}</th>
            <th class="dob-cell" rowspan="2">{{ $lang === 'gu' ? 'જન્મ તા.' : 'DOB' }}</th>
            <th class="cat-cell" rowspan="2">{{ $lang === 'gu' ? 'શ્રેણી' : 'Cat' }}</th>
            <th colspan="{{ $daysInMonth }}" style="font-size:6pt;letter-spacing:0.5px">
                {{ $lang === 'gu' ? 'તારીખ અને વાર (૧ થી' : 'Date & Day (1 to' }} {{ $daysInMonth }}{{ $lang === 'gu' ? ')' : ')' }}
            </th>
            <th class="sum-cell" rowspan="2">{{ $lang === 'gu' ? 'આ માસ' : 'This Mo' }}</th>
            <th class="sum-cell" rowspan="2">{{ $lang === 'gu' ? 'આગળનો (સંચિત)' : 'Cumul' }}</th>
            <th class="sum-cell" rowspan="2">{{ $lang === 'gu' ? 'કુલ' : 'Tot' }}</th>
        </tr>
        <tr>
            @foreach($dates as $date)
                @php
                    $thClass = $date['isSunday'] || $date['isHoliday'] ? 'th-hol' : '';
                @endphp
                <th class="{{ $thClass }}" style="font-size:6pt;padding:1px 0">
                    <div class="date-label">{{ $date['day'] }}</div>
                    <div style="font-size:5.5pt;font-weight:400">{{ $date['dayName'] }}</div>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{-- Student rows --}}
        @foreach($students as $idx => $student)
            <tr class="student-row">
                <td class="sr-cell">{{ $idx + 1 }}</td>
                <td class="gr-cell">{{ $student->gr_number }}</td>
                <td class="name-cell{{ $student->sharirik_jaati === 'kumari' ? ' name-kumari' : '' }}">{{ $lang === 'gu' ? $student->full_name_gu : $student->full_name_en }}</td>
                <td class="dob-cell">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y') : '' }}</td>
                <td class="cat-cell">{{ $lang === 'gu' ? $student->category_gu : $student->category_en }}</td>
                @foreach($dates as $d2)
                    @php
                        $attKey = $student->id . '-' . $d2['dateKey'];
                        $attStatus = optional($attendanceData[$attKey] ?? null)->status;
                    @endphp
                    <td class="date-cell" style="{{ $attStatus === 'present' ? 'color:#059669;font-weight:600' : ($attStatus === 'absent' ? 'color:#dc2626;font-weight:600' : ($attStatus === 'absent_with_leave' ? 'color:#d97706;font-weight:600' : ($attStatus === 'medical_leave' ? 'color:#2563eb;font-weight:600' : ''))) }}">
                        @if($d2['isSunday'] || $d2['isHoliday'])
                            <span class="holiday-red">{{ $lang === 'gu' ? 'રજા' : 'OFF' }}</span>
                        @elseif($attStatus === 'present')
                            P
                        @elseif($attStatus === 'absent')
                            A
                        @elseif($attStatus === 'absent_with_leave')
                            L
                        @elseif($attStatus === 'medical_leave')
                            S
                        @endif
                    </td>
                @endforeach
                @php
                    $stuTot = $studentTotals[$student->id] ?? null;
                @endphp
                <td class="sum-cell" style="{{ $stuTot ? 'font-weight:600' : '' }}">{{ $stuTot['current'] ?? '' }}</td>
                <td class="sum-cell" style="{{ $stuTot ? 'font-weight:600' : '' }}">{{ $stuTot['prev'] ?? '' }}</td>
                <td class="sum-cell" style="font-weight:700">{{ $stuTot ? $stuTot['total'] : '' }}</td>
            </tr>
        @endforeach
    </tbody>

    <tbody class="keep-together">
        {{-- Blank rows for new admissions --}}
        @for($b = 0; $b < $blankRows; $b++)
            <tr class="blank">
                <td class="sr-cell">&nbsp;</td>
                <td class="gr-cell"></td>
                <td class="name-cell"></td>
                <td class="dob-cell"></td>
                <td class="cat-cell"></td>
                @foreach($dates as $date)
                    <td class="date-cell"></td>
                @endforeach
                <td class="sum-cell"></td>
                <td class="sum-cell"></td>
                <td class="sum-cell"></td>
            </tr>
        @endfor

        {{-- Summary rows --}}
        @php $rowLabels = [
            1 => ['gu' => 'આગલા દિવસની સંખ્યા', 'en' => 'Previous Day Count'],
            2 => ['gu' => 'દાખલ સંખ્યા', 'en' => 'Admission Count'],
            3 => ['gu' => 'છોડીને ગયા સંખ્યા', 'en' => 'Left Count'],
            4 => ['gu' => 'કુલ (૧+૨+૩)', 'en' => 'Total (1+2+3)'],
            5 => ['gu' => 'હાજર સંખ્યા', 'en' => 'Present Count'],
            6 => ['gu' => 'રજા વગર ગેરહાજર', 'en' => 'Absent W/O Leave'],
            7 => ['gu' => 'રજા સાથે ગેરહાજર', 'en' => 'Absent With Leave'],
            8 => ['gu' => 'માંદગી રજા', 'en' => 'Medical Leave'],
            9 => ['gu' => 'કુલ (૫+૬+૭+૮)', 'en' => 'Total (5+6+7+8)'],
        ]; $rowClasses = [
            1 => 'summary-divider', 2 => '', 3 => '', 4 => 'summary-subtotal',
            5 => '', 6 => '', 7 => '', 8 => '', 9 => 'summary-total',
        ]; $rowIdxArr = [1,2,3,4,5,6,7,8,9]; @endphp
        @foreach($rowIdxArr as $ri)
            @php $sumKey = $ri - 1; @endphp
            <tr class="summary-row {{ $rowClasses[$ri] }}">
                <td colspan="5" class="section-label">{{ $ri }}. {{ $rowLabels[$ri][$lang] }}:</td>
                @foreach($dates as $d3)
                    @php $val = $dailySummary[$d3['dateKey']][$sumKey] ?? null; @endphp
                    <td class="date-cell" style="{{ $val !== null ? 'font-weight:500' : '' }}">
                        {{ $val !== null ? $val : '' }}
                    </td>
                @endforeach
                <td class="sum-cell"></td><td class="sum-cell"></td><td class="sum-cell"></td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="keep-together" style="margin-top:4px;font-size:7pt;display:flex;justify-content:space-between;align-items:center">
    <div>
        <span>{{ $lang === 'gu' ? 'આગલા માસના કામના દિવસો :' : 'Prev Working Days:' }} ___________</span>
        <span style="margin-left:12px">{{ $lang === 'gu' ? 'આ માસના કામના દિવસો :' : 'This Mo Working Days:' }} ___________</span>
        <span style="margin-left:12px;font-weight:700">{{ $lang === 'gu' ? 'કુલ કામના દિવસો :' : 'Total Working Days:' }} ___________</span>
    </div>
    <span style="font-weight:700">{{ $lang === 'gu' ? 'વર્ગ શિક્ષક સહી :' : 'Class Teacher Signature:' }} ______________________</span>
</div>

<script>
window.onload = function() { };
</script>
</body>
</html>
