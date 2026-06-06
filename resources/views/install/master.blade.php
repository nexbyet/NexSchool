<!DOCTYPE html>
<html lang="{{ session('install.lang', 'gu') }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Install — NexSchool</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.lineicons.com/5.1/line/lineicons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Anek+Gujarati:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{font-family:'Inter','Anek Gujarati',sans-serif}
body{background:linear-gradient(135deg,#090e1a 0%,#111827 40%,#0f1923 100%);min-height:100vh}
.sb{background:rgba(15,23,42,.65);-webkit-backdrop-filter:blur(24px);backdrop-filter:blur(24px);border-right:1px solid rgba(255,255,255,.04)}
.st{display:flex;align-items:center;gap:12px;padding:12px 18px;border-radius:12px;transition:all .25s;position:relative}
.st .n{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;transition:all .3s}
.st .l{font-size:.82rem;font-weight:600}
.st .d{font-size:.68rem;margin-top:1px}
.st.done .n{background:#059669;color:#fff;box-shadow:0 0 12px rgba(5,150,105,.25)}
.st.done .l{color:#6ee7b7}
.st.done .d{color:rgba(110,231,183,.45)}
.st.active{background:linear-gradient(135deg,rgba(99,102,241,.1),rgba(139,92,246,.05));border:1px solid rgba(99,102,241,.12)}
.st.active .n{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;box-shadow:0 0 18px rgba(99,102,241,.3)}
.st.active .l{color:#a5b4fc}
.st.active .d{color:rgba(165,180,252,.45)}
.st.active::before{content:'';position:absolute;left:-1px;top:50%;transform:translateY(-50%);width:3px;height:36px;background:linear-gradient(180deg,#6366f1,#8b5cf6);border-radius:0 3px 3px 0}
.st.pend .n{background:rgba(255,255,255,.03);color:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.05)}
.st.pend .l{color:rgba(255,255,255,.22)}
.st.pend .d{color:rgba(255,255,255,.1)}
.cd{background:rgba(255,255,255,.018);border:1px solid rgba(255,255,255,.055);border-radius:18px;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.ig label{display:block;font-size:.78rem;font-weight:600;color:rgba(255,255,255,.48);margin-bottom:5px;letter-spacing:.02em;text-transform:uppercase}
.ig input,.ig textarea,.ig select{width:100%;padding:11px 15px;border-radius:10px;font-size:.88rem;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.07);color:#fff;transition:all .2s}
.ig input:focus,.ig textarea:focus,.ig select:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.12);outline:none}
.ig input::placeholder,.ig textarea::placeholder{color:rgba(255,255,255,.12)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 24px;border-radius:10px;font-weight:600;font-size:.84rem;cursor:pointer;transition:all .25s;border:none}
.btn-p{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff}
.btn-p:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(99,102,241,.3)}
.btn-p:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none}
.btn-g{background:rgba(255,255,255,.035);color:rgba(255,255,255,.45);border:1px solid rgba(255,255,255,.05)}
.btn-g:hover{background:rgba(255,255,255,.06);color:#fff}
.btn-d{background:rgba(239,68,68,.08);color:#fca5a5;border:1px solid rgba(239,68,68,.15)}
.btn-d:hover{background:rgba(239,68,68,.12)}
.alert-s{background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.16);border-radius:10px;padding:12px 16px;color:#6ee7b7;font-size:.88rem}
.alert-e{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.16);border-radius:10px;padding:12px 16px;color:#fca5a5;font-size:.88rem}
.sp{width:18px;height:18px;border:2px solid rgba(255,255,255,.12);border-top-color:#a5b4fc;border-radius:50%;animation:spin .6s linear infinite;display:inline-block;vertical-align:middle}
@keyframes spin{to{transform:rotate(360deg)}}
.fi{animation:fi .35s ease-out}
@keyframes fi{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:768px){.sb{display:none}.main{padding:16px}}
</style>
</head>
<body class="flex min-h-screen">

@php
$stepNames = ['welcome','requirements','database','license','admin','school','install'];
$stepLabels = ['Welcome','Requirements','Database','License','Admin','School','Install'];
$stepDescs = ['Language','System Check','MySQL','License Key','Admin Account','School Info','Run'];
$routeName = Route::currentRouteName();
$stepMap = ['install.welcome'=>0,'install.requirements'=>1,'install.database'=>2,'install.license'=>3,'install.admin'=>4,'install.school'=>5,'install.run'=>6,'install.complete'=>6];
$currentStep = $stepMap[$routeName] ?? 0;
@endphp

<aside class="w-[270px] sb flex flex-col flex-shrink-0 p-7 min-h-screen max-md:hidden">
    <div class="flex items-center gap-3 mb-10 px-2">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/15">
            <i class="lni lni-graduation text-lg text-white"></i>
        </div>
        <div>
            <div class="text-white font-bold text-sm tracking-tight">NexSchool</div>
            <div class="text-gray-600 text-[11px]">Install Wizard</div>
        </div>
    </div>
    <nav class="flex-1 space-y-1">
        @foreach($stepNames as $i => $name)
        @php $state = $i < $currentStep ? 'done' : ($i == $currentStep ? 'active' : 'pend'); @endphp
        <div class="st {{ $state }}">
            <div class="n">@if($state==='done')<i class="lni lni-checkmark text-xs"></i>@else{{ $i+1 }}@endif</div>
            <div class="min-w-0">
                <div class="l">{{ $stepLabels[$i] }}</div>
                <div class="d">{{ $stepDescs[$i] }}</div>
            </div>
        </div>
        @endforeach
    </nav>
    <div class="pt-6 border-t border-white/[.04] mt-6">
        <p class="text-[11px] text-gray-700">© {{ date('Y') }} NexSchool</p>
    </div>
</aside>

<main class="flex-1 flex items-center justify-center p-5 lg:p-8 main">
    <div class="w-full max-w-lg fi">
        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
