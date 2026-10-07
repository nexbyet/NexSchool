<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<title>રિપોર્ટ — પ્રિન્ટ</title>
<link rel="stylesheet" href="{{ asset('css/app-1.1.7.css') }}">
<style>
    @page { size: A4 landscape; margin: 8mm; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 400; font-display: swap; src: url("{{ asset('fonts/anek/anek-gujarati-400.woff2') }}") format("woff2"); unicode-range: U+0951-0952, U+0964-0965, U+0A80-0AFF, U+200C-200D, U+20B9, U+25CC, U+A830-A839; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 400; font-display: swap; src: url("{{ asset('fonts/anek/anek-latin-400.woff2') }}") format("woff2"); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 500; font-display: swap; src: url("{{ asset('fonts/anek/anek-gujarati-500.woff2') }}") format("woff2"); unicode-range: U+0951-0952, U+0964-0965, U+0A80-0AFF, U+200C-200D, U+20B9, U+25CC, U+A830-A839; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 500; font-display: swap; src: url("{{ asset('fonts/anek/anek-latin-500.woff2') }}") format("woff2"); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 600; font-display: swap; src: url("{{ asset('fonts/anek/anek-gujarati-600.woff2') }}") format("woff2"); unicode-range: U+0951-0952, U+0964-0965, U+0A80-0AFF, U+200C-200D, U+20B9, U+25CC, U+A830-A839; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 600; font-display: swap; src: url("{{ asset('fonts/anek/anek-latin-600.woff2') }}") format("woff2"); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 700; font-display: swap; src: url("{{ asset('fonts/anek/anek-gujarati-700.woff2') }}") format("woff2"); unicode-range: U+0951-0952, U+0964-0965, U+0A80-0AFF, U+200C-200D, U+20B9, U+25CC, U+A830-A839; }
    @font-face { font-family: 'Anek Gujarati'; font-style: normal; font-weight: 700; font-display: swap; src: url("{{ asset('fonts/anek/anek-latin-700.woff2') }}") format("woff2"); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
    body { font-family: 'Anek Gujarati', sans-serif; background: #fff; }
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
    table.report { border-collapse: collapse; width: 100%; font-size: 9px; }
    table.report th, table.report td { border: 1px solid #999; padding: 2px 3px; }
    table.report thead th { background: #eee; }
    .vhead { writing-mode: vertical-rl; transform: rotate(180deg); white-space: nowrap; max-height: 105px; margin: 0 auto; font-size: 9px; }
    table.report thead th.vcol { height: 120px; vertical-align: bottom; }
</style>
</head>
<body class="p-4">
<div class="no-print mb-4 flex items-center gap-3">
    <button onclick="window.print()" class="px-5 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg">પ્રિન્ટ કરો</button>
    <button onclick="window.close()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">બંધ કરો</button>
</div>

@php
    $stdName = ''; $clsName = ''; $yearName = '';
    try {
        $stdName = \App\Models\Standard::find($filters['standard_id'])->name ?? '';
        $cls = \App\Models\SchoolClass::find($filters['school_class_id']);
        $clsName = $cls->name ?? '';
        $yearName = \App\Models\AcademicYear::find($filters['academic_year_id'])->year ?? '';
    } catch (\Exception $e) {}
    $subHeadCounts = [];
    $subTotals = [];
    $grandTotal = 0;
    foreach ($subjects as $s) {
        $n = 0;
        $t = 0;
        foreach ($head_cols as $hc) {
            if ($hc['subject_id'] == $s['id']) { $n++; $t += $hc['converted']; }
        }
        $subHeadCounts[$s['id']] = $n;
        $subTotals[$s['id']] = $t;
        $grandTotal += $t;
    }
@endphp

<div class="text-center mb-3">
    <h1 class="text-xl font-bold">{{ $school->school_name_gu ?? 'NexSchool' }}</h1>
    <p class="text-xs text-gray-600">{{ $school->address ?? '' }}</p>
    <h2 class="text-base font-bold mt-1">રિપોર્ટ ({{ $semester_label }}) — {{ $stdName }}-{{ $clsName }} ({{ $yearName }})</h2>
</div>

<table class="report">
    <thead>
        <tr>
            <th rowspan="2">ક્રમ</th>
            <th rowspan="2">GR</th>
            <th rowspan="2">નામ</th>
            @foreach($subjects as $s)
            <th colspan="{{ $subHeadCounts[$s['id']] + 2 }}">{{ $s['name'] }}</th>
            @endforeach
            <th rowspan="2" class="vcol"><div class="vhead">કુલ / {{ number_format($grandTotal, 0) }}</div></th>
            <th rowspan="2">ટકા</th>
            <th rowspan="2">ગ્રેડ</th>
            <th rowspan="2">પાસ?</th>
            <th rowspan="2">ક્રમ</th>
        </tr>
        @php $multiSem = count(array_unique(array_column($head_cols, 'semester'))) > 1; @endphp
        <tr>
            @for($hci = 0; $hci < count($head_cols); $hci++)
            @php $hc = $head_cols[$hci]; @endphp
            <th class="vcol"><div class="vhead">{{ $hc['head_name'] }}·S{{ $hc['semester'] }}</div><div>{{ number_format($hc['converted'], 0) }}</div></th>
            @if($hci + 1 >= count($head_cols) || $head_cols[$hci + 1]['subject_id'] != $hc['subject_id'])
            <th class="vcol"><div class="vhead">કુલ / {{ number_format($subTotals[$hc['subject_id']] ?? 0, 0) }}</div></th>
            <th>ગ્રેડ</th>
            @endif
            @endfor
        </tr>
    </thead>
    <tbody>
        @php $sr = 0; @endphp
        @foreach($rows as $r)
        @php $sr++; @endphp
        <tr>
            <td>{{ $sr }}</td>
            <td>{{ $r['gr_number'] }}</td>
            <td>{{ $r['name'] }}</td>
            @foreach($r['cells'] as $ci => $v)
            <td style="text-align:center;font-weight:bold;">@if($v === null) — @else {{ number_format($v, 0) }} @endif</td>
            @php
                $hc = $head_cols[$ci];
                $nextIdx = $ci + 1;
                $isLast = !isset($head_cols[$nextIdx]) || $head_cols[$nextIdx]['subject_id'] != $hc['subject_id'];
            @endphp
            @if($isLast)
                @php
                    $si = null;
                    foreach ($subjects as $sii => $ss) {
                        if ($ss['id'] == $hc['subject_id']) { $si = $sii; break; }
                    }
                    $sub = ($si !== null && isset($r['subs'][$si])) ? $r['subs'][$si] : null;
                @endphp
                <td style="text-align:center;font-weight:bold;">@if(!$sub) — @else {{ number_format($sub['obtained'], 0) }} @endif</td>
                <td style="text-align:center;font-weight:bold;">@if(!$sub) — @else {{ $sub['grade'] ?? '' }} @endif</td>
            @endif
            @endforeach
            <td style="text-align:center;font-weight:bold;">@if($r['has_any']) {{ number_format($r['obtained'], 0) }} @else — @endif</td>
            <td style="text-align:center;font-weight:bold;">@if($r['percent'] === null) — @else {{ number_format($r['percent'], 2) }}% @endif</td>
            <td style="text-align:center;font-weight:bold;">{{ $r['grade'] ?? '—' }}</td>
            <td style="text-align:center;">@if(!$r['has_any']) બાકી @elseif($r['pass']) પાસ @else નાપાસ @endif</td>
            <td style="text-align:center;font-weight:bold;">{{ $r['rank'] ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="flex justify-between mt-8 text-xs">
    <span>વર્ગશિક્ષકની સહી: _______________</span>
    <span>તારીખ: {{ date('d/m/Y') }}</span>
    <span>આચાર્યની સહી: _______________</span>
</div>
</body>
</html>
