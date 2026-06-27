<!DOCTYPE html>
<html lang="gu">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>કસ્ટમ રિપોર્ટ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@page {
    size: A4 landscape;
    margin: 15mm 12mm;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Anek Gujarati', 'Inter', sans-serif;
    font-size: 9pt;
    color: #000;
    line-height: 1.4;
}
.report-header {
    text-align: center;
    margin-bottom: 10px;
    position: relative;
}
.school-logo {
    max-height: 50px;
    margin-bottom: 4px;
}
.school-name {
    font-size: 14pt;
    font-weight: 700;
}
.school-address {
    font-size: 8pt;
    color: #4b5563;
}
.report-title {
    font-size: 11pt;
    font-weight: 600;
    margin: 6px 0;
    text-align: center;
}
.report-subtitle {
    font-size: 8pt;
    color: #6b7280;
    text-align: center;
    margin-bottom: 8px;
}
.report-std-class {
    font-size: 8pt;
    text-align: center;
    margin-bottom: 6px;
}
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8pt;
}
thead th {
    background: transparent;
    color: #000;
    padding: 5px 4px;
    font-weight: 600;
    text-align: center;
    border: 1px solid #000;
    font-size: 7.5pt;
}
tbody td {
    padding: 4px;
    border: 1px solid #000;
    text-align: center;
    vertical-align: middle;
}
.sr-col { width: 30px; }
.footer {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    font-size: 8pt;
    color: #4b5563;
    padding: 0 20px;
}
.footer .sign-line {
    display: inline-block;
    width: 150px;
    border-top: 1px solid #000;
    padding-top: 3px;
    text-align: center;
    font-size: 7pt;
}
@media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>
<div class="report-header">
    @if($school && $school->school_logo)
        <img src="{{ asset('storage/' . $school->school_logo) }}" alt="Logo" class="school-logo">
    @endif
    @if($school)
        <div class="school-name">{{ $school->school_name_gu ?? $school->school_name_en }}</div>
        <div class="school-address">{{ $school->address ?? '' }} | મોબાઇલ: {{ $school->mobile ?? '' }}</div>
    @endif
</div>

@if($titleGu || $titleEn)
    <div class="report-title">{{ $titleGu }}</div>
    <div class="report-subtitle">{{ $titleEn }}</div>
@endif

@if($standardName)
    <div class="report-std-class">
        ધોરણ: <strong>{{ $standardName }}</strong>@if($className) — વર્ગ: <strong>{{ $className }}</strong>@endif
    </div>
@endif

<table>
    <thead>
        <tr>
            @if($hasSrNo)
                <th class="sr-col">ક્રમ</th>
            @endif
            @foreach($columns as $col)
                @php
                    $w = $columnWidths[$col] ?? null;
                    $style = $w ? ' style="width:'.$w.'px"' : '';
                    $labels = [
                        'gr_number' => 'GR નંબર',
                        'full_name_gu' => 'પૂરું નામ',
                        'full_name_en' => 'Full Name',
                        'student_name_gu' => 'નામ (ગુ.)',
                        'student_name_en' => 'Name (En)',
                        'father_name_gu' => 'પિતાનું નામ',
                        'father_name_en' => "Father's Name",
                        'surname_gu' => 'અટક',
                        'surname_en' => 'Surname',
                        'mother_name_gu' => 'માતાનું નામ',
                        'mother_name_en' => "Mother's Name",
                        'date_of_birth' => 'જન્મ તા.',
                        'age' => 'ઉંમર',
                        'sharirik_jaati' => 'કુ./કુ.',
                        'category_gu' => 'શ્રેણી',
                        'category_en' => 'Category',
                        'religion_gu' => 'ધર્મ',
                        'religion_en' => 'Religion',
                        'cast_gu' => 'જ્ઞાતિ',
                        'cast_en' => 'Cast',
                        'mobile' => 'મોબાઇલ',
                        'whatsapp' => 'WhatsApp',
                        'aadhar_no' => 'આધાર નં.',
                        'apaar_id' => 'APAAR',
                        'uid_no' => 'UID',
                        'pen_no' => 'PEN',
                        'current_standard' => 'ધોરણ',
                        'current_class' => 'વર્ગ',
                        'date_of_admission' => 'પ્રવેશ તા.',
                        'admission_standard' => 'પ્ર.ધોરણ',
                        'last_school_gu' => 'છેલ્લી શાળા',
                        'last_school_en' => 'Last School',
                        'birth_place_gu' => 'જન્મ સ્થળ',
                        'native_place_gu' => 'વતન',
                        'is_minority' => 'લઘુ.',
                        'admission_under_rte' => 'RTE',
                    ];
                    $header = $labels[$col] ?? $col;
                    // check if this key has a custom column header override
                    if (isset($customColumns[$col]['header_gu']) && $customColumns[$col]['header_gu']) {
                        $header = $customColumns[$col]['header_gu'];
                    }
                @endphp
                <th{!! $style !!}>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $rh = $rowHeight > 0 ? ' style="height:'.$rowHeight.'mm"' : ''; @endphp
        @forelse($students as $idx => $row)
            <tr{!! $rh !!}>
                @if($hasSrNo)
                    <td>{{ $idx + 1 }}</td>
                @endif
                @foreach($columns as $col)
                    @php
                        $tdStyle = '';
                        if (isset($columnWidths[$col])) {
                            $tdStyle = ' style="width:'.$columnWidths[$col].'px"';
                        }
                        $val = $row[$col] ?? '';
                        // If it's a custom blank column and has no value, show blank
                        if (isset($customColumns[$col])) {
                            $val = '';
                        }
                    @endphp
                    <td{!! $tdStyle !!}>{{ $val }}</td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ count($columns) + ($hasSrNo ? 1 : 0) }}" style="text-align:center;padding:20px;color:#9ca3af;">કોઈ વિદ્યાર્થી મળ્યો નથી</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <div>
        <span class="sign-line">આચાર્ય</span>
    </div>
    <div>
        <span>કુલ વિદ્યાર્થીઓ: {{ $studentCount }}</span>
    </div>
    <div>
        <span class="sign-line">તારીખ</span>
    </div>
</div>
</body>
</html>
