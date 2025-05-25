@extends('mobile.layout')

@section('title', 'Technician Details')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="mb-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($technician->name) }}&background=eee&color=555&size=64" class="rounded-full w-16 h-16 mx-auto" alt="Profile">
        </div>
        <div class="text-center font-bold text-lg mb-2">{{ $technician->name }}</div>
        <div class="text-center text-gray-600 mb-2">{{ $technician->email }}</div>
        <div class="text-center text-gray-600 mb-2">{{ $technician->phone }}</div>
        <!-- Add more technician details as needed -->
        <div class="mt-4">
            <a href="{{ url('m/at') }}" class="text-blue-700 underline">Back to list</a>
        </div>
    </div>
</div>
@endsection 