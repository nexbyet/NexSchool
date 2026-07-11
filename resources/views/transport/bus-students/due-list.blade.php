@extends('layouts.app')
@section('title', 'બસ વિદ્યાર્થી બાકી યાદી')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">બસ વિદ્યાર્થી — બાકી યાદી</h1>
            <p class="text-amber-200 mt-1 text-sm">બસ વિદ્યાર્થીઓની બાકી ફીની વિગત</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('transport.bus-students.due-list') }}" class="flex items-center gap-3">
            <select name="route_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white" onchange="this.form.submit()">
                <option value="">બધા રૂટ</option>
                @foreach($routes as $r)
                <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>{{ $r->route_name }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('transport.bus-students.print-due-list', request()->all()) }}" target="_blank" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition flex items-center gap-1"><i class="lni lni-printer text-xs"></i> પ્રિન્ટ</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">ક્રમ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">નામ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">રૂટ</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">કુલ ફી</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">ભરેલ ફી</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">બાકી ફી</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $i => $s)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $s->full_name_gu }}</td>
                        <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 bg-teal-50 rounded-md text-xs font-medium text-teal-700">{{ $s->route->route_name }}</span></td>
                        <td class="px-4 py-3 text-center font-mono text-gray-700">₹{{ number_format($s->total_fee, 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono text-emerald-600">₹{{ number_format($s->paid_fee, 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono {{ $s->due_fee > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">₹{{ number_format($s->due_fee, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">કોઈ ડેટા નથી</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">કુલ:</td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-gray-800">₹{{ number_format($grandTotal, 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-emerald-700">₹{{ number_format($grandPaid, 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono font-bold {{ $grandDue > 0 ? 'text-red-700' : 'text-gray-600' }}">₹{{ number_format($grandDue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
