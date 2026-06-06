<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <title>રજાઓની યાદી</title>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 12mm 12mm 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Anek Gujarati', sans-serif; color: #1e293b; font-size: 12px; }
        .small-header { position: fixed; top: 0; left: 0; right: 0; height: 10mm; display: flex; align-items: center; justify-content: space-between; padding: 0 12mm; background: #fff; border-bottom: 1px solid #cbd5e1; font-size: 9px; color: #64748b; z-index: 100; }
        .small-header .school { font-weight: 600; color: #1e40af; }
        .small-header .list-info { text-align: right; }
        .first-page-cover { display: flex; align-items: center; gap: 14px; padding-top: 14mm; margin-bottom: 12px; }
        .first-page-cover .logo { width: 64px; height: 64px; object-fit: contain; border-radius: 8px; flex-shrink: 0; }
        .first-page-cover .info { flex: 1; }
        .first-page-cover h1 { font-size: 18px; font-weight: 700; color: #1e3a5f; }
        .first-page-cover p { font-size: 11px; color: #475569; line-height: 1.5; }
        .title-section { text-align: center; margin-bottom: 10px; }
        .title-section h2 { font-size: 15px; color: #1e293b; }
        .title-section p { font-size: 10px; color: #64748b; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead { display: table-header-group; }
        th { background: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-weight: 600; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 9px; font-weight: 600; }
        .badge-jaher { background: #fee2e2; color: #b91c1c; }
        .badge-sthanik { background: #fef3c7; color: #92400e; }
        .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body>
    <div class="small-header">
        <span class="school">{{ $schoolSetting?->school_name_gu ?? 'શાળાનું નામ' }}</span>
        <span class="list-info"><strong>રજાઓની યાદી</strong> | શૈક્ષણિક વર્ષ: {{ $activeYear?->year ?? '—' }}</span>
    </div>

    <div class="first-page-cover">
        @if($schoolSetting && $schoolSetting->logo)
            <img src="{{ asset('storage/' . $schoolSetting->logo) }}" class="logo" onerror="this.style.display='none'">
        @endif
        <div class="info">
            <h1>{{ $schoolSetting?->school_name_gu ?? 'શાળાનું નામ' }}</h1>
            <p>
                @if($schoolSetting?->address){{ $schoolSetting->address }}@endif
                @if($schoolSetting?->mobile) | મોબાઇલ: {{ $schoolSetting->mobile }}@endif
                @if($schoolSetting?->email) | ઇમેઇલ: {{ $schoolSetting->email }}@endif
            </p>
        </div>
    </div>

    <div class="title-section">
        <h2>રજાઓની યાદી</h2>
        <p>શૈક્ષણિક વર્ષ: {{ $activeYear?->year ?? '—' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40px">ક્રમ</th>
                <th>રજાનું નામ</th>
                <th style="width:90px">પ્રકાર</th>
                <th style="width:80px">તારીખ</th>
                <th style="width:75px">વાર</th>
            </tr>
        </thead>
        <tbody>
            @forelse($holidays as $i => $h)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $h->name }}</td>
                <td><span class="badge {{ $h->type === 'jaher' ? 'badge-jaher' : 'badge-sthanik' }}">{{ $h->type === 'jaher' ? 'જાહેર' : 'સ્થાનિક' }}</span></td>
                <td>{{ \Carbon\Carbon::parse($h->date)->format('d/m/Y') }}</td>
                <td>{{ str_replace('બુધ્વાર', 'બુધવાર', \Carbon\Carbon::parse($h->date)->locale('gu')->dayName) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">કોઈ રજા નથી</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">કમ્પ્યુટર સિસ્ટમ દ્વારા જનરેટ — {{ now()->locale('gu')->isoFormat('DD/MM/YYYY') }}</div>
</body>
</html>