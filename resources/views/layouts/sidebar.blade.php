@php
$userRole = auth()->user()->role;
@endphp

{{-- NexSchool Sidebar Navigation --}}
<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 shadow-sm overflow-y-auto lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Logo + App Name + Close button (mobile only) --}}
    <div class="flex items-center justify-between h-16 px-5 border-b border-gray-200">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center shadow-sm">
                <span class="text-white font-bold text-base">N</span>
            </div>
            <div>
                <span class="font-bold text-base text-gray-900">NexSchool</span>
                <p class="text-[10px] text-gray-400 -mt-0.5">School Management</p>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
            <i class="lni lni-xmark text-xl"></i>
        </button>
    </div>

    <nav class="p-3 space-y-0.5">

@if($userRole === 'admin')
        {{-- ==================== ADMIN: FULL SIDEBAR ==================== --}}
        {{-- CORE --}}
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-home-2 text-xs"></i> મુખ્ય</div>

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('dashboard')) bg-indigo-50 text-indigo-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-dashboard-square-1 text-lg @if(request()->routeIs('dashboard')) text-indigo-600 @endif"></i></span>
            ડેશબોર્ડ
            @if(request()->routeIs('dashboard'))<span class="ml-auto w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>@endif
        </a>

        <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('students*') && !request()->routeIs('students.import*')) bg-blue-50 text-blue-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-user-multiple-4 text-lg @if(request()->routeIs('students*')) text-blue-600 @endif"></i></span>
            વિદ્યાર્થીઓ
        </a>

        <a href="{{ route('teachers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('teachers*')) bg-emerald-50 text-emerald-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-user-4 text-lg @if(request()->routeIs('teachers*')) text-emerald-600 @endif"></i></span>
            શિક્ષકો
        </a>

        <a href="{{ route('standards.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('standards*')) bg-purple-50 text-purple-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-buildings-1 text-lg @if(request()->routeIs('standards*')) text-purple-600 @endif"></i></span>
            ધોરણ અને વર્ગ
        </a>

        <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('subjects*')) bg-amber-50 text-amber-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-book-1 text-lg @if(request()->routeIs('subjects*')) text-amber-600 @endif"></i></span>
            વિષયો
        </a>

        <a href="{{ route('class-teachers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('class-teachers*')) bg-indigo-50 text-indigo-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-user-4 text-lg @if(request()->routeIs('class-teachers*')) text-indigo-600 @endif"></i></span>
            વર્ગશિક્ષક
        </a>

        <a href="{{ route('subject-assignments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('subject-assignments*')) bg-amber-50 text-amber-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-book-1 text-lg @if(request()->routeIs('subject-assignments*')) text-amber-600 @endif"></i></span>
            વિષય શિક્ષક
        </a>

        {{-- SCHEDULE --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-calendar-days text-xs"></i> સમયપત્રક</div>

        <a href="{{ route('academic-years.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('academic-years*')) bg-pink-50 text-pink-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-calendar-days text-lg @if(request()->routeIs('academic-years*')) text-pink-600 @endif"></i></span>
            શૈક્ષણિક વર્ષો
        </a>

        <a href="{{ route('timetable.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('timetable*')) bg-cyan-50 text-cyan-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-calendar-days text-lg @if(request()->routeIs('timetable*')) text-cyan-600 @endif"></i></span>
            ટાઇમટેબલ
        </a>

        <a href="{{ route('activity-plans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('activity-plans*')) bg-emerald-50 text-emerald-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-book-1 text-lg @if(request()->routeIs('activity-plans*')) text-emerald-600 @endif"></i></span>
            પ્રવૃત્તિઓનું આયોજન
        </a>

        <a href="{{ route('public-holidays.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('public-holidays*')) bg-red-50 text-red-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-calendar-days text-lg @if(request()->routeIs('public-holidays*')) text-red-600 @endif"></i></span>
            જાહેર રજાઓ
        </a>

        {{-- ATTENDANCE --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-clipboard text-xs"></i> હાજરી</div>
        <div x-data="{ open: @json(request()->routeIs('roll-number-sort*') || request()->routeIs('attendance-register*') || request()->routeIs('attendance*') || request()->routeIs('daily-stats*')) }">
            <button @click="open = !open" class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('roll-number-sort*') || request()->routeIs('attendance-register*') || request()->routeIs('attendance*') || request()->routeIs('daily-stats*')) bg-indigo-50 text-indigo-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-clipboard text-lg @if(request()->routeIs('roll-number-sort*') || request()->routeIs('attendance-register*') || request()->routeIs('attendance*') || request()->routeIs('daily-stats*')) text-indigo-600 @endif"></i></span>
                    હાજરી પત્રક
                </div>
                <i class="lni lni-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-0' : '-rotate-90'"></i>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 space-y-0.5">
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('attendance.index')) bg-indigo-50 text-indigo-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('attendance.index')) bg-indigo-500 @else bg-gray-300 @endif"></span>
                    દૈનિક હાજરી
                </a>
                <a href="{{ route('attendance-register.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('attendance-register*')) bg-indigo-50 text-indigo-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('attendance-register*')) bg-indigo-500 @else bg-gray-300 @endif"></span>
                    હાજરી પત્રક પ્રિન્ટ
                </a>
                <a href="{{ route('roll-number-sort.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('roll-number-sort*')) bg-indigo-50 text-indigo-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('roll-number-sort*')) bg-indigo-500 @else bg-gray-300 @endif"></span>
                    રોલ નંબર ગોઠવણી
                </a>
                <a href="{{ route('daily-stats.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('daily-stats*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('daily-stats*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    દૈનિક આંકડાબુક
                </a>
            </div>
        </div>

        {{-- FEES --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-wallet-1 text-xs"></i> ફી</div>
        <div x-data="{ open: @json(request()->routeIs('fees*')) }">
            <button @click="open = !open" class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees*')) bg-amber-50 text-amber-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-wallet-1 text-lg @if(request()->routeIs('fees*')) text-amber-600 @endif"></i></span>
                    ફી વ્યવસ્થાપન
                </div>
                <i class="lni lni-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-0' : '-rotate-90'"></i>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 space-y-0.5">
                <a href="{{ route('fees.heads.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.heads*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.heads*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    ફી હેડ્સ
                </a>
                <a href="{{ route('fees.structures.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.structures*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.structures*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    ફી માળખું
                </a>
                <a href="{{ route('fees.assignments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.assignments*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.assignments*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    ફી સોંપણી
                </a>
                <a href="{{ route('fees.carry-forward.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.carry-forward*')) bg-red-50 text-red-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.carry-forward*')) bg-red-500 @else bg-gray-300 @endif"></span>
                    જૂની ફી કેરી ફોરવર્ડ
                </a>
                <a href="{{ route('fees.collection.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.collection*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.collection*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    ફી વસૂલાત
                </a>
                <a href="{{ route('fees.reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.reports*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.reports*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    ફી રિપોર્ટ્સ
                </a>
                <a href="{{ route('fees.register.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('fees.register*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('fees.register*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    ફી રજિસ્ટર
                </a>
            </div>
        </div>

        {{-- TRANSPORT --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-truck-delivery-1 text-xs"></i> વાહન</div>
        <div x-data="{ open: @json(request()->routeIs('transport*')) }">
            <button @click="open = !open" class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport*')) bg-emerald-50 text-emerald-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-truck-delivery-1 text-lg @if(request()->routeIs('transport*')) text-emerald-600 @endif"></i></span>
                    વાહન વ્યવસ્થાપન
                </div>
                <i class="lni lni-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-0' : '-rotate-90'"></i>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 space-y-0.5">
                <a href="{{ route('transport.vehicles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport.vehicles*')) bg-emerald-50 text-emerald-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('transport.vehicles*')) bg-emerald-500 @else bg-gray-300 @endif"></span>
                    વાહનો
                </a>
                <a href="{{ route('transport.routes.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport.routes*')) bg-emerald-50 text-emerald-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('transport.routes*')) bg-emerald-500 @else bg-gray-300 @endif"></span>
                    રૂટ
                </a>
                <a href="{{ route('transport.student-route.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport.student-route*')) bg-emerald-50 text-emerald-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('transport.student-route*')) bg-emerald-500 @else bg-gray-300 @endif"></span>
                    વિદ્યાર્થી રૂટ સોંપણી
                </a>
                <a href="{{ route('transport.bus-attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport.bus-attendance*')) bg-emerald-50 text-emerald-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('transport.bus-attendance*')) bg-emerald-500 @else bg-gray-300 @endif"></span>
                    બસ હાજરી
                </a>
                <a href="{{ route('transport.bus-students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport.bus-students*')) bg-emerald-50 text-emerald-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('transport.bus-students*')) bg-emerald-500 @else bg-gray-300 @endif"></span>
                    બીજી શાળાના બસ વિદ્યાર્થીઓ
                </a>
                <a href="{{ route('transport.routes.timetable') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('transport.routes.timetable*')) bg-emerald-50 text-emerald-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-800 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('transport.routes.timetable*')) bg-emerald-500 @else bg-gray-300 @endif"></span>
                    રૂટ ટાઇમટેબલ
                </a>
            </div>
        </div>

        {{-- DOCUMENTS --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-certificate-badge-1 text-xs"></i> દસ્તાવેજો</div>

        <a href="{{ route('lc.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('lc*')) bg-slate-50 text-slate-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-exit text-lg @if(request()->routeIs('lc*')) text-slate-600 @endif"></i></span>
            LC — શાળા છોડવાનું પ્રમાણપત્ર
        </a>
        <a href="{{ route('certificates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('certificates*')) bg-teal-50 text-teal-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-certificate-badge-1 text-lg @if(request()->routeIs('certificates*')) text-teal-600 @endif"></i></span>
            બોનાફાઈડ પ્રમાણપત્ર
        </a>
        <a href="{{ route('custom-report.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('custom-report*')) bg-violet-50 text-violet-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-layers-1 text-lg @if(request()->routeIs('custom-report*')) text-violet-600 @endif"></i></span>
            કસ્ટમ રિપોર્ટ
        </a>

        {{-- WEBSITE --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-globe-1 text-xs"></i> વેબસાઇટ</div>
        <div x-data="{ open: @json(request()->routeIs('pages*') || request()->routeIs('menus*') || request()->routeIs('notice-board*') || request()->routeIs('sliders*') || request()->routeIs('galleries*') || request()->routeIs('homepage-sections*') || request()->routeIs('admission-inquiries*')) }">
            <button @click="open = !open" class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('pages*') || request()->routeIs('menus*') || request()->routeIs('notice-board*') || request()->routeIs('sliders*') || request()->routeIs('galleries*') || request()->routeIs('homepage-sections*') || request()->routeIs('admission-inquiries*')) bg-sky-50 text-sky-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-globe-1 text-lg @if(request()->routeIs('pages*') || request()->routeIs('menus*') || request()->routeIs('notice-board*') || request()->routeIs('sliders*') || request()->routeIs('galleries*') || request()->routeIs('homepage-sections*') || request()->routeIs('admission-inquiries*')) text-sky-600 @endif"></i></span>
                    વેબસાઇટ વ્યવસ્થાપન
                </div>
                <i class="lni lni-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-0' : '-rotate-90'"></i>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 space-y-0.5">
                <a href="{{ route('homepage-sections.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('homepage-sections*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('homepage-sections*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    હોમપેજ
                </a>
                <a href="{{ route('pages.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('pages*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('pages*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    પેજ
                </a>
                <a href="{{ route('menus.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('menus*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('menus*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    મેનુ
                </a>
                <a href="{{ route('notice-board.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('notice-board*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('notice-board*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    સૂચના બોર્ડ
                </a>
                <a href="{{ route('sliders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('sliders*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('sliders*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    સ્લાઇડર
                </a>
                <a href="{{ route('galleries.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('galleries*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('galleries*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    ગેલેરી
                </a>
                <a href="{{ route('admission-inquiries.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('admission-inquiries*')) bg-sky-50 text-sky-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('admission-inquiries*')) bg-sky-500 @else bg-gray-300 @endif"></span>
                    પ્રવેશ અરજીઓ
                </a>
                <hr class="my-1.5 border-gray-100">
                <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-emerald-600 hover:bg-emerald-50 transition">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <i class="lni lni-exit-up text-xs"></i> વેબસાઇટ જુઓ
                </a>
            </div>
        </div>

        {{-- SETTINGS --}}
        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-gear-1 text-xs"></i> સેટિંગ્સ</div>

        <a href="{{ route('settings.school-info') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings.school-info')) bg-gray-100 text-gray-800 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-gear-1 text-lg @if(request()->routeIs('settings.school-info')) text-gray-700 @endif"></i></span>
            શાળા માહિતી
        </a>
        <a href="{{ route('settings.site') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings.site')) bg-gray-100 text-gray-800 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-globe-1 text-lg @if(request()->routeIs('settings.site')) text-gray-700 @endif"></i></span>
            સાઇટ સેટિંગ્સ
        </a>
        <a href="{{ route('settings.dropdowns.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings.dropdowns*')) bg-gray-100 text-gray-800 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-check-square-2 text-lg @if(request()->routeIs('settings.dropdowns*')) text-gray-700 @endif"></i></span>
            ડ્રોપડાઉન વિકલ્પો
        </a>
        <a href="{{ route('settings.theme.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings.theme*')) bg-gray-100 text-gray-800 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-paint-roller-1 text-lg @if(request()->routeIs('settings.theme*')) text-gray-700 @endif"></i></span>
            ફ્રન્ટસાઇટ થીમ
        </a>
        <a href="{{ route('settings.license') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings.license*')) bg-violet-50 text-violet-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-shield-2 text-lg @if(request()->routeIs('settings.license*')) text-violet-600 @endif"></i></span>
            લાઇસન્સ
        </a>
        <a href="{{ route('settings.updates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings.updates*')) bg-emerald-50 text-emerald-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-cloud-2 text-lg @if(request()->routeIs('settings.updates*')) text-emerald-600 @endif"></i></span>
            અપડેટ્સ
        </a>

@elseif($userRole === 'teacher')
        {{-- ==================== TEACHER: LIMITED SIDEBAR ==================== --}}
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-home-2 text-xs"></i> મુખ્ય</div>

        <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('teacher.dashboard')) bg-emerald-50 text-emerald-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-dashboard-square-1 text-lg @if(request()->routeIs('teacher.dashboard')) text-emerald-600 @endif"></i></span>
            ડેશબોર્ડ
            @if(request()->routeIs('teacher.dashboard'))<span class="ml-auto w-1.5 h-1.5 bg-emerald-600 rounded-full"></span>@endif
        </a>

        <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('students*')) bg-blue-50 text-blue-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-user-multiple-4 text-lg @if(request()->routeIs('students*')) text-blue-600 @endif"></i></span>
            વિદ્યાર્થીઓ
        </a>

        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-calendar-days text-xs"></i> સમયપત્રક</div>

        <a href="{{ route('timetable.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('timetable*')) bg-cyan-50 text-cyan-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-calendar-days text-lg @if(request()->routeIs('timetable*')) text-cyan-600 @endif"></i></span>
            ટાઇમટેબલ
        </a>

        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-clipboard text-xs"></i> હાજરી</div>

        <div x-data="{ open: @json(request()->routeIs('roll-number-sort*') || request()->routeIs('attendance-register*') || request()->routeIs('attendance*') || request()->routeIs('daily-stats*')) }">
            <button @click="open = !open" class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('roll-number-sort*') || request()->routeIs('attendance-register*') || request()->routeIs('attendance*') || request()->routeIs('daily-stats*')) bg-indigo-50 text-indigo-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-5 h-5">
                        <i class="lni lni-clipboard text-lg @if(request()->routeIs('roll-number-sort*') || request()->routeIs('attendance-register*') || request()->routeIs('attendance*') || request()->routeIs('daily-stats*')) text-indigo-600 @endif"></i>
                    </span>
                    હાજરી પત્રક
                </div>
                <i class="lni lni-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-0' : '-rotate-90'"></i>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 space-y-0.5">
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('attendance.index')) bg-indigo-50 text-indigo-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('attendance.index')) bg-indigo-500 @else bg-gray-300 @endif"></span>
                    દૈનિક હાજરી
                </a>
                <a href="{{ route('attendance-register.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('attendance-register*')) bg-indigo-50 text-indigo-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('attendance-register*')) bg-indigo-500 @else bg-gray-300 @endif"></span>
                    હાજરી પત્રક પ્રિન્ટ
                </a>
                <a href="{{ route('roll-number-sort.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('roll-number-sort*')) bg-indigo-50 text-indigo-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('roll-number-sort*')) bg-indigo-500 @else bg-gray-300 @endif"></span>
                    રોલ નંબર ગોઠવણી
                </a>
                <a href="{{ route('daily-stats.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request()->routeIs('daily-stats*')) bg-amber-50 text-amber-700 @else text-gray-500 hover:bg-gray-100 hover:text-gray-700 @endif">
                    <span class="w-1.5 h-1.5 rounded-full @if(request()->routeIs('daily-stats*')) bg-amber-500 @else bg-gray-300 @endif"></span>
                    દૈનિક આંકડાબુક
                </a>
            </div>
        </div>

        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-user-4 text-xs"></i> એકાઉન્ટ</div>

        <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('profile*')) bg-gray-100 text-gray-800 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-user-4 text-lg @if(request()->routeIs('profile*')) text-gray-700 @endif"></i></span>
            પ્રોફાઇલ
        </a>

@elseif($userRole === 'student')
        {{-- ==================== STUDENT: MINIMAL SIDEBAR ==================== --}}
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-home-2 text-xs"></i> મુખ્ય</div>

        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('student.dashboard')) bg-cyan-50 text-cyan-700 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-dashboard-square-1 text-lg @if(request()->routeIs('student.dashboard')) text-cyan-600 @endif"></i></span>
            ડેશબોર્ડ
            @if(request()->routeIs('student.dashboard'))<span class="ml-auto w-1.5 h-1.5 bg-cyan-600 rounded-full"></span>@endif
        </a>

        <hr class="my-2 border-gray-100">
        <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1 flex items-center gap-1"><i class="lni lni-user-4 text-xs"></i> એકાઉન્ટ</div>

        <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('profile*')) bg-gray-100 text-gray-800 shadow-sm @else text-gray-600 hover:bg-gray-100 hover:text-gray-800 @endif">
            <span class="flex items-center justify-center w-5 h-5"><i class="lni lni-user-4 text-lg @if(request()->routeIs('profile*')) text-gray-700 @endif"></i></span>
            પ્રોફાઇલ
        </a>

@endif

        {{-- Logout (common to all roles) --}}
        <div class="pt-2 mt-2 border-t border-gray-100">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 text-red-600 hover:bg-red-50">
                <span class="flex items-center justify-center w-5 h-5">
                    <i class="lni lni-exit text-lg"></i>
                </span>
                લોગઆઉટ
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </nav>
</aside>
