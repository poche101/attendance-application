@extends('layouts.app')
@section('title', 'Admin Login')

@section('content')
<div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-[420px]">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-white-700 flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-200">
                <img src="/images/lekki-logo.png" alt="logo" class="w-12 h-12 object-contain">
            </div>
            <h1 class="font-head text-3xl font-extrabold tracking-tight text-slate-900">Admin Portal</h1>
            <p class="text-sm text-slate-500 mt-2 font-body">Sign in to manage Sunday Service attendance</p>
        </div>

        {{-- Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <label class="block text-xs font-body font-semibold uppercase tracking-widest text-blue-700 mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email" placeholder="admin@church.org" required
                        value="{{ old('email') }}"
                        class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-body text-slate-800 bg-slate-50 placeholder-slate-400 transition-all duration-200
                               focus:outline-none focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100">
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="block text-xs font-body font-semibold uppercase tracking-widest text-blue-700 mb-2">
                        Password
                    </label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-body text-slate-800 bg-slate-50 placeholder-slate-400 transition-all duration-200
                               focus:outline-none focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100">
                </div>

                {{-- Error --}}
                @if($errors->any())
                <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm font-body text-red-600">{{ $errors->first('email') }}</p>
                </div>
                @endif

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3 px-6 bg-blue-700 hover:bg-blue-800 active:bg-blue-900 text-white font-head font-bold text-sm tracking-wide rounded-xl transition-all duration-200 shadow-md shadow-blue-200 hover:shadow-lg hover:shadow-blue-300 hover:-translate-y-0.5">
                    Sign In →
                </button>
            </form>
        </div>

        {{-- Back link --}}
        <p class="text-center text-sm text-slate-400 font-body mt-6">
            <a href="{{ route('checkin') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                ← Back to Check-in
            </a>
        </p>
    </div>
</div>
@endsection
