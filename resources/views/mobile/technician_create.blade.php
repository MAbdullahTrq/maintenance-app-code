@extends('mobile.layout')
@section('title', 'Add Technician')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <h2 class="text-center text-lg font-bold mb-4">Add Technician</h2>
        <form method="POST" action="{{ route('mobile.technicians.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Name*</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Email*</label>
                <input type="email" name="email" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Phone*</label>
                <input type="text" name="phone" class="w-full border rounded p-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Technician</button>
        </form>
    </div>
</div>
@endsection 