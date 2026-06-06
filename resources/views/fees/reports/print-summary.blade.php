<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>ફી સારાંશ — {{ $academicYear->year }}</title>
<style>
@page { size: A4 portrait; margin: 15mm 12mm; }
body { font-family: 'Anek Gujarati', sans-serif; font-size: 10px; color: #1f2937; margin: 0; padding: 0; }
h1 { font-size: 16px; text-align: center; margin: 0 0 4px; }
.subtitle { text-align: center; font-size: 11px; color: #6b7280; margin-bottom: 12px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; padding: 6px 8px; text-align: left; border: 1px solid #d1d5db; }
td { padding: 5px 8px; border: 1px solid #d1d5db; text-align: left; }
.text-right { text-align: right; }
.font-bold { font-weight: 700; }
.text-green { color: #059669; }
.text-red { color: #dc2626; }
.text-amber { color: #d97706; }
.card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; margin-bottom: 10px; }
.summary-grid { display: flex; gap: 8px; margin-bottom: 12px; }
.summary-item { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; text-align: center; }
.summary-item .label { font-size: 8px; color: #6b7280; text-transform: uppercase; }
.summary-item .value { font-size: 14px; font-weight: 700; }
.footer { text-align: center; font-size: 9px; color: #9ca3af; margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
@media print { .no-print { display: none; } }
</style>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<h1>ફી સારાંશ — {{ $academicYear->year }}</h1>
<p class="subtitle">{{ $semLabel }} | {{ date('d/m/Y') }}</p>

<div class="summary-grid no-print" style="page-break-after:avoid">
    <div class="summary-item"><div class="label">કુલ સોંપાયેલ</div><div class="value">₹{{ number_format($totalAssigned, 2) }}</div></div>
    <div class="summary-item"><div class="label text-green">કુલ વસૂલાયેલ</div><div class="value text-green">₹{{ number_format($totalCollected, 2) }}</div></div>
    <div class="summary-item"><div class="label text-red">કુલ બાકી</div><div class="value text-red">₹{{ number_format($totalDue, 2) }}</div></div>
    <div class="summary-item"><div class="label text-amber">કુલ છૂટ</div><div class="value text-amber">₹{{ number_format($totalConcession, 2) }}</div></div>
</div>

@if(!empty($byType))
<h2 style="font-size:12px;margin:8px 0">ફી પ્રકાર મુજબ સારાંશ</h2>
<table>
<thead><tr><th>ફી પ્રકાર</th><th class="text-right">સોંપાયેલ</th><th class="text-right">વસૂલાયેલ</th><th class="text-right">બાકી</th><th class="text-right">છૂટ</th></tr></thead>
<tbody>
@foreach($byType as $type => $tv)
<tr>
    <td>{{ $typeLabels[$type] ?? $type }}</td>
    <td class="text-right">₹{{ number_format($tv['assigned'], 2) }}</td>
    <td class="text-right text-green">₹{{ number_format($tv['collected'], 2) }}</td>
    <td class="text-right text-red">₹{{ number_format($tv['due'], 2) }}</td>
    <td class="text-right text-amber">₹{{ number_format($tv['concession'], 2) }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

@if(!empty($perStandard))
<h2 style="font-size:12px;margin:8px 0">ધોરણ મુજબ સારાંશ</h2>
<table>
<thead><tr><th>ધોરણ</th><th class="text-right">સોંપાયેલ</th><th class="text-right">વસૂલાયેલ</th><th class="text-right">બાકી</th><th class="text-right">છૂટ</th><th class="text-right">%</th></tr></thead>
<tbody>
@foreach($perStandard as $ps)
<tr>
    <td>{{ $ps['standard'] }}</td>
    <td class="text-right">₹{{ number_format($ps['assigned'], 2) }}</td>
    <td class="text-right text-green">₹{{ number_format($ps['collected'], 2) }}</td>
    <td class="text-right text-red">₹{{ number_format($ps['due'], 2) }}</td>
    <td class="text-right text-amber">₹{{ number_format($ps['concession'], 2) }}</td>
    <td class="text-right">{{ $ps['assigned'] > 0 ? number_format(($ps['collected']/$ps['assigned'])*100, 1) : '0.0' }}%</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<div class="footer">NexSchool — {{ $academicYear->year }} | {{ $semLabel }}</div>
<script>window.print();</script>
</body>
</html>
