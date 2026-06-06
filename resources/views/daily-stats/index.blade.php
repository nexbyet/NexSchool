@extends('layouts.app')
@section('title', 'દૈનિક આંકડાબુક')
@section('content')
<div class="p-4 md:p-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-600 to-orange-600 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">દૈનિક આંકડાબુક</h1>
            <p class="text-amber-200 mt-1 text-sm">બધા ધોરણો અને વર્ગોના આંકડા — એક જ રીપોર્ટમાં</p>
        </div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/4 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">તારીખ પસંદ કરો</h2>
            </div>
            <form id="stats-form" class="p-5">
                @csrf
                <div class="flex items-end gap-4 flex-wrap">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">તારીખ</label>
                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="w-52 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <button type="submit" id="loadBtn" class="px-5 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition flex items-center gap-2 shadow-sm">
                        <i class="lni lni-search-1 text-base"></i> આંકડા બતાવો
                    </button>
                </div>
            </form>
        </div>

        <div id="statsContainer"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('stats-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('loadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="lni lni-spinner-3 text-base animate-spin"></i> લોડ થાય છે...';
    document.getElementById('statsContainer').innerHTML = '';

    var formData = new FormData(this);

    fetch('{{ route("daily-stats.show") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData,
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.html) {
            document.getElementById('statsContainer').innerHTML = res.html;
        } else if (res.error) {
            document.getElementById('statsContainer').innerHTML = '<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center"><p class="text-amber-600 font-medium">' + res.error + '</p></div>';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-search-1 text-base"></i> આંકડા બતાવો';
    })
    .catch(function() {
        NexSchool.alert.danger('આંકડા લાવવામાં ભૂલ.');
        btn.disabled = false;
        btn.innerHTML = '<i class="lni lni-search-1 text-base"></i> આંકડા બતાવો';
    });
});
</script>
@endpush

