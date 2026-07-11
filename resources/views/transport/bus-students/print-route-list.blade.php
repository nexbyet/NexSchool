<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>બસ વિદ્યાર્થી — રૂટ યાદી</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: A4 portrait; margin: 10mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Anek Gujarati', 'DejaVu Sans', sans-serif; font-size: 10pt; color: #000; }
    .header { text-align: center; margin-bottom: 8px; }
    .header .school-name { font-size: 14pt; font-weight: 800; }
    .header .title { font-size: 12pt; font-weight: 700; margin-top: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
    th { background: #e2e8f0; font-size: 8pt; font-weight: 700; }
    td { font-size: 9pt; }
    .text-left { text-align: left; }
    .route-header { background: #f0fdf4; font-weight: 700; font-size: 10pt; text-align: left; padding: 6px 8px; }
    .footer { margin-top: 20px; display: flex; justify-content: space-between; font-size: 8pt; }
    .sign-line { border-top: 1px solid #333; display: inline-block; min-width: 50mm; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
</style>
</head>
<body>
<div class="header">
    <div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en ?? 'શાળા' }}</div>
    <div class="title">બસ વિદ્યાર્થી — રૂટ યાદી</div>
    <div style="font-size:9pt;color:#555">{{ now()->format('d/m/Y') }}</div>
</div>

@foreach($routes as $route)
@php
    $routeStudents = $routeId ? $busStudents : $busStudents->where('route_id', $route->id);
@endphp
@if($routeStudents->isNotEmpty())
<table>
    <tr><td colspan="4" class="route-header">રૂટ: {{ $route->route_name }}</td></tr>
    <thead>
        <tr>
            <th style="width:8mm">ક્રમ</th>
            <th class="text-left">નામ</th>
            <th style="width:18mm">ધોરણ</th>
            <th style="width:22mm">મોબાઇલ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($routeStudents as $i => $s)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="text-left">{{ $s->full_name_gu }}</td>
            <td>{{ $s->standard_label ?? '—' }}</td>
            <td>{{ $s->mobile ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="height:10px"></div>
@endif
@endforeach

<div class="footer">
    <span>તારીખ: ___________</span>
    <span>સહી: <span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
</div>
</body>
</html>
