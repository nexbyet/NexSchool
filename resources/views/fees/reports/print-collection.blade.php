<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>વસૂલાત રિપોર્ટ — {{ $academicYear->year }}</title>
<style>
@page { size: A4 landscape; margin: 8mm; }
body { font-family: 'Anek Gujarati', sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }
.school-name { text-align: center; font-size: 18px; font-weight: 700; margin: 0 0 2px; }
h1 { font-size: 15px; text-align: center; margin: 0 0 3px; font-weight: 600; }
.subtitle { text-align: center; font-size: 11px; color: #6b7280; margin-bottom: 10px; }
table { width: 100%; border-collapse: collapse; }
th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; padding: 6px 7px; text-align: left; border: 1px solid #d1d5db; }
td { font-size: 12px; padding: 5px 7px; border: 1px solid #d1d5db; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en ?? 'NexSchool' }}</div>
<h1>વસૂલાત રિપોર્ટ — {{ $academicYear->year }}</h1>
<p class="subtitle">{{ $semLabel }} | {{ request('from_date') }} થી {{ request('to_date') }} | {{ date('d/m/Y') }}</p>

@if($payments->isEmpty())
<p style="text-align:center;color:#6b7280;padding:40px 0">કોઈ વસૂલાત નથી</p>
@else
<table>
<thead>
<tr>
    <th>તારીખ</th>
    <th>રસીદ #</th>
    <th>વિદ્યાર્થી</th>
    <th>GR</th>
    <th>ધોરણ-વર્ગ</th>
    <th>સત્ર</th>
    <th class="text-right">રકમ</th>
    <th>પદ્ધતિ</th>
    <th>સંદર્ભ</th>
    <th>લેનાર</th>
</tr>
</thead>
<tbody>
@foreach($payments as $p)
<tr>
    <td>{{ $p->payment_date?->format('d/m/Y') }}</td>
    <td>{{ $p->receipt_number ?? '—' }}</td>
    <td>{{ $p->student?->full_name_gu ?? $p->student?->full_name_en ?? '—' }}</td>
    <td>{{ $p->student?->gr_number ?? '—' }}</td>
    <td>{{ $p->student?->currentStandard?->name ?? '' }} - {{ $p->student?->currentClass?->name ?? '' }}</td>
    <td>@if($p->semester)સત્ર {{ $p->semester }}@else—@endif</td>
    <td class="text-right font-bold text-green">₹{{ number_format($p->amount_paid, 2) }}</td>
    <td>{{ ['cash'=>'રોકડા','bank'=>'બેંક','cheque'=>'ચેક','online'=>'ઓનલાઇન'][$p->payment_method] ?? $p->payment_method }}</td>
    <td>{{ $p->reference_number ?? '—' }}</td>
    <td>{{ $p->receiver?->name ?? '—' }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr style="font-weight:700;background:#f3f4f6">
    <td colspan="6" class="text-right">કુલ</td>
    <td class="text-right text-green">₹{{ number_format($totalAmount, 2) }}</td>
    <td colspan="3"></td>
</tr>
</tfoot>
</table>
@endif

<div class="footer">NexSchool — {{ $academicYear->year }} | {{ $semLabel }} | કુલ વસૂલાત: ₹{{ number_format($totalAmount, 2) }}</div>
<script>window.print();</script>
</body>
</html>
