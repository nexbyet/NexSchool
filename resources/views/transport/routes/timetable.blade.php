@extends('layouts.app')
@section('title', 'બસ રૂટ ટાઇમટેબલ')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-600 to-cyan-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">બસ રૂટ ટાઇમટેબલ</h1>
            <p class="text-sky-200 mt-1 text-sm">રૂટ મુજબ સ્ટોપ અને સમયનું ટાઇમટેબલ</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <form method="GET" action="{{ route('transport.routes.timetable') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">રૂટ પસંદ કરો</label>
                <select name="route_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="">— પસંદ કરો —</option>
                    @foreach($routes as $r)
                    <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>{{ $r->route_name }} ({{ $r->vehicle?->vehicle_no ?? 'કોઈ વાહન નથી' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ભાષા</label>
                <select name="lang" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="gu" {{ request('lang', 'gu') === 'gu' ? 'selected' : '' }}>ગુજરાતી</option>
                    <option value="en" {{ request('lang') === 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
            <div class="flex items-end">
                @if(request('route_id'))
                <a href="{{ route('transport.routes.timetable.print', ['route_id' => request('route_id'), 'lang' => request('lang', 'gu')]) }}" target="_blank" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700 transition flex items-center gap-2"><i class="lni lni-printer text-sm"></i> પ્રિન્ટ કરો</a>
                @endif
                <a href="{{ route('transport.routes.timetable') }}" class="ml-2 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">રીસેટ</a>
            </div>
        </form>
    </div>

    @if($selectedRoute)
    @php $lang = request('lang', 'gu'); @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-sky-50 to-cyan-50 border-b border-sky-100">
            <h3 class="text-lg font-bold text-sky-800">{{ $selectedRoute->route_name }}</h3>
            <p class="text-sm text-sky-600 mt-0.5">{{ $lang === 'gu' ? 'વાહન' : 'Vehicle' }}: {{ $selectedRoute->vehicle?->vehicle_no ?? '—' }} @if($selectedRoute->vehicle) | {{ $selectedRoute->vehicle->vehicle_type ?? '' }} @endif</p>
            @if($selectedRoute->description)
            <p class="text-xs text-gray-500 mt-1">{{ $selectedRoute->description }}</p>
            @endif
        </div>

        @if($selectedRoute->stops->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase" style="width:8mm">ક્રમ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">{{ $lang === 'gu' ? 'સ્ટોપ' : 'Stop' }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">{{ $lang === 'gu' ? 'આવક સમય (Pickup)' : 'Pickup Time' }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">{{ $lang === 'gu' ? 'જાવક સમય (Drop)' : 'Drop Time' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($selectedRoute->stops->sortBy('stop_order') as $stop)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 py-3 text-gray-500 text-xs text-center">{{ $stop->stop_order }}</td>
                        <td class="px-4 py-3 font-medium">{{ $stop->stop_name }}</td>
                        <td class="px-4 py-3 text-center font-mono text-sm">{{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : '—' }}</td>
                        <td class="px-4 py-3 text-center font-mono text-sm">{{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <i class="lni lni-map-marker-1 text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500 font-medium">{{ $lang === 'gu' ? 'આ રૂટ પર કોઈ સ્ટોપ ઉમેરાયેલ નથી' : 'No stops added to this route' }}</p>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
