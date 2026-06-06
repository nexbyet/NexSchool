{{-- NexSchool Header --}}
<header class="sticky top-0 z-20 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shadow-sm">
    {{-- Mobile sidebar toggle --}}
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
        <i class="lni lni-menu-hamburger-1 text-2xl"></i>
    </button>

    {{-- School name + breadcrumb --}}
    <div class="hidden sm:flex items-center gap-3 text-sm">
        @if($schoolSetting && $schoolSetting->school_name_gu)
        <div class="flex items-center gap-2">
            @if($schoolSetting->logo)
                <img src="{{ asset('storage/' . $schoolSetting->logo) }}" class="w-7 h-7 rounded-lg object-contain border border-gray-200">
            @else
                <div class="w-7 h-7 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-xs font-bold text-white">{{ substr($schoolSetting->school_name_gu, 0, 1) }}</span>
                </div>
            @endif
            <span class="font-semibold text-gray-800">{{ $schoolSetting->school_name_gu }}</span>
        </div>
        <span class="text-gray-300">|</span>
        @endif
        <span class="text-gray-500 font-medium">@yield('title', 'ડેશબોર્ડ')</span>
    </div>

    {{-- Right: user profile with dropdown --}}
    <div class="flex items-center gap-3 ml-auto" x-data="{ userMenu: false }">
        {{-- Active year badge --}}
        @if(isset($activeYear) && $activeYear)
        <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 rounded-lg text-xs font-medium text-indigo-700">
            <i class="lni lni-calendar-days text-xs"></i>
            {{ $activeYear->year }}
        </span>
        @endif

        {{-- User button --}}
        <div class="relative">
            <button @click="userMenu = !userMenu" @click.outside="userMenu = false" class="flex items-center gap-2.5 p-1.5 pr-3 rounded-xl hover:bg-gray-50 transition group">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow transition">
                    <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-400 capitalize leading-tight">{{ auth()->user()->role ?? 'admin' }}</p>
                </div>
                <i class="lni lni-chevron-down text-xs text-gray-400 group-hover:text-gray-600 transition" :class="userMenu ? 'rotate-180' : ''"></i>
            </button>

            {{-- Dropdown menu --}}
            <div x-show="userMenu" x-cloak @click.outside="userMenu = false" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5 z-50 origin-top-right" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="lni lni-user-4 text-base text-gray-400"></i>
                    પ્રોફાઇલ
                </a>
                <a href="{{ route('settings.school-info') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="lni lni-gear-1 text-base text-gray-400"></i>
                    શાળા માહિતી
                </a>
                <div class="border-t border-gray-100 mt-1 pt-1">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                        <i class="lni lni-exit text-base"></i>
                        લોગઆઉટ
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
