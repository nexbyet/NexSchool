@extends('layouts.app')

@section('title', 'વિદ્યાર્થીઓ')

@push('styles')
<style>
.modal-fieldset { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; }
.modal-fieldset legend { font-size: 0.875rem; font-weight: 600; color: #4f46e5; padding: 0 0.5rem; }
.dual-lang { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.auto-field input { background-color: #f3f4f6; }
.btn-leave { background-color: #f59e0b; }
.btn-leave:hover { background-color: #d97706; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">
    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">વિદ્યાર્થીઓ</h1>
                <p class="text-indigo-200 mt-1 text-sm">શાળાના તમામ વિદ્યાર્થીઓની માહિતી અને સંચાલન</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('students.import.view') }}" class="px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition flex items-center gap-2 backdrop-blur-sm" title="Excel થી જથ્થાબંધ વિદ્યાર્થી ઉમેરો">
                    <i class="lni lni-upload-1 text-base"></i> Import
                </a>
                <button onclick="openModal()" class="px-4 py-2 bg-white text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-50 transition flex items-center gap-2 shadow-lg">
                    <i class="lni lni-plus text-base"></i> નવો વિદ્યાર્થી
                </button>
            </div>
        </div>
        {{-- Decorative circles --}}
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-user-multiple-4 text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ સક્રિય</p>
                <p class="text-xl font-bold text-gray-900" id="stat-total">{{ $totalActive }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-user-4 text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુમાર</p>
                <p class="text-xl font-bold text-emerald-700" id="stat-boys">{{ $totalBoys }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-user-4 text-red-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુમારી</p>
                <p class="text-xl font-bold text-red-600" id="stat-girls">{{ $totalGirls }}</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-wrap items-center gap-3 shadow-sm">
        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative">
                <i class="lni lni-calendar-days absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <select id="filter-standard" class="pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white appearance-none cursor-pointer">
                    <option value="">બધા ધોરણ</option>
                    @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <i class="lni lni-buildings-1 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <select id="filter-class" class="pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white appearance-none cursor-pointer">
                    <option value="">બધા વર્ગ</option>
                </select>
            </div>
        </div>
        <div class="flex-1 min-w-[200px] relative">
            <i class="lni lni-search-1 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input type="text" id="filter-search" placeholder="GR નંબર, નામ, પિતાનું નામ, મોબાઇલ..." class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <button onclick="applyFilters()" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 shadow-sm">
            <i class="lni lni-search-1 text-sm"></i> શોધો
        </button>
    </div>

    {{--學生 Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm relative" id="student-table-wrap">
        <div id="table-preloader" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10 hidden">
            <div class="flex items-center gap-3 px-5 py-3 bg-white rounded-xl shadow-lg border border-gray-100">
                <i class="lni lni-spinner-3 text-indigo-600 text-xl spin"></i>
                <span class="text-sm font-medium text-gray-600">લોડ થાય છે...</span>
            </div>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white text-xs uppercase tracking-wider">
                    <th class="text-left px-4 py-3.5 font-semibold">GR નં.</th>
                    <th class="text-left px-4 py-3.5 font-semibold">ફોટો</th>
                    <th class="text-left px-4 py-3.5 font-semibold">નામ</th>
                    <th class="text-left px-4 py-3.5 font-semibold">પિતાનું નામ</th>
                    <th class="text-left px-4 py-3.5 font-semibold">જન્મ તારીખ</th>
                    <th class="text-left px-4 py-3.5 font-semibold">ઉંમર</th>
                    <th class="text-left px-4 py-3.5 font-semibold">હાલનું ધોરણ</th>
                    <th class="text-left px-4 py-3.5 font-semibold">મોબાઇલ</th>
                    <th class="text-left px-4 py-3.5 font-semibold">સ્થિતિ</th>
                    <th class="text-center px-4 py-3.5 font-semibold">ક્રિયા</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="student-table-body">
                @forelse ($students as $s)
                <tr class="hover:bg-gray-50 transition group" @if($s->sharirik_jaati === 'kumar') style="border-left: 4px solid #10b981;" @elseif($s->sharirik_jaati === 'kumari') style="border-left: 4px solid #f87171;" @endif>
                    <td class="px-4 py-3 font-mono font-medium text-gray-900">{{ $s->gr_number }}</td>
                    <td class="px-4 py-3">
                        @if($s->photo)
                            <img src="{{ asset('storage/' . $s->photo) }}" class="w-9 h-9 rounded-full object-cover border-2 border-gray-200 shadow-sm" alt="photo">
                        @else
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                <i class="lni lni-user-4 text-gray-400 text-xs"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $s->full_name_gu }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $s->father_name_gu }}</td>
                    <td class="px-4 py-3 font-mono text-gray-600">{{ $s->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->format('d/m/Y') : '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-md text-xs font-medium text-gray-600">
                            <i class="lni lni-calendar-days text-xs"></i>
                            {{ $s->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->age . ' વર્ષ' : '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 rounded-md text-xs font-medium text-indigo-700">
                            <i class="lni lni-buildings-1 text-xs"></i>
                            {{ $s->currentStandard?->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-600">{{ $s->mobile }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusClass = ['active' => 'bg-emerald-100 text-emerald-700', 'inactive' => 'bg-gray-100 text-gray-600', 'alumni' => 'bg-amber-100 text-amber-700'];
                            $statusLabel = ['active' => 'સક્રિય', 'inactive' => 'નિષ્ક્રિય', 'alumni' => 'ભૂતપૂર્વ'];
                            $statusIcon = ['active' => 'lni-check-circle-1', 'inactive' => 'lni-ban-2', 'alumni' => 'lni-exit'];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass[$s->status] ?? 'bg-gray-100 text-gray-600' }}">
                            <i class="{{ $statusIcon[$s->status] ?? 'lni-ban-2' }} text-xs"></i>
                            {{ $statusLabel[$s->status] ?? $s->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1 opacity-70 group-hover:opacity-100 transition">
                            <a href="{{ url('students') }}/{{ $s->id }}" class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-lg transition" title="પ્રોફાઇલ જુઓ"><i class="lni lni-eye"></i></a>
                            <button onclick="editStudent({{ $s->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1"></i></button>
                            @if(auth()->user()->role === 'admin')
                            <button onclick="deleteStudent({{ $s->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો"><i class="lni lni-trash-3"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl flex items-center justify-center shadow-sm">
                            <i class="lni lni-user-multiple-4 text-3xl text-indigo-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">હજી કોઈ વિદ્યાર્થી નથી</p>
                        <p class="text-gray-400 text-sm mt-1">પ્રથમ વિદ્યાર્થી ઉમેરવા માટે નીચેનું બટન દબાવો</p>
                        <button onclick="openModal()" class="mt-4 px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">નવો વિદ્યાર્થી ઉમેરો</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-5 mb-2 flex flex-col sm:flex-row items-center justify-between gap-3" id="pagination-wrap">
        <p class="text-sm text-gray-500" id="pagination-info">
            કુલ <strong id="pagination-total">{{ $students->total() }}</strong> માંથી
            @if($students->total() > 0) <strong>{{ $students->firstItem() }}</strong> થી <strong>{{ $students->lastItem() }}</strong>@else—@endif બતાવ્યા
        </p>
        <div class="flex items-center gap-1 flex-wrap" id="pagination-links">
            @if ($students->lastPage() > 1)
                @php
                    $maxVisible = 7;
                    $half = floor($maxVisible / 2);
                    $start = max(1, $students->currentPage() - $half);
                    $end = min($students->lastPage(), $start + $maxVisible - 1);
                    if ($end - $start + 1 < $maxVisible) $start = max(1, $end - $maxVisible + 1);
                @endphp
                <button onclick="goToPage({{ $students->currentPage() - 1 }})" class="px-3 py-1.5 text-sm rounded-lg transition font-medium @if($students->currentPage() <= 1) bg-gray-50 text-gray-300 cursor-not-allowed @else bg-gray-100 text-gray-600 hover:bg-gray-200 @endif" @if($students->currentPage() <= 1) disabled @endif><i class="lni lni-chevron-left text-xs"></i></button>
                @if($start > 1)
                    <button onclick="goToPage(1)" class="px-3 py-1.5 text-sm rounded-lg transition font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">1</button>
                    @if($start > 2)<span class="px-1 text-gray-400 text-xs">...</span>@endif
                @endif
                @for ($i = $start; $i <= $end; $i++)
                    <button onclick="goToPage({{ $i }})" class="px-3 py-1.5 text-sm rounded-lg transition font-medium @if($i == $students->currentPage()) bg-indigo-600 text-white shadow-sm @else bg-gray-100 text-gray-600 hover:bg-gray-200 @endif">{{ $i }}</button>
                @endfor
                @if($end < $students->lastPage())
                    @if($end < $students->lastPage() - 1)<span class="px-1 text-gray-400 text-xs">...</span>@endif
                    <button onclick="goToPage({{ $students->lastPage() }})" class="px-3 py-1.5 text-sm rounded-lg transition font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">{{ $students->lastPage() }}</button>
                @endif
                <button onclick="goToPage({{ $students->currentPage() + 1 }})" class="px-3 py-1.5 text-sm rounded-lg transition font-medium @if($students->currentPage() >= $students->lastPage()) bg-gray-50 text-gray-300 cursor-not-allowed @else bg-gray-100 text-gray-600 hover:bg-gray-200 @endif" @if($students->currentPage() >= $students->lastPage()) disabled @endif><i class="lni lni-chevron-right text-xs"></i></button>
            @endif
        </div>
    </div>

{{-- Create/Edit Modal --}}
<div id="student-modal" class="fixed inset-0 z-[9998] flex items-start justify-center bg-black/40 backdrop-blur-sm p-4 hidden" style="opacity:0;transition:opacity 0.2s;overflow-y:auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full my-8 p-6">
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 -mx-6 -mt-6 rounded-t-2xl mb-4">
            <h3 class="text-lg font-semibold text-white" id="modal-title">નવો વિદ્યાર્થી ઉમેરો</h3>
            <button type="button" onclick="closeModal()" class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition"><i class="lni lni-xmark text-xl"></i></button>
        </div>
        <form id="student-form">
            <input type="hidden" id="student-id">
            <div class="max-h-[70vh] overflow-y-auto pr-2 space-y-1">

                {{-- Section 1: GR & Standards --}}
                <fieldset class="modal-fieldset">
                    <legend>પ્રવેશ માહિતી</legend>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">GR નંબર <span class="text-red-500">*</span></label>
                            <input type="text" id="gr_number" name="gr_number" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">પ્રવેશ ધોરણ <span class="text-red-500">*</span></label>
                            <select id="admission_standard_id" name="admission_standard_id" required onchange="filterClasses('admission')" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">-- પસંદ કરો --</option>
                                @foreach ($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">પ્રવેશ વર્ગ</label>
                            <select id="admission_class_id" name="admission_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">-- પસંદ કરો --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">પ્રવેશ તારીખ <span class="text-red-500">*</span></label>
                            <input type="text" id="date_of_admission" name="date_of_admission" placeholder="dd/mm/yyyy" required class="date-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">હાલનું ધોરણ <span class="text-red-500">*</span></label>
                            <select id="current_standard_id" name="current_standard_id" required onchange="filterClasses('current')" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">-- પસંદ કરો --</option>
                                @foreach ($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">હાલનો વર્ગ</label>
                            <select id="current_class_id" name="current_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">-- પસંદ કરો --</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                {{-- Section 2: Student Name (Dual Language) --}}
                <fieldset class="modal-fieldset">
                    <legend>વિદ્યાર્થીનું નામ</legend>
                    <div class="space-y-3">
                        <div class="dual-lang">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                                <input type="text" id="student_name_gu" name="student_name_gu" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">નામ (English) <span class="text-red-500">*</span></label>
                                <input type="text" id="student_name_en" name="student_name_en" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div class="dual-lang">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">પિતાનું નામ (ગુજરાતી) <span class="text-red-500">*</span></label>
                                <input type="text" id="father_name_gu" name="father_name_gu" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">પિતાનું નામ (English) <span class="text-red-500">*</span></label>
                                <input type="text" id="father_name_en" name="father_name_en" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div class="dual-lang">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">અટક (ગુજરાતી) <span class="text-red-500">*</span></label>
                                <input type="text" id="surname_gu" name="surname_gu" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">અટક (English) <span class="text-red-500">*</span></label>
                                <input type="text" id="surname_en" name="surname_en" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div class="dual-lang auto-field">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">પૂરું નામ (ગુજરાતી)</label>
                                <input type="text" id="full_name_gu" name="full_name_gu" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none transition cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">પૂરું નામ (English)</label>
                                <input type="text" id="full_name_en" name="full_name_en" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none transition cursor-not-allowed">
                            </div>
                        </div>
                    </div>
                </fieldset>

                {{-- Section 3: Mother --}}
                <fieldset class="modal-fieldset">
                    <legend>માતાનું નામ</legend>
                    <div class="dual-lang">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">માતાનું નામ (ગુજરાતી)</label>
                            <input type="text" id="mother_name_gu" name="mother_name_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">માતાનું નામ (English)</label>
                            <input type="text" id="mother_name_en" name="mother_name_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                    </div>
                </fieldset>

                {{-- Section 4: DOB --}}
                <fieldset class="modal-fieldset">
                    <legend>જન્મ તારીખ</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ તારીખ <span class="text-red-500">*</span></label>
                            <input type="text" id="date_of_birth" name="date_of_birth" placeholder="dd/mm/yyyy" required class="date-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                        <div class="auto-field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ તારીખ અક્ષરમાં (ગુજરાતી)</label>
                            <input type="text" id="dob_in_text_gu" name="dob_in_text_gu" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none transition cursor-not-allowed">
                        </div>
                        <div class="auto-field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ તારીખ અક્ષરમાં (English)</label>
                            <input type="text" id="dob_in_text_en" name="dob_in_text_en" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none transition cursor-not-allowed">
                        </div>
                    </div>
                </fieldset>

                {{-- Section 5: Birth & Native Place --}}
                <fieldset class="modal-fieldset">
                    <legend>જન્મ અને વતન</legend>
                    <div class="space-y-3">
                        <div class="dual-lang">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ સ્થળ (ગુજરાતી)</label>
                                <input type="text" id="birth_place_gu" name="birth_place_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ સ્થળ (English)</label>
                                <input type="text" id="birth_place_en" name="birth_place_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div class="dual-lang">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">વતન (ગુજરાતી)</label>
                                <input type="text" id="native_place_gu" name="native_place_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">વતન (English)</label>
                                <input type="text" id="native_place_en" name="native_place_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                </fieldset>

                {{-- Section 6: Religion, Caste, Category --}}
                <fieldset class="modal-fieldset">
                    <legend>ધર્મ, જ્ઞાતિ, કેટેગરી</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="dual-lang" style="grid-template-columns:1fr 1fr">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ધર્મ (ગુજરાતી)</label>
                                <select id="religion_gu" name="religion_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                    <option value="">-- પસંદ કરો --</option>
                                    <option value="હિન્દુ">હિન્દુ</option>
                                    <option value="મુસ્લિમ">મુસ્લિમ</option>
                                    <option value="શીખ">શીખ</option>
                                    <option value="બૌદ્ધ">બૌદ્ધ</option>
                                    <option value="ઈસાઈ">ઈસાઈ</option>
                                    <option value="પારસી">પારસી</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ધર્મ (English)</label>
                                <select id="religion_en" name="religion_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                    <option value="">-- Select --</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Muslim">Muslim</option>
                                    <option value="Sikh">Sikh</option>
                                    <option value="Buddhist">Buddhist</option>
                                    <option value="Christian">Christian</option>
                                    <option value="Parsi">Parsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="dual-lang" style="grid-template-columns:1fr 1fr">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">જ્ઞાતિ (ગુજરાતી)</label>
                                <input type="text" id="cast_gu" name="cast_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">જ્ઞાતિ (English)</label>
                                <input type="text" id="cast_en" name="cast_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <div class="dual-lang col-span-2" style="grid-template-columns:1fr 1fr">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">કેટેગરી (ગુજરાતી)</label>
                                <select id="category_gu" name="category_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                    <option value="">-- પસંદ કરો --</option>
                                    <option value="સામાન્ય">સામાન્ય</option>
                                    <option value="અનુસુચિત જાતિ">અનુસુચિત જાતિ</option>
                                    <option value="અનુસુચિત જન જાતિ">અનુસુચિત જન જાતિ</option>
                                    <option value="બક્ષીપંચ">બક્ષીપંચ</option>
                                    <option value="આર્થિક પછાત">આર્થિક પછાત</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">કેટેગરી (English)</label>
                                <select id="category_en" name="category_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                    <option value="">-- Select --</option>
                                    <option value="General">General</option>
                                    <option value="SC">SC</option>
                                    <option value="ST">ST</option>
                                    <option value="OBC">OBC</option>
                                    <option value="EWS">EWS</option>
                                </select>
                            </div>
                        </div>
                        <div id="minority-group" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">બક્ષીપંચ પૈકી લઘુમતી?</label>
                            <select id="is_minority" name="is_minority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="0">ના (No)</option>
                                <option value="1">હા (Yes)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">શારીરિક જાતિ</label>
                            <select id="sharirik_jaati" name="sharirik_jaati" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">-- પસંદ કરો --</option>
                                <option value="kumar">કુમાર (Boy)</option>
                                <option value="kumari">કુમારી (Girl)</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                {{-- Section 7: Last School & RTE --}}
                <fieldset class="modal-fieldset">
                    <legend>શાળા માહિતી</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="dual-lang md:col-span-2" style="grid-template-columns:1fr 1fr">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">છેલ્લી શાળા (ગુજરાતી)</label>
                                <input type="text" id="last_school_gu" name="last_school_gu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">છેલ્લી શાળા (English)</label>
                                <input type="text" id="last_school_en" name="last_school_en" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RTE હેઠળ પ્રવેશ?</label>
                            <select id="admission_under_rte" name="admission_under_rte" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="0">ના (No)</option>
                                <option value="1">હા (Yes)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">અગાઉની શાળાના હાજરી દિવસો</label>
                            <input type="number" id="previous_attendance_days" name="previous_attendance_days" min="0" max="365" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            <p class="text-xs text-gray-400 mt-1">બીજી શાળામાંથી આવેલ હોય તો જ ભરો</p>
                        </div>
                    </div>
                </fieldset>

                {{-- Section 7b: Photo --}}
                <fieldset class="modal-fieldset">
                    <legend>ફોટો</legend>
                    <div class="flex items-center gap-4">
                        <img id="photo-preview" class="w-16 h-16 rounded-full object-cover border border-gray-200 hidden flex-shrink-0">
                        <div class="flex-1">
                            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>
                </fieldset>

                {{-- Section 8: Contact --}}
                <fieldset class="modal-fieldset">
                    <legend>સંપર્ક માહિતી</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">મોબાઇલ નંબર (10 આંકડા)</label>
                            <input type="text" id="mobile" name="mobile" maxlength="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp નંબર (10 આંકડા)</label>
                            <input type="text" id="whatsapp" name="whatsapp" maxlength="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                    </div>
                </fieldset>

                {{-- Section 9: Documents --}}
                <fieldset class="modal-fieldset">
                    <legend>દસ્તાવેજો</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">APAAR ID (12 આંકડા)</label>
                            <input type="text" id="apaar_id" name="apaar_id" maxlength="12" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">UID નંબર (18 આંકડા)</label>
                            <input type="text" id="uid_no" name="uid_no" maxlength="18" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PEN નંબર (11 આંકડા)</label>
                            <input type="text" id="pen_no" name="pen_no" maxlength="11" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">આધાર નંબર (12 આંકડા)</label>
                            <input type="text" id="aadhar_no" name="aadhar_no" maxlength="12" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">આધાર પ્રમાણે નામ</label>
                            <input type="text" id="name_as_per_aadhar" name="name_as_per_aadhar" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">રેશન કાર્ડ નંબર</label>
                            <input type="text" id="ration_card_no" name="ration_card_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                    </div>
                </fieldset>

                {{-- Section 10: Bank Details --}}
                <fieldset class="modal-fieldset">
                    <legend>બેંક વિગત</legend>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">બેંકનું નામ</label>
                            <input type="text" id="bank_name" name="bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">શાખા</label>
                            <input type="text" id="bank_branch" name="bank_branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IFSC કોડ</label>
                            <input type="text" id="bank_ifsc" name="bank_ifsc" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ખાતા નંબર</label>
                            <input type="text" id="bank_account_no" name="bank_account_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">બેંક પ્રમાણે નામ</label>
                            <input type="text" id="name_as_per_bank" name="name_as_per_bank" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                    </div>
                </fieldset>

            </div>
            <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રદ કરો</button>
                <button type="submit" id="submit-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:ring-indigo-200 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Class data for dynamic filtering (grouped by standard_id)
const allClasses = @json($classes->map(fn($c) => ['id' => $c->id, 'standard_id' => $c->standard_id, 'name' => $c->name])->values());
const classesByStandard = {};
allClasses.forEach(function(c) {
    if (!classesByStandard[c.standard_id]) classesByStandard[c.standard_id] = [];
    classesByStandard[c.standard_id].push(c);
});

function filterClasses(prefix) {
    const stdId = document.getElementById(prefix + '_standard_id').value;
    const clsSelect = document.getElementById(prefix + '_class_id');
    clsSelect.innerHTML = '<option value="">-- પસંદ કરો --</option>';
    if (stdId && classesByStandard[stdId]) {
        classesByStandard[stdId].forEach(function(c) {
            clsSelect.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>';
        });
    }
}
const modal = document.getElementById('student-modal');
const form = document.getElementById('student-form');
const studentId = document.getElementById('student-id');
const submitBtn = document.getElementById('submit-btn');
const modalTitle = document.getElementById('modal-title');

function openModal(data = null) {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => modal.style.opacity = '1');
    modalTitle.textContent = data ? 'વિદ્યાર્થી માહિતી સુધારો' : 'નવો વિદ્યાર્થી ઉમેરો';
    submitBtn.textContent = data ? 'સુધારો' : 'સાચવો';
    if (!data) {
        form.reset();
        studentId.value = '';
        document.getElementById('photo-preview').classList.add('hidden');
        const lastDate = localStorage.getItem('last_admission_date');
        if (lastDate) document.getElementById('date_of_admission').value = lastDate;
    }
}

function closeModal() {
    modal.style.opacity = '0';
    setTimeout(() => modal.classList.add('hidden'), 200);
}

modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

// Auto-generate full name
function updateFullName() {
    const nameGu = document.getElementById('student_name_gu').value.trim();
    const fatherGu = document.getElementById('father_name_gu').value.trim();
    const surGu = document.getElementById('surname_gu').value.trim();
    const nameEn = document.getElementById('student_name_en').value.trim();
    const fatherEn = document.getElementById('father_name_en').value.trim();
    const surEn = document.getElementById('surname_en').value.trim();
    document.getElementById('full_name_gu').value = [nameGu, fatherGu, surGu].filter(Boolean).join(' ');
    document.getElementById('full_name_en').value = [nameEn, fatherEn, surEn].filter(Boolean).join(' ');
}
['student_name_gu','student_name_en','father_name_gu','father_name_en','surname_gu','surname_en'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateFullName);
});

// Auto-generate DOB in text — ordinal day + month name + year in words
// Gujarati ordinal days
const guOrdinal = ['','પહેલી','બીજી','ત્રીજી','ચોથી','પાંચમી','છઠ્ઠી','સાતમી','આઠમી','નવમી','દસમી','અગિયારમી','બારમી','તેરમી','ચૌદમી','પંદરમી','સોળમી','સત્તરમી','અઢારમી','ઓગણીસમી','વીસમી','એકવીસમી','બાવીસમી','ત્રેવીસમી','ચોવીસમી','પચ્ચીસમી','છવ્વીસમી','સત્તાવીસમી','અઠ્ઠાવીસમી','ઓગણત્રીસમી','ત્રીસમી','એકત્રીસમી'];
const guMonths = ['જાન્યુઆરી','ફેબ્રુઆરી','માર્ચ','એપ્રિલ','મે','જૂન','જુલાઈ','ઓગસ્ટ','સપ્ટેમ્બર','ઓક્ટોબર','નવેમ્બર','ડિસેમ્બર'];
const enOrdinal = ['','FIRST','SECOND','THIRD','FOURTH','FIFTH','SIXTH','SEVENTH','EIGHTH','NINTH','TENTH','ELEVENTH','TWELFTH','THIRTEENTH','FOURTEENTH','FIFTEENTH','SIXTEENTH','SEVENTEENTH','EIGHTEENTH','NINETEENTH','TWENTIETH','TWENTY-FIRST','TWENTY-SECOND','TWENTY-THIRD','TWENTY-FOURTH','TWENTY-FIFTH','TWENTY-SIXTH','TWENTY-SEVENTH','TWENTY-EIGHTH','TWENTY-NINTH','THIRTIETH','THIRTY-FIRST'];
const enMonths = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];

// Gujarati numbers 0-99
const guNums = ['','એક','બે','ત્રણ','ચાર','પાંચ','છ','સાત','આઠ','નવ','દસ','અગિયાર','બાર','તેર','ચૌદ','પંદર','સોળ','સત્તર','અઢાર','ઓગણીસ','વીસ','એકવીસ','બાવીસ','ત્રેવીસ','ચોવીસ','પચ્ચીસ','છવ્વીસ','સત્તાવીસ','અઠ્ઠાવીસ','ઓગણત્રીસ','ત્રીસ','એકત્રીસ','બત્રીસ','તેત્રીસ','ચોત્રીસ','પાંત્રીસ','છત્રીસ','સાડત્રીસ','અડત્રીસ','ઓગણચાળીસ','ચાળીસ','એકતાળીસ','બેતાળીસ','તેતાળીસ','ચુંમાળીસ','પિસ્તાળીસ','છેતાળીસ','સુડતાળીસ','અડતાળીસ','ઓગણપચાસ','પચાસ','એકાવન','બાવન','ત્રેપન','ચોપન','પંચાવન','છપ્પન','સત્તાવન','અઠ્ઠાવન','ઓગણસાઠ','સાઠ','એકસઠ','બાસઠ','ત્રેસઠ','ચોસઠ','પાંસઠ','છસઠ','સડસઠ','અડસઠ','ઓગણોસિત્તેર','સિત્તેર','એકોતેર','બોતેર','તેોતેર','ચુમોતેર','પંચોતેર','છોતેર','સિત્તોતેર','ઈઠોતેર','ઓગણાએંસી','એંસી','એક્યાસી','બ્યાસી','તેરાસી','ચોરાસી','પંચાસી','છ્યાસી','સત્તાસી','અઠ્ઠાસી','નેવાસી','નેવું','એકાણું','બાણું','ત્રાણું','ચોરાણું','પંચાણું','છાણું','સત્તાણું','અઠ્ઠાણું','નવ્વાણું'];
const enNums = ['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN','TWENTY','TWENTY-ONE','TWENTY-TWO','TWENTY-THREE','TWENTY-FOUR','TWENTY-FIVE','TWENTY-SIX','TWENTY-SEVEN','TWENTY-EIGHT','TWENTY-NINE','THIRTY','THIRTY-ONE','THIRTY-TWO','THIRTY-THREE','THIRTY-FOUR','THIRTY-FIVE','THIRTY-SIX','THIRTY-SEVEN','THIRTY-EIGHT','THIRTY-NINE','FORTY','FORTY-ONE','FORTY-TWO','FORTY-THREE','FORTY-FOUR','FORTY-FIVE','FORTY-SIX','FORTY-SEVEN','FORTY-EIGHT','FORTY-NINE','FIFTY','FIFTY-ONE','FIFTY-TWO','FIFTY-THREE','FIFTY-FOUR','FIFTY-FIVE','FIFTY-SIX','FIFTY-SEVEN','FIFTY-EIGHT','FIFTY-NINE','SIXTY','SIXTY-ONE','SIXTY-TWO','SIXTY-THREE','SIXTY-FOUR','SIXTY-FIVE','SIXTY-SIX','SIXTY-SEVEN','SIXTY-EIGHT','SIXTY-NINE','SEVENTY','SEVENTY-ONE','SEVENTY-TWO','SEVENTY-THREE','SEVENTY-FOUR','SEVENTY-FIVE','SEVENTY-SIX','SEVENTY-SEVEN','SEVENTY-EIGHT','SEVENTY-NINE','EIGHTY','EIGHTY-ONE','EIGHTY-TWO','EIGHTY-THREE','EIGHTY-FOUR','EIGHTY-FIVE','EIGHTY-SIX','EIGHTY-SEVEN','EIGHTY-EIGHT','EIGHTY-NINE','NINETY','NINETY-ONE','NINETY-TWO','NINETY-THREE','NINETY-FOUR','NINETY-FIVE','NINETY-SIX','NINETY-SEVEN','NINETY-EIGHT','NINETY-NINE'];

function yearInWords(y) {
    const lastTwo = y % 100;
    const suffix = lastTwo > 0 ? ' ' + (guNums[lastTwo] || lastTwo) : '';
    if (y >= 2000) return 'બે હજાર' + suffix;
    return 'ઓગણીસસો' + suffix;
}
function enYearInWords(y) {
    const lastTwo = y % 100;
    if (y >= 2000) {
        if (lastTwo === 0) return 'TWO THOUSAND';
        return 'TWO THOUSAND ' + (enNums[lastTwo] || lastTwo);
    }
    if (lastTwo === 0) return 'NINETEEN HUNDRED';
    if (lastTwo < 10) return 'NINETEEN OH ' + (enNums[lastTwo] || lastTwo);
    return 'NINETEEN ' + (enNums[lastTwo] || lastTwo);
}
function updateDobText() {
    let val = document.getElementById('date_of_birth').value.trim();
    // Accept both dd/mm/yyyy and yyyy-mm-dd
    let m = val.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (!m) m = val.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (m) {
        let d, mo, y;
        if (m[0].indexOf('/') > -1) { d = parseInt(m[1]); mo = parseInt(m[2]); y = parseInt(m[3]); }
        else { y = parseInt(m[1]); mo = parseInt(m[2]); d = parseInt(m[3]); }
        const guDay = guOrdinal[d] || d;
        const guMonth = guMonths[mo - 1] || '';
        const enDay = enOrdinal[d] || d;
        const enMonth = enMonths[mo - 1] || '';
        document.getElementById('dob_in_text_gu').value = guDay + ' ' + guMonth + ' ' + yearInWords(y);
        document.getElementById('dob_in_text_en').value = enDay + ' ' + enMonth + ' ' + enYearInWords(y);
    } else {
        document.getElementById('dob_in_text_gu').value = '';
        document.getElementById('dob_in_text_en').value = '';
    }
}
document.getElementById('date_of_birth').addEventListener('input', updateDobText);

window.isAdmin = @json(auth()->user()->role === 'admin');

// Auto-format date: digits only → dd/mm/yyyy
function autoFormatDate(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
    if (v.length > 5) v = v.slice(0,5) + '/' + v.slice(5);
    if (v.length > 10) v = v.slice(0,10);
    input.value = v;
}
document.querySelectorAll('.date-input').forEach(el => {
    el.addEventListener('input', function() { autoFormatDate(this); });
});

// Remember last admission date
document.getElementById('date_of_admission').addEventListener('change', function() {
    if (this.value.trim()) localStorage.setItem('last_admission_date', this.value.trim());
});

// Show/hide minority field based on category
document.getElementById('category_gu').addEventListener('change', function() {
    document.getElementById('minority-group').classList.toggle('hidden', this.value !== 'બક્ષીપંચ');
});

// Auto-digit-only for numeric fields
['gr_number','mobile','whatsapp','apaar_id','uid_no','pen_no','aadhar_no'].forEach(id => {
    document.getElementById(id).addEventListener('input', function() { this.value = this.value.replace(/\D/g,''); });
});

// ===== CRUD Operations =====
form.addEventListener('submit', function(e) {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.textContent = 'સાચવાઈ રહ્યું છે...';

    const id = studentId.value;
    const url = id ? '{{ url("students") }}/' + id : '{{ url("students") }}';
    const method = id ? 'PUT' : 'POST';

    const data = new FormData(form);
    if (id) { data.append('_method', 'PUT'); }

    fetch(url, {
        method: 'POST',
        body: data,
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(res => { if (!res.ok) return res.json().then(e => { throw e; }); return res.json(); })
    .then(res => {
        if (res.success) {
            NexSchool.alert.success(res.message);
            closeModal();
            setTimeout(() => loadStudents(currentPage), 400);
        } else {
            NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
        }
    })
    .catch(err => {
        if (err.errors) {
            const msgs = Object.values(err.errors).flat().join('<br>');
            NexSchool.alert.danger(msgs);
        } else {
            NexSchool.alert.danger(err.message || 'સર્વર ભૂલ');
        }
    })
    .finally(() => { submitBtn.disabled = false; submitBtn.textContent = id ? 'સુધારો' : 'સાચવો'; });
});

function editStudent(id) {
    fetch('{{ url("students") }}/' + id, {
        headers: { 'Accept': 'application/json' },
    })
    .then(res => res.json())
    .then(data => {
        openModal(data);
        studentId.value = data.id;
        // Set standards FIRST so filterClasses has them
        document.getElementById('admission_standard_id').value = data.admission_standard_id || '';
        document.getElementById('current_standard_id').value = data.current_standard_id || '';
        // Now populate class dropdowns (standards are set)
        filterClasses('admission');
        filterClasses('current');
        // Set all remaining field values (class dropdowns already built)
        for (const [key, val] of Object.entries(data)) {
            if (key === 'admission_standard_id' || key === 'current_standard_id' || key === 'photo') continue;
            const el = document.getElementById(key);
            if (el) {
                if (el.tagName === 'SELECT' && el.querySelector('option[value="' + val + '"]')) {
                    el.value = val;
                } else if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = val == 1 || val === true || val === '1';
                } else if (el.type !== 'file') {
                    el.value = val ?? '';
                }
            }
        }
        updateFullName();
        updateDobText();
        document.getElementById('category_gu').dispatchEvent(new Event('change'));
        // Photo preview
        const preview = document.getElementById('photo-preview');
        if (data.photo) {
            preview.src = '{{ asset("storage") }}/' + data.photo;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    })
    .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
}

// Photo preview on file select
document.getElementById('photo').addEventListener('change', function() {
    const preview = document.getElementById('photo-preview');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(this.files[0]);
    } else {
        preview.classList.add('hidden');
    }
});

function deleteStudent(id) {
    NexSchool.confirm.show('ખાતરી કરો', 'શું તમે આ વિદ્યાર્થીને કાઢી નાખવા માંગો છો?', 'danger')
    .then(() => {
        fetch('{{ url("students") }}/' + id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                NexSchool.alert.success(res.message);
                setTimeout(() => loadStudents(currentPage), 400);
            } else {
                NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
            }
        })
        .catch(() => NexSchool.alert.danger('સર્વર ભૂલ'));
    })
    .catch(() => {});
}

// ===== AJAX Filter, Search & Pagination =====
let currentPage = 1;

function loadStudents(page) {
    currentPage = page || 1;
    const preloader = document.getElementById('table-preloader');
    if (preloader) preloader.classList.remove('hidden');
    const stdId = document.getElementById('filter-standard').value;
    const clsId = document.getElementById('filter-class').value;
    const search = document.getElementById('filter-search').value.trim();

    let url = '{{ route("students.data") }}?page=' + currentPage;
    if (stdId) url += '&standard_id=' + stdId;
    if (clsId) url += '&class_id=' + clsId;
    if (search) url += '&search=' + encodeURIComponent(search);

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
    .then(data => {
        renderTable(data.students);
        if (data.pagination) renderPagination(data.pagination);
        if (data.stats) updateStats(data.stats);
    })
    .catch(err => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ: ' + err.message))
    .finally(function () {
        if (preloader) preloader.classList.add('hidden');
    });
}

function renderTable(students) {
    const tbody = document.getElementById('student-table-body');
    if (!students.length) {
        tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-10 text-center text-gray-400">કોઈ વિદ્યાર્થી મળ્યા નહીં</td></tr>';
        return;
    }
    tbody.innerHTML = students.map(s => {
        const statusClass = { active: 'bg-emerald-100 text-emerald-700', inactive: 'bg-gray-100 text-gray-600', alumni: 'bg-amber-100 text-amber-700' }[s.status] || 'bg-gray-100 text-gray-600';
        const statusLabel = { active: 'સક્રિય', inactive: 'નિષ્ક્રિય', alumni: 'ભૂતપૂર્વ' }[s.status] || s.status;
        const rowBg = s.sharirik_jaati === 'kumar' ? 'style="border-left: 4px solid #10b981;"' : s.sharirik_jaati === 'kumari' ? 'style="border-left: 4px solid #f87171;"' : '';
        const dob = s.date_of_birth ? formatDate(s.date_of_birth) : '—';
        const age = s.date_of_birth ? calcAge(s.date_of_birth) + ' વર્ષ' : '—';
        let photoHtml = '<div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm"><i class="lni lni-user-4 text-gray-400 text-xs"></i></div>';
        if (s.photo) photoHtml = '<img src="{{ asset("storage") }}/' + s.photo + '" class="w-9 h-9 rounded-full object-cover border-2 border-gray-200 shadow-sm" alt="photo">';
        return `<tr class="hover:bg-gray-50 transition group" ${rowBg}>
            <td class="px-4 py-3 font-mono font-medium text-gray-900">${s.gr_number}</td>
            <td class="px-4 py-3">${photoHtml}</td>
            <td class="px-4 py-3 font-medium text-gray-800">${s.full_name_gu || ''}</td>
            <td class="px-4 py-3 text-gray-600">${s.father_name_gu || ''}</td>
            <td class="px-4 py-3 font-mono text-gray-600">${dob}</td>
            <td class="px-4 py-3"><span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-md text-xs font-medium text-gray-600"><i class="lni lni-calendar-days text-xs"></i> ${age}</span></td>
            <td class="px-4 py-3"><span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 rounded-md text-xs font-medium text-indigo-700"><i class="lni lni-buildings-1 text-xs"></i> ${s.current_standard ? s.current_standard.name : ''}</span></td>
            <td class="px-4 py-3 font-mono text-gray-600">${s.mobile || ''}</td>
            <td class="px-4 py-3"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}"><i class="lni lni-${statusIcon(s.status)} text-xs"></i> ${statusLabel}</span></td>
            <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1 opacity-70 group-hover:opacity-100 transition">
                    <a href="/students/${s.id}" class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-lg transition" title="પ્રોફાઇલ જુઓ"><i class="lni lni-eye"></i></a>
                    <button onclick="editStudent(${s.id})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1"></i></button>
                    ${window.isAdmin ? `<button onclick="deleteStudent(${s.id})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો"><i class="lni lni-trash-3"></i></button>` : ''}
                </div>
            </td>
        </tr>`;
    }).join('');
}

function renderPagination(pg) {
    const elTotal = document.getElementById('pagination-total');
    const elInfo = document.getElementById('pagination-info');
    const elLinks = document.getElementById('pagination-links');
    if (!elTotal || !elInfo) return;
    elTotal.textContent = pg.total;
    if (pg.total > 0) {
        const from = (pg.current_page - 1) * pg.per_page + 1;
        const to = Math.min(pg.current_page * pg.per_page, pg.total);
        elInfo.innerHTML = 'કુલ <strong>' + pg.total + '</strong> માંથી <strong>' + from + '</strong> થી <strong>' + to + '</strong> બતાવ્યા';
    } else {
        elInfo.innerHTML = 'કુલ 0 માંથી — બતાવ્યા';
    }
    if (!elLinks) return;
    let html = '';
    if (pg.last_page > 1) {
        html += '<button onclick="goToPage(' + (pg.current_page - 1) + ')" class="px-3 py-1.5 text-sm rounded-lg transition font-medium ' + (pg.current_page <= 1 ? 'bg-gray-50 text-gray-300 cursor-not-allowed' : 'bg-gray-100 text-gray-600 hover:bg-gray-200') + '" ' + (pg.current_page <= 1 ? 'disabled' : '') + '><i class="lni lni-chevron-left text-xs"></i></button>';
        const maxVisible = 7;
        const half = Math.floor(maxVisible / 2);
        let start = Math.max(1, pg.current_page - half);
        let end = Math.min(pg.last_page, start + maxVisible - 1);
        if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1);
        if (start > 1) {
            html += '<button onclick="goToPage(1)" class="px-3 py-1.5 text-sm rounded-lg transition font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">1</button>';
            if (start > 2) html += '<span class="px-1 text-gray-400 text-xs">...</span>';
        }
        for (let i = start; i <= end; i++) {
            html += '<button onclick="goToPage(' + i + ')" class="px-3 py-1.5 text-sm rounded-lg transition font-medium ' + (i === pg.current_page ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200') + '">' + i + '</button>';
        }
        if (end < pg.last_page) {
            if (end < pg.last_page - 1) html += '<span class="px-1 text-gray-400 text-xs">...</span>';
            html += '<button onclick="goToPage(' + pg.last_page + ')" class="px-3 py-1.5 text-sm rounded-lg transition font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">' + pg.last_page + '</button>';
        }
        html += '<button onclick="goToPage(' + (pg.current_page + 1) + ')" class="px-3 py-1.5 text-sm rounded-lg transition font-medium ' + (pg.current_page >= pg.last_page ? 'bg-gray-50 text-gray-300 cursor-not-allowed' : 'bg-gray-100 text-gray-600 hover:bg-gray-200') + '" ' + (pg.current_page >= pg.last_page ? 'disabled' : '') + '><i class="lni lni-chevron-right text-xs"></i></button>';
    }
    elLinks.innerHTML = html;
}

function updateStats(stats) {
    const el = id => document.getElementById(id);
    if (el('stat-total')) el('stat-total').textContent = stats.total_active;
    if (el('stat-boys')) el('stat-boys').textContent = stats.total_boys;
    if (el('stat-girls')) el('stat-girls').textContent = stats.total_girls;
}

function goToPage(page) {
    loadStudents(page);
}

function applyFilters() {
    loadStudents(1);
}

function statusIcon(status) {
    return { active: 'check-circle-1', inactive: 'ban-2', alumni: 'exit' }[status] || 'ban-2';
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr + (dateStr.includes('T') ? '' : 'T00:00:00'));
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    return dd + '/' + mm + '/' + yyyy;
}

function calcAge(dateStr) {
    const dob = new Date(dateStr + (dateStr.includes('T') ? '' : 'T00:00:00'));
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    return age;
}

// Filter class dropdown based on standard
document.getElementById('filter-standard').addEventListener('change', function() {
    const stdId = this.value;
    const clsSelect = document.getElementById('filter-class');
    const oldVal = clsSelect.value;
    clsSelect.innerHTML = '<option value="">બધા વર્ગ</option>';
    if (stdId && classesByStandard[stdId]) {
        classesByStandard[stdId].forEach(function(c) {
            clsSelect.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>';
        });
    }
    applyFilters();
});

document.getElementById('filter-class').addEventListener('change', applyFilters);
document.getElementById('filter-search').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') applyFilters();
});
</script>
@endpush
