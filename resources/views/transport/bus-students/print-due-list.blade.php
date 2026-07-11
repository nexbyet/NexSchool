<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>બસ વિદ્યાર્થી — બાકી યાદી</title>
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
    .header .subtitle { font-size: 9pt; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
    th { background: #e2e8f0; font-size: 8pt; font-weight: 700; }
    td { font-size: 9pt; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .bg-red { color: #dc2626; font-weight: 700; }
    .bg-green { color: #059669; }
    .tfoot td { font-weight: 700; background: #f1f5f9; border-top: 2px solid #000; }
    .footer { margin-top: 20px; display: flex; justify-content: space-between; font-size: 8pt; }
    .sign-line { border-top: 1px solid #333; display: inline-block; min-width: 50mm; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
</style>
</head>
<body>
<div class="header">
    <div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en ?? 'શાળા' }}</div>
    <div class="title">બસ વિદ્યાર્થી — બાકી ફી યાદી</div>
    @if($request->route_id)
    @php $rt = \App\Models\Route::find($request->route_id); @endphp
    <div class="subtitle">રૂટ: {{ $rt?->route_name ?? '—' }} / {{ now()->format('d/m/Y') }}</div>
    @else
    <div class="subtitle">બધા રૂટ / {{ now()->format('d/m/Y') }}</div>
    @endif
</div>
<table>
    <thead>
        <tr>
            <th style="width:8mm">ક્રમ</th>
            <th class="text-left">નામ</th>
            <th style="width:20mm">રૂટ</th>
            <th style="width:22mm">કુલ ફી</th>
            <th style="width:22mm">ભરેલ</th>
            <th style="width:22mm">બાકી</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $i => $s)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="text-left">{{ $s->full_name_gu }}</td>
            <td>{{ $s->route->route_name }}</td>
            <td>₹{{ number_format($s->total_fee, 0) }}</td>
            <td class="bg-green">₹{{ number_format($s->paid_fee, 0) }}</td>
            <td class="{{ $s->due_fee > 0 ? 'bg-red' : '' }}">₹{{ number_format($s->due_fee, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="tfoot">
            <td colspan="3" class="text-right">કુલ:</td>
            <td>₹{{ number_format($grandTotal, 0) }}</td>
            <td class="bg-green">₹{{ number_format($grandPaid, 0) }}</td>
            <td class="{{ $grandDue > 0 ? 'bg-red' : '' }}">₹{{ number_format($grandDue, 0) }}</td>
        </tr>
    </tfoot>
</table>
<div class="footer">
    <span>તારીખ: ___________</span>
    <span>સહી: <span class="sign-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
</div>
</body>
</html>
