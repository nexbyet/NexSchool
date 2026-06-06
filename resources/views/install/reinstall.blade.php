@extends('layouts.app')
@section('title', 'Reinstall NexSchool')
@section('page_heading', 'Reinstall NexSchool')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-sm">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500 to-orange-600 shadow-lg shadow-red-500/20 mb-4">
                <i class="lni lni-warning text-3xl text-white"></i>
            </div>
            <h2 class="text-xl font-bold text-white">Reinstall NexSchool</h2>
            <p class="text-gray-400 text-sm mt-1">This will not delete your existing data, but will re-run migrations and allow you to update admin/school settings.</p>
        </div>

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-5 text-red-300 text-sm flex items-start gap-2">
            <i class="lni lni-warning mt-0.5"></i> {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('settings.reinstall.confirm') }}">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-400 mb-2">Confirm your password to continue</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500/30 outline-none" placeholder="Enter your admin password" required>
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-red-500 to-orange-600 text-white font-semibold text-sm hover:shadow-lg hover:shadow-red-500/20 transition-all">Confirm Reinstall</button>
        </form>

        <div class="mt-6 pt-6 border-t border-white/5">
            <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-white text-sm transition-colors"><i class="lni lni-arrow-left"></i> Go back</a>
        </div>
    </div>

    <div class="mt-6 bg-amber-500/5 border border-amber-500/10 rounded-xl p-5">
        <h4 class="text-sm font-semibold text-amber-400 mb-2 flex items-center gap-1.5"><i class="lni lni-shield"></i> What happens during reinstall?</h4>
        <ul class="text-xs text-amber-300/70 space-y-1.5 pl-4 list-disc">
            <li>Your existing database tables are <strong>preserved</strong></li>
            <li>New migrations are applied (if any)</li>
            <li>You can update the admin name, email, and password</li>
            <li>You can update school settings</li>
            <li>You can re-activate or change your license key</li>
        </ul>
    </div>
</div>
@endsection
