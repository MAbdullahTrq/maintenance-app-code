@extends('mobile.layout')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
            <div class="mb-6 text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create Account</h1>
                <p class="mt-2 text-sm text-gray-600">Join MaintainXtra today</p>
            </div>
            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="sr-only">Full Name</label>
                    <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Email address" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Password" required>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Confirm Password" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-sm transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Create Account
                </button>
            </form>
            <div class="mt-6 space-y-3 text-center">
                <a href="/" class="block text-blue-600 hover:text-blue-800 text-sm font-medium transition duration-200">&larr; Back to homepage</a>
                <div class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium transition duration-200">Sign in</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 