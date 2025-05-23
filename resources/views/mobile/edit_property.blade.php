@extends('mobile.layout')

@section('title', 'Edit Property - Manager')

@section('content')
<div class="bg-white rounded-xl shadow p-4 max-w-md mx-auto">
    <h2 class="text-center text-2xl font-bold mb-4">Edit Property</h2>
    <form method="POST" action="{{ route('mobile.properties.update', $property->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="block font-semibold mb-1">Property name*</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', $property->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Property address*</label>
            <input type="text" name="address" class="w-full border rounded p-2 font-bold" value="{{ old('address', $property->address) }}" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Special instructions</label>
            <input type="text" name="special_instructions" class="w-full border rounded p-2" value="{{ old('special_instructions', $property->special_instructions) }}">
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Property image</label>
            <div class="bg-white border rounded flex items-center justify-center py-4">
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="">
            </div>
            <div class="text-xs text-gray-500 mt-1">(JPEG, PNG, JPG, GIF, max 2MB)</div>
        </div>
        <div class="flex gap-2 mt-6">
            <a href="{{ route('mobile.properties.index') }}" class="w-1/2 bg-gray-300 text-black py-2 rounded text-center font-semibold">Cancel</a>
            <button type="submit" class="w-1/2 bg-blue-600 text-white py-2 rounded font-semibold">Save</button>
        </div>
    </form>
</div>
@endsection 