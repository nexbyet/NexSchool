<footer class="no-print" style="background-color: var(--theme-footer-bg); color: #d1d5db;">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if($school && $school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="h-10 w-10 object-contain rounded-lg bg-white p-1">
                    @else
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, var(--theme-header-from), var(--theme-header-to));">N</div>
                    @endif
                    <h3 class="text-lg font-bold text-white">{{ $school->school_name_gu ?? 'શાળા' }}</h3>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">{{ $school->address ?? '' }}</p>
                @if($school && $school->email)
                <p class="text-sm text-gray-400 mt-2"><i class="lni lni-envelope-1 mr-1.5"></i>{{ $school->email }}</p>
                @endif
                @if($school && $school->mobile)
                <p class="text-sm text-gray-400"><i class="lni lni-phone mr-1.5"></i>{{ $school->mobile }}</p>
                @endif
            </div>

            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">ઝડપી લિંક</h4>
                <ul class="space-y-2">
                    @php
                        $footerLinks = \App\Models\Menu::with('activeItems')->where('location', 'footer')->where('status', true)->first();
                        $fItems = $footerLinks ? $footerLinks->activeItems : collect();
                    @endphp
                    @forelse ($fItems as $item)
                    @php $link = $item->page_id ? url('/' . ($item->page->slug ?? '')) : ($item->url ?: '#'); @endphp
                    <li><a href="{{ $link }}" target="{{ $item->target }}" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-1.5"><i class="lni lni-angle-double-right text-[10px]"></i>{{ $item->title_gu }}</a></li>
                    @empty
                    <li><a href="{{ url('/') }}" class="text-sm text-gray-400 hover:text-white transition">હોમ</a></li>
                    <li><a href="{{ url('/about-us') }}" class="text-sm text-gray-400 hover:text-white transition">અમારા વિશે</a></li>
                    <li><a href="{{ url('/contact') }}" class="text-sm text-gray-400 hover:text-white transition">સંપર્ક</a></li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">સંપર્ક</h4>
                <ul class="space-y-3">
                    @if($school && $school->mobile)
                    <li class="flex items-start gap-2.5"><div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"><i class="lni lni-phone text-primary"></i></div><div><p class="text-sm text-gray-400">{{ $school->mobile }}</p>@if($school && $school->whatsapp_number)<p class="text-xs text-gray-500">WhatsApp: {{ $school->whatsapp_number }}</p>@endif</div></li>
                    @endif
                    @if($school && $school->email)
                    <li class="flex items-start gap-2.5"><div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"><i class="lni lni-envelope-1 text-primary"></i></div><p class="text-sm text-gray-400">{{ $school->email }}</p></li>
                    @endif
                    @if($school && $school->address)
                    <li class="flex items-start gap-2.5"><div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"><i class="lni lni-location-arrow-right text-primary"></i></div><p class="text-sm text-gray-400">{{ $school->address }}</p></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-2">
            <p class="text-xs text-gray-500">@if($school && $school->copyright_text){{ $school->copyright_text }}@else&copy; {{ date('Y') }} {{ $school->school_name_gu ?? 'શાળા' }}. સર્વ અધિકાર સુરક્ષિત.@endif</p>
            @if($school && $school->footer_credits)
            <p class="text-xs text-gray-600">{{ $school->footer_credits }}</p>
            @endif
        </div>
    </div>
</footer>
