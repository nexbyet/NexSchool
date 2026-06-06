<!DOCTYPE html>
<html lang="{{ $lang === 'gu' ? 'gu' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>Bonafied Certificate — {{ $fullName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Anek Gujarati', 'Inter', sans-serif;
            font-size: 11pt;
            color: #111;
            line-height: 1.5;
        }
        @page { size: A4 portrait; margin: 6mm 8mm; }

        .certificate {
            max-width: 185mm;
            margin: 0 auto;
            border: 2px solid #111;
            padding: 5mm 8mm 5mm 15mm;
        }

        .letterhead {
            display: flex;
            align-items: center;
            gap: 4mm;
            border-bottom: 2px solid #111;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }
        .letterhead .logo-wrap {
            flex-shrink: 0;
            width: 22mm;
            height: 22mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .letterhead .logo-wrap img {
            max-width: 22mm;
            max-height: 22mm;
            object-fit: contain;
        }
        .letterhead .school-info { flex: 1; text-align: center; }
        .letterhead .school-name { font-size: 15pt; font-weight: 700; }
        .letterhead .line { font-size: 8.5pt; color: #333; }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            text-decoration: underline;
            letter-spacing: 1pt;
            margin: 4mm 0;
        }

        .info-line {
            font-size: 10.5pt;
            margin: 0.8mm 0;
        }
        .info-line .lbl { font-weight: 500; color: #333; }
        .info-line .val { font-weight: 600; }

        .cert-text {
            margin: 5mm 0 3mm 0;
            font-size: 11pt;
            line-height: 1.8;
            text-align: justify;
        }

        .principal-line {
            text-align: right;
            margin-top: 16mm;
        }
        .principal-line .sign-line {
            display: inline-block;
            width: 60mm;
            border-top: 2px solid #111;
            padding-top: 2mm;
            text-align: center;
            font-weight: 700;
            font-size: 11pt;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="certificate">
        {{-- Letterhead with logo --}}
        <div class="letterhead">
            <div class="logo-wrap">
                @if($school && $school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" onerror="this.style.display='none'">
                @endif
            </div>
            <div class="school-info">
                <div class="school-name">{{ $school?->school_name_gu ?? 'શાળા' }}</div>
                @if($school && $school->management_name_gu)
                    <div class="line">સંચાલન: {{ $school->management_name_gu }}</div>
                @endif
                @if($school && $school->address)
                    <div class="line">{{ $school->address }}</div>
                @endif
                <div class="line">
                    @if($school && $school->mobile)મોબાઇલ: {{ $school->mobile }} @endif
                    @if($school && $school->email) | ઇમેઇલ: {{ $school->email }} @endif
                    @if($school && $school->uid_number) | UID: {{ $school->uid_number }} @endif
                </div>
                @if($school && $school->grant_number)
                    <div class="line">
                        મંજૂરી નં: {{ $school->grant_number }}
                        @if($school->grant_date) | તા: {{ \Carbon\Carbon::parse($school->grant_date)->format('d/m/Y') }} @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <div class="title">{{ $lang === 'gu' ? 'બોનાફાઈડ પ્રમાણપત્ર' : 'BONAFIDE CERTIFICATE' }}</div>

        {{-- Info + Photo --}}
        <div style="display:flex; gap:4mm;">
            <div style="flex:1;">
                @if($lang === 'gu')
                    <div class="info-line"><span class="lbl">તારીખ:</span> <span class="val">{{ $today->format('d/m/Y') }}</span></div>
                    <div class="info-line"><span class="lbl">GR નંબર:</span> <span class="val">{{ $grNumber }}</span></div>
                    <div class="info-line"><span class="lbl">વિદ્યાર્થીનું પૂરું નામ:</span> <span class="val">{{ $fullName }}</span></div>
                    <div class="info-line"><span class="lbl">જન્મ તારીખ:</span> <span class="val">{{ $dob }}</span></div>
                    <div class="info-line"><span class="lbl">જન્મ તારીખ (અક્ષરમાં):</span> <span class="val">{{ $dobInText }}</span></div>
                @else
                    <div class="info-line"><span class="lbl">Date:</span> <span class="val">{{ $today->format('d/m/Y') }}</span></div>
                    <div class="info-line"><span class="lbl">GR Number:</span> <span class="val">{{ $grNumber }}</span></div>
                    <div class="info-line"><span class="lbl">Full Name of Student:</span> <span class="val">{{ $fullName }}</span></div>
                    <div class="info-line"><span class="lbl">Date of Birth:</span> <span class="val">{{ $dob }}</span></div>
                    <div class="info-line"><span class="lbl">Date of Birth (in Words):</span> <span class="val">{{ $dobInText }}</span></div>
                @endif
            </div>
            <div style="flex-shrink:0; width:28mm; text-align:right;">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" style="width:25mm; height:32mm; object-fit:cover; border:1px solid #333;">
                @else
                    <div style="width:25mm; height:32mm; border:1px dashed #999; display:inline-flex; align-items:center; justify-content:center; font-size:7pt; color:#999;">PHOTO</div>
                @endif
            </div>
        </div>

        @if($lang === 'gu')
            <p class="cert-text">
                &emsp;&emsp;&emsp;આથી પ્રમાણિત કરવામાં આવે છે કે <strong>{{ $fullName }}</strong> અમારી શાળામાં ધોરણ <strong>{{ $standard }}</strong> માં અભ્યાસ કરે છે. જી.આર. માં તેની જન્મ તારીખ <strong>{{ $dob }}</strong> અને શબ્દોમાં <strong>{{ $dobInText }}</strong> નોંધાયેલી છે. જેનો ધર્મ <strong>{{ $religion }}</strong> અને જ્ઞાતિ <strong>{{ $cast }}</strong> છે. આ પ્રમાણપત્ર વાલીની વિનંતીથી અને શાળાના રેકોર્ડ પરથી ખરાઈ કરી આપવામાં આવે છે.
            </p>
        @else
            <p class="cert-text">
                &emsp;&emsp;&emsp;This is to certify that <strong>{{ $fullName }}</strong> is studying in Standard <strong>{{ $standard }}</strong> in our school. Her/his date of birth <strong>{{ $dob }}</strong> (in words: <strong>{{ $dobInText }}</strong>) is recorded in the school records. Her/his religion is <strong>{{ $religion }}</strong> and caste is <strong>{{ $cast }}</strong>. This certificate is issued upon the request of the parent/guardian and verified from the school records.
            </p>
        @endif

        {{-- Principal signature --}}
        <div class="principal-line">
            <div class="sign-line">{{ $lang === 'gu' ? 'શાળાના આચાર્ય' : 'Principal' }}</div>
        </div>
    </div>

    <script>window.print();</script>
</body>
</html>
