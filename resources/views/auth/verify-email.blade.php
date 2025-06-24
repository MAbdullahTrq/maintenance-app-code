@extends('layouts.guest')

@section('title', 'Verify Your Email')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h1>
            <div class="text-green-600 text-4xl mb-4">
                <i class="fas fa-envelope-circle-check"></i>
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
            Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
        </div>

        <div class="mb-4 text-sm text-gray-600 text-center">
            <strong>Important:</strong> Your account is currently inactive. You won't be able to log in until you verify your email address.
        </div>

        <form method="POST" action="{{ route('verification.resend') }}" class="mb-4">
            @csrf
            @if(session('email'))
                <input type="hidden" name="email" value="{{ session('email') }}">
            @else
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Resend Verification Email
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                Already verified? Log in here
            </a>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('verification.resend.form') }}" class="text-sm text-gray-600 hover:text-gray-500">
                Need to use a different email? Click here
            </a>
        </div>
    </div>
</div>
@endsection 