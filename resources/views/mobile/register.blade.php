@extends('mobile.layout')

@section('title', 'Mobile Register')

@section('content')
<div class="flex bg-blue-50">
    <div class="flex flex-col w-full max-w-xs mx-auto mt-10">
        <div class="bg-white rounded-xl shadow p-6 w-full">
            <div class="mb-4 text-center">
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
            <div class="mt-4 flex flex-col gap-2 text-center">
                <a href="/mobile" class="text-blue-700 underline text-sm">&larr; Back to homepage</a>
                <span class="text-xs text-gray-500">Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-700 underline">Login</a>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection 