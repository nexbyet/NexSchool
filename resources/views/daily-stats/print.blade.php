<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <title>દૈનિક આંકડાબુક — {{ $date->format('d-m-Y') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Anek Gujarati', 'Inter', sans-serif;
            font-size: 7.5pt;
            color: #111;
            padding: 6mm 8mm;
        }
        @page { size: A4 landscape; margin: 5mm 7mm; }

        .header { text-align: center; margin-bottom: 3mm; }
        .header h1 { font-size: 11pt; font-weight: 700; }
        .header .meta { font-size: 8pt; color: #333; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 0.5px solid #222; padding: 0.8mm 1.5mm; text-align: center; font-size: 7pt; vertical-align: middle; }
        th { background: #e5e5e5; font-weight: 600; }
        .left { text-align: left; }
        .kumar { color: #1d4ed8; }
        .kumari { color: #be185d; }
        .bold { font-weight: 700; }
        .bg-gray { background: #e5e5e5; }
        .nowrap { white-space: nowrap; }

        .print-btn { display: block; margin: 4mm auto 0; padding: 2mm 6mm; font-size: 8pt; cursor: pointer; }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school ? $school->school_name_gu : 'શાળા' }}</h1>
        @if($school && $school->management_name_gu)
            <p style="font-size:8pt; margin:0.5mm 0;">સંચાલન: {{ $school->management_name_gu }}</p>
        @endif
        <div class="meta">
            <strong>દૈનિક આંકડાબુક</strong>
            @if($school && $school->address)
                &nbsp;|&nbsp; {{ $school->address }}
            @endif
            &nbsp;|&nbsp; તારીખ: {{ $dayNamesGu[$date->dayOfWeek] }}, {{ $date->format('d-m-Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="nowrap">ધોરણ</th>
                <th rowspan="2" class="nowrap">વર્ગ</th>
                <th colspan="3">આગલા દિવસની સંખ્યા</th>
                <th colspan="3">દાખલ સંખ્યા</th>
                <th colspan="3">છોડીને ગયા સંખ્યા</th>
                <th colspan="3" class="bg-gray">કુલ (૧+૨+૩)</th>
                <th colspan="3">હાજર સંખ્યા</th>
                <th colspan="3">રજા વગર ગેરહાજર</th>
                <th colspan="3">રજા સાથે ગેરહાજર</th>
                <th colspan="3">માંદગી રજા</th>
                <th colspan="3" class="bg-gray">કુલ (૫+૬+૭+૮)</th>
            </tr>
            <tr>
                @for($i = 0; $i < 9; $i++)
                <th>કુમાર</th><th>કુમારી</th><th>કુલ</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php $metrics = ['prev', 'adm', 'lft', 't1', 'p', 'a', 'l', 's', 't2']; @endphp
            @foreach($rows as $r)
            <tr>
                <td class="left nowrap">{{ $r['standard'] }}</td>
                <td class="nowrap">{{ $r['class'] }}</td>
                @foreach($metrics as $m)
                <td class="kumar bold">{{ $r[$m]['kumar'] }}</td>
                <td class="kumari bold">{{ $r[$m]['kumari'] }}</td>
                <td class="bold">{{ $r[$m]['total'] }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        @if(!empty($grandTotals))
        <tfoot>
            <tr class="bold" style="border-top:1.5px solid #222;">
                <td colspan="2" style="text-align:center;">કુલ સરવાળો</td>
                @foreach($metrics as $m)
                <td>{{ $grandTotals[$m]['kumar'] }}</td>
                <td>{{ $grandTotals[$m]['kumari'] }}</td>
                <td>{{ $grandTotals[$m]['total'] }}</td>
                @endforeach
            </tr>
        </tfoot>
        @endif
    </table>

    <button class="print-btn" onclick="window.print()">🖨️ પ્રિન્ટ કરો</button>

    <script>window.print();</script>
</body>
</html>
