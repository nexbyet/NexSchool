@if($students->isEmpty())
    <div class="text-center py-12">
        <i class="lni lni-user-multiple-4 text-4xl text-gray-300"></i>
        <p class="text-gray-500 mt-2 text-sm">કોઈ વિદ્યાર્થી મળ્યો નહીં</p>
    </div>
@else
    <div class="divide-y divide-gray-100">
        @foreach($students as $s)
        <div class="flex items-center gap-3 px-3 py-2.5 hover:bg-teal-50 rounded-lg transition cursor-pointer student-select-btn" data-id="{{ $s['id'] }}">
            @if($s['photo'])
                <img src="{{ $s['photo'] }}" alt="" class="w-10 h-10 rounded-full object-cover border border-gray-200">
            @else
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-500 border border-gray-200">
                    {{ mb_substr($s['name_gu'], 0, 1) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $s['name_gu'] }}</p>
                <p class="text-xs text-gray-500">GR: {{ $s['gr_number'] }} | {{ $s['standard'] }} - {{ $s['class'] }}</p>
            </div>
            <i class="lni lni-arrow-right text-gray-300 text-sm flex-shrink-0"></i>
        </div>
        @endforeach
    </div>
@endif

