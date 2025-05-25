@extends('mobile.layout')

@section('title', 'Profile')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full mt-4">
        <h2 class="text-center text-2xl font-bold mb-4">My Profile</h2>
        <div class="flex flex-col items-center mb-4">
            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center mb-2 overflow-hidden">
                @if($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}" class="w-20 h-20 rounded-full object-cover" alt="Profile">
                @else
                    <i class="fas fa-user text-4xl text-gray-400"></i>
                @endif
            </div>
            <div class="text-lg font-semibold mt-2">{{ $user->name }}</div>
            <div class="text-gray-600 text-sm">{{ $user->email }}</div>
            @if($user->phone)
                <div class="text-gray-600 text-sm">{{ $user->phone }}</div>
            @endif
            <div class="text-gray-500 text-xs mt-1">Role: {{ $user->role->name ?? 'N/A' }}</div>
        </div>
        <form method="POST" action="{{ route('mobile.profile.update-picture') }}" enctype="multipart/form-data" class="w-full flex flex-col items-center mb-4">
            @csrf
            <label class="block w-full text-center mb-2 font-semibold">Change Profile Picture</label>
            <div class="w-full flex items-center gap-2 mb-2">
                <label class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer font-semibold" for="profile-image-input">
                    Choose File
                </label>
                <span id="file-chosen" class="text-gray-700 text-sm truncate">No file chosen</span>
                <input id="profile-image-input" type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden" onchange="document.getElementById('file-chosen').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded font-semibold w-full">Upload</button>
        </form>
        <a href="{{ route('mobile.profile.change-password') }}" class="block w-full bg-yellow-500 text-white py-2 rounded font-semibold text-center mb-2">Change Password</a>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded font-semibold text-center">Logout</button>
        </form>
    </div>
</div>
@endsection 