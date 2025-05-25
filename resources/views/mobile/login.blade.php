@extends('mobile.layout')

@section('title', 'Mobile Login')

@section('content')
<div class="flex justify-center min-h-screen bg-blue-50">
    <div class="flex flex-col justify-center w-full max-w-xs mx-auto" style="min-height: 70vh;">
        <div class="bg-white rounded-xl shadow p-6 w-full">
            <div class="mb-4 text-center">
                <h1 class="text-xl font-bold">Sign In</h1>
            </div>
            <form method="POST" action="{{ route('mobile.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <input type="email" name="email" class="w-full border rounded p-2" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="w-full border rounded p-2" placeholder="Password" required>
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2">
                    <label for="remember" class="text-xs">Remember me</label>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold">Login</button>
            </form>
            <div class="mt-4 flex flex-col gap-2 text-center">
                <a href="/" class="text-blue-700 underline text-sm">&larr; Back to homepage</a>
                <span class="text-xs text-gray-500">Don't have an account?
                    <a href="{{ route('mobile.register') }}" class="text-blue-700 underline">Sign up</a>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection 