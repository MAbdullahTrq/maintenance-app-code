@extends('mobile.layout')
@section('title', 'Add Owner')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="mb-2 flex items-center">
            <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        <h2 class="text-center text-lg font-bold mb-4">Add Owner</h2>
        <form method="POST" action="/m/ao/add" id="owner-form">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Full Name*</label>
                <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Email Address*</label>
                <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Phone Number</label>
                <input type="tel" name="phone" class="w-full border rounded p-2" value="{{ old('phone') }}">
                @error('phone')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Company</label>
                <input type="text" name="company" class="w-full border rounded p-2" value="{{ old('company') }}">
                @error('company')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Address</label>
                <textarea name="address" class="w-full border rounded p-2" rows="3">{{ old('address') }}</textarea>
                @error('address')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Notes</label>
                <textarea name="notes" class="w-full border rounded p-2" rows="4" placeholder="Any additional notes about the owner...">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" id="submit-btn" class="w-full bg-blue-700 text-white py-2 rounded">Add Owner</button>
        </form>
    </div>
</div>
@endsection 