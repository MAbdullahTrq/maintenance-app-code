@extends('layouts.guest')

@section('title', 'Resend Verification Email')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Resend Verification Email</h1>
            <div class="text-blue-600 text-4xl mb-4">
                <i class="fas fa-paper-plane"></i>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        @if (session('message'))
            <div class="mb-4 font-medium text-sm text-blue-600">
                {{ session('message') }}
            </div>
        @endif

        <div class="mb-4 text-sm text-gray-600 text-center">
            Enter your email address below and we'll send you a new verification link.
        </div>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                       required autofocus>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Send Verification Email
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                Back to Login
            </a>
        </div>

        <div class="mt-2 text-center">
            <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-500">
                Need to create an account?
            </a>
        </div>
    </div>
</div>
@endsection 