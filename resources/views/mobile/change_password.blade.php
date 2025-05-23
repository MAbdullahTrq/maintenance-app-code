@extends('mobile.layout')

@section('title', 'Change Password')

@section('content')
<div class="bg-white rounded-xl shadow p-4 max-w-md mx-auto mt-4">
    <h2 class="text-center text-2xl font-bold mb-4">Change Password</h2>
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-4">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('mobile.profile.change-password.submit') }}">
        @csrf
        <div class="mb-3">
            <label class="block font-semibold mb-1">Current Password</label>
            <input type="password" name="current_password" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">New Password</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
        </div>
        <div class="flex gap-2 mt-6">
            <a href="{{ route('mobile.profile') }}" class="w-1/2 bg-gray-300 text-black py-2 rounded text-center font-semibold">Cancel</a>
            <button type="submit" class="w-1/2 bg-blue-600 text-white py-2 rounded font-semibold">Change</button>
        </div>
    </form>
</div>
@endsection 