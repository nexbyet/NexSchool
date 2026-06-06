<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div>
            <h3 class="font-semibold text-gray-900">દૈનિક આંકડાબુક</h3>
            <p class="text-sm text-gray-500">{{ $dayNamesGu[$date->dayOfWeek] }}, {{ $date->format('d-m-Y') }}
                @if($isSunday)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700 rounded-full">રવિવાર — રજા</span>
                @elseif($isHoliday)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700 rounded-full">રજા</span>
                @endif
            </p>
        </div>
        <button onclick="window.open('{{ route("daily-stats.print", ["date" => $date->format("Y-m-d")]) }}', '_blank')" class="px-3 py-1.5 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition flex items-center gap-1.5">
            <i class="lni lni-printer"></i> પ્રિન્ટ
        </button>
    </div>

    <div class="overflow-x-auto" style="max-height:80vh; overflow-y:auto;">
        <table class="w-full text-xs border-collapse">
            <thead class="sticky top-0 z-10">
                {{-- Header row 1: metric group labels --}}
                <tr class="bg-amber-100 text-amber-900">
                    <th rowspan="2" class="px-2 py-1.5 border border-amber-300 font-semibold whitespace-nowrap min-w-[70px]">ધોરણ</th>
                    <th rowspan="2" class="px-2 py-1.5 border border-amber-300 font-semibold whitespace-nowrap min-w-[60px]">વર્ગ</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">આગલા દિવસની સંખ્યા</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">દાખલ સંખ્યા</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">છોડીને ગયા સંખ્યા</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center bg-amber-200">કુલ (૧+૨+૩)</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">હાજર સંખ્યા</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">રજા વગર ગેરહાજર</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">રજા સાથે ગેરહાજર</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center">માંદગી રજા</th>
                    <th colspan="3" class="px-2 py-1.5 border border-amber-300 font-semibold text-center bg-amber-200">કુલ (૫+૬+૭+૮)</th>
                </tr>
                {{-- Header row 2: kumar/kumari/kul sub-headers --}}
                <tr class="bg-amber-50 text-amber-800">
                    @for($i = 0; $i < 9; $i++)
                    <th class="px-1 py-1 border border-amber-200 font-medium text-xs">કુમાર</th>
                    <th class="px-1 py-1 border border-amber-200 font-medium text-xs">કુમારી</th>
                    <th class="px-1 py-1 border border-amber-200 font-medium text-xs">કુલ</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php
                    $metrics = ['prev', 'adm', 'lft', 't1', 'p', 'a', 'l', 's', 't2'];
                    $label = ''; // not needed in this layout
                @endphp
                @foreach($rows as $r)
                <tr class="hover:bg-amber-50/50">
                    <td class="px-2 py-1.5 border border-gray-200 font-medium text-gray-800 whitespace-nowrap">{{ $r['standard'] }}</td>
                    <td class="px-2 py-1.5 border border-gray-200 text-gray-600 whitespace-nowrap">{{ $r['class'] }}</td>
                    @foreach($metrics as $m)
                    <td class="px-1 py-1.5 border border-gray-200 text-center font-mono text-blue-700">{{ $r[$m]['kumar'] }}</td>
                    <td class="px-1 py-1.5 border border-gray-200 text-center font-mono text-rose-700">{{ $r[$m]['kumari'] }}</td>
                    <td class="px-1 py-1.5 border border-gray-200 text-center font-mono font-bold">{{ $r[$m]['total'] }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
            {{-- Grand Total --}}
            @if(!empty($grandTotals))
            <tfoot>
                <tr class="bg-amber-100 font-bold text-amber-900">
                    <td colspan="2" class="px-2 py-2 border border-amber-300 text-center font-bold">કુલ સરવાળો</td>
                    @foreach($metrics as $m)
                    <td class="px-1 py-2 border border-amber-300 text-center font-mono">{{ $grandTotals[$m]['kumar'] }}</td>
                    <td class="px-1 py-2 border border-amber-300 text-center font-mono">{{ $grandTotals[$m]['kumari'] }}</td>
                    <td class="px-1 py-2 border border-amber-300 text-center font-mono">{{ $grandTotals[$m]['total'] }}</td>
                    @endforeach
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
