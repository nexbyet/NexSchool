@php $stdId = $std->id; @endphp
<div id="standard-card-{{ $stdId }}" class="standard-card min-w-[280px] bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300" data-id="{{ $stdId }}">
    {{-- Card header with gradient --}}
    <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100">
        <div class="flex items-center gap-3">
            <span class="standard-drag-handle cursor-grab text-gray-400 hover:text-gray-600">
                <i class="lni lni-menu-meatballs-2 text-lg"></i>
            </span>
            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-lg flex items-center justify-center shadow-sm">
                <span class="text-white font-bold text-xs">{{ $std->name }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 text-base">{{ $std->name }}</h3>
        </div>
        <div class="flex items-center gap-1">
            <button onclick="addClass({{ $stdId }})" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="વર્ગ ઉમેરો">
                <i class="lni lni-plus text-base"></i>
            </button>
            <button onclick="editStandard({{ $stdId }})" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="સુધારો">
                <i class="lni lni-pencil-1 text-base"></i>
            </button>
            <button onclick="deleteStandard({{ $stdId }}, '{{ $std->name }}')" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="કાઢો">
                <i class="lni lni-trash-3 text-base"></i>
            </button>
        </div>
    </div>

    {{-- Card body: classes list --}}
    <div class="class-list p-4 space-y-2" data-standard-id="{{ $stdId }}">
        @forelse ($std->classes as $cls)
            <div id="class-item-{{ $cls->id }}" class="class-item flex items-center justify-between px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all" data-id="{{ $cls->id }}" data-standard-id="{{ $stdId }}">
                <div class="flex items-center gap-3">
                    <span class="class-drag-handle cursor-grab text-gray-400 hover:text-gray-600">
                        <i class="lni lni-layout-9 text-sm"></i>
                    </span>
                    <div class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center">
                        <i class="lni lni-buildings-1 text-xs text-indigo-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ $cls->name }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="editClass({{ $cls->id }})" class="p-1 text-gray-400 hover:text-indigo-600 rounded transition" title="સુધારો">
                        <i class="lni lni-pencil-1 text-sm"></i>
                    </button>
                    <button onclick="deleteClass({{ $cls->id }}, '{{ $cls->name }}')" class="p-1 text-gray-400 hover:text-red-600 rounded transition" title="કાઢો">
                        <i class="lni lni-trash-3 text-sm"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-6">
                <div class="w-10 h-10 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="lni lni-buildings-1 text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-400">હજી સુધી કોઈ વર્ગ નથી</p>
            </div>
        @endforelse
    </div>

    {{-- Subject pills footer --}}
    @if ($std->subjects->isNotEmpty())
    <div class="px-4 py-3 bg-gradient-to-r from-amber-50 to-orange-50 border-t border-amber-100 subject-list" data-standard-id="{{ $stdId }}">
        <p class="text-xs font-medium text-amber-500 mb-2 flex items-center gap-1">
            <i class="lni lni-book-1 text-xs"></i> વિષયો:
        </p>
        <div class="flex flex-wrap gap-1.5">
            @foreach ($std->subjects as $sub)
            <span class="subject-pill inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-white text-amber-700 border border-amber-200 cursor-grab shadow-sm hover:shadow" data-id="{{ $sub->id }}">
                <i class="lni lni-layout-9 text-xs text-amber-400"></i>
                {{ $sub->name }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Add class button at bottom --}}
    <div class="px-4 pb-4 pt-3">
        <button onclick="addClass({{ $stdId }})" class="w-full py-2.5 border-2 border-dashed border-gray-200 rounded-lg text-sm text-gray-400 hover:text-indigo-600 hover:border-indigo-300 transition flex items-center justify-center gap-1.5">
            <i class="lni lni-plus text-sm"></i> વર્ગ ઉમેરો
        </button>
    </div>
</div>
