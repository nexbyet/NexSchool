{{-- NexSchool Main Layout --}}
{{-- Blade + Tailwind CDN + Alpine.js — cPanel ready, no Node.js needed --}}
{{-- ગુજરાતી: મુખ્ય લેઆઉટ જેમાં sidebar, header અને main content નો સમાવેશ થાય છે --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NexSchool') }} - @yield('title', 'Dashboard')</title>
    @php $__site = App\Models\SchoolSetting::find(1); $__fav = $__site?->backend_favicon ?: $__site?->favicon; @endphp
    @if($__fav)
    <link rel="icon" type="image/png" href="{{ asset('storage/'.$__fav) }}">
    @else
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%234f46e5'/><text x='16' y='22' font-size='18' font-weight='bold' text-anchor='middle' fill='white'>N</text></svg>">
    @endif

    {{-- Tailwind CSS via CDN (no build step needed) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js for interactivity (sidebar toggle) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- LineIcons 5.1 Free CDN --}}
    <link rel="stylesheet" href="https://cdn.lineicons.com/5.1/line/lineicons.css">
    {{-- SortableJS for drag-and-drop reordering --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    {{-- Google Fonts: Anek Gujarati (primary) + Inter + Gabarito --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&family=Anek+Gujarati:wght@300;400;500;600;700;800&family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Anek Gujarati', 'Inter', 'Gabarito', sans-serif; }
        .lni { display: inline-flex; align-items: center; justify-content: center; line-height: 1; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    {{-- Alpine.js container for sidebar state management --}}
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
        @auth
            @include('layouts.sidebar')
        @endauth

        <div class="flex-1 flex flex-col lg:ml-64">
            @auth
                @include('layouts.header')
            @endauth

            <main class="flex-1 p-6">
                @yield('content')
            </main>
            @if($__site && ($__site->footer_credits || $__site->copyright_text))
            <footer class="px-6 py-3 border-t border-gray-200 bg-white text-center flex items-center justify-center gap-3">
                @if($__site->copyright_text)
                <span class="text-xs text-gray-500">{{ $__site->copyright_text }}</span>
                @endif
                @if($__site->footer_credits)
                <span class="text-xs text-gray-400">{{ $__site->footer_credits }}</span>
                @endif
                <span class="text-xs text-gray-300">v{{ $__site->app_version ?? config('app.version', '1.0.0') }}</span>
            </footer>
            @endif
            {{-- Global Components: Alert Toast + Confirm Dialog --}}
            @include('components.alert')
            @include('components.confirm')
        </div>
    </div>

    @stack('styles')

    {{-- NexSchool Global Alert & Confirm (Pure JS, no Alpine dependency) --}}
    <script>
        window.NexSchool = window.NexSchool || {};
        NexSchool.alert = {
            container: null,
            init() { this.container = document.getElementById('nexschool-alert-container'); },
            show(type, message, duration = 4000) {
                if (!this.container) this.init();
                const icons = {success:'check-circle-1',danger:'xmark-circle',warning:'ban-2',info:'question-mark-circle',note:'bell-1'};
                const styles = {
                    success:{grad:'from-emerald-500 to-emerald-600',ring:'ring-emerald-200',text:'text-emerald-800'},
                    danger:{grad:'from-red-500 to-red-600',ring:'ring-red-200',text:'text-red-800'},
                    warning:{grad:'from-amber-500 to-amber-600',ring:'ring-amber-200',text:'text-amber-800'},
                    info:{grad:'from-blue-500 to-blue-600',ring:'ring-blue-200',text:'text-blue-800'},
                    note:{grad:'from-gray-500 to-gray-600',ring:'ring-gray-200',text:'text-gray-800'}
                };
                const s = styles[type] || styles.note;
                const el = document.createElement('div');
                el.className = 'pointer-events-auto flex items-start gap-3 p-4 rounded-xl border border-gray-200 bg-white shadow-xl cursor-pointer translate-x-full opacity-0 transition-all duration-300 hover:shadow-2xl';
                el.innerHTML = '<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br '+s.grad+' flex items-center justify-center shadow-sm"><i class="lni lni-'+ (icons[type]||'note') +' text-sm text-white"></i></div><p class="text-sm font-medium '+s.text+' flex-1 mt-1">'+message+'</p><button class="flex-shrink-0 text-gray-400 hover:text-gray-600 p-0.5"><i class="lni lni-xmark text-base"></i></button>';
                el.querySelector('button').onclick = () => this.dismiss(el);
                el.onclick = () => this.dismiss(el);
                this.container.appendChild(el);
                requestAnimationFrame(() => { el.classList.remove('translate-x-full','opacity-0'); });
                if (duration > 0) setTimeout(() => this.dismiss(el), duration);
            },
            dismiss(el) { if (!el||el.dataset.dismissing) return; el.dataset.dismissing='true'; el.classList.add('translate-x-full','opacity-0'); setTimeout(() => el.remove(), 300); },
            success(m,d) { this.show('success',m,d); }, danger(m,d) { this.show('danger',m,d); }, warning(m,d) { this.show('warning',m,d); }, info(m,d) { this.show('info',m,d); }, note(m,d) { this.show('note',m,d); },
        };
        NexSchool.confirm = {
            show(title, message, type = 'danger', confirmText = '') {
                return new Promise((resolve, reject) => {
                    const o = document.createElement('div');
                    o.className = 'fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4';
                    o.style.opacity = '0'; o.style.transition = 'opacity 0.2s ease-out';
                    const icons = {danger:'ban-2',primary:'question-mark-circle'};
                    const grades = {danger:'from-red-500 to-red-600',primary:'from-indigo-500 to-indigo-600'};
                    const ibg = {danger:'bg-red-100',primary:'bg-indigo-100'};
                    const ic = {danger:'text-red-600',primary:'text-indigo-600'};
                    const g = grades[type]||grades.danger;
                    const ib = ibg[type]||ibg.danger;
                    const icn = ic[type]||ic.danger;
                    const cl = confirmText||'હા, ખાતરી કરો';
                    o.innerHTML = '<div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 scale-95 opacity-0 transition-all duration-200"><div class="flex items-center gap-4 mb-4"><div class="w-12 h-12 '+ib+' rounded-full flex items-center justify-center flex-shrink-0"><i class="lni lni-'+(icons[type]||'warning')+' text-2xl leading-none '+icn+'"></i></div><div><h3 class="text-lg font-semibold text-gray-900">'+title+'</h3><p class="text-sm text-gray-500 mt-1">'+message+'</p></div></div><div class="flex items-center justify-end gap-3 mt-6"><button class="cancel-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button><button class="confirm-btn px-4 py-2 text-sm font-medium text-white rounded-lg focus:ring-4 focus:ring-indigo-200 transition bg-gradient-to-r '+g+' shadow-sm hover:shadow-md">'+cl+'</button></div></div>';
                    document.body.appendChild(o);
                    requestAnimationFrame(() => { o.style.opacity='1'; o.querySelector('div>div').style.transform='scale(1)'; o.querySelector('div>div').style.opacity='1'; });
                    const close = () => { o.style.opacity='0'; o.querySelector('div>div').style.transform='scale(95%)'; o.querySelector('div>div').style.opacity='0'; setTimeout(() => o.remove(), 200); };
                    o.querySelector('.cancel-btn').onclick = () => { close(); resolve(false); };
                    o.querySelector('.confirm-btn').onclick = () => { close(); resolve(true); };
                    o.onclick = (e) => { if (e.target===o) { close(); resolve(false); } };
                });
            },
        };
        document.addEventListener('DOMContentLoaded', () => { NexSchool.alert.init(); });
    </script>

    @stack('scripts')
</body>
</html>
