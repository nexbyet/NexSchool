<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
<meta charset="UTF-8">
<title>હાજરી પત્રક - બેક પેજ - {{ $standard->name }} - {{ $monthName }} {{ $data['year'] }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: Legal portrait; margin: 5mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { height: 100%; }
    body {
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
    .school-name { font-size: 22pt; font-weight: 800; }
    .header-line { border-top: 3px solid #1a1a1a; margin: 4px 0; }
    .title { font-size: 18pt; font-weight: 700; text-align: center; }
    .subtitle { font-size: 14pt; font-weight: 500; text-align: center; margin: 3px 0; }

    .info-bar {
        display: flex; justify-content: space-between; align-items: center;
        margin: 6px 0; padding: 5px 10px;
        background: #f0f4f8; border: 1px solid #cbd5e1; border-radius: 3px;
        font-size: 12pt; font-weight: 500;
    }

    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1.5px solid #000; padding: 3px 3px; text-align: center; vertical-align: middle; }
    th { background: #e2e8f0; font-weight: 600; font-size: 11pt; }

    .section-title { font-size: 13pt; font-weight: 700; margin: 8px 0 4px; padding: 4px 8px; background: #e2e8f0; border-radius: 2px; text-align: center; }

    .content-wrap { flex: 1; }

    .list-table th { font-size: 10pt; padding: 3px 4px; }
    .list-table td { font-size: 11pt; padding: 2px 4px; }
    .list-table .col-sr { width: 8mm; }
    .list-table .col-gr { width: 16mm; }
    .list-table .col-name { text-align: left !important; padding-left: 4px !important; min-width: 40mm; }
    .list-table .col-date { width: 18mm; }
    .list-table .col-remark { text-align: left !important; padding-left: 4px !important; min-width: 24mm; }
    .list-table .col-reason { text-align: left !important; padding-left: 4px !important; min-width: 26mm; }
    .list-table .col-lc { width: 16mm; }

    .footer { margin-top: auto; padding-top: 16px; display: flex; justify-content: space-between; font-size: 12pt; align-items: flex-end; }
    .footer-item { text-align: center; }
    .sign-line { border-top: 1.5px solid #333; padding-top: 3px; display: inline-block; min-width: 55mm; max-width: 70mm; }

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
        </div>
    </div>
    <div class="header-line"></div>
    <div class="title">{{ $lang === 'gu' ? 'માસિક હાજરી પત્રક' : 'MONTHLY ATTENDANCE REGISTER' }}</div>
    <div class="subtitle">
        {{ $monthName }} {{ $data['year'] }} — 
        {{ $lang === 'gu' ? 'ધોરણ' : 'Std' }}: {{ $standard->name }} — 
        {{ $lang === 'gu' ? 'વર્ગ' : 'Class' }}: {{ $class->name }}
    </div>
</div>

{{-- Table 1: Newly Admitted Students --}}
<div class="section-title">{{ $lang === 'gu' ? 'આ માસમાં નવા પ્રવેશ મેળવેલ વિદ્યાર્થીઓ' : 'Newly Admitted Students This Month' }}</div>
<table class="list-table">
    <thead>
        <tr>
            <th class="col-sr">{{ $lang === 'gu' ? 'ક્રમ' : '#' }}</th>
            <th class="col-gr">GR {{ $lang === 'gu' ? 'નંબર' : 'No' }}</th>
            <th class="col-name">{{ $lang === 'gu' ? 'વિદ્યાર્થીનું પૂરું નામ' : 'Full Name' }}</th>
            <th class="col-date">{{ $lang === 'gu' ? 'પ્રવેશ તારીખ' : 'Admission Date' }}</th>
            <th class="col-remark">{{ $lang === 'gu' ? 'નોંધ' : 'Remarks' }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $admittedItems = $monthAdmitted->sortBy('gr_number')->values();
        @endphp
        @for($i = 0; $i < 10; $i++)
            @php
                $student = $admittedItems->get($i);
                $isEmpty = !$student;
            @endphp
            <tr>
                <td style="text-align:center;font-weight:600">{{ $i + 1 }}</td>
                <td style="text-align:center{{ $isEmpty ? ';color:#ccc' : '' }}">{{ $isEmpty ? '' : $student->gr_number }}</td>
                <td class="col-name" style="{{ $isEmpty ? 'color:#ccc;' : '' }}">{{ $isEmpty ? '' : ($student->full_name_gu ?: $student->full_name_en) }}</td>
                <td style="text-align:center{{ $isEmpty ? ';color:#ccc' : '' }}">{{ $isEmpty ? '' : (\Carbon\Carbon::parse($student->date_of_admission)->format('d/m/Y')) }}</td>
                <td class="col-remark" style="{{ $isEmpty ? 'color:#ccc;' : '' }}"></td>
            </tr>
        @endfor
    </tbody>
</table>

{{-- Table 2: Students Who Left --}}
<div class="section-title">{{ $lang === 'gu' ? 'આ માસમાં શાળા છોડી ગયેલ વિદ્યાર્થીઓ' : 'Students Who Left This Month' }}</div>
<table class="list-table">
    <thead>
        <tr>
            <th class="col-sr">{{ $lang === 'gu' ? 'ક્રમ' : '#' }}</th>
            <th class="col-gr">GR {{ $lang === 'gu' ? 'નંબર' : 'No' }}</th>
            <th class="col-name">{{ $lang === 'gu' ? 'વિદ્યાર્થીનું પૂરું નામ' : 'Full Name' }}</th>
            <th class="col-date">{{ $lang === 'gu' ? 'શાળા છોડ્યાની તારીખ' : 'Leaving Date' }}</th>
            <th class="col-reason">{{ $lang === 'gu' ? 'શાળા છોડવાનું કારણ' : 'Reason for Leaving' }}</th>
            <th class="col-lc">LC {{ $lang === 'gu' ? 'નંબર' : 'No' }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $leftItems = $monthLeft->sortBy('gr_number')->values();
        @endphp
        @for($i = 0; $i < 10; $i++)
            @php
                $student = $leftItems->get($i);
                $isEmpty = !$student;
            @endphp
            <tr>
                <td style="text-align:center;font-weight:600">{{ $i + 1 }}</td>
                <td style="text-align:center{{ $isEmpty ? ';color:#ccc' : '' }}">{{ $isEmpty ? '' : $student->gr_number }}</td>
                <td class="col-name" style="{{ $isEmpty ? 'color:#ccc;' : '' }}">{{ $isEmpty ? '' : ($student->full_name_gu ?: $student->full_name_en) }}</td>
                <td style="text-align:center{{ $isEmpty ? ';color:#ccc' : '' }}">{{ $isEmpty ? '' : (\Carbon\Carbon::parse($student->leaving_date)->format('d/m/Y')) }}</td>
                <td class="col-reason" style="{{ $isEmpty ? 'color:#ccc;' : '' }}">{{ $isEmpty ? '' : ($lang === 'gu' ? ($student->leaving_reason_gu ?? '') : ($student->leaving_reason_en ?? '')) }}</td>
                <td style="text-align:center{{ $isEmpty ? ';color:#ccc' : '' }}">{{ $isEmpty ? '' : $student->lc_number }}</td>
            </tr>
        @endfor
    </tbody>
</table>

</div>{{-- end content-wrap --}}

<div class="footer">
    <div class="footer-item">{{ $lang === 'gu' ? 'તારીખ :' : 'Date:' }}<br><span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
    <div class="footer-item">{{ $lang === 'gu' ? 'વર્ગ શિક્ષક સહી :' : 'Teacher Signature:' }}<br><span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
    <div class="footer-item">{{ $lang === 'gu' ? 'મુખ્યાધ્યાપક સહી :' : 'Principal Signature:' }}<br><span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
</div>

</div>{{-- end print-wrapper --}}

<script>window.onload=function(){}</script>
</body>
</html>