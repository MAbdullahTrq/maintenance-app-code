@extends('layouts.app')

@section('title', 'Edit Technician')

@section('header', 'Edit Technician')

@section('content')
<div class="container mx-auto px-4 pt-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('technicians.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-gray-500"></i>Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror" 
                        placeholder="Enter technician's phone number" required>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-image mr-2 text-gray-500"></i>Profile Image
                    </label>
                    @if($user->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-32 h-32 object-cover rounded-full">
                            <p class="text-sm text-gray-500 mt-1">Current image</p>
                        </div>
                    @endif
                    <input type="file" id="image" name="image" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Optional. Upload a new profile image to replace the current one (JPEG, PNG, JPG, GIF, max 2MB).</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('technicians.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Update Technician
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 