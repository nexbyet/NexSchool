@extends('frontsite.layouts.master')
@section('title', 'પરિણામ')
@section('content')
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 md:p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">પરિણામ જુઓ</h1>
        <p class="text-sm text-gray-500 mb-6">GR નંબર અને જન્મ તારીખ દાખલ કરો.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('frontsite.result.show') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">શૈક્ષણિક વર્ષ</label>
                <select name="academic_year_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    @foreach ($academicYears as $y)
                        <option value="{{ $y->id }}" {{ old('academic_year_id') == $y->id ? 'selected' : '' }}>{{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">GR નંબર</label>
                <input type="text" name="gr_number" value="{{ old('gr_number') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">જન્મ તારીખ (DDMMYYYY)</label>
                <input type="text" name="dob" value="{{ old('dob') }}" required maxlength="8" pattern="\d{8}" placeholder="DDMMYYYY" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">સત્ર</label>
                <select name="semester" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="both" {{ old('semester','both') === 'both' ? 'selected' : '' }}>બંને (વાર્ષિક)</option>
                    <option value="1" {{ old('semester') === '1' ? 'selected' : '' }}>સત્ર 1</option>
                    <option value="2" {{ old('semester') === '2' ? 'selected' : '' }}>સત્ર 2</option>
                </select>
            </div>
            <button type="submit" class="w-full px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                પરિણામ જુઓ
            </button>
        </form>
    </div>
</div>
@endsection