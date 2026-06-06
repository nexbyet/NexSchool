@php
    use App\Models\Menu;
    $headerMenu = Menu::with('activeItems.children')->where('location', 'header')->where('status', true)->first();
    $menuItems = $headerMenu ? $headerMenu->activeItems->whereNull('parent_id') : collect();
@endphp
<header id="site-header" class="sticky top-0 left-0 right-0 z-40 transition-all duration-300 no-print bg-white/90 backdrop-blur-md shadow-sm shadow-gray-200/50 border-b border-gray-100/50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-16 md:h-20">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                @if($school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="h-9 w-9 md:h-10 md:w-10 object-contain rounded-xl ring-2 ring-emerald-100">
                @else
                <div class="h-9 w-9 md:h-10 md:w-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-lg" style="background: linear-gradient(135deg, var(--theme-header-from), var(--theme-header-to));">N</div>
                @endif
                <div>
                    <h1 class="text-sm md:text-base font-bold text-gray-900 leading-tight group-hover:text-[var(--theme-primary)] transition-colors">{{ $school->school_name_gu ?? 'શાળા' }}</h1>
                    <p class="text-[10px] md:text-xs text-gray-400 leading-tight">{{ $school->address ?? '' }}</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <nav class="hidden lg:flex items-center gap-1">
                    @foreach ($menuItems as $item)
                    @php
                        $link = $item->page_id ? url('/' . ($item->page->slug ?? '')) : ($item->url ?: '#');
                        $hasChildren = $item->children->where('status', true)->isNotEmpty();
                    @endphp
                    <div class="relative group">
                        <a href="{{ $link }}" target="{{ $item->target }}" class="nav-link flex items-center gap-1 whitespace-nowrap px-3 py-2 text-gray-700 hover:text-[var(--theme-primary)] font-medium text-sm rounded-lg hover:bg-gray-100 transition">
                            {{ $item->title_gu }}
                            @if($hasChildren)<i class="lni lni-chevron-down text-[9px] mt-0.5"></i>@endif
                        </a>
                        @if($hasChildren)
                        <div class="absolute top-full left-0 mt-0.5 bg-white rounded-xl shadow-lg shadow-gray-200/60 border border-gray-100 py-2 min-w-[200px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            @foreach ($item->children->where('status', true) as $child)
                            @php $childLink = $child->page_id ? url('/' . ($child->page->slug ?? '')) : ($child->url ?: '#'); @endphp
                            <a href="{{ $childLink }}" target="{{ $child->target }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-[var(--theme-primary)] hover:bg-gray-100 transition">{{ $child->title_gu }}</a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </nav>

                <div class="hidden lg:flex items-center ml-2 pl-2 border-l border-gray-200">
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary flex items-center gap-2 px-3 py-2 text-sm shadow-sm">
                        <i class="lni lni-dashboard-square-1 text-sm"></i>
                        ડેશબોર્ડ
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn-primary flex items-center gap-2 px-4 py-2 text-sm shadow-lg">
                        <i class="lni lni-enter text-sm"></i>
                        લોગિન
                    </a>
                    @endauth
                </div>

                <button id="mobile-menu-btn" class="lg:hidden relative z-[60] p-2.5 text-gray-600 hover:text-[var(--theme-primary)] hover:bg-gray-100 rounded-xl transition">
                    <i id="hamburger-icon" class="lni lni-menu-hamburger-1 text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-300"></div>
    <div id="mobile-menu" class="fixed top-0 right-0 h-full w-72 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
        <div class="p-5 pt-20">
            @auth
            <div class="pb-4 mb-4 border-b border-gray-100">
                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
            </div>
            @endauth
            <div class="space-y-1">
                @foreach ($menuItems as $item)
                @php $link = $item->page_id ? url('/' . ($item->page->slug ?? '')) : ($item->url ?: '#'); @endphp
                <a href="{{ $link }}" target="{{ $item->target }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:text-[var(--theme-primary)] hover:bg-gray-100 rounded-lg transition font-medium">{{ $item->title_gu }}</a>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                @auth
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm btn-primary rounded-lg transition font-medium shadow-sm">
                    <i class="lni lni-dashboard-square-1 text-sm"></i>
                    ડેશબોર્ડ
                </a>
                @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm btn-primary rounded-lg transition font-medium shadow-sm">
                    <i class="lni lni-enter text-sm"></i>
                    લોગિન
                </a>
                @endauth
            </div>
        </div>
    </div>
</header>
