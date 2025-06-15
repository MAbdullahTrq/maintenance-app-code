@extends('layouts.guest')

@section('title', isset($is_verification) && $is_verification ? 'Set Your Password' : 'Reset Password')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                @if(isset($is_verification) && $is_verification)
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Welcome to MaintainXtra!</h2>
                    <p class="mb-6 text-gray-600 text-center">Hi {{ $user_name ?? 'there' }}! Please set your password to complete your account setup.</p>
                @else
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reset your password</h2>
                @endif
                
                @if ($errors->has('token'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ $errors->first('token') }}</span>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    @if(isset($is_verification) && $is_verification)
                        <input type="hidden" name="is_verification" value="1">
                    @endif
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
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">
                            @if(isset($is_verification) && $is_verification)
                                Create Password
                            @else
                                New Password
                            @endif
                        </label>
                        <input id="password" type="password" name="password" required autofocus
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            @if(isset($is_verification) && $is_verification)
                                Set Password & Activate Account
                            @else
                                Reset Password
                            @endif
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