<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>બાકી યાદી — {{ $academicYear->year }}</title>
<style>
@page { size: A4 landscape; margin: 12mm; }
body { font-family: 'Anek Gujarati', sans-serif; font-size: 9px; color: #1f2937; margin: 0; padding: 0; }
h1 { font-size: 14px; text-align: center; margin: 0 0 4px; }
.subtitle { text-align: center; font-size: 10px; color: #6b7280; margin-bottom: 10px; }
table { width: 100%; border-collapse: collapse; }
th { background: #f3f4f6; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; padding: 5px 6px; text-align: left; border: 1px solid #d1d5db; }
td { padding: 4px 6px; border: 1px solid #d1d5db; }
.text-right { text-align: right; }
.font-bold { font-weight: 700; }
.text-green { color: #059669; }
.text-red { color: #dc2626; }
.footer { text-align: center; font-size: 8px; color: #9ca3af; margin-top: 10px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<h1>બાકી ફી યાદી — {{ $academicYear->year }}</h1>
<p class="subtitle">{{ $semLabel }} | {{ date('d/m/Y') }}</p>

@if($results->isEmpty())
<p style="text-align:center;color:#6b7280;padding:40px 0">કોઈ બાકી વિદ્યાર્થી નથી</p>
@else
<table>
<thead>
<tr>
    <th>ક્રમ</th>
    <th>GR</th>
    <th>નામ</th>
    <th>પિતાનું નામ</th>
    <th>ધોરણ-વર્ગ</th>
    <th>ફી પ્રકાર</th>
    <th>સત્ર</th>
    <th class="text-right">કુલ ફી</th>
    <th class="text-right">ચૂકવેલ</th>
    <th class="text-right">બાકી</th>
</tr>
</thead>
<tbody>
@foreach($results as $i => $sf)
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $sf->gr_number }}</td>
    <td>{{ $sf->full_name_gu ?? $sf->full_name_en }}</td>
    <td>{{ $sf->father_name_gu ?? $sf->father_name_en }}</td>
    <td>{{ $sf->student?->currentStandard?->name ?? '' }} - {{ $sf->student?->currentClass?->name ?? '' }}</td>
    <td>{{ $typeLabels[$sf->feeStructure?->type ?? 'other'] ?? '—' }}</td>
    <td>@if($sf->semester)સત્ર {{ $sf->semester }}@else—@endif</td>
    <td class="text-right">₹{{ number_format($sf->net_amount, 2) }}</td>
    <td class="text-right text-green">₹{{ number_format($sf->paid_amount, 2) }}</td>
    <td class="text-right font-bold text-red">₹{{ number_format($sf->due_amount, 2) }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<div class="footer">NexSchool — {{ $academicYear->year }} | {{ $semLabel }}</div>
<script>window.print();</script>
</body>
</html>
