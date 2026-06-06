<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
        <i class="lni lni-bar-chart-4 text-teal-600 text-lg"></i>
        <h3 class="font-semibold text-gray-900">પાછલા 5 દિવસની હાજરી</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-4 py-3 text-left border-b border-gray-200">તારીખ</th>
                    <th class="px-4 py-3 text-center border-b border-gray-200">વાર</th>
                    <th class="px-4 py-3 text-center border-b border-gray-200">
                        <span class="text-emerald-600">હાજર</span>
                    </th>
                    <th class="px-4 py-3 text-center border-b border-gray-200">
                        <span class="text-red-600">ગેર</span>
                    </th>
                    <th class="px-4 py-3 text-center border-b border-gray-200">
                        <span class="text-amber-600">રજા સાથે</span>
                    </th>
                    <th class="px-4 py-3 text-center border-b border-gray-200">
                        <span class="text-blue-600">માંદગી</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceData as $row)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50">
                        <td class="px-4 py-2.5 text-gray-700 font-medium">{{ $row['date']->format('d/m/Y') }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-500">{{ $row['dayName'] }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-emerald-600">{{ $row['present'] }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-red-600">{{ $row['absent'] }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-amber-600">{{ $row['absent_with_leave'] }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-blue-600">{{ $row['medical_leave'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">હજુ સુધી કોઈ હાજરી નોંધાઈ નથી.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

