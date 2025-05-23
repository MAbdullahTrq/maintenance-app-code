@extends('mobile.layout')

@section('title', 'Properties')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div x-data="{ showForm: false }">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg">All Properties</div>
                <button @click="showForm = !showForm" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Add Property</button>
            </div>
            <form x-show="showForm" method="POST" action="{{ route('mobile.properties.store') }}" class="mb-4 bg-gray-50 p-3 rounded border" @submit="showForm = false">
                @csrf
                <input type="text" name="name" class="w-full border rounded p-2 mb-2" placeholder="Name" required>
                <input type="text" name="address" class="w-full border rounded p-2 mb-2" placeholder="Address" required>
                <input type="text" name="special_instructions" class="w-full border rounded p-2 mb-2" placeholder="Special Instructions">
                <div class="flex gap-2">
                    <button type="submit" class="w-1/2 bg-blue-700 text-white py-2 rounded">Add</button>
                    <button type="button" @click="showForm = false" class="w-1/2 bg-gray-300 text-black py-2 rounded">Cancel</button>
                </div>
            </form>
            <div class="overflow-x-visible">
                <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 border-r border-gray-400">Name</th>
                            <th class="p-1 border-r border-gray-400">Address</th>
                            <th class="p-1">Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        <tr class="border-b border-gray-400">
                            <td class="p-1 align-top border-r border-gray-400">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($property->name) }}&background=eee&color=555&size=32" class="rounded-full w-7 h-7" alt="Profile">
                                    <a href="{{ route('mobile.properties.show', $property->id) }}" class="font-semibold text-blue-700">{{ $property->name }}</a>
                                </div>
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">{{ $property->address }}</td>
                            <td class="p-1 align-top">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="px-2 py-1"><i class="fas fa-ellipsis-v"></i></button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded shadow-lg z-50 border text-xs">
                                        <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="block px-4 py-2 hover:bg-gray-100">QR Code</a>
                                        <a href="{{ route('guest.request.form', $property->access_link) }}" class="block px-4 py-2 hover:bg-gray-100">Link</a>
                                        <a href="{{ route('mobile.properties.edit', $property->id) }}" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 