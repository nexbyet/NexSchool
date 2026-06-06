<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
<meta charset="UTF-8">
<title>બસ ટાઇમટેબલ - {{ $route->route_name }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: A4 portrait; margin: 10mm 12mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Anek Gujarati', 'DejaVu Sans', sans-serif; font-size: 11pt; color: #000; line-height: 1.4; }
    .header { text-align: center; margin-bottom: 6px; }
    .header-top { display: flex; align-items: center; justify-content: center; gap: 14px; }
    .logo { width: 60px; height: 60px; object-fit: contain; }
    .school-name { font-size: 16pt; font-weight: 800; }
    .school-addr { font-size: 8pt; color: #555; }
    .header-line { border-top: 2px solid #000; margin: 4px 0; }
    .title { font-size: 14pt; font-weight: 700; text-align: center; margin: 4px 0; }
    .route-info { text-align: center; font-size: 10pt; color: #444; margin-bottom: 8px; }

    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: middle; }
    th { background: #e2e8f0; font-weight: 700; font-size: 10pt; text-align: center; }
    td { font-size: 10pt; }
    td.sr { text-align: center; width: 12mm; }
    td.time { text-align: center; font-family: 'Courier New', monospace; font-size: 10pt; }

    .footer { margin-top: 20px; display: flex; justify-content: space-between; font-size: 9pt; }
    .sign-line { border-top: 1px solid #333; display: inline-block; min-width: 50mm; padding-top: 2px; }

    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { display: none !important; }
    }
    .no-print { text-align: center; margin-bottom: 10px; }
    .no-print .btn { padding: 8px 24px; font-size: 11pt; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; background: #e11d48; color: #fff; }
    .no-print .btn:hover { background: #be123c; }
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
    <div class="title">{{ $lang === 'gu' ? 'બસ રૂટ ટાઇમટેબલ' : 'BUS ROUTE TIMETABLE' }}</div>
    <div class="route-info">
        <strong>{{ $route->route_name }}</strong>
        @if($route->vehicle)
        | {{ $lang === 'gu' ? 'વાહન' : 'Vehicle' }}: {{ $route->vehicle->vehicle_no }}
        @endif
    </div>
    @if($route->vehicle && ($route->vehicle->driver_name || $route->vehicle->driver_mobile))
    <div style="text-align:center;font-size:9pt;margin-bottom:6px;padding:3px 0;border-top:1px dashed #aaa;border-bottom:1px dashed #aaa;">
        <strong>{{ $lang === 'gu' ? 'ડ્રાઇવર' : 'Driver' }}:</strong> {{ $route->vehicle->driver_name ?? '—' }}
        @if($route->vehicle->driver_mobile)
        | <strong>{{ $lang === 'gu' ? 'મોબાઇલ' : 'Mobile' }}:</strong> {{ $route->vehicle->driver_mobile }}
        @endif
    </div>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th class="sr">{{ $lang === 'gu' ? 'ક્રમ' : 'Sr' }}</th>
            <th>{{ $lang === 'gu' ? 'સ્ટોપ' : 'Stop' }}</th>
            <th>{{ $lang === 'gu' ? 'આવક સમય' : 'Pickup Time' }}</th>
            <th>{{ $lang === 'gu' ? 'જાવક સમય' : 'Drop Time' }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($route->stops->sortBy('stop_order') as $stop)
        <tr>
            <td class="sr">{{ $stop->stop_order }}</td>
            <td><strong>{{ $stop->stop_name }}</strong></td>
            <td class="time">{{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : '—' }}</td>
            <td class="time">{{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <span>{{ $lang === 'gu' ? 'તારીખ :' : 'Date:' }} ___________</span>
    <span>{{ $lang === 'gu' ? 'ડ્રાઇવર સહી :' : 'Driver Signature:' }} <span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
</div>

</body>
</html>
