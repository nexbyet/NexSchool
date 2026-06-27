<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>બાકી યાદી — {{ $academicYear->year }}</title>
<style>
@page { size: A4 landscape; margin: 6mm; }
body { font-family: 'Anek Gujarati', sans-serif; font-size: 13px; color: #1f2937; margin: 0; padding: 0; }
.school-name { text-align: center; font-size: 18px; font-weight: 700; margin: 0 0 2px; }
h1 { font-size: 15px; text-align: center; margin: 0 0 3px; font-weight: 600; }
.subtitle { text-align: center; font-size: 12px; color: #6b7280; margin-bottom: 8px; }
table { width: 100%; border-collapse: collapse; }
th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; padding: 7px 8px; text-align: left; border: 1px solid #d1d5db; }
td { font-size: 13px; padding: 6px 8px; border: 1px solid #d1d5db; word-break: keep-all; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.font-bold { font-weight: 700; }
.text-green { color: #059669; }
.text-red { color: #dc2626; }
.footer { text-align: center; font-size: 10px; color: #9ca3af; margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en ?? 'NexSchool' }}</div>
<h1>બાકી ફી યાદી — {{ $academicYear->year }}</h1>
<p class="subtitle">{{ $semLabel }} | {{ date('d/m/Y') }}</p>

@if(empty($studentsData))
<p style="text-align:center;color:#6b7280;padding:40px 0">કોઈ બાકી વિદ્યાર્થી નથી</p>
@else
@php
    // Build column definitions: sorted by semester then fee type
    $typeOrder = ['tuition', 'transport', 'other'];
    $cols = [];
    foreach ($typeOrder as $t) {
        if (in_array($t, $typeList)) {
            foreach ($semList as $s) {
                $cols[] = ['key' => "sem_{$s}_{$t}", 'label' => 'સત્ર ' . $s . ' - ' . ($typeLabels[$t] ?? $t)];
            }
        }
    }
@endphp
<table>
<thead>
<tr>
    <th rowspan="2">ક્રમ</th>
    <th rowspan="2">GR</th>
    <th rowspan="2">નામ</th>
    <th rowspan="2">પિતાનું નામ</th>
    <th rowspan="2">ધોરણ-વર્ગ</th>
    <th colspan="{{ count($cols) }}" class="text-center">બાકી ફી (ફી પ્રકાર મુજબ)</th>
    <th rowspan="2" class="text-right">કુલ બાકી</th>
</tr>
<tr>
    @foreach($cols as $col)
    <th class="text-right" style="min-width:70px">{{ $col['label'] }}</th>
    @endforeach
</tr>
</thead>
<tbody>
@foreach($studentsData as $i => $sd)
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $sd->gr_number }}</td>
    <td>{{ $sd->full_name_gu ?? $sd->full_name_en }}</td>
    <td>{{ $sd->father_name_gu ?? $sd->father_name_en }}</td>
    <td>{{ $sd->student['current_standard']?->name ?? '' }} - {{ $sd->student['current_class']?->name ?? '' }}</td>
    @foreach($cols as $col)
        @php $entry = $sd->entries[$col['key']] ?? null; @endphp
        <td class="text-right">{{ $entry ? '₹' . number_format($entry['due_amount'], 2) : '—' }}</td>
    @endforeach
    <td class="text-right font-bold text-red">₹{{ number_format($sd->total_due, 2) }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<div class="footer">NexSchool — {{ $academicYear->year }} | {{ $semLabel }}</div>
<script>window.print();</script>
</body>
</html>
