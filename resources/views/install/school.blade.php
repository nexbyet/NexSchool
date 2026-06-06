@extends('install.master')
@section('content')
<div class="cd p-6">
    <h2 class="text-base font-semibold text-white">School Information</h2>
    <p class="text-gray-400 text-xs mt-1 mb-5">Tell us about your school.</p>

    @if($errors->any())
    <div class="alert-e mb-4 flex items-start gap-2 text-sm"><i class="lni lni-warning mt-0.5"></i> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('install.school.save') }}">@csrf
        <div class="space-y-3.5 mb-5">
            <div class="ig"><label>School Name (Gujarati)</label><input type="text" name="school_name_gu" value="{{ old('school_name_gu', session('install.school.school_name_gu', '')) }}" placeholder="શાળાનું નામ" required></div>
            <div class="ig"><label>School Name (English)</label><input type="text" name="school_name_en" value="{{ old('school_name_en', session('install.school.school_name_en', '')) }}" placeholder="e.g. NexSchool International" required></div>
            <div class="ig"><label>Address</label><textarea name="address" rows="2" placeholder="School address...">{{ old('address', session('install.school.address', '')) }}</textarea></div>
            <div class="ig"><label>Mobile</label><input type="text" name="mobile" value="{{ old('mobile', session('install.school.mobile', '')) }}" placeholder="+91 98765 43210"></div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('install.admin') }}" class="btn btn-g flex-1"><i class="lni lni-arrow-left"></i> Back</a>
            <button type="submit" class="btn btn-p flex-1">Install <i class="lni lni-checkmark-circle"></i></button>
        </div>
    </form>
</div>
@endsection
