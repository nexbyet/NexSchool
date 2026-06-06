@extends('install.master')
@section('content')
<div class="cd p-6">
    <h2 class="text-base font-semibold text-white">Admin Account</h2>
    <p class="text-gray-400 text-xs mt-1 mb-5">Create the super admin account.</p>

    @if($errors->any())
    <div class="alert-e mb-4 flex items-start gap-2 text-sm"><i class="lni lni-warning mt-0.5"></i> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('install.admin.save') }}">@csrf
        <div class="space-y-3.5 mb-5">
            <div class="ig"><label>Full Name</label><input type="text" name="name" value="{{ old('name', session('install.admin.name', '')) }}" placeholder="e.g. Admin" required></div>
            <div class="ig"><label>Email</label><input type="email" name="email" value="{{ old('email', session('install.admin.email', '')) }}" placeholder="admin@nexschool.com" required></div>
            <div class="ig"><label>Password</label><input type="password" name="password" placeholder="Min 6 characters" required minlength="6"></div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('install.license') }}" class="btn btn-g flex-1"><i class="lni lni-arrow-left"></i> Back</a>
            <button type="submit" class="btn btn-p flex-1">Continue <i class="lni lni-arrow-right"></i></button>
        </div>
    </form>
</div>
@endsection
