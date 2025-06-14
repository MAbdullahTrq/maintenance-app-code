@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reset your password</h2>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <div class="bg-gray-50 border rounded-md w-full py-2 px-3 text-gray-700">
                            {{ $email ?? old('email') }}
                        </div>
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">New Password</label>
                        <input id="password" type="password" name="password" required autofocus
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Reset Password
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