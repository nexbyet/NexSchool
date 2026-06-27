<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>LC રજીસ્ટર — {{ $activeYear?->year ?? '' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: A4 landscape; margin: 15mm 15mm 15mm 15mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Anek Gujarati', 'DejaVu Sans', sans-serif; font-size: 9pt; color: #000; line-height: 1.3; }
    .header { text-align: center; margin-bottom: 6px; }
    .header-top { display: flex; align-items: center; justify-content: center; gap: 12px; }
    .logo { width: 55px; height: 55px; object-fit: contain; }
    .school-name { font-size: 15pt; font-weight: 700; letter-spacing: 0.5px; }
    .school-addr { font-size: 7pt; color: #555; }
    .header-line { border-top: 2px solid #333; margin: 3px 0; }
    .title { font-size: 11pt; font-weight: 700; margin-top: 3px; margin-bottom: 5px; text-align: center; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 3px 4px; text-align: center; vertical-align: middle; }
    th { background: #e8e8e8; font-weight: 600; font-size: 7pt; }
    td { font-size: 7.5pt; }
    td.name-cell { text-align: left; padding-left: 5px; font-size: 8pt; }
    td.reason-cell { text-align: left; padding-left: 5px; font-size: 7pt; }
    .student-row:nth-child(even) { background: #f7f8fa; }
    .footer { margin-top: 10px; display: flex; justify-content: space-between; font-size: 8pt; }
    .sign-line { border-top: 1px solid #333; padding-top: 2px; display: inline-block; min-width: 120px; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        @if($school && $school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="logo">
        @endif
        <div>
            <div class="school-name">{{ $school->school_name_gu ?? '' }}</div>
            <div class="school-addr">{{ $school->address ?? '' }}</div>
        </div>
    </div>
    <div class="header-line"></div>
    <div class="title">LC (શાળા છોડવાનું પ્રમાણપત્ર) રજીસ્ટર — {{ $activeYear?->year ?? '' }}</div>
</div>

@if($students->isEmpty())
<p style="text-align:center;color:#666;padding:40px 0">કોઈ LC જારી થયેલ નથી</p>
@else
<table>
    <thead>
        <tr>
            <th style="width:6mm">ક્રમ</th>
            <th style="width:12mm">LC નંબર</th>
            <th style="width:12mm">GR નંબર</th>
            <th style="width:35mm">વિદ્યાર્થીનું નામ</th>
            <th style="width:28mm">પિતાનું નામ</th>
            <th style="width:10mm">ધોરણ</th>
            <th style="width:14mm">શાળા છોડી તા.</th>
            <th style="width:14mm">LC તારીખ</th>
            <th style="width:30mm">શાળા છોડવાનું કારણ</th>
            <th style="width:10mm">હાજરી દિવસ</th>
            <th style="width:20mm">નોંધ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $i => $s)
        <tr class="student-row">
            <td>{{ $i + 1 }}</td>
            <td>{{ $s->lc_number }}</td>
            <td>{{ $s->gr_number }}</td>
            <td class="name-cell">{{ $s->full_name_gu ?: $s->full_name_en }}</td>
            <td class="name-cell">{{ $s->father_name_gu ?: $s->father_name_en }}</td>
            <td>{{ $s->leavingStandard?->name ?? $s->currentStandard?->name ?? '' }}</td>
            <td>{{ $s->leaving_date ? \Carbon\Carbon::parse($s->leaving_date)->format('d/m/Y') : '' }}</td>
            <td>{{ $s->lc_issue_date ? \Carbon\Carbon::parse($s->lc_issue_date)->format('d/m/Y') : '' }}</td>
            <td class="reason-cell">{{ $s->leaving_reason_gu ?: $s->leaving_reason_en ?: '' }}</td>
            <td>{{ $s->attendance_days ?? '' }}</td>
            <td class="reason-cell">{{ $s->leaving_remarks ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">
    <div>કુલ LC: {{ $students->count() }}</div>
    <div>શિક્ષક સહી: <span class="sign-line"></span></div>
    <div>આચાર્ય સહી: <span class="sign-line"></span></div>
</div>

<script>window.print();</script>
</body>
</html>
