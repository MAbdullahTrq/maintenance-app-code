@extends('mobile.layout')

@section('title', 'Properties')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div>
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg md:text-xl lg:text-2xl">All Properties</div>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Name</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Address</th>
                            <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        <tr class="border-b border-gray-400 hover:bg-gray-50">
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <div class="flex items-center gap-2 md:gap-3">
                                    @if($property->image)
                                        <img src="{{ asset('storage/' . $property->image) }}" class="rounded-full w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 object-cover" alt="Property Image">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($property->name) }}&background=eee&color=555&size=48" class="rounded-full w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12" alt="Profile">
                                    @endif
                                    <a href="{{ route('mobile.properties.show', $property->id) }}" class="font-semibold text-blue-700 hover:text-blue-900 cursor-pointer">{{ $property->name }}</a>
                                </div>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">{{ $property->address }}</td>
                            <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="px-2 py-1 text-gray-600 hover:text-gray-800 text-lg md:text-xl">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50" x-cloak>
                                        <div class="py-1">
                                            <a href="{{ route('mobile.properties.show', $property->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye mr-2"></i>View
                                            </a>
                                            <a href="{{ route('mobile.properties.edit', $property->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </a>
                                            <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-qrcode mr-2"></i>QR Code
                                            </a>
                                            @if($property->access_link)
                                            <a href="{{ route('guest.request.form', $property->access_link) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-link mr-2"></i>Public Link
                                            </a>
                                            @endif
                                        </div>
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

 