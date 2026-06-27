<!DOCTYPE html>
<html lang="gu" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $school->school_name_gu ?? 'શાળા') — {{ $school->school_name_gu ?? '' }}</title>
    <meta name="description" content="@yield('meta_description', $school->school_name_gu ?? '')">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Gujarati:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/5.1/line/lineicons.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php $__fav = $school && $school->favicon ? asset('storage/'.$school->favicon) : null; @endphp
    @if($__fav)
    <link rel="icon" type="image/png" href="{{ $__fav }}">
    @else
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23059669'/><text x='16' y='22' font-size='18' font-weight='bold' text-anchor='middle' fill='white'>N</text></svg>">
    @endif
    @php
        $themeJson = App\Models\Setting::getValue('frontsite_theme');
        $theme = $themeJson ? json_decode($themeJson, true) : [];
        function hexToRgb($hex) { $hex = ltrim($hex, '#'); return strlen($hex) === 3 ? sscanf($hex, '%1x%1x%1x') : sscanf($hex, '%2x%2x%2x'); }
        $primary       = $theme['primary'] ?? '#059669';
        $primaryHover  = $theme['primary_hover'] ?? '#047857';
        $headerFrom    = $theme['header_from'] ?? '#059669';
        $headerTo      = $theme['header_to'] ?? '#0d9488';
        $footerBg      = $theme['footer_bg'] ?? '#111827';
        $accent        = $theme['accent'] ?? '#d97706';
        $pRgb = implode(',', hexToRgb($primary) ?: [5,150,105]);
        $hFRgb = implode(',', hexToRgb($headerFrom) ?: [5,150,105]);
        $hTRgb = implode(',', hexToRgb($headerTo) ?: [13,148,136]);
        $aRgb = implode(',', hexToRgb($accent) ?: [217,119,6]);
    @endphp
    <style>
        :root {
            --theme-primary: <?= $primary ?>;
            --theme-primary-rgb: <?= $pRgb ?>;
            --theme-primary-hover: <?= $primaryHover ?>;
            --theme-header-from: <?= $headerFrom ?>;
            --theme-header-from-rgb: <?= $hFRgb ?>;
            --theme-header-to: <?= $headerTo ?>;
            --theme-header-to-rgb: <?= $hTRgb ?>;
            --theme-footer-bg: <?= $footerBg ?>;
            --theme-accent: <?= $accent ?>;
            --theme-accent-rgb: <?= $aRgb ?>;
        }
        body { font-family: 'Anek Gujarati', sans-serif; }
        .lni { display: inline-flex; align-items: center; justify-content: center; line-height: 1; }
        .slider-btn { @apply absolute top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition cursor-pointer z-10; }
        .stat-number { font-size: 2.5rem; font-weight: 800; }
        .section-title { @apply text-2xl md:text-3xl font-bold text-gray-900 text-center mb-2; }
        .section-subtitle { @apply text-gray-500 text-center max-w-2xl mx-auto mb-10; }
        .nav-link { @apply text-gray-700 hover:text-[var(--theme-primary)] font-medium transition text-sm px-3 py-2 rounded-lg hover:bg-gray-100; }
        .btn-primary { background-color: var(--theme-primary); color: white; font-weight: 500; border-radius: 0.5rem; padding: 0.5rem 1.5rem; transition: all 0.2s; }
        .btn-primary:hover { background-color: var(--theme-primary-hover); }
        .bg-primary { background-color: var(--theme-primary); }
        .text-primary { color: var(--theme-primary); }
        .bg-primary-light { background-color: rgba(var(--theme-primary-rgb), 0.12); }
        .border-primary { border-color: rgba(var(--theme-primary-rgb), 0.3); }
        .bg-accent { background-color: var(--theme-accent); }
        .text-accent { color: var(--theme-accent); }
        .bg-accent-light { background-color: rgba(var(--theme-accent-rgb), 0.12); }
        .border-accent { border-color: rgba(var(--theme-accent-rgb), 0.3); }
        .text-white-70 { color: rgba(255,255,255,0.7); }
        .text-white-80 { color: rgba(255,255,255,0.8); }
        .stat-number-light { color: rgba(255,255,255,0.7); }
        .stat-label-light { color: rgba(255,255,255,0.85); }
        @media print { .no-print { display: none !important; } }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-gray-800 antialiased overflow-x-hidden">
    @include('frontsite.partials.header')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('frontsite.partials.footer')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var mobBtn = document.getElementById('mobile-menu-btn');
        var mobClose = document.getElementById('mobile-close-btn');
        var mobMenu = document.getElementById('mobile-menu');
        var mobOverlay = document.getElementById('mobile-overlay');

        function toggleMobile(open) {
            if (mobOverlay) { mobOverlay.classList.toggle('hidden', !open); setTimeout(function(){mobOverlay.classList.toggle('opacity-100', open);mobOverlay.classList.toggle('opacity-0', !open);}, 10); }
            if (mobMenu) { mobMenu.classList.toggle('translate-x-full', !open); mobMenu.classList.toggle('translate-x-0', open); }
            document.body.classList.toggle('overflow-hidden', open);
        }

        if (mobBtn && mobMenu) {
            mobBtn.addEventListener('click', function() {
                toggleMobile(mobMenu.classList.contains('translate-x-full'));
            });
        }
        if (mobClose && mobMenu) {
            mobClose.addEventListener('click', function() { toggleMobile(false); });
        }
        if (mobOverlay) {
            mobOverlay.addEventListener('click', function() { toggleMobile(false); });
        }
    });
    </script>
    @stack('scripts')
</body>
</html>
