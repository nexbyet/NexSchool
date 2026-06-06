<div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm" style="max-width:210mm; margin:0 auto;">
    {{-- Mini letterhead --}}
    <div class="border-b border-gray-300 px-5 py-3 text-center bg-white">
        <h3 class="font-bold text-gray-900 text-base">{{ $school?->school_name_gu ?? 'શાળા' }}</h3>
        @if($school)
            <p class="text-xs text-gray-600">{{ $school->address }}</p>
            <p class="text-xs text-gray-600">
                મોબાઇલ: {{ $school->mobile }}{{ $school->email ? ' | ' . $school->email : '' }}
                @if($school->uid_number) | UID: {{ $school->uid_number }} @endif
            </p>
            @if($school->grant_number)
                <p class="text-xs text-gray-600">મંજૂરી નં: {{ $school->grant_number }}{{ $school->grant_date ? ' | તા. ' . \Carbon\Carbon::parse($school->grant_date)->format('d/m/Y') : '' }}</p>
            @endif
        @endif
    </div>

    <div class="px-5 py-3 bg-white text-sm leading-relaxed">
        <h4 class="font-bold text-gray-900 text-sm text-center underline mb-2">{{ $lang === 'gu' ? 'બોનાફાઈડ પ્રમાણપત્ર' : 'BONAFIDE CERTIFICATE' }}</h4>

        <div class="flex gap-2">
            <div class="flex-1 min-w-0">
                @if($lang === 'gu')
                    <div class="text-xs mb-1"><span class="text-gray-600">તારીખ:</span> <strong>{{ $dateGu }}</strong></div>
                    <div class="text-xs mb-1"><span class="text-gray-600">GR નંબર:</span> <strong>{{ $grNumber }}</strong></div>
                    <div class="text-xs mb-1"><span class="text-gray-600">વિદ્યાર્થીનું પૂરું નામ:</span> <strong>{{ $fullName ?? $name }}</strong></div>
                    <div class="text-xs mb-1"><span class="text-gray-600">જન્મ તારીખ:</span> <strong>{{ $dob }}</strong></div>
                    <div class="text-xs mb-2"><span class="text-gray-600">જન્મ તારીખ (અક્ષરમાં):</span> <strong>{{ $dobInText }}</strong></div>
                @else
                    <div class="text-xs mb-1"><span class="text-gray-600">Date:</span> <strong>{{ $dateGu }}</strong></div>
                    <div class="text-xs mb-1"><span class="text-gray-600">GR Number:</span> <strong>{{ $grNumber }}</strong></div>
                    <div class="text-xs mb-1"><span class="text-gray-600">Full Name:</span> <strong>{{ $fullName ?? $name }}</strong></div>
                    <div class="text-xs mb-1"><span class="text-gray-600">Date of Birth:</span> <strong>{{ $dob }}</strong></div>
                    <div class="text-xs mb-2"><span class="text-gray-600">DOB (in Words):</span> <strong>{{ $dobInText }}</strong></div>
                @endif
            </div>
            @if($photoUrl)
                <div class="flex-shrink-0"><img src="{{ $photoUrl }}" class="w-16 h-20 object-cover border border-gray-300 rounded"></div>
            @else
                <div class="flex-shrink-0 w-16 h-20 border border-dashed border-gray-300 rounded flex items-center justify-center text-[8px] text-gray-400">PHOTO</div>
            @endif
        </div>

        @if($lang === 'gu')
            <p class="text-xs text-gray-800 leading-relaxed mt-1">
                આથી પ્રમાણિત કરવામાં આવે છે કે <strong>{{ $fullName ?? $name }}</strong> અમારી શાળામાં ધોરણ <strong>{{ $standard }}</strong> માં અભ્યાસ કરે છે. જી.આર. માં તેની જન્મ તારીખ <strong>{{ $dob }}</strong> અને શબ્દોમાં <strong>{{ $dobInText }}</strong> નોંધાયેલી છે. જેનો ધર્મ <strong>{{ $religion }}</strong> અને જ્ઞાતિ <strong>{{ $cast }}</strong> છે. આ પ્રમાણપત્ર વાલીની વિનંતીથી અને શાળાના રેકોર્ડ પરથી ખરાઈ કરી આપવામાં આવે છે.
            </p>
        @else
            <p class="text-xs text-gray-800 leading-relaxed mt-1">
                This is to certify that <strong>{{ $fullName ?? $name }}</strong> is studying in Standard <strong>{{ $standard }}</strong> in our school. Her/his date of birth <strong>{{ $dob }}</strong> (in words: <strong>{{ $dobInText }}</strong>) is recorded in the school records. Her/his religion is <strong>{{ $religion }}</strong> and caste is <strong>{{ $cast }}</strong>. This certificate is issued upon request of the parent/guardian and verified from the school records.
            </p>
        @endif

        <div class="text-right mt-3 pt-2 border-t border-gray-200">
            <p class="text-xs font-medium text-gray-800">{{ $lang === 'gu' ? 'શાળાના આચાર્ય' : 'Principal' }}</p>
        </div>
    </div>
</div>
