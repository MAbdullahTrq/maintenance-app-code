@extends('mobile.layout')

@section('title', 'Profile')

@section('content')
<div class="bg-white rounded-xl shadow p-4 max-w-md mx-auto mt-4">
    <h2 class="text-center text-2xl font-bold mb-4">My Profile</h2>
    <div class="flex flex-col items-center mb-4">
        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center mb-2">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" class="w-20 h-20 rounded-full object-cover" alt="Profile">
            @else
                <i class="fas fa-user text-4xl text-gray-400"></i>
            @endif
        </div>
        <div class="text-lg font-semibold">{{ $user->name }}</div>
        <div class="text-gray-600 text-sm">{{ $user->email }}</div>
        <div class="text-gray-600 text-sm">{{ $user->phone }}</div>
        <div class="text-gray-500 text-xs mt-1">Role: {{ $user->role->name ?? 'N/A' }}</div>
    </div>
    <form method="POST" action="{{ route('mobile.profile.update-picture') }}" enctype="multipart/form-data" class="w-full flex flex-col items-center mb-4">
        @csrf
        <label class="block w-full text-center mb-2 font-semibold">Change Profile Picture</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full mb-2 border rounded p-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded font-semibold">Upload</button>
    </form>
    <a href="{{ route('mobile.profile.change-password') }}" class="w-full bg-yellow-500 text-white py-2 rounded font-semibold text-center mb-2">Change Password</a>
    <div class="flex flex-col gap-2">
        <a href="{{ route('logout') }}" class="w-full bg-red-600 text-white py-2 rounded font-semibold text-center">Logout</a>
    </div>
</div>
@endsection 