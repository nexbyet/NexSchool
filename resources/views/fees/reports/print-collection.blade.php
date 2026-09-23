<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>વસૂલાત રિપોર્ટ — {{ $academicYear->year }}</title>
<style>
@page { size: A4 landscape; margin: 8mm; }
body { font-family: 'Anek Gujarati', sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
.school-name { text-align: center; font-size: 18px; font-weight: 700; margin: 0 0 2px; }
h1 { font-size: 15px; text-align: center; margin: 0 0 3px; font-weight: 600; }
h2 { font-size: 13px; font-weight: 700; margin: 14px 0 6px; padding-bottom: 4px; border-bottom: 2px solid #e5e7eb; }
h3 { font-size: 11px; font-weight: 700; margin: 10px 0 4px; color: #374151; }
.subtitle { text-align: center; font-size: 10px; color: #6b7280; margin-bottom: 10px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; padding: 5px 6px; text-align: left; border: 1px solid #d1d5db; }
td { font-size: 11px; padding: 4px 6px; border: 1px solid #d1d5db; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.font-bold { font-weight: 700; }
.text-green { color: #059669; }
.badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: 600; }
.badge-tuition { background: #dbeafe; color: #1e40af; }
.badge-transport { background: #fef3c7; color: #92400e; }
.badge-other { background: #e5e7eb; color: #374151; }
.summary-grid { display: flex; gap: 8px; margin-bottom: 10px; }
.summary-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 7px 8px; text-align: center; }
.summary-card .label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
.summary-card .value { font-size: 14px; font-weight: 700; }
.page-break { page-break-before: always; }
.footer { text-align: center; font-size: 8px; color: #9ca3af; margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
@media print { .no-print { display: none; } }
</style>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en ?? 'NexSchool' }}</div>
<h1>વસૂલાત રિપોર્ટ — {{ $academicYear->year }}</h1>
<p class="subtitle">{{ $semLabel }} | {{ $feeTypeLabel ?? 'બધા હેડ' }} | {{ request('from_date') }} થી {{ request('to_date') }} | {{ date('d/m/Y') }}</p>

@if($payments->isEmpty())
<p style="text-align:center;color:#6b7280;padding:40px 0">કોઈ વસૂલાત નથી</p>
@else
{{-- ===== COMBINE SUMMARY ===== --}}
<h2>૧. સંયુક્ત સારાંશ (Combine Summary)</h2>
<div class="summary-grid">
    <div class="summary-card"><div class="label">કુલ વસૂલાત</div><div class="value">₹{{ number_format($totalAmount, 2) }}</div><div style="font-size:9px;color:#6b7280">{{ $payments->count() }} રસીદ</div></div>
    <div class="summary-card"><div class="label" style="color:#1e40af">શાળા ફી</div><div class="value" style="color:#1e40af">₹{{ number_format($byType['tuition'] ?? 0, 2) }}</div></div>
    <div class="summary-card"><div class="label" style="color:#92400e">બસ ફી</div><div class="value" style="color:#92400e">₹{{ number_format($byType['transport'] ?? 0, 2) }}</div></div>
    @if(($byType['other'] ?? 0) > 0)
    <div class="summary-card"><div class="label">અન્ય</div><div class="value">₹{{ number_format($byType['other'], 2) }}</div></div>
    @endif
</div>
<div class="summary-grid">
    <div class="summary-card"><div class="label">સત્ર 1</div><div class="value">₹{{ number_format($bySemester[1] ?? 0, 2) }}</div></div>
    <div class="summary-card"><div class="label">સત્ર 2</div><div class="value">₹{{ number_format($bySemester[2] ?? 0, 2) }}</div></div>
    <div class="summary-card" style="background:#f0fdf4"><div class="label">કુલ વસૂલાત</div><div class="value text-green">₹{{ number_format($totalAmount, 2) }}</div></div>
</div>

{{-- Matrix: Semester x Head --}}
<h3>સત્ર × હેડ મેટ્રિક્સ</h3>
<table>
<thead>
<tr>
    <th>સત્ર \ હેડ</th>
    <th class="text-right">શાળા ફી</th>
    <th class="text-right">બસ ફી</th>
    @if(($byType['other'] ?? 0) > 0)<th class="text-right">અન્ય</th>@endif
    <th class="text-right" style="background:#ecfdf5">સત્ર કુલ</th>
</tr>
</thead>
<tbody>
<tr>
    <td class="font-bold">સત્ર 1</td>
    <td class="text-right">₹{{ number_format($byMatrix[1]['tuition'] ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($byMatrix[1]['transport'] ?? 0, 2) }}</td>
    @if(($byType['other'] ?? 0) > 0)<td class="text-right">₹{{ number_format($byMatrix[1]['other'] ?? 0, 2) }}</td>@endif
    <td class="text-right font-bold" style="background:#f0fdf4">₹{{ number_format($bySemester[1] ?? 0, 2) }}</td>
</tr>
<tr>
    <td class="font-bold">સત્ર 2</td>
    <td class="text-right">₹{{ number_format($byMatrix[2]['tuition'] ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($byMatrix[2]['transport'] ?? 0, 2) }}</td>
    @if(($byType['other'] ?? 0) > 0)<td class="text-right">₹{{ number_format($byMatrix[2]['other'] ?? 0, 2) }}</td>@endif
    <td class="text-right font-bold" style="background:#f0fdf4">₹{{ number_format($bySemester[2] ?? 0, 2) }}</td>
</tr>
</tbody>
<tfoot>
<tr style="font-weight:700;background:#f3f4f6">
    <td>હેડ કુલ</td>
    <td class="text-right">₹{{ number_format($byType['tuition'] ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($byType['transport'] ?? 0, 2) }}</td>
    @if(($byType['other'] ?? 0) > 0)<td class="text-right">₹{{ number_format($byType['other'] ?? 0, 2) }}</td>@endif
    <td class="text-right" style="background:#ecfdf5">₹{{ number_format($totalAmount, 2) }}</td>
</tr>
</tfoot>
</table>

{{-- ===== HEAD-WISE DETAIL ===== --}}
@if(empty($feeType))
<h2>૨. હેડ મુજબ અલગ વિગત</h2>
@foreach(['tuition' => 'શાળા ફી','transport' => 'બસ ફી','other' => 'અન્ય'] as $tKey => $tLabel)
@php $grp = $groupedByType[$tKey] ?? collect(); @endphp
@if($grp->count() > 0)
<h3><span class="badge badge-{{ $tKey }}">{{ $tLabel }}</span> — {{ $grp->count() }} રસીદ, કુલ ₹{{ number_format($byType[$tKey] ?? 0, 2) }}</h3>
<table>
<thead>
<tr>
    <th>ક્રમ</th>
    <th>તારીખ</th>
    <th>રસીદ #</th>
    <th>વિદ્યાર્થી</th>
    <th>GR</th>
    <th>ધોરણ-વર્ગ</th>
    <th>સત્ર</th>
    <th class="text-right">રકમ</th>
    <th>પદ્ધતિ</th>
</tr>
</thead>
<tbody>
@foreach($grp as $idx => $p)
<tr>
    <td>{{ $idx + 1 }}</td>
    <td>{{ $p->payment_date?->format('d/m/Y') }}</td>
    <td style="font-size:10px">{{ $p->receipt_number ?? '—' }}</td>
    <td>{{ $p->student?->full_name_gu ?? $p->student?->full_name_en ?? '—' }}</td>
    <td>{{ $p->student?->gr_number ?? '—' }}</td>
    <td>{{ $p->student?->currentStandard?->name ?? '' }}-{{ $p->student?->currentClass?->name ?? '' }}</td>
    <td>@if($p->semester)સત્ર {{ $p->semester }}@else—@endif</td>
    <td class="text-right font-bold">₹{{ number_format($p->amount_paid, 2) }}</td>
    <td>{{ ['cash'=>'રોકડા','bank'=>'બેંક','cheque'=>'ચેક','online'=>'ઓનલાઇન'][$p->payment_method] ?? $p->payment_method }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr style="font-weight:700;background:#f3f4f6"><td colspan="7" class="text-right">{{ $tLabel }} કુલ</td><td class="text-right">₹{{ number_format($byType[$tKey] ?? 0, 2) }}</td><td></td></tr>
</tfoot>
</table>
@endif
@endforeach
@endif

{{-- ===== COMBINED DETAIL (full list) ===== --}}
<h2>@if(empty($feeType)) ૩. સંપૂર્ણ વિગતવાર યાદી (બધા હેડ ભેગા) @else વિગતવાર યાદી — {{ $feeTypeLabel }} @endif</h2>
<table>
<thead>
<tr>
    <th>ક્રમ</th>
    <th>તારીખ</th>
    <th>રસીદ #</th>
    <th>વિદ્યાર્થી</th>
    <th>GR</th>
    <th>મોબાઇલ</th>
    <th>ધોરણ-વર્ગ</th>
    <th>હેડ</th>
    <th>સત્ર</th>
    <th class="text-right">રકમ</th>
    <th>પદ્ધતિ</th>
    <th>લેનાર</th>
</tr>
</thead>
<tbody>
@foreach($payments as $idx => $p)
@php $ptype = $p->studentFee?->feeStructure?->type ?? 'other'; @endphp
<tr>
    <td>{{ $idx + 1 }}</td>
    <td>{{ $p->payment_date?->format('d/m/Y') }}</td>
    <td style="font-size:10px">{{ $p->receipt_number ?? '—' }}</td>
    <td>{{ $p->student?->full_name_gu ?? $p->student?->full_name_en ?? '—' }}</td>
    <td>{{ $p->student?->gr_number ?? '—' }}</td>
    <td>{{ $p->student?->mobile ?? '' }}</td>
    <td>{{ $p->student?->currentStandard?->name ?? '' }}-{{ $p->student?->currentClass?->name ?? '' }}</td>
    <td><span class="badge badge-{{ $ptype }}">{{ $typeLabels[$ptype] ?? $ptype }}</span></td>
    <td>@if($p->semester)સત્ર {{ $p->semester }}@else—@endif</td>
    <td class="text-right font-bold">₹{{ number_format($p->amount_paid, 2) }}</td>
    <td>{{ ['cash'=>'રોકડા','bank'=>'બેંક','cheque'=>'ચેક','online'=>'ઓનલાઇન'][$p->payment_method] ?? $p->payment_method }}</td>
    <td>{{ $p->receiver?->name ?? '—' }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr style="font-weight:700;background:#f3f4f6">
    <td colspan="9" class="text-right">કુલ</td>
    <td class="text-right text-green">₹{{ number_format($totalAmount, 2) }}</td>
    <td colspan="2"></td>
</tr>
</tfoot>
</table>
@endif

<div class="footer">NexSchool — {{ $academicYear->year }} | {{ $semLabel }} | {{ $feeTypeLabel ?? 'બધા હેડ' }} | કુલ વસૂલાત: ₹{{ number_format($totalAmount, 2) }} | {{ date('d/m/Y H:i') }}</div>
<script>window.print();</script>
</body>
</html>
