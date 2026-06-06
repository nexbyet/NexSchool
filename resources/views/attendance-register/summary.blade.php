<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
<meta charset="UTF-8">
<title>હાજરી પત્રક સમરી - {{ $standard->name }} - {{ $monthName }} {{ $data['year'] }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: Legal portrait; margin: 5mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body {
        height: 100%;
        font-family: 'Anek Gujarati', 'Inter', 'DejaVu Sans', sans-serif;
        font-size: 14pt; color: #1a1a1a; line-height: 1.5;
    }
    .print-wrapper {
        border: 3px double #000;
        height: 100%;
        padding: 5mm 7mm;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }
    .header { text-align: center; margin-bottom: 6px; }
    .header-top { display: flex; align-items: center; justify-content: center; gap: 14px; }
    .logo { width: 65px; height: 65px; object-fit: contain; }
    .school-name { font-size: 22pt; font-weight: 800; letter-spacing: 0.5px; }
    .school-addr { font-size: 11pt; color: #555; }
    .header-line { border-top: 3px solid #1a1a1a; margin: 5px 0; }
    .title { font-size: 18pt; font-weight: 700; margin-top: 4px; letter-spacing: 0.5px; }
    .subtitle { font-size: 13pt; color: #444; font-weight: 500; }

    .info-bar {
        display: flex; justify-content: space-between; align-items: center;
        margin: 6px 0; padding: 5px 10px;
        background: #f0f4f8; border: 1px solid #cbd5e1; border-radius: 3px;
        font-size: 12pt; font-weight: 500;
    }

    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1.5px solid #000; padding: 3px 3px; text-align: center; vertical-align: middle; }
    th { background: #e2e8f0; font-weight: 600; font-size: 11pt; }

    .boxes-row { display: flex; gap: 8px; margin: 6px 0; align-items: stretch; }
    .box { flex: 1; min-width: 0; }
    .box table { margin: 0; }
    .box th { font-size: 11pt; padding: 4px 3px; }
    .box td { font-size: 12pt; padding: 3px 4px; }
    .box .col-cat { text-align: left !important; padding-left: 6px !important; font-weight: 500; }
    .box .col-num { text-align: center; font-weight: 600; }
    .box .row-grand { background: #e2e8f0; border-top: 3px solid #000; }
    .box .row-grand td { font-weight: 700; }
    .box .row-blank td { color: #ccc; }

    .minority-box { margin: 8px 0; }
    .minority-box table { width: auto; min-width: 50%; margin: 0 auto; }
    .minority-box th, .minority-box td { font-size: 12pt; padding: 4px 12px; }

    .student-list { margin: 8px 0; }
    .student-list .list-wrap { display: flex; gap: 8px; }
    .student-list .list-col { flex: 1; min-width: 0; }
    .student-list .list-col h4 { font-size: 12pt; font-weight: 700; margin-bottom: 4px; padding: 4px 6px; background: #f0f4f8; border: 1px solid #cbd5e1; border-radius: 2px; }
    .student-list th { font-size: 10pt; padding: 3px 4px; }
    .student-list td { font-size: 11pt; padding: 2px 4px; }
    .student-list .col-gr { width: 16mm; }
    .student-list .col-name { text-align: left !important; padding-left: 4px !important; }
    .student-list .col-date { width: 18mm; }

    .content-wrap { flex: 1; }
    .footer { margin-top: auto; padding-top: 12px; display: flex; justify-content: space-between; font-size: 12pt; }
    .sign-line { border-top: 1px solid #333; padding-top: 2px; display: inline-block; min-width: 80mm; }

    .no-data { font-size: 11pt; color: #999; font-style: italic; padding: 6px; text-align: center; }

    @media print {
        html, body { height: 100%; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .print-wrapper { height: 100%; border: 3px double #000; }
        .no-print { display: none !important; }
    }
    .no-print { text-align: center; margin-bottom: 10px; }
    .no-print .btn { padding: 8px 24px; font-size: 12pt; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; display: inline-block; margin: 0 5px; }
    .no-print .btn-primary { background: #e11d48; color: #fff; }
    .no-print .btn-primary:hover { background: #be123c; }
    .no-print .btn-secondary { background: #475569; color: #fff; }
    .no-print .btn-secondary:hover { background: #334155; }
</style>
</head>
<body>

<div class="no-print">
    <button class="btn btn-primary" onclick="window.print()">🖨️ {{ $lang === 'gu' ? 'પ્રિન્ટ કરો' : 'Print' }}</button>
    <button class="btn btn-secondary" onclick="history.back()">⬅️ {{ $lang === 'gu' ? 'પાછળ' : 'Back' }}</button>
</div>

<div class="print-wrapper">

<div class="content-wrap">

<div class="header">
    <div class="header-top">
        @if($school && $school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="logo">
        @endif
        <div>
            <div class="school-name">{{ $lang === 'gu' ? ($school->school_name_gu ?? '') : ($school->school_name_en ?? '') }}</div>
            <div class="school-addr">{{ $school->address ?? '' }}</div>
        </div>
    </div>
    <div class="header-line"></div>
    <div class="title">{{ $lang === 'gu' ? 'હાજરી પત્રક સમરી (ગણતરી)' : 'ATTENDANCE REGISTER SUMMARY' }}</div>
    <div class="subtitle">{{ $monthName }} {{ $data['year'] }}</div>
</div>

<div class="info-bar">
    <span>📚 {{ $lang === 'gu' ? 'ધોરણ' : 'Std' }}: <strong>{{ $standard->name }}</strong></span>
    <span>🏫 {{ $lang === 'gu' ? 'વર્ગ' : 'Class' }}: <strong>{{ $class->name }}</strong></span>
    <span>👤 {{ $lang === 'gu' ? 'વર્ગ શિક્ષક' : 'Teacher' }}: <strong>{{ $classTeacher?->teacher?->name ?? '______________' }}</strong></span>
    <span>📅 {{ $lang === 'gu' ? 'મહિનો' : 'Month' }}: <strong>{{ $monthName }}</strong></span>
</div>

{{-- Two side-by-side boxes --}}
<div class="boxes-row">

    {{-- Box 1: Enrollment Summary --}}
    <div class="box">
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;padding-left:6px;min-width:30mm">{{ $lang === 'gu' ? 'વિગત' : 'Detail' }}</th>
                    <th style="color:#1e40af;">{{ $lang === 'gu' ? 'કુમાર' : 'Boy' }}</th>
                    <th style="color:#be123c;">{{ $lang === 'gu' ? 'કુમારી' : 'Girl' }}</th>
                    <th>{{ $lang === 'gu' ? 'કુલ' : 'Total' }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $box1Labels = [
                        'first_day' => ['gu' => 'માસનાં પ્રથમ દિવસે', 'en' => 'First Day of Month'],
                        'admitted'  => ['gu' => 'નવા દાખલ થયા', 'en' => 'New Admissions'],
                        'sum_total' => ['gu' => 'કુલ સંખ્યા', 'en' => 'Total Count'],
                        'left'      => ['gu' => 'ઉઠી જનારની સંખ્યા', 'en' => 'Left Count'],
                        'month_end' => ['gu' => 'માસ અંતે કુલ સંખ્યા', 'en' => 'Month End Total'],
                    ];
                    $box1Keys = ['first_day', 'admitted', 'sum_total', 'left', 'month_end'];
                    $box1Blank = $type === 'blank';
                @endphp
                @foreach($box1Keys as $i => $key)
                    @php
                        $row = $box1[$key];
                        $isFirstRow = $i === 0;
                        $isEmpty = $box1Blank && !$isFirstRow;
                    @endphp
                    <tr style="{{ $i === 4 ? 'background:#e2e8f0;font-weight:700;border-top:2.5px solid #000;' : ($i === 2 ? 'border-top:2px solid #000;' : '') }}">
                        <td class="col-cat">{{ $box1Labels[$key][$lang] }}</td>
                        <td class="col-num" style="color:#1e40af;{{ $isEmpty ? 'color:#ddd;' : '' }}">{{ $isEmpty ? '' : $row['kumar'] }}</td>
                        <td class="col-num" style="color:#be123c;{{ $isEmpty ? 'color:#ddd;' : '' }}">{{ $isEmpty ? '' : $row['kumari'] }}</td>
                        <td class="col-num" style="{{ $isEmpty ? 'color:#ddd;' : '' }}">{{ $isEmpty ? '' : $row['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Box 2: Category-wise Breakdown --}}
    <div class="box">
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;padding-left:6px;min-width:24mm">{{ $lang === 'gu' ? 'કેટેગરી' : 'Category' }}</th>
                    <th style="color:#1e40af;">{{ $lang === 'gu' ? 'કુમાર' : 'Boy' }}</th>
                    <th style="color:#be123c;">{{ $lang === 'gu' ? 'કુમારી' : 'Girl' }}</th>
                    <th>{{ $lang === 'gu' ? 'કુલ' : 'Total' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($box2 as $catRow)
                <tr>
                    <td class="col-cat">{{ $catRow['category'] }}</td>
                    <td class="col-num" style="color:#1e40af;">{{ $catRow['kumar'] }}</td>
                    <td class="col-num" style="color:#be123c;">{{ $catRow['kumari'] }}</td>
                    <td class="col-num">{{ $catRow['total'] }}</td>
                </tr>
                @empty
                <tr>
                    <td class="col-cat" colspan="4" style="text-align:center;color:#999;font-style:italic;">{{ $lang === 'gu' ? 'કોઈ ડેટા નથી' : 'No data' }}</td>
                </tr>
                @endforelse
                <tr class="row-grand">
                    <td class="col-cat" style="font-weight:700">{{ $lang === 'gu' ? 'કુલ એકંદર' : 'Grand Total' }}</td>
                    <td class="col-num" style="font-weight:700;color:#1e40af;">{{ $box2Grand['kumar'] }}</td>
                    <td class="col-num" style="font-weight:700;color:#be123c;">{{ $box2Grand['kumari'] }}</td>
                    <td class="col-num" style="font-weight:700">{{ $box2Grand['total'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- Minority count --}}
<div class="minority-box">
    <table>
        <thead>
            <tr>
                <th colspan="4">{{ $lang === 'gu' ? 'OBC બક્ષીપંચ પૈકી લઘુમતી' : 'Minority among OBC & Remaining' }}</th>
            </tr>
            <tr>
                <th style="min-width:18mm"></th>
                <th style="color:#1e40af;">{{ $lang === 'gu' ? 'કુમાર' : 'Boy' }}</th>
                <th style="color:#be123c;">{{ $lang === 'gu' ? 'કુમારી' : 'Girl' }}</th>
                <th>{{ $lang === 'gu' ? 'કુલ' : 'Total' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-cat" style="font-weight:500">{{ $lang === 'gu' ? 'લઘુમતી વિદ્યાર્થીઓ' : 'Minority Students' }}</td>
                <td class="col-num" style="color:#1e40af;">{{ $minorityData['kumar'] }}</td>
                <td class="col-num" style="color:#be123c;">{{ $minorityData['kumari'] }}</td>
                <td class="col-num" style="font-weight:600">{{ $minorityData['total'] }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Admitted & Left student lists --}}
<div class="student-list">
    <div class="list-wrap">
        {{-- Admitted this month --}}
        <div class="list-col">
            <h4>{{ $lang === 'gu' ? 'નવા દાખલ થયેલ વિદ્યાર્થીઓ' : 'Newly Admitted Students' }}</h4>
            @if($admittedList->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th class="col-gr">{{ $lang === 'gu' ? 'GR નંબર' : 'GR No' }}</th>
                        <th class="col-name">{{ $lang === 'gu' ? 'નામ' : 'Name' }}</th>
                        <th class="col-date">{{ $lang === 'gu' ? 'પ્રવેશ તારીખ' : 'Admission Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admittedList as $a)
                    <tr>
                        <td style="text-align:center;font-weight:500">{{ $a['gr'] }}</td>
                        <td class="col-name">{{ $a['name'] }}</td>
                        <td style="text-align:center">{{ $a['date'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">{{ $lang === 'gu' ? 'આ મહિને કોઈ નવો પ્રવેશ નથી' : 'No new admissions this month' }}</div>
            @endif
        </div>

        {{-- Left this month --}}
        <div class="list-col">
            <h4>{{ $lang === 'gu' ? 'શાળા છોડી ગયેલ વિદ્યાર્થીઓ' : 'Students Who Left' }}</h4>
            @if($leftList->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th class="col-gr">{{ $lang === 'gu' ? 'GR નંબર' : 'GR No' }}</th>
                        <th class="col-name">{{ $lang === 'gu' ? 'નામ' : 'Name' }}</th>
                        <th class="col-date">{{ $lang === 'gu' ? 'છોડી ગયાની તારીખ' : 'Leaving Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leftList as $l)
                    <tr>
                        <td style="text-align:center;font-weight:500">{{ $l['gr'] }}</td>
                        <td class="col-name">{{ $l['name'] }}</td>
                        <td style="text-align:center">{{ $l['date'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">{{ $lang === 'gu' ? 'આ મહિને કોઈએ શાળા છોડી નથી' : 'No student left this month' }}</div>
            @endif
        </div>
    </div>
</div>

</div>{{-- end content-wrap --}}

<div class="footer">
    <span>{{ $lang === 'gu' ? 'તારીખ :' : 'Date:' }} ___________</span>
    <span>{{ $lang === 'gu' ? 'વર્ગ શિક્ષક સહી :' : 'Class Teacher Signature:' }} <span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
</div>

</div>{{-- end print-wrapper --}}

<script>window.onload=function(){}</script>
</body>
</html>