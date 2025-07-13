@extends('mobile.layout')

@section('title', 'Edit Property - Manager')

@section('content')
<div class="bg-white rounded-xl shadow p-4 w-full max-w-4xl mx-auto">
    <div class="mb-2 flex items-center">
        <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
    <h2 class="text-center text-2xl font-bold mb-4">Edit Property</h2>
    <form method="POST" action="{{ route('mobile.properties.update', $property->id) }}" enctype="multipart/form-data" x-data="{ imagePreview: null, showPreview: false }">
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
            <label class="block font-semibold mb-1">Property Owner*</label>
            <select name="owner_id" class="w-full border rounded p-2" required>
                <option value="">Select an owner</option>
                @if(isset($owners))
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ old('owner_id', $property->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                    @endforeach
                @endif
            </select>
            @if(!isset($owners) || $owners->count() === 0)
                <div class="text-yellow-600 text-xs mt-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    No owners found. <a href="/m/ao/create" class="underline hover:text-yellow-800">Create an owner first</a>.
                </div>
            @endif
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Special instructions</label>
            <input type="text" name="special_instructions" class="w-full border rounded p-2" value="{{ old('special_instructions', $property->special_instructions) }}">
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Property image</label>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white border rounded p-4 flex flex-col items-center">
                    <label for="property-image-upload" class="cursor-pointer flex flex-col items-center justify-center h-full w-full">
                        <img src="/icons/icon_upload.png" alt="Upload" class="w-full h-auto max-h-32 object-contain mb-2">
                        <span class="text-xs text-gray-500">(JPEG, PNG, JPG, GIF, max 2MB)</span>
                        <input id="property-image-upload" type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full hidden"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        imagePreview = e.target.result;
                                        showPreview = true;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            ">
                    </label>
                </div>
                <div class="bg-white border rounded p-4 flex items-center justify-center">
                    <template x-if="showPreview">
                        <img :src="imagePreview" class="max-h-40 object-contain">
                    </template>
                    <template x-if="!showPreview && '{{ $property->image }}'">
                        <img src="{{ asset('storage/' . $property->image) }}" class="max-h-40 object-contain">
                    </template>
                    <template x-if="!showPreview && !'{{ $property->image }}'">
                        <div class="text-gray-400 text-center">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <div class="text-sm">No image</div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex gap-2 mt-6">
            <a href="{{ route('mobile.properties.index') }}" class="w-1/2 bg-gray-300 text-black py-2 rounded text-center font-semibold">Cancel</a>
            <button type="submit" class="w-1/2 bg-blue-600 text-white py-2 rounded font-semibold">Save</button>
        </div>
    </form>
</div>
@endsection 