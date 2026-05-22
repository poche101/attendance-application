@extends('layouts.app')
@section('title', 'Admin Login')

@section('content')
    <div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[380px]">

            <div class="text-center mb-9">
                <h1 class="cg text-4xl" style="color:#1E1208; font-weight:500;">Admin Portal</h1>
                <p class="text-sm mt-2" style="color:#B86A1A; font-family:'Jost',sans-serif;">Sign in to manage attendance</p>
            </div>

            <div class="bg-white border rounded-xl p-8" style="border-color:#FAD9B5;">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label"
                            style="font-family:'Jost',sans-serif;font-size:12px;letter-spacing:0.07em;text-transform:uppercase;color:#B86A1A;margin-bottom:6px;display:block;">
                            Email Address
                        </label>
                        <input type="email" name="email" placeholder="admin@church.org" required
                            style="width:100%;border:1px solid #FAD9B5;border-radius:8px;padding:10px 14px;font-size:14px;background:#FFFBF5;color:#1E1208;transition:border 0.2s;font-family:'Jost',sans-serif;"
                            value="{{ old('email') }}">
                    </div>
                    <div class="mb-6">
                        <label class="form-label"
                            style="font-family:'Jost',sans-serif;font-size:12px;letter-spacing:0.07em;text-transform:uppercase;color:#B86A1A;margin-bottom:6px;display:block;">
                            Password
                        </label>
                        <input type="password" name="password" placeholder="••••••••" required
                            style="width:100%;border:1px solid #FAD9B5;border-radius:8px;padding:10px 14px;font-size:14px;background:#FFFBF5;color:#1E1208;transition:border 0.2s;font-family:'Jost',sans-serif;">
                    </div>

                    @if ($errors->any())
                        <p class="text-sm mb-4" style="color:#991b1b; font-family:'Jost',sans-serif;">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                    <button type="submit"
                        class="w-full text-center bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold rounded-xl transition duration-150 py-3 shadow-sm shadow-orange-500/10">
                        Sign In
                    </button>

                </form>
            </div>
        </div>
    </div>
@endsection
