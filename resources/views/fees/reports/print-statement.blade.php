<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>વિદ્યાર્થી સ્ટેટમેન્ટ — {{ $student->full_name_gu ?? $student->full_name_en }}</title>
<style>
@page { size: A4 portrait; margin: 15mm 12mm; }
body { font-family: 'Anek Gujarati', sans-serif; font-size: 10px; color: #1f2937; margin: 0; padding: 0; }
h1 { font-size: 14px; text-align: center; margin: 0 0 4px; }
.student-info { text-align: center; font-size: 11px; margin-bottom: 4px; }
.subtitle { text-align: center; font-size: 10px; color: #6b7280; margin-bottom: 12px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; padding: 6px 8px; text-align: left; border: 1px solid #d1d5db; }
td { padding: 5px 8px; border: 1px solid #d1d5db; }
.text-right { text-align: right; }
.font-bold { font-weight: 700; }
.text-green { color: #059669; }
.text-red { color: #dc2626; }
.text-amber { color: #d97706; }
.footer { text-align: center; font-size: 8px; color: #9ca3af; margin-top: 10px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
.section-title { font-size: 11px; font-weight: 700; margin: 8px 0 4px; }
.summary-grid { display: flex; gap: 8px; margin-bottom: 10px; }
.summary-item { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; text-align: center; }
.summary-item .label { font-size: 8px; color: #6b7280; }
.summary-item .value { font-size: 13px; font-weight: 700; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<h1>વિદ્યાર્થી ફી સ્ટેટમેન્ટ</h1>
<p class="student-info">{{ $student->full_name_gu ?? $student->full_name_en }} (GR: {{ $student->gr_number }})</p>
<p class="subtitle">{{ $academicYear->year }} — {{ $semLabel }} | {{ date('d/m/Y') }}</p>

<div class="summary-grid">
    <div class="summary-item"><div class="label">કુલ ફી</div><div class="value">₹{{ number_format($totalNetFee, 2) }}</div></div>
    <div class="summary-item"><div class="label text-green">ચૂકવેલ</div><div class="value text-green">₹{{ number_format($totalPaid, 2) }}</div></div>
    <div class="summary-item"><div class="label text-red">બાકી</div><div class="value text-red">₹{{ number_format($dueAmount, 2) }}</div></div>
</div>

@if($carryForwards->where('to_academic_year_id', $academicYear->id)->isNotEmpty())
<div class="section-title">કેરી ફોરવર્ડ</div>
<table>
<thead><tr><th>વર્ષથી</th><th>વર્ષ સુધી</th><th class="text-right">રકમ</th></tr></thead>
<tbody>
@foreach($carryForwards->where('to_academic_year_id', $academicYear->id) as $cf)
<tr><td>{{ $cf->fromAcademicYear?->year ?? '—' }}</td><td>{{ $cf->toAcademicYear?->year ?? '—' }}</td><td class="text-right text-amber">₹{{ number_format($cf->amount, 2) }}</td></tr>
@endforeach
</tbody>
</table>
@endif

@if($studentFees->isNotEmpty())
<div class="section-title">ફી વિગત</div>
<table>
<thead><tr><th>ફી પ્રકાર</th><th>સત્ર</th><th class="text-right">કુલ</th><th class="text-right">છૂટ</th><th class="text-right">ચોખ્ખી</th></tr></thead>
<tbody>
@foreach($studentFees as $sf)
<tr>
    <td>{{ ['tuition'=>'શાળા ફી','transport'=>'બસ ફી','other'=>'અન્ય'][$sf->feeStructure?->type ?? 'other'] ?? 'અન્ય' }}</td>
    <td>@if($sf->semester)સત્ર {{ $sf->semester }}@else—@endif</td>
    <td class="text-right">₹{{ number_format($sf->total_amount, 2) }}</td>
    <td class="text-right text-amber">₹{{ number_format($sf->concession_amount, 2) }}</td>
    <td class="text-right font-bold">₹{{ number_format($sf->net_amount, 2) }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

@if($payments->isNotEmpty())
<div class="section-title">ચુકવણી ઇતિહાસ</div>
<table>
<thead><tr><th>તારીખ</th><th>રસીદ #</th><th class="text-right">રકમ</th><th>સત્ર</th><th>પદ્ધતિ</th><th>સંદર્ભ</th></tr></thead>
<tbody>
@foreach($payments as $p)
<tr>
    <td>{{ $p->payment_date?->format('d/m/Y') }}</td>
    <td>{{ $p->receipt_number ?? '—' }}</td>
    <td class="text-right text-green font-bold">₹{{ number_format($p->amount_paid, 2) }}</td>
    <td>@if($p->semester)સત્ર {{ $p->semester }}@else—@endif</td>
    <td>{{ ['cash'=>'રોકડા','bank'=>'બેંક','cheque'=>'ચેક','online'=>'ઓનલાઇન'][$p->payment_method] ?? $p->payment_method }}</td>
    <td>{{ $p->reference_number ?? '—' }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<div class="footer">NexSchool — {{ $academicYear->year }} | {{ $semLabel }} | Printed: {{ date('d/m/Y H:i') }}</div>
<script>window.print();</script>
</body>
</html>
