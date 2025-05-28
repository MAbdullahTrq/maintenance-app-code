@extends('mobile.layout')
@section('title', 'Add Property')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <h2 class="text-center text-lg font-bold mb-4">Add Property</h2>
        <form method="POST" action="{{ route('mobile.properties.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Name*</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Address*</label>
                <input type="text" name="address" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Special Instructions</label>
                <textarea name="special_instructions" class="w-full border rounded p-2"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Property</button>
        </form>
    </div>
</div>
@endsection 