@extends('mobile.layout')

@section('title', 'Edit Technician')

@section('content')
<div class="bg-white rounded-xl shadow p-4 max-w-md mx-auto">
    <h2 class="text-center text-2xl font-bold mb-4">Edit Technician</h2>
    <form method="POST" action="{{ route('mobile.technicians.update', $technician->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="block font-semibold mb-1">Technician name*</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', $technician->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Email*</label>
            <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email', $technician->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Phone*</label>
            <input type="text" name="phone" class="w-full border rounded p-2" value="{{ old('phone', $technician->phone) }}" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Technician image</label>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-white border rounded p-4 flex items-center justify-center">
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full">
                </div>
                <div class="bg-white border rounded p-4 flex items-center justify-center">
                    @if($technician->image)
                        <img src="{{ asset('storage/' . $technician->image) }}" class="max-h-20 object-contain">
                    @else
                        <div class="text-gray-400 text-center">
                            <i class="fas fa-user text-4xl mb-2"></i>
                            <div class="text-sm">No image</div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-xs text-gray-500 mt-1">(JPEG, PNG, JPG, GIF, max 2MB)</div>
        </div>
        <div class="flex gap-2 mt-6">
            <a href="{{ route('mobile.technicians.index') }}" class="w-1/2 bg-gray-300 text-black py-2 rounded text-center font-semibold">Cancel</a>
            <button type="submit" class="w-1/2 bg-blue-600 text-white py-2 rounded font-semibold">Save</button>
        </div>
    </form>
    <form method="POST" action="{{ route('mobile.technicians.destroy', $technician->id) }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded font-semibold" onclick="return confirm('Are you sure you want to delete this technician?')">Delete Technician</button>
    </form>
</div>
@endsection 