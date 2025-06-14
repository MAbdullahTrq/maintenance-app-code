@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Forgot your password?</h2>
                <p class="mb-6 text-gray-600 text-center">Enter your email address and we'll send you a link to reset your password.</p>
                @if (session('status'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Send Password Reset Link
                        </button>
                    </div>
                </form>
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition duration-200">&larr; Back to login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 