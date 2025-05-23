@extends('mobile.layout')

@section('title', 'Mobile Register')

@section('content')
<div class="flex justify-center min-h-screen items-center bg-blue-50">
    <div class="bg-white rounded-xl shadow p-6 w-full max-w-xs">
        <div class="mb-4 text-center">
            <img src="/logo.png" alt="Logo" class="mx-auto h-12 mb-2">
            <h1 class="text-xl font-bold">Register</h1>
        </div>
        <form method="POST" action="{{ route('mobile.register.submit') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="name" class="w-full border rounded p-2" placeholder="Name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="w-full border rounded p-2" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="w-full border rounded p-2" placeholder="Password" required>
                @error('password')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="password_confirmation" class="w-full border rounded p-2" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold">Register</button>
        </form>
        <div class="mt-4 text-center text-xs">
            Already have an account? <a href="{{ route('mobile.login') }}" class="text-blue-600 font-semibold">Login</a>
        </div>
    </div>
</div>
@endsection 