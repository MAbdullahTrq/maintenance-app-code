@extends('mobile.layout')
@section('title', 'Make a Request')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <h2 class="text-center text-lg font-bold mb-4">Make a Request</h2>
        <form method="POST" action="{{ route('mobile.requests.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Property*</label>
                <select name="property_id" class="w-full border rounded p-2" required>
                    <option value="">Select Property</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Title*</label>
                <input type="text" name="title" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Description*</label>
                <textarea name="description" class="w-full border rounded p-2" required></textarea>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Location*</label>
                <input type="text" name="location" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Priority*</label>
                <select name="priority" class="w-full border rounded p-2" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Images</label>
                <input type="file" name="images[]" class="w-full border rounded p-2" multiple accept="image/*">
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Submit Request</button>
        </form>
    </div>
</div>
@endsection 