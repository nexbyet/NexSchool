@extends('layouts.app')

@section('title', 'શિક્ષકો')

@push('styles')
<style>
.modal-fieldset { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; }
.modal-fieldset legend { font-size: 0.875rem; font-weight: 600; color: #4f46e5; padding: 0 0.5rem; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">
    {{-- Decorative header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-6 mb-6">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">શિક્ષકો</h1>
                <p class="text-emerald-200 mt-1 text-sm">શાળાના તમામ શિક્ષકોની માહિતી અને સંચાલન</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teachers.import.view') }}" class="px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition flex items-center gap-2 backdrop-blur-sm" title="Excel થી જથ્થાબંધ શિક્ષક ઉમેરો">
                    <i class="lni lni-upload-1 text-base"></i> Import
                </a>
                <button onclick="openModal()" class="px-4 py-2 bg-white text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50 transition flex items-center gap-2 shadow-lg">
                    <i class="lni lni-plus text-base"></i> નવા શિક્ષક
                </button>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-user-multiple-4 text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">કુલ</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalActive + $totalInactive }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-check-circle-1 text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">સક્રિય</p>
                <p class="text-xl font-bold text-emerald-700">{{ $totalActive }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-user-4 text-blue-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">પુરુષ</p>
                <p class="text-xl font-bold text-blue-700">{{ $totalMale }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rose-100 to-rose-200 flex items-center justify-center flex-shrink-0">
                <i class="lni lni-user-4 text-rose-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">સ્ત્રી</p>
                <p class="text-xl font-bold text-rose-600">{{ $totalFemale }}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs uppercase tracking-wider">
                        <th class="text-left px-4 py-3.5 font-semibold">ID</th>
                        <th class="text-left px-4 py-3.5 font-semibold">નામ</th>
                        <th class="text-left px-4 py-3.5 font-semibold">ઇમેઇલ</th>
                        <th class="text-left px-4 py-3.5 font-semibold">મોબાઇલ</th>
                        <th class="text-left px-4 py-3.5 font-semibold">જાતિ</th>
                        <th class="text-left px-4 py-3.5 font-semibold">જોડાણ</th>
                        <th class="text-left px-4 py-3.5 font-semibold">સ્થિતિ</th>
                        <th class="text-center px-4 py-3.5 font-semibold">ક્રિયા</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($teachers as $t)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-4 py-3 font-mono font-medium text-gray-900">{{ $t->teacher_id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="lni lni-user-4 text-emerald-600 text-xs"></i>
                                </div>
                                {{ $t->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <span class="inline-flex items-center gap-1">
                                <i class="lni lni-envelope-1 text-xs text-gray-400"></i>
                                {{ $t->email }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-gray-600">{{ $t->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php $gender = ['male' => 'પુરુષ', 'female' => 'સ્ત્રી']; @endphp
                            @if($t->gender)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium @if($t->gender === 'male') bg-blue-50 text-blue-700 @else bg-rose-50 text-rose-700 @endif">
                                    <i class="lni lni-user-4 text-xs"></i>
                                    {{ $gender[$t->gender] ?? $t->gender }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-md text-xs font-medium text-gray-600">
                                <i class="lni lni-calendar-days text-xs"></i>
                                {{ $t->joining_date ? \Carbon\Carbon::parse($t->joining_date)->format('d/m/Y') : '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $tsc = $t->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500';
                                $tsl = $t->status === 'active' ? 'સક્રિય' : 'નિષ્ક્રિય';
                                $tsi = $t->status === 'active' ? 'lni-check-circle-1' : 'lni-ban-2';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tsc }}">
                                <i class="{{ $tsi }} text-xs"></i>
                                {{ $tsl }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1 opacity-70 group-hover:opacity-100 transition">
                                <a href="{{ url('teachers') }}/{{ $t->id }}" class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-lg transition" title="પ્રોફાઇલ જુઓ"><i class="lni lni-eye"></i></a>
                                <button onclick="editTeacher({{ $t->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો"><i class="lni lni-pencil-1"></i></button>
                                <button onclick="resetPassword({{ $t->id }}, '{{ $t->name }}')" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="પાસવર્ડ રીસેટ"><i class="lni lni-key-1"></i></button>
                                <button onclick="deleteTeacher({{ $t->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="કાઢી નાખો"><i class="lni lni-trash-3"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl flex items-center justify-center shadow-sm">
                                <i class="lni lni-user-4 text-3xl text-emerald-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">હજી કોઈ શિક્ષક નથી</p>
                            <p class="text-gray-400 text-sm mt-1">પ્રથમ શિક્ષક ઉમેરવા માટે બટન દબાવો</p>
                            <button onclick="openModal()" class="mt-4 px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm">નવા શિક્ષક ઉમેરો</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($teachers->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $teachers->links() }}
        </div>
        @endif
        </div>
    </div>

{{-- Modal --}}
<div id="teacher-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal()"></div>
    <div class="absolute inset-4 md:inset-x-12 md:inset-y-6 bg-white rounded-2xl shadow-2xl overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4 flex items-center justify-between z-10 rounded-t-2xl">
            <h2 class="text-lg font-semibold text-white" id="modal-title">નવા શિક્ષક</h2>
            <button onclick="closeModal()" class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition"><i class="lni lni-xmark text-xl"></i></button>
        </div>

        <form id="teacher-form" class="p-6 space-y-4">
            <input type="hidden" id="teacher_id" name="teacher_id">

            {{-- Personal Info --}}
            <fieldset class="modal-fieldset">
                <legend>વ્યક્તિગત માહિતી</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">નામ <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ઇમેઇલ <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">જાતિ</label>
                        <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            <option value="">પસંદ કરો</option>
                            <option value="male">પુરુષ</option>
                            <option value="female">સ્ત્રી</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ તારીખ</label>
                        <input type="text" id="date_of_birth" name="date_of_birth" placeholder="dd/mm/yyyy" class="date-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">બ્લડ ગ્રુપ</label>
                        <select id="blood_group" name="blood_group" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            <option value="">પસંદ કરો</option>
                            <option value="A+">A+</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B-">B-</option>
                            <option value="AB+">AB+</option><option value="AB-">AB-</option>
                            <option value="O+">O+</option><option value="O-">O-</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">અનુભવ (વર્ષ)</label>
                        <input type="number" id="experience_in_years" name="experience_in_years" min="0" max="70" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>
            </fieldset>

            {{-- Contact --}}
            <fieldset class="modal-fieldset">
                <legend>સંપર્ક</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">મોબાઇલ નંબર</label>
                        <input type="text" id="phone" name="phone" maxlength="20" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp નંબર</label>
                        <input type="text" id="whatsapp_number" name="whatsapp_number" maxlength="20" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">સરનામું</label>
                        <textarea id="address" name="address" rows="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"></textarea>
                    </div>
                </div>
            </fieldset>

            {{-- Joining & Employment --}}
            <fieldset class="modal-fieldset">
                <legend>જોડાણ અને રોજગાર</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">જોડાણ તારીખ</label>
                        <input type="text" id="joining_date" name="joining_date" placeholder="dd/mm/yyyy" class="date-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">જોડાણ ક્રમ નં.</label>
                        <input type="text" id="joining_number" name="joining_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">રેટિંગ્સ</label>
                        <input type="text" id="ratings" name="ratings" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>
            </fieldset>

            {{-- Salary & Leave --}}
            <fieldset class="modal-fieldset">
                <legend>પગાર અને રજા</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">મૂળ પગાર (Basic Pay)</label>
                        <input type="number" id="basic_pay" name="basic_pay" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">મૂળ વેતન (Basic Salary)</label>
                        <input type="number" id="basic_salary" name="basic_salary" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">અન્ય વેતન (Other Salary)</label>
                        <input type="number" id="other_salary" name="other_salary" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">મહત્તમ LWP (રજા વગર પગાર)</label>
                        <input type="number" id="max_lwp" name="max_lwp" min="0" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">મહત્તમ CL (કેઝ્યુઅલ રજા)</label>
                        <input type="number" id="max_cl" name="max_cl" min="0" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>
            </fieldset>

            {{-- Status --}}
            <fieldset class="modal-fieldset">
                <legend>સ્થિતિ</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">સ્થિતિ</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            <option value="active">સક્રિય</option>
                            <option value="inactive">નિષ્ક્રિય</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">નિષ્ક્રિય થવાનું કારણ</label>
                        <textarea id="reason_inactive" name="reason_inactive" rows="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">નિષ્ક્રિય થવાની તારીખ</label>
                        <input type="text" id="date_inactive" name="date_inactive" placeholder="dd/mm/yyyy" class="date-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">રદ કરો</button>
                <button type="submit" id="submit-btn" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">સાચવો</button>
            </div>
        </form>
    </div>
</div>

{{-- Password Reset Modal --}}
<div id="password-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePasswordModal()"></div>
    <div class="relative top-1/2 -translate-y-1/2 mx-auto max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="lni lni-key-1 text-xl text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900" id="pwd-modal-title">પાસવર્ડ રીસેટ</h3>
                    <p class="text-sm text-gray-500" id="pwd-modal-desc">શિક્ષક માટે નવો પાસવર્ડ લખો</p>
                </div>
            </div>
            <input type="hidden" id="pwd-teacher-id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">નવો પાસવર્ડ</label>
                <input type="text" id="pwd-new-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none transition" placeholder="ઓછામાં ઓછા 6 અક્ષર" minlength="6">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closePasswordModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">રદ કરો</button>
                <button onclick="submitPasswordReset()" id="pwd-submit-btn" class="px-6 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">બદલો</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('teacher-modal');
const form = document.getElementById('teacher-form');
const submitBtn = document.getElementById('submit-btn');

function openModal(data = null) {
    form.reset();
    document.getElementById('teacher_id').value = '';
    document.getElementById('modal-title').textContent = 'નવા શિક્ષક';
    submitBtn.textContent = 'સાચવો';
    modal.classList.remove('hidden');
    if (data) {
        document.getElementById('modal-title').textContent = 'શિક્ષક સુધારો';
        submitBtn.textContent = 'સુધારો';
        for (const [key, val] of Object.entries(data)) {
            const el = document.getElementById(key);
            if (el) {
                el.value = val ?? '';
            }
        }
    }
}

function closeModal() {
    modal.classList.add('hidden');
}

// Date input auto-format
document.querySelectorAll('.date-input').forEach(inp => {
    inp.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
        if (v.length > 5) v = v.slice(0, 5) + '/' + v.slice(5, 9);
        this.value = v;
    });
});

form.addEventListener('submit', function(e) {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.textContent = 'સાચવી રહ્યા...';

    const id = document.getElementById('teacher_id').value;
    const url = id ? '{{ url("teachers") }}/' + id : '{{ url("teachers") }}';
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
            setTimeout(() => location.reload(), 400);
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

function editTeacher(id) {
    fetch('{{ url("teachers") }}/' + id, {
        headers: { 'Accept': 'application/json' },
    })
    .then(res => res.json())
    .then(data => {
        openModal(data);
        document.getElementById('teacher_id').value = data.id;
    })
    .catch(() => NexSchool.alert.danger('ડેટા મેળવવામાં ભૂલ.'));
}

function deleteTeacher(id) {
    NexSchool.confirm.show('શિક્ષક કાઢી નાખો', 'શું તમે આ શિક્ષક કાઢી નાખવા માંગો છો?', 'danger')
    .then(() => {
        fetch('{{ url("teachers") }}/' + id, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: new URLSearchParams({ _method: 'DELETE' }),
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                NexSchool.alert.success(res.message);
                setTimeout(() => location.reload(), 400);
            } else {
                NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
            }
        })
        .catch(() => NexSchool.alert.danger('કાઢતી વખતે ભૂલ.'));
    })
    .catch(() => {});
}

function resetPassword(id, name) {
    document.getElementById('pwd-teacher-id').value = id;
    document.getElementById('pwd-modal-title').textContent = name + ' — પાસવર્ડ બદલો';
    document.getElementById('pwd-modal-desc').textContent = name + ' માટે નવો પાસવર્ડ લખો';
    document.getElementById('pwd-new-password').value = 'Teacher@123';
    document.getElementById('pwd-new-password').focus();
    document.getElementById('password-modal').classList.remove('hidden');
}

function closePasswordModal() {
    document.getElementById('password-modal').classList.add('hidden');
}

function submitPasswordReset() {
    const id = document.getElementById('pwd-teacher-id').value;
    const pwd = document.getElementById('pwd-new-password').value;
    if (!pwd || pwd.length < 6) {
        NexSchool.alert.warning('પાસવર્ડ ઓછામાં ઓછા 6 અક્ષરનો હોવો જોઈએ.');
        return;
    }
    document.getElementById('pwd-submit-btn').disabled = true;
    fetch('{{ url("teachers") }}/' + id + '/reset-password', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ password: pwd }),
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            NexSchool.alert.success(res.message);
            closePasswordModal();
        } else {
            NexSchool.alert.danger(res.message || 'ભૂલ આવી.');
        }
    })
    .catch(() => NexSchool.alert.danger('પાસવર્ડ રીસેટ કરતી વખતે ભૂલ.'))
    .finally(() => { document.getElementById('pwd-submit-btn').disabled = false; });
}
</script>
@endpush
