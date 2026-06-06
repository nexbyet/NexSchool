{{-- NexSchool Login Page --}}
{{-- સાઇન ઇન પેજ - ઇમેઇલ અને પાસવર્ડ વડે પ્રમાણીકરણ --}}

@extends('layouts.app')

@section('title', 'લોગિન')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        {{-- Logo + Heading --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-xl">N</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">NexSchool માં પ્રવેશ</h1>
            <p class="text-gray-500 mt-1">તમારું ખાતું ખોલવા માટે સાઇન ઇન કરો</p>
        </div>

        {{-- Error message display --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ઇમેઇલ / GR નંબર / મોબાઇલ</label>
                <input type="text" name="login" value="{{ old('login') }}" required autofocus
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">પાસવર્ડ</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>

            {{-- Remember me + Forgot password --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    યાદ રાખો
                </label>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">પાસવર્ડ ભૂલી ગયા?</a>
            </div>

            <button type="submit"
                    class="w-full py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition">
                સાઇન ઇન
            </button>


</div>
@endsection
