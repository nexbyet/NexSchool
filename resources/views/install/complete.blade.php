@extends('install.master')
@section('content')
<div class="cd p-6 text-center fi">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg shadow-emerald-500/15 mb-5">
        <i class="lni lni-checkmark-circle text-2xl text-white"></i>
    </div>
    <h2 class="text-base font-semibold text-white mb-1">Installation Complete!</h2>
    <p class="text-gray-400 text-xs mb-6">NexSchool has been installed successfully.</p>

    <div class="bg-white/[.025] border border-white/[.05] rounded-xl p-5 text-left mb-6">
        <h3 class="text-xs font-semibold text-gray-400 mb-3 uppercase tracking-wider">Your Details</h3>
        <div class="space-y-2.5">
            <div class="flex justify-between items-center p-2.5 rounded-lg bg-white/[.02]">
                <span class="text-gray-500 text-xs">Email</span>
                <span class="text-white font-medium text-xs">{{ $email }}</span>
            </div>
            <div class="flex justify-between items-center p-2.5 rounded-lg bg-white/[.02]">
                <span class="text-gray-500 text-xs">Password</span>
                <span class="text-gray-400 text-xs">(the one you set)</span>
            </div>
            <div class="flex justify-between items-center p-2.5 rounded-lg bg-white/[.02]">
                <span class="text-gray-500 text-xs">School</span>
                <span class="text-white font-medium text-xs">{{ $school_name }}</span>
            </div>
        </div>
    </div>

    <a href="{{ route('login') }}" class="btn btn-p w-full mb-4">Go to Login <i class="lni lni-arrow-right"></i></a>

    {{-- Security warning --}}
    <div class="bg-amber-500/5 border border-amber-500/10 rounded-lg p-4 text-left">
        <h4 class="text-xs font-semibold text-amber-400 mb-2 flex items-center gap-1.5"><i class="lni lni-shield"></i> Security Notice</h4>
        <p class="text-[11px] text-amber-300/70 leading-relaxed mb-2">
            For maximum security, <strong class="text-amber-300">delete the installer files</strong> from your server.
            Leaving them accessible may allow attackers to reset your installation and gain admin access.
        </p>
        <p class="text-[11px] text-amber-300/70 leading-relaxed">
            To reinstall in the future, restore these files from backup or <code class="px-1 py-0.5 bg-black/20 rounded text-amber-300/80">git checkout</code>.
            Alternatively, log in as admin and visit <strong class="text-amber-300">Settings → Reinstall</strong>.
        </p>
    </div>
</div>
@endsection
